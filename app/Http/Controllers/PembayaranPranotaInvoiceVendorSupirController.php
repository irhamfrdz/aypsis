<?php

namespace App\Http\Controllers;

use App\Models\PembayaranPranotaVendorSupir;
use App\Models\PembayaranPranotaVendorSupirItem;
use App\Models\PranotaInvoiceVendorSupir;
use App\Models\VendorSupir;
use App\Models\Coa; // Added Coa model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PembayaranPranotaInvoiceVendorSupirController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Permissions will be added later if needed, for now use standard auth
    }

    public function index(Request $request)
    {
        $query = PembayaranPranotaVendorSupir::with(['vendor', 'creator']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_pembayaran', 'like', "%{$search}%")
                  ->orWhere('nomor_accurate', 'like', "%{$search}%")
                  ->orWhereHas('vendor', function($vendorQ) use ($search) {
                      $vendorQ->where('nama_vendor', 'like', "%{$search}%");
                  });
            });
        }

        $pembayarans = $query->latest()->paginate(15);

        return view('pembayaran-pranota-invoice-vendor-supir.index', compact('pembayarans'));
    }

    public function create(Request $request)
    {
        $vendors = VendorSupir::orderBy('nama_vendor')->get();
        
        $selectedVendorId = $request->vendor_id;
        $pranotas = [];
        
        if ($selectedVendorId) {
            $pranotas = PranotaInvoiceVendorSupir::where('vendor_id', $selectedVendorId)
                ->where('status_pembayaran', '!=', 'lunas')
                ->orderBy('tanggal_pranota', 'desc')
                ->get();
        }

        // Get akun COA for bank selection
        $akunCoa = Coa::where('tipe_akun', 'LIKE', '%bank%')
                      ->orWhere('nama_akun', 'LIKE', '%bank%')
                      ->orWhere('nama_akun', 'LIKE', '%kas%')
                      ->orderBy('nama_akun')
                      ->get();

        // Generate nomor pembayaran
        $lastPayment = PembayaranPranotaVendorSupir::latest()->first();
        $nextId = $lastPayment ? $lastPayment->id + 1 : 1;
        $nomorPembayaran = 'PAY-VS-' . date('Ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        return view('pembayaran-pranota-invoice-vendor-supir.create', compact('vendors', 'pranotas', 'selectedVendorId', 'nomorPembayaran', 'akunCoa'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor_pembayaran' => 'required|unique:pembayaran_pranota_vendor_supirs',
            'tanggal_pembayaran' => 'required|date',
            'vendor_id' => 'required|exists:vendor_supirs,id',
            'total_pembayaran' => 'required|numeric|min:0',
            'total_tagihan_penyesuaian' => 'nullable|numeric',
            'total_tagihan_setelah_penyesuaian' => 'required|numeric|min:0',
            'alasan_penyesuaian' => 'nullable|string',
            'metode_pembayaran' => 'required|string',
            'pranota_ids' => 'required|array|min:1',
            'nominal_bayar' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            $pembayaran = PembayaranPranotaVendorSupir::create([
                'nomor_pembayaran' => $request->nomor_pembayaran,
                'nomor_accurate' => $request->nomor_accurate,
                'tanggal_pembayaran' => $request->tanggal_pembayaran,
                'vendor_id' => $request->vendor_id,
                'total_pembayaran' => $request->total_pembayaran,
                'total_tagihan_penyesuaian' => $request->total_tagihan_penyesuaian,
                'total_tagihan_setelah_penyesuaian' => $request->total_tagihan_setelah_penyesuaian,
                'alasan_penyesuaian' => $request->alasan_penyesuaian,
                'metode_pembayaran' => $request->metode_pembayaran,
                'bank' => $request->bank,
                'keterangan' => $request->keterangan,
                'created_by' => Auth::id(),
            ]);

            foreach ($request->pranota_ids as $pranotaId) {
                $nominal = $request->nominal_bayar[$pranotaId] ?? 0;
                if ($nominal > 0) {
                    PembayaranPranotaVendorSupirItem::create([
                        'pembayaran_id' => $pembayaran->id,
                        'pranota_id' => $pranotaId,
                        'nominal' => $nominal,
                    ]);

                    // Update Pranota Status
                    $pranota = PranotaInvoiceVendorSupir::find($pranotaId);
                    $totalTelahDibayar = PembayaranPranotaVendorSupirItem::where('pranota_id', $pranotaId)->sum('nominal');
                    
                    if ($totalTelahDibayar >= $pranota->total_nominal) {
                        $pranota->status_pembayaran = 'lunas';
                    } else {
                        $pranota->status_pembayaran = 'sebagian';
                    }
                    $pranota->save();
                }
            }

            DB::commit();
            return redirect()->route('pembayaran-pranota-invoice-vendor-supir.index')->with('success', 'Pembayaran berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error store pembayaran: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function show($pembayaran)
    {
        $pembayaran = PembayaranPranotaVendorSupir::with(['vendor', 'items.pranota', 'creator'])->findOrFail($pembayaran);
        return view('pembayaran-pranota-invoice-vendor-supir.show', compact('pembayaran'));
    }

    public function destroy($pembayaran)
    {
        DB::beginTransaction();
        try {
            $pembayaranRecord = PembayaranPranotaVendorSupir::with('items')->findOrFail($pembayaran);
            
            foreach ($pembayaranRecord->items as $item) {
                $pranotaId = $item->pranota_id;
                $item->delete();

                // Recalculate status pranota
                $pranota = PranotaInvoiceVendorSupir::find($pranotaId);
                $totalTelahDibayar = PembayaranPranotaVendorSupirItem::where('pranota_id', $pranotaId)->sum('nominal');
                
                if ($totalTelahDibayar <= 0) {
                    $pranota->status_pembayaran = 'belum_dibayar';
                } elseif ($totalTelahDibayar < $pranota->total_nominal) {
                    $pranota->status_pembayaran = 'sebagian';
                } else {
                    $pranota->status_pembayaran = 'lunas';
                }
                $pranota->save();
            }

            $pembayaranRecord->delete();
            DB::commit();
            return redirect()->route('pembayaran-pranota-invoice-vendor-supir.index')->with('success', 'Pembayaran berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
