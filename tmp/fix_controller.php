<?php
$filename = 'c:\\folder_joki\\aypsis\\aypsis\\app\\Http\\Controllers\\SuratJalanController.php';
$content = file_get_contents($filename);

// Find the last closing brace
$pos = strrpos($content, '}');

if ($pos !== false) {
    // Let's verify if that's the end of file or if there are method braces before it
    // Splitting by lines could help
    $lines = file($filename);
    $lastLine = trim(end($lines));
    
    // We can also just append before the last line or insert properly
    // Let's just create a new file with accurate appending at end of class
    $newMethods = "
    /**
     * Display page for surat jalan cancellation
     */
    public function pembatalan(Request $request)
    {
        try {
            \$query = SuratJalan::with(['order', 'tujuanPengambilanRelation', 'tujuanPengirimanRelation'])
                ->whereIn('status', ['draft', 'active']); // items that CAN be cancelled

            if (\$request->filled('search')) {
                \$search = \$request->search;
                \$query->where(function(\$q) use (\$search) {
                    \$q->where('no_surat_jalan', 'like', \"%{\$search}%\")
                      ->orWhere('pengirim', 'like', \"%{\$search}%\");
                });
            }

            \$suratJalans = \$query->orderBy('created_at', 'desc')->paginate(10);
            
            \$cancelledSuratJalans = SuratJalan::where('status', 'cancelled')
                ->orderBy('updated_at', 'desc')
                ->paginate(5, ['*'], 'cancelled_page');

            return view('surat-jalan.pembatalan', compact('suratJalans', 'cancelledSuratJalans'));

        } catch (\\Exception \$e) {
            \\Log::error('Error displaying pembatalan page: ' . \$e->getMessage());
            return redirect()->back()->with('error', 'Gagal memuat halaman pembatalan');
        }
    }

    /**
     * Method to cancel Surat Jalan with proper cleanup
     */
    public function cancel(Request \$request, \$id)
    {
        try {
            \$suratJalan = SuratJalan::findOrFail(\$id);
            
            if (\$suratJalan->status === 'cancelled') {
                return response()->json(['success' => false, 'message' => 'Surat Jalan sudah dibatalkan']);
            }

            \$orderId = \$suratJalan->order_id;
            \$jumlahKontainer = \$suratJalan->jumlah_kontainer;

            \$suratJalan->status = 'cancelled';
            \$suratJalan->save();

            if (\$orderId && \$jumlahKontainer) {
                try {
                    \$order = \\App\\Models\\Order::find(\$orderId);
                    if (\$order) {
                        \$wasApproved = \\App\Models\\SuratJalanApproval::where('surat_jalan_id', \$id)
                            ->where('status', 'approved')
                            ->exists();
                        
                        if (\$wasApproved) {
                            \$order->sisa += \$jumlahKontainer;
                            \$history = \$order->processing_history ?? [];
                            \$history[] = [
                                'action' => 'cancelled_surat_jalan',
                                'by' => auth()->user()->id ?? 0,
                                'date' => now()->toDateTimeString(),
                                'notes' => 'Surat Jalan ' . \$suratJalan->no_surat_jalan . ' was cancelled',
                                'previous_sisa' => \$order->sisa - \$jumlahKontainer,
                                'new_sisa' => \$order->sisa
                            ];
                            \$order->processing_history = \$history;
                            \$order->save();
                        }
                    }
                } catch (\\Exception \$e) {
                    \\Log::error('Error updating order units on cancel: ' . \$e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Surat Jalan berhasil dibatalkan'
            ]);

        } catch (\\Exception \$e) {
            \\Log::error('Error cancelling Surat Jalan: ' . \$e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal membatalkan Surat Jalan']);
        }
    }
}
";
    // We remove the last } so we can replace it with methods and a new }
    // It's safer to just replace from the back where it matches `    }\n}\n` or similar
    // Let's create a regex search
    
    // Read final bits
    $classClosing = "    }\r\n}\r\n";
    if (strpos($content, $classClosing) !== false) {
        $content = str_replace($classClosing, "    }\r\n" . $newMethods, $content);
    } else {
        $classClosing = "    }\n}\n";
        if (strpos($content, $classClosing) !== false) {
             $content = str_replace($classClosing, "    }\n" . $newMethods, $content);
        } else {
             // Let's take general last brace position
             $content = substr($content, 0, $pos) . $newMethods;
        }
    }
    
    file_put_contents($filename, $content);
    echo "Controller fixed with new methods.\n";
} else {
    echo "Closing brace not found\n";
}
