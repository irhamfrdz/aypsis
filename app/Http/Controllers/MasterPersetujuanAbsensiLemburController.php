<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PersetujuanAbsensiLembur;
use App\Models\Karyawan;
use App\Models\Absensi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PersetujuanAbsensiLemburNotification;
use App\Services\ExpoNotificationService;

class MasterPersetujuanAbsensiLemburController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = PersetujuanAbsensiLembur::with(['karyawan', 'approver', 'creator'])
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

                $foto_html = '-';
                if ($row->foto) {
                    $fotoUrl = asset('storage/' . $row->foto);
                    $foto_html = '<a href="' . $fotoUrl . '" target="_blank"><img src="' . $fotoUrl . '" class="w-10 h-10 rounded-md object-cover border" alt="Foto"></a>';
                }

                $action = view('master-persetujuan-absensi-lembur.action', ['row' => $row])->render();

                return [
                    'DT_RowIndex' => $index + 1,
                    'karyawan_nama' => $row->karyawan ? ($row->karyawan->nama_panggilan ?: $row->karyawan->nama_lengkap) : 'Unknown',
                    'karyawan_nik' => $row->karyawan ? $row->karyawan->nik : '-',
                    'tanggal_format' => Carbon::parse($row->tanggal)->format('d M Y'),
                    'jam_mulai_format' => Carbon::parse($row->jam_mulai)->format('H:i'),
                    'jam_selesai_format' => Carbon::parse($row->jam_selesai)->format('H:i'),
                    'keterangan' => $row->keterangan,
                    'foto' => $foto_html,
                    'status_badge' => $status_badge,
                    'action' => $action
                ];
            });

            return response()->json(['data' => $formattedData]);
        }

        return view('master-persetujuan-absensi-lembur.index');
    }

    public function create()
    {
        $karyawans = Karyawan::where('status', 'Aktif')->get();
        return view('master-persetujuan-absensi-lembur.create', compact('karyawans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'nullable',
            'keterangan' => 'required|string',
        ]);

        $pengajuan = PersetujuanAbsensiLembur::create([
            'karyawan_id' => $request->karyawan_id,
            'tanggal' => $request->tanggal,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'keterangan' => $request->keterangan,
            'status' => 'pending',
            'created_by' => auth()->id(),
        ]);
        
        // Kirim notifikasi
        $approvers = User::all()->filter(function($user) {
            return $user->can('approval-absensi-lembur-approve');
        });
        
        if ($approvers->count() > 0) {
            Notification::send($approvers, new PersetujuanAbsensiLemburNotification($pengajuan));
            
            $pengajuan->load('karyawan');
            $karyawanNama = $pengajuan->karyawan ? $pengajuan->karyawan->nama_lengkap : 'Karyawan';
            $title = "Pengajuan Lembur Baru";
            $body = "{$karyawanNama} mengajukan lembur pada tanggal " . Carbon::parse($pengajuan->tanggal)->format('d M Y') . ".";
            
            foreach ($approvers as $approver) {
                if ($approver->expo_push_token) {
                    ExpoNotificationService::send(
                        $approver->expo_push_token,
                        $title,
                        $body,
                        ['url' => route('master.persetujuan-absensi-lembur.index', [], false)]
                    );
                }
            }
        }

        return redirect()->route('master.persetujuan-absensi-lembur.index')->with('success', 'Pengajuan lembur berhasil dibuat.');
    }

    public function edit(PersetujuanAbsensiLembur $persetujuanAbsensiLembur)
    {
        if ($persetujuanAbsensiLembur->status !== 'pending') {
            return redirect()->route('master.persetujuan-absensi-lembur.index')->with('error', 'Hanya pengajuan pending yang dapat diubah.');
        }

        $karyawans = Karyawan::where('status', 'Aktif')->get();
        return view('master-persetujuan-absensi-lembur.edit', compact('persetujuanAbsensiLembur', 'karyawans'));
    }

    public function update(Request $request, PersetujuanAbsensiLembur $persetujuanAbsensiLembur)
    {
        if ($persetujuanAbsensiLembur->status !== 'pending') {
            return redirect()->route('master.persetujuan-absensi-lembur.index')->with('error', 'Hanya pengajuan pending yang dapat diubah.');
        }

        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'nullable',
            'keterangan' => 'required|string',
        ]);

        $persetujuanAbsensiLembur->update([
            'karyawan_id' => $request->karyawan_id,
            'tanggal' => $request->tanggal,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'keterangan' => $request->keterangan,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('master.persetujuan-absensi-lembur.index')->with('success', 'Pengajuan lembur berhasil diperbarui.');
    }

    public function destroy(PersetujuanAbsensiLembur $persetujuanAbsensiLembur)
    {
        $persetujuanAbsensiLembur->delete();
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Pengajuan lembur berhasil dihapus.'
            ]);
        }

        return redirect()->route('master.persetujuan-absensi-lembur.index')->with('success', 'Pengajuan lembur berhasil dihapus.');
    }

    public function approve(Request $request, PersetujuanAbsensiLembur $persetujuanAbsensiLembur)
    {
        if ($persetujuanAbsensiLembur->status !== 'pending') {
            return redirect()->back()->with('error', 'Pengajuan ini sudah diproses.');
        }

        DB::beginTransaction();
        try {
            $persetujuanAbsensiLembur->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'catatan_admin' => $request->catatan_admin
            ]);

            // Sync ke absensi
            $waktuMasuk = Carbon::parse($persetujuanAbsensiLembur->tanggal->format('Y-m-d') . ' ' . $persetujuanAbsensiLembur->jam_mulai);
            $waktuPulang = Carbon::parse($persetujuanAbsensiLembur->tanggal->format('Y-m-d') . ' ' . $persetujuanAbsensiLembur->jam_selesai);
            
            // Jika jam selesai lebih kecil dari jam mulai (misal lewat tengah malam), tambah 1 hari
            if ($waktuPulang->lt($waktuMasuk)) {
                $waktuPulang->addDay();
            }
            
            $karyawan = Karyawan::find($persetujuanAbsensiLembur->karyawan_id);
            if ($karyawan) {
                // Insert Lembur Masuk
                Absensi::create([
                    'karyawan_id' => $karyawan->id,
                    'nik' => $karyawan->nik ?? '-',
                    'waktu' => $waktuMasuk,
                    'tipe' => 'lembur_masuk',
                    'keterangan' => "Disetujui dari form lembur: {$persetujuanAbsensiLembur->keterangan}",
                ]);

                // Insert Lembur Pulang jika ada jam selesai
                if ($persetujuanAbsensiLembur->jam_selesai) {
                    Absensi::create([
                        'karyawan_id' => $karyawan->id,
                        'nik' => $karyawan->nik ?? '-',
                        'waktu' => $waktuPulang,
                        'tipe' => 'lembur_pulang',
                        'keterangan' => "Disetujui dari form lembur: {$persetujuanAbsensiLembur->keterangan}",
                    ]);
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Pengajuan lembur berhasil disetujui dan data absensi telah ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, PersetujuanAbsensiLembur $persetujuanAbsensiLembur)
    {
        if ($persetujuanAbsensiLembur->status !== 'pending') {
            return redirect()->back()->with('error', 'Pengajuan ini sudah diproses.');
        }

        $persetujuanAbsensiLembur->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'catatan_admin' => $request->catatan_admin
        ]);

        return redirect()->back()->with('success', 'Pengajuan lembur berhasil ditolak.');
    }
}
