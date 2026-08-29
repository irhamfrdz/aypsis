<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\PersetujuanAbsensiLupa;
use Illuminate\Http\Request;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Absensi;

class MasterPersetujuanAbsensiLupaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = PersetujuanAbsensiLupa::with(['karyawan', 'approver', 'creator'])
                ->orderBy('created_at', 'desc')
                ->get();
            
            $formattedData = $data->map(function ($row, $index) {
                $status_badge = '';
                if ($row->status == 'approved') {
                    $status_badge = '<span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Disetujui</span>';
                } elseif ($row->status == 'rejected') {
                    $status_badge = '<span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-full">Ditolak</span>';
                } else {
                    $status_badge = '<span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded-full">Menunggu</span>';
                }

                return [
                    'DT_RowIndex' => $index + 1,
                    'karyawan_nama' => $row->karyawan ? $row->karyawan->nama_lengkap : '-',
                    'karyawan_nik' => $row->karyawan ? $row->karyawan->nik : '-',
                    'tanggal_format' => Carbon::parse($row->tanggal)->format('d-m-Y'),
                    'waktu_format' => Carbon::parse($row->waktu)->format('H:i'),
                    'tipe_absen' => $row->tipe_absen,
                    'alasan' => $row->alasan,
                    'status_badge' => $status_badge,
                    'action' => view('master-persetujuan-absensi-lupa.action', compact('row'))->render(),
                ];
            });

            return response()->json(['data' => $formattedData]);
        }

        return view('master-persetujuan-absensi-lupa.index');
    }

    public function create()
    {
        $karyawans = Karyawan::where('status', 'Aktif')->get();
        return view('master-persetujuan-absensi-lupa.create', compact('karyawans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'tanggal' => 'required|date',
            'tipe_absen' => 'required|string',
            'waktu' => 'required',
            'alasan' => 'required|string',
        ]);

        $pengajuan = PersetujuanAbsensiLupa::create([
            'karyawan_id' => $request->karyawan_id,
            'tanggal' => $request->tanggal,
            'tipe_absen' => $request->tipe_absen,
            'waktu' => $request->waktu,
            'alasan' => $request->alasan,
            'status' => 'pending',
            'created_by' => auth()->id(),
        ]);
        
        // Kirim notifikasi ke semua user yang memiliki hak akses approval
        $approvers = \App\Models\User::all()->filter(function($user) {
            return $user->can('approval-absensi-lupa-approve');
        });
        
        if ($approvers->count() > 0) {
            \Illuminate\Support\Facades\Notification::send($approvers, new \App\Notifications\PersetujuanAbsensiLupaNotification($pengajuan));
            
            // Siapkan data untuk Push Notification (Device/HP)
            $pengajuan->load('karyawan');
            $karyawanNama = $pengajuan->karyawan ? $pengajuan->karyawan->nama_lengkap : 'Karyawan';
            $title = "Pengajuan Lupa Absen";
            $body = "{$karyawanNama} mengajukan lupa absen {$pengajuan->tipe_absen}.";
            
            // Kirim Push Notification ke setiap approver yang memiliki token
            foreach ($approvers as $approver) {
                if ($approver->expo_push_token) {
                    \App\Services\ExpoNotificationService::send(
                        $approver->expo_push_token,
                        $title,
                        $body,
                        ['url' => route('master.persetujuan-absensi-lupa.index', [], false)]
                    );
                }
            }
        }

        return redirect()->route('master.persetujuan-absensi-lupa.index')->with('success', 'Pengajuan absensi lupa berhasil dibuat.');
    }

    public function edit(PersetujuanAbsensiLupa $persetujuanAbsensiLupa)
    {
        if ($persetujuanAbsensiLupa->status !== 'pending') {
            return redirect()->route('master.persetujuan-absensi-lupa.index')->with('error', 'Data sudah diproses dan tidak dapat diubah.');
        }

        $karyawans = Karyawan::where('status', 'Aktif')->get();
        return view('master-persetujuan-absensi-lupa.edit', compact('persetujuanAbsensiLupa', 'karyawans'));
    }

    public function update(Request $request, PersetujuanAbsensiLupa $persetujuanAbsensiLupa)
    {
        if ($persetujuanAbsensiLupa->status !== 'pending') {
            return redirect()->route('master.persetujuan-absensi-lupa.index')->with('error', 'Data sudah diproses dan tidak dapat diubah.');
        }

        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'tanggal' => 'required|date',
            'tipe_absen' => 'required|string',
            'waktu' => 'required',
            'alasan' => 'required|string',
        ]);

        $persetujuanAbsensiLupa->update([
            'karyawan_id' => $request->karyawan_id,
            'tanggal' => $request->tanggal,
            'tipe_absen' => $request->tipe_absen,
            'waktu' => $request->waktu,
            'alasan' => $request->alasan,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('master.persetujuan-absensi-lupa.index')->with('success', 'Pengajuan absensi lupa berhasil diperbarui.');
    }

    public function destroy(PersetujuanAbsensiLupa $persetujuanAbsensiLupa)
    {
        if ($persetujuanAbsensiLupa->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Data sudah diproses dan tidak dapat dihapus.'], 403);
        }

        $persetujuanAbsensiLupa->delete();
        return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
    }

    public function approve(Request $request, PersetujuanAbsensiLupa $persetujuanAbsensiLupa)
    {
        if ($persetujuanAbsensiLupa->status !== 'pending') {
            return redirect()->route('master.persetujuan-absensi-lupa.index')->with('error', 'Data sudah diproses sebelumnya.');
        }

        DB::beginTransaction();
        try {
            $persetujuanAbsensiLupa->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            // Tambahkan ke tabel absensis juga agar tercatat sebagai kehadiran sah
            $karyawan = Karyawan::find($persetujuanAbsensiLupa->karyawan_id);
            if($karyawan) {
                // Mapping tipe absen ke format absensis yang standar
                $mappedTipe = $persetujuanAbsensiLupa->tipe_absen;
                if (strtolower($mappedTipe) === 'mulai lembur') {
                    $mappedTipe = 'lembur_masuk';
                } elseif (strtolower($mappedTipe) === 'selesai lembur') {
                    $mappedTipe = 'lembur_pulang';
                } elseif (strtolower($mappedTipe) === 'check in') {
                    $mappedTipe = 'masuk';
                } elseif (strtolower($mappedTipe) === 'pulang') {
                    $mappedTipe = 'pulang';
                }

                // Kombinasikan tanggal dan waktu
                $waktuDateTime = Carbon::parse($persetujuanAbsensiLupa->tanggal)->format('Y-m-d') . ' ' . Carbon::parse($persetujuanAbsensiLupa->waktu)->format('H:i:s');
                
                $tanggalAbsen = Carbon::parse($persetujuanAbsensiLupa->tanggal);
                $startDateObj = $tanggalAbsen->copy()->setTime(6, 0, 0);
                $endDateObj = $tanggalAbsen->copy()->addDays(1)->setTime(5, 59, 59);

                $existingLog = Absensi::where('nik', $karyawan->nik)
                    ->where('tipe', $mappedTipe)
                    ->whereBetween('waktu', [$startDateObj, $endDateObj])
                    ->first();

                if ($existingLog) {
                    $existingLog->update([
                        'waktu' => $waktuDateTime,
                        'keterangan' => 'Lupa Absen: ' . $persetujuanAbsensiLupa->alasan,
                        'status' => 'Valid',
                        'device' => 'Manual Approval',
                    ]);
                } else {
                    Absensi::create([
                        'karyawan_id' => $karyawan->id,
                        'nik' => $karyawan->nik,
                        'waktu' => $waktuDateTime,
                        'tipe' => $mappedTipe,
                        'keterangan' => 'Lupa Absen: ' . $persetujuanAbsensiLupa->alasan,
                        'status' => 'Valid',
                        'device' => 'Manual Approval',
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('master.persetujuan-absensi-lupa.index')->with('success', 'Pengajuan absensi lupa berhasil disetujui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('master.persetujuan-absensi-lupa.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, PersetujuanAbsensiLupa $persetujuanAbsensiLupa)
    {
        if ($persetujuanAbsensiLupa->status !== 'pending') {
            return redirect()->route('master.persetujuan-absensi-lupa.index')->with('error', 'Data sudah diproses sebelumnya.');
        }

        $request->validate([
            'catatan_admin' => 'nullable|string',
        ]);

        $persetujuanAbsensiLupa->update([
            'status' => 'rejected',
            'catatan_admin' => $request->catatan_admin,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('master.persetujuan-absensi-lupa.index')->with('success', 'Pengajuan absensi lupa telah ditolak.');
    }
}
