<?php

namespace App\Http\Controllers;

use App\Models\Gudang;
use App\Models\Kontainer;
use App\Models\StockKontainer;
use App\Services\GudangPlanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GudangPlanController extends Controller
{
    public function __construct(private GudangPlanService $plans) {}

    public function index()
    {
        $gudangs = Gudang::orderBy('nama_gudang')->get();

        return view('denah-gudang.index', compact('gudangs'));
    }

    public function show(Gudang $gudang)
    {
        return view('denah-gudang.show', ['gudang' => $gudang, 'state' => $this->plans->state($gudang)]);
    }

    public function layout(Gudang $masterGudang)
    {
        return view('master-gudang.layout', ['gudang' => $masterGudang, 'state' => $this->plans->state($masterGudang)]);
    }

    public function updateLayout(Request $request, Gudang $masterGudang)
    {
        $data = $request->validate(['version' => 'required|integer|min:0', 'layout' => 'required|array:blocks']);
        $layout = $this->plans->validateLayout($data['layout']);

        return DB::transaction(function () use ($masterGudang, $data, $layout) {
            $gudang = $this->lockedGudang($masterGudang->id, $data['version']);
            $this->plans->validatePositions($layout, $gudang->positions()->get()->toArray());
            $gudang->denah_layout = $layout;
            $gudang->denah_version++;
            $gudang->save();

            return response()->json($this->plans->state($gudang));
        });
    }

    public function store(Request $request, Gudang $gudang)
    {
        $data = $request->validate([
            'version' => 'required|integer|min:0',
            'source' => 'required|in:sewa,stock',
            'container_id' => 'required|integer|min:1',
            'block' => 'required|string|max:12',
            'bay' => 'required|integer|min:1',
            'row' => 'required|integer|min:1',
            'tier' => 'required|integer|min:1',
        ], [], [
            'block' => 'area',
            'bay' => 'slot',
            'row' => 'baris',
            'tier' => 'tingkat',
        ]);

        return DB::transaction(function () use ($gudang, $data) {
            $gudang = $this->lockedGudang($gudang->id, $data['version']);
            if ($gudang->status !== 'aktif') {
                throw ValidationException::withMessages(['gudang' => 'Gudang nonaktif tidak dapat menerima penempatan kontainer.']);
            }
            $model = $data['source'] === 'stock' ? StockKontainer::class : Kontainer::class;
            $container = $model::whereKey($data['container_id'])->where('gudangs_id', $gudang->id)
                ->where('status', '!=', 'inactive')->lockForUpdate()->first();
            if (! $container) {
                throw ValidationException::withMessages(['container_id' => 'Kontainer tidak tersedia di gudang ini. Muat ulang halaman.']);
            }
            $span = $this->plans->span($container->ukuran);
            $number = $container->nomor_seri_gabungan ?: $container->awalan_kontainer.$container->nomor_seri_kontainer.$container->akhiran_kontainer;
            if (! $span || ! trim($number)) {
                throw ValidationException::withMessages(['container_id' => 'Lengkapi nomor dan ukuran kontainer (20 atau 40 kaki) pada master kontainer terlebih dahulu.']);
            }
            unset($data['version']);
            $data['span'] = $span;
            $data['container_number'] = $number;
            $positions = $gudang->positions()->get()->reject(fn ($p) => $p->source === $data['source'] && $p->container_id === (int) $data['container_id'])->toArray();
            $positions[] = $data;
            $this->plans->validatePositions($gudang->denah_layout, $positions);
            $gudang->positions()->updateOrCreate([
                'source' => $data['source'], 'container_id' => $data['container_id'],
            ], $data);
            $gudang->denah_version++;
            $gudang->save();

            return response()->json($this->plans->state($gudang));
        });
    }

    public function destroy(Request $request, Gudang $gudang, int $position)
    {
        $data = $request->validate(['version' => 'required|integer|min:0']);

        return DB::transaction(function () use ($gudang, $position, $data) {
            $gudang = $this->lockedGudang($gudang->id, $data['version']);
            $record = $gudang->positions()->findOrFail($position);
            $positions = $gudang->positions()->where('id', '!=', $record->id)->get()->toArray();
            $this->plans->validatePositions($gudang->denah_layout, $positions);
            $record->delete();
            $gudang->denah_version++;
            $gudang->save();

            return response()->json($this->plans->state($gudang));
        });
    }

    private function lockedGudang(int $id, int $version): Gudang
    {
        $gudang = Gudang::lockForUpdate()->findOrFail($id);
        abort_if($gudang->denah_version !== $version, 409, 'Denah sudah diubah pengguna lain. Muat ulang halaman sebelum menyimpan kembali.');

        return $gudang;
    }
}
