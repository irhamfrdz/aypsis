<?php

namespace Tests\Feature;

use App\Http\Controllers\HrdDashboardController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class HrdDashboardDetailExportTest extends TestCase
{
    public function test_missing_clock_out_dates_are_matched_per_day(): void
    {
        $controller = new class extends HrdDashboardController {
            public function dates(array $masuk, array $pulang): array
            {
                $logs = fn ($dates) => collect($dates)->map(fn ($date) => (object) ['waktu' => $date]);

                return $this->missingClockOutDates($logs($masuk), $logs($pulang));
            }
        };

        $this->assertSame(['2026-10-07', '2026-10-09'], $controller->dates([
            '2026-10-09 08:00:00', '2026-10-07 08:00:00',
            '2026-10-07 08:01:00', '2026-10-08 08:00:00',
        ], ['2026-10-08 17:00:00']));
        $this->assertSame([], $controller->dates(['2026-10-07 08:00:00'], ['2026-10-07 17:00:00']));
        $this->assertSame(['2026-10-07'], $controller->dates(['2026-10-07 08:00:00'], []));
        $this->assertSame([], $controller->dates([], ['2026-10-07 17:00:00']));
    }

    public function test_missing_clock_in_export_respects_search_and_preserves_nik(): void
    {
        $data = [
            'startDate' => Carbon::parse('2026-10-03'),
            'endDate' => Carbon::parse('2026-10-09'),
            'karyawanBelumAbsen' => collect([
                (object) [
                    'nik' => '0233',
                    'nama_lengkap' => 'ACENG JAELANI',
                    'divisi' => 'KRANI',
                    'cabang' => 'JAKARTA',
                    'tanggal_belum_masuk' => ['2026-10-07'],
                ],
                (object) [
                    'nik' => '0671',
                    'nama_lengkap' => 'ATOSOKHI ZAI',
                    'divisi' => 'KRANI',
                    'cabang' => 'JAKARTA',
                    'tanggal_belum_masuk' => ['2026-10-04'],
                ],
            ]),
        ];

        $controller = new class($data) extends HrdDashboardController {
            public function __construct(private array $data) {}

            public function index(Request $request)
            {
                return new class($this->data) {
                    public function __construct(private array $data) {}

                    public function getData(): array
                    {
                        return $this->data;
                    }
                };
            }
        };

        $response = $controller->exportDetail(Request::create('/hrd-dashboard/export-detail', 'GET', [
            'kategori' => 'belum-masuk',
            'q' => 'ACENG',
        ]));
        $path = $response->getFile()->getPathname();

        try {
            $this->assertStringContainsString('Belum_Absen_Masuk_20261003_20261009.xlsx', $response->headers->get('Content-Disposition'));
            $sheet = IOFactory::load($path)->getActiveSheet();
            $this->assertSame(2, $sheet->getHighestRow());
            $this->assertSame(['No', 'NIK', 'Nama Karyawan', 'Divisi', 'Cabang', 'Tanggal Tanpa Scan Masuk'], $sheet->rangeToArray('A1:F1')[0]);
            $this->assertSame('0233', $sheet->getCell('B2')->getValue());
            $this->assertSame(DataType::TYPE_STRING, $sheet->getCell('B2')->getDataType());
            $this->assertSame('ACENG JAELANI', $sheet->getCell('C2')->getValue());
            $this->assertSame('07-10-2026', $sheet->getCell('F2')->getValue());
        } finally {
            unlink($path);
        }
    }

    public function test_export_detail_route_requires_dashboard_export_permission(): void
    {
        $route = app('router')->getRoutes()->match(Request::create('/hrd-dashboard/export-detail'));

        $this->assertSame('hrd.dashboard.export-detail', $route->getName());
        $this->assertContains('can:hrd-dashboard-export', $route->gatherMiddleware());
    }

    public function test_each_other_detail_category_downloads_its_own_data(): void
    {
        $karyawan = (object) [
            'nik' => '0233', 'nama_lengkap' => 'ACENG JAELANI',
            'divisi' => 'KRANI', 'cabang' => 'JAKARTA',
            'tanggal_belum_pulang' => ['2026-10-07', '2026-10-09'],
        ];
        $absen = (object) [
            'karyawan' => $karyawan,
            'waktu' => '2026-10-07 19:40:00',
            'jarak_absen_meter' => 120.5,
            'radius_absensi_meter' => 200,
            'nama_lokasi_absensi' => 'Kantor Jakarta',
            'tipe' => 'Pulang',
            'detail_lokasi' => 'Di luar radius',
        ];
        $data = [
            'startDate' => Carbon::parse('2026-10-03'),
            'endDate' => Carbon::parse('2026-10-09'),
            'karyawanHadirNormal' => collect([$absen]),
            'karyawanBelumAbsenPulang' => collect([$karyawan]),
            'karyawanTerlambat' => collect([$absen]),
            'absensiLuarRadius' => collect([$absen]),
        ];
        $controller = new class($data) extends HrdDashboardController {
            public function __construct(private array $data) {}

            public function index(Request $request)
            {
                return new class($this->data) {
                    public function __construct(private array $data) {}

                    public function getData(): array
                    {
                        return $this->data;
                    }
                };
            }
        };

        foreach (['hadir-normal', 'belum-pulang', 'terlambat', 'luar-radius'] as $category) {
            $response = $controller->exportDetail(Request::create('/hrd-dashboard/export-detail', 'GET', [
                'kategori' => $category,
            ]));
            $path = $response->getFile()->getPathname();

            try {
                $sheet = IOFactory::load($path)->getActiveSheet();
                $this->assertSame(2, $sheet->getHighestRow(), $category);
                $this->assertSame('0233', $sheet->getCell('B2')->getValue(), $category);
                $this->assertSame('ACENG JAELANI', $sheet->getCell('C2')->getValue(), $category);
                if ($category === 'belum-pulang') {
                    $this->assertSame('Tanggal Belum Absen Pulang', $sheet->getCell('F1')->getValue());
                    $this->assertSame('07-10-2026, 09-10-2026', $sheet->getCell('F2')->getValue());
                }
            } finally {
                unlink($path);
            }
        }
    }
}
