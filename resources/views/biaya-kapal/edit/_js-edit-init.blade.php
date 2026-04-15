    // PREPARE DATA FOR EDIT MODE
    @php
        $editKapalSections = [];
        $editAirSections = [];
        $editTkbmSections = [];
        $editOperasionalSections = [];
        $editMeratusSections = [];

        // Group Buruh
        if($biayaKapal->barangDetails->count() > 0) {
            $grouped = $biayaKapal->barangDetails->groupBy(function($item) {
                return $item->kapal . '|||' . $item->voyage;
            });
             foreach($grouped as $key => $items) {
                  $parts = explode('|||', $key);
                  if(count($parts) == 2) {
                      $firstItem = $items->first();
                      $editKapalSections[] = [
                          'kapal' => $parts[0],
                          'voyage' => $parts[1],
                          'adjustment' => $firstItem->adjustment ?? 0,
                          'notes_adjustment' => $firstItem->notes_adjustment ?? '',
                          'barang' => $items->map(function($i){ return ['barang_id' => $i->barang_id, 'jumlah' => $i->jumlah]; })
                      ];
                  }
             }
        }

        // Map Air
        if($biayaKapal->airDetails->count() > 0) {
            $groupedAir = $biayaKapal->airDetails->groupBy(function($item) {
                $tgl = $item->tanggal_invoice_vendor ? \Carbon\Carbon::parse($item->tanggal_invoice_vendor)->format('Y-m-d') : '';
                return $item->kapal . '|||' . $item->voyage . '|||' . $item->vendor . '|||' . ($item->lokasi ?? '') . '|||' . ($item->jasa_air ?? 0) . '|||' . ($item->penerima ?? '') . '|||' . ($item->nomor_rekening ?? '') . '|||' . ($item->nomor_referensi ?? '') . '|||' . $tgl;
            });
            foreach($groupedAir as $key => $items) {
                 $parts = explode('|||', $key);
                 if(count($parts) >= 9) {
                     $editAirSections[] = [
                         'kapal' => $parts[0],
                         'voyage' => $parts[1],
                         'vendor' => $parts[2],
                         'lokasi' => $parts[3],
                         'jasa_air' => $parts[4],
                         'penerima' => $parts[5],
                         'nomor_rekening' => $parts[6],
                         'nomor_referensi' => $parts[7],
                         'tanggal_invoice_vendor' => $parts[8],
                         'types' => $items->map(function($i){
                             return [
                                 'type_id' => $i->type_id,
                                 'type_keterangan' => $i->type_keterangan,
                                 'is_lumpsum' => $i->is_lumpsum,
                                 'kuantitas' => $i->kuantitas,
                                 'harga' => $i->harga
                             ];
                         })->toArray()
                     ];
                 }
            }
        }

        // Group TKBM
        if($biayaKapal->tkbmDetails->count() > 0) {
            $grouped = $biayaKapal->tkbmDetails->groupBy(function($item) {
                $tgl = $item->tanggal_invoice_vendor ? \Carbon\Carbon::parse($item->tanggal_invoice_vendor)->format('Y-m-d') : '';
                return $item->kapal . '|||' . $item->voyage . '|||' . ($item->no_referensi ?? '') . '|||' . $tgl;
            });
            foreach($grouped as $key => $items) {
                 $parts = explode('|||', $key); 
                 if(count($parts) >= 2) {
                     $firstItem = $items->first();
                     $editTkbmSections[] = [
                         'kapal' => $parts[0],
                         'voyage' => $parts[1],
                         'no_referensi' => $parts[2] ?? '',
                         'tanggal_invoice_vendor' => $parts[3] ?? '',
                         'adjustment' => $firstItem->adjustment ?? 0,
                         'barang' => $items->map(function($i){ return ['barang_id' => $i->pricelist_tkbm_id, 'jumlah' => $i->jumlah]; })
                     ];
                 }
            }
        }
        
        // Map Operasional
        if(old('operasional_sections')) {
            foreach(old('operasional_sections') as $oldOp) {
                // Nominal in old input might be formatted (e.g. 1.000.000)
                // We need to clean it for the JS to integers properly
                $rawNominal = isset($oldOp['nominal']) ? str_replace('.', '', $oldOp['nominal']) : 0;
                
                $editOperasionalSections[] = [
                    'kapal' => $oldOp['kapal'] ?? '',
                    'voyage' => $oldOp['voyage'] ?? '',
                    'nominal' => $rawNominal
                ];
            }
        } else {
            foreach($biayaKapal->operasionalDetails as $op) {
                $editOperasionalSections[] = [
                    'kapal' => $op->kapal,
                    'voyage' => $op->voyage,
                    'nominal' => $op->nominal
                ];
            }
        }

        // Map Stuffing
        $editStuffingSections = [];
        foreach($biayaKapal->stuffingDetails as $stuff) {
            $editStuffingSections[] = [
                'kapal' => $stuff->kapal,
                'voyage' => $stuff->voyage,
                'tanda_terima_ids' => $stuff->tanda_terima_ids ?? [],
            ];
        }

        // Map Trucking
        $editTruckingSections = [];
        foreach($biayaKapal->truckingDetails as $truck) {
            $editTruckingSections[] = [
                'kapal' => $truck->kapal,
                'voyage' => $truck->voyage,
                'nama_vendor' => $truck->nama_vendor,
                'no_bl_ids' => $truck->no_bl_ids ?? [], // Assuming it's an array of IDs
                'subtotal' => $truck->subtotal,
            ];
        }

        // Map Labuh Tambat - group by kapal+voyage+vendor
        $editLabuhTambatSections = [];
        $labuhTambatGrouped = [];
        foreach($biayaKapal->labuhTambatDetails as $labuh) {
            $key = ($labuh->kapal ?? '') . '|' . ($labuh->voyage ?? '') . '|' . ($labuh->vendor ?? '');
            if (!isset($labuhTambatGrouped[$key])) {
                $labuhTambatGrouped[$key] = [
                    'kapal' => $labuh->kapal,
                    'voyage' => $labuh->voyage,
                    'nomor_referensi' => $labuh->nomor_referensi,
                    'vendor' => $labuh->vendor,
                    'lokasi' => $labuh->lokasi,
                    'sub_total' => $labuh->sub_total,
                    'ppn' => $labuh->ppn,
                    'biaya_materai' => $labuh->biaya_materai,
                    'grand_total' => $labuh->grand_total,
                    'penerima' => $labuh->penerima,
                    'nomor_rekening' => $labuh->nomor_rekening,
                    'tanggal_invoice_vendor' => $labuh->tanggal_invoice_vendor ? \Carbon\Carbon::parse($labuh->tanggal_invoice_vendor)->format('Y-m-d') : null,
                    'types' => [],
                ];
            }
            $labuhTambatGrouped[$key]['types'][] = [
                'type_id' => $labuh->type_id,
                'type_keterangan' => $labuh->type_keterangan,
                'is_lumpsum' => $labuh->is_lumpsum,
                'kuantitas' => $labuh->kuantitas,
                'harga' => $labuh->harga,
            ];
        }
        $editLabuhTambatSections = array_values($labuhTambatGrouped);
        // Map Perijinan
        $editPerijinanSections = [];
        if($biayaKapal->perijinanDetails->count() > 0) {
            foreach($biayaKapal->perijinanDetails as $p) {
                $editPerijinanSections[] = [
                    'nama_kapal' => $p->nama_kapal,
                    'no_voyage' => $p->no_voyage,
                    'nomor_referensi' => $p->nomor_referensi,
                    'vendor' => $p->vendor,
                    'lokasi' => $p->lokasi,
                    'jumlah_biaya' => $p->jumlah_biaya,
                    'sub_total' => $p->sub_total,
                    'grand_total' => $p->grand_total,
                    'penerima' => $p->penerima,
                    'nomor_rekening' => $p->nomor_rekening,
                    'tanggal_invoice_vendor' => $p->tanggal_invoice_vendor ? \Carbon\Carbon::parse($p->tanggal_invoice_vendor)->format('Y-m-d') : null,
                    'keterangan' => $p->keterangan,
                    'items' => $p->details->map(function($d) {
                        return [
                            'pricelist_perijinan_id' => $d->pricelist_perijinan_id,
                            'nama_perijinan' => $d->nama_perijinan,
                            'tarif' => $d->tarif
                        ];
                    })->toArray()
                ];
            }
        }

        // Map Meratus
        if($biayaKapal->meratusDetails->count() > 0) {
            $groupedMeratus = $biayaKapal->meratusDetails->groupBy(function($item) {
                $tgl = $item->tanggal_invoice_vendor ? \Carbon\Carbon::parse($item->tanggal_invoice_vendor)->format('Y-m-d') : '';
                return ($item->kapal ?? '') . '|||' . ($item->voyage ?? '') . '|||' . ($item->penerima ?? '') . '|||' . ($item->nomor_rekening ?? '') . '|||' . ($item->nomor_referensi ?? '') . '|||' . $tgl . '|||' . ($item->keterangan ?? '');
            });
            foreach($groupedMeratus as $key => $items) {
                 $parts = explode('|||', $key);
                 if(count($parts) >= 2) {
                     $firstItem = $items->first();
                     $editMeratusSections[] = [
                         'kapal' => $parts[0],
                         'voyage' => $parts[1],
                         'penerima' => $parts[2],
                         'nomor_rekening' => $parts[3],
                         'nomor_referensi' => $parts[4],
                         'tanggal_invoice_vendor' => $parts[5],
                         'keterangan' => $parts[6],
                         'types' => $items->map(function($i){
                             return [
                                 'type_id' => $i->pricelist_meratus_id ?? 'MANUAL',
                                 'manual_name' => $i->jenis_biaya,
                                 'lokasi' => $i->lokasi,
                                 'size' => $i->size,
                                 'harga' => $i->harga,
                                 'kuantitas' => $i->kuantitas,
                                 'is_muat' => $i->is_muat,
                                 'is_bongkar' => $i->is_bongkar
                             ];
                         })->toArray()
                     ];
                 }
            }
        }
    @endphp

    var existingKapalSections = @json($editKapalSections);
    var existingAirSections = @json($editAirSections);
    var existingTkbmSections = @json($editTkbmSections);
    var existingOperasionalSections = @json($editOperasionalSections);
    var existingStuffingSections = @json($editStuffingSections);
    var existingTruckingSections = @json($editTruckingSections);
    var existingLabuhTambatSections = @json($editLabuhTambatSections);
    var existingPerijinanSections = @json($editPerijinanSections);
    var existingMeratusSections = @json($editMeratusSections);

    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(initializeEditMode, 500);
    });

    function initializeEditMode() {
        console.log("Initializing Edit Mode Data...");
        
        // 1. Jenis Biaya
        const currentJenis = "{{ $biayaKapal->jenis_biaya }}";
        const jbOption = Array.from(document.querySelectorAll('.jenis-biaya-option')).find(o => o.getAttribute('data-kode') === currentJenis);
        if(jbOption) {
            jbOption.click();
        }

        // 2. BURUH SECTIONS
        if(existingKapalSections.length > 0) {
            clearAllKapalSections();
            existingKapalSections.forEach(myData => {
                (async function() {
                    const section = addKapalSection();
                    const sectionIndex = section.getAttribute('data-section-index');
                    
                    if(section) {
                        const kapalSel = section.querySelector('.kapal-select');
                        if (kapalSel && myData.kapal) {
                            kapalSel.value = myData.kapal;
                        }
                        
                        const voySel = section.querySelector('.voyage-select');
                        if (myData.kapal) {
                            try {
                                const res = await fetch(`{{ url('biaya-kapal/get-voyages') }}/${encodeURIComponent(myData.kapal)}`);
                                const vData = await res.json();
                                voySel.disabled = false;
                                let opt = '<option value="">-- Pilih Voyage --</option>';
                                if (vData && vData.success && vData.voyages) {
                                    vData.voyages.forEach(v => opt += `<option value="${v}">${v}</option>`);
                                }
                                voySel.innerHTML = opt;
                                if (myData.voyage) voySel.value = myData.voyage;
                            } catch(e) {
                                voySel.innerHTML = `<option value="${myData.voyage}">${myData.voyage}</option>`;
                                voySel.value = myData.voyage;
                                voySel.disabled = false;
                            }
                        }
                        
                        section.querySelector('.barang-container-section').innerHTML = '';
                        myData.barang.forEach(b => {
                            addBarangToSectionWithValue(sectionIndex, b.barang_id, b.jumlah);
                        });
                        
                        // Set adjustment values
                        const adjInput = section.querySelector('.adjustment-input');
                        const notesInput = section.querySelector('input[name="kapal_sections['+sectionIndex+'][notes_adjustment]"]');
                        
                        if(adjInput && myData.adjustment) {
                            adjInput.value = Math.round(myData.adjustment).toLocaleString('id-ID');
                        }
                        if(notesInput && myData.notes_adjustment) {
                            notesInput.value = myData.notes_adjustment;
                        }
                        
                        calculateTotalFromAllSections();
                    }
                })();
            });
        }

        // 3. AIR SECTIONS
        if(existingAirSections.length > 0) {
             const airContainer = document.getElementById('air_sections_container');
             if(airContainer) airContainer.innerHTML = '';
             
             existingAirSections.forEach(data => {
                (async function() {
                    const sec = addAirSection();
                    const sectionIndex = sec.getAttribute('data-section-index');
                    
                    const kapalSel = sec.querySelector('.kapal-select-air');
                    if (kapalSel && data.kapal) {
                        kapalSel.value = data.kapal;
                    }
                    
                    // Load voyages async and set saved value
                    const voyageSelect = sec.querySelector('.voyage-select-air');
                    if (data.kapal) {
                        try {
                            const res = await fetch(`{{ url('biaya-kapal/get-voyages') }}/${encodeURIComponent(data.kapal)}`);
                            const vData = await res.json();
                            voyageSelect.disabled = false;
                            let opt = '<option value="">-- Pilih Voyage --</option><option value="DOCK">DOCK</option>';
                            if (vData && vData.success && vData.voyages) {
                                vData.voyages.forEach(v => opt += `<option value="${v}">${v}</option>`);
                            }
                            voyageSelect.innerHTML = opt;
                            if (data.voyage) voyageSelect.value = data.voyage;
                        } catch(e) {
                            voyageSelect.innerHTML = `<option value="${data.voyage}">${data.voyage}</option>`;
                            voyageSelect.value = data.voyage;
                            voyageSelect.disabled = false;
                        }
                    }
                    
                    if(data.lokasi) {
                        sec.querySelector('.lokasi-select-air').value = data.lokasi;
                        updateVendorsForLokasi(sectionIndex, data.lokasi);
                    } else {
                        sec.querySelector('.lokasi-select-air').value = '';
                        updateVendorsForLokasi(sectionIndex, '');
                    }
                    
                    if (data.vendor) {
                        sec.querySelector('.vendor-select-air').value = data.vendor;
                        loadTypesForVendor(sectionIndex, data.vendor); 
                    }
                    
                    sec.querySelector('.jasa-air-input').value = data.jasa_air;
                    if(data.penerima) sec.querySelector('.penerima-input-air').value = data.penerima;
                    if(data.nomor_rekening) sec.querySelector('.nomor-rekening-input-air').value = data.nomor_rekening;
                    if(data.nomor_referensi) sec.querySelector('input[name="air['+sectionIndex+'][nomor_referensi]"]').value = data.nomor_referensi;
                    if(data.tanggal_invoice_vendor) sec.querySelector('input[name="air['+sectionIndex+'][tanggal_invoice_vendor]"]').value = data.tanggal_invoice_vendor;
                    
                    const typesList = sec.querySelector('.types-list-air');
                    typesList.innerHTML = ''; // clear default template
                    data.types.forEach((t) => {
                        addTypeToAirSectionWithValue(sectionIndex, t.type_id, t.type_keterangan, t.is_lumpsum, t.kuantitas, t.harga);
                    });
                    
                    calculateAirSectionTotal(sectionIndex);
                })();
             });
        }

        // 4. TKBM SECTIONS
        if(existingTkbmSections.length > 0) {
            clearAllTkbmSections();
            existingTkbmSections.forEach(data => {
                (async function() {
                    const sec = addTkbmSection();
                    const sectionIndex = sec.getAttribute('data-tkbm-section-index');
                    
                    const kapalSel = sec.querySelector('.tkbm-kapal-select');
                    if (kapalSel && data.kapal) {
                        kapalSel.value = data.kapal;
                    }
                    
                    const voySel = sec.querySelector('.tkbm-voyage-select');
                    if (data.kapal) {
                        try {
                            const res = await fetch(`{{ url('biaya-kapal/get-voyages') }}/${encodeURIComponent(data.kapal)}`);
                            const vData = await res.json();
                            voySel.disabled = false;
                            let opt = '<option value="">-- Pilih Voyage --</option>';
                            if (vData && vData.success && vData.voyages) {
                                vData.voyages.forEach(v => opt += `<option value="${v}">${v}</option>`);
                            }
                            voySel.innerHTML = opt;
                            if (data.voyage) voySel.value = data.voyage;
                        } catch(e) {
                            voySel.innerHTML = `<option value="${data.voyage}">${data.voyage}</option>`;
                            voySel.value = data.voyage;
                            voySel.disabled = false;
                        }
                    }
                    
                    sec.querySelector('input[name="tkbm_sections['+sectionIndex+'][no_referensi]"]').value = data.no_referensi;
                    sec.querySelector('input[name="tkbm_sections['+sectionIndex+'][tanggal_invoice_vendor]"]').value = data.tanggal_invoice_vendor;
                    if(data.adjustment) {
                        sec.querySelector('.tkbm-adjustment-input').value = data.adjustment;
                    }
                    
                    sec.querySelector('.tkbm-barang-container').innerHTML = '';
                    data.barang.forEach(b => {
                        addBarangToTkbmSectionWithValue(sectionIndex, b.barang_id, b.jumlah);
                    });
                    calculateTotalFromAllTkbmSections();
                })();
            });
        }

        // 5. OPERASIONAL SECTIONS
        if (existingOperasionalSections.length > 0) {
            initializeOperasionalSections();
        }

        // 6. STUFFING SECTIONS
        if (existingStuffingSections.length > 0) {
            clearAllStuffingSections();
            existingStuffingSections.forEach(myData => {
                (async function() {
                    const section = addStuffingSection();
                    const sectionIndex = section.getAttribute('data-stuffing-section-index');
                    
                    if (section) {
                        const kapalSel = section.querySelector('.stuffing-kapal-select');
                        if (kapalSel && myData.kapal) {
                            kapalSel.value = myData.kapal;
                        }
                        
                        const voySel = section.querySelector('.stuffing-voyage-select');
                        if (myData.kapal) {
                            try {
                                const res = await fetch(`{{ url('biaya-kapal/get-voyages') }}/${encodeURIComponent(myData.kapal)}`);
                                const vData = await res.json();
                                voySel.disabled = false;
                                let opt = '<option value="">-- Pilih Voyage --</option>';
                                if (vData && vData.success && vData.voyages) {
                                    vData.voyages.forEach(v => opt += `<option value="${v}">${v}</option>`);
                                }
                                voySel.innerHTML = opt;
                                if (myData.voyage) voySel.value = myData.voyage;
                            } catch(e) {
                                voySel.innerHTML = `<option value="${myData.voyage}">${myData.voyage}</option>`;
                                voySel.value = myData.voyage;
                                voySel.disabled = false;
                            }
                        }
                        
                        // Load Tanda Terimas
                        const ttContainer = section.querySelector('.stuffing-tt-container');
                        if (ttContainer) ttContainer.innerHTML = '';
                        
                        if (myData.tanda_terima_ids && myData.tanda_terima_ids.length > 0) {
                            myData.tanda_terima_ids.forEach(ttId => {
                                addTandaTerimaToSectionWithId(sectionIndex, ttId);
                            });
                        } else {
                            addTandaTerimaToSection(sectionIndex);
                        }
                    }
                })();
            });
        }
        // 7. TRUCKING SECTIONS
        if (existingTruckingSections.length > 0) {
            clearAllTruckingSections();
            existingTruckingSections.forEach(myData => {
                (async function() {
                    const section = addTruckingSection();
                    const sectionIndex = section.getAttribute('data-trucking-section-index');
                    
                    if (section) {
                        const kapalSel = section.querySelector('.trucking-kapal-select');
                        if (kapalSel && myData.kapal) {
                            kapalSel.value = myData.kapal;
                        }
                        
                        const voyageSelect = section.querySelector('.trucking-voyage-select');
                        if (myData.kapal) {
                            try {
                                const res = await fetch(`{{ url('biaya-kapal/get-voyages') }}/${encodeURIComponent(myData.kapal)}`);
                                const vData = await res.json();
                                voyageSelect.disabled = false;
                                let opt = '<option value="">-- Pilih Voyage --</option>';
                                if (vData && vData.success && vData.voyages) {
                                    vData.voyages.forEach(v => opt += `<option value="${v}">${v}</option>`);
                                }
                                voyageSelect.innerHTML = opt;
                                if (myData.voyage) voyageSelect.value = myData.voyage;
                            } catch(e) {
                                voyageSelect.innerHTML = `<option value="${myData.voyage}">${myData.voyage}</option>`;
                                voyageSelect.value = myData.voyage;
                                voyageSelect.disabled = false;
                            }
                        }
                        
                        section.querySelector('.trucking-vendor-select').value = myData.nama_vendor;
                        
                        if (myData.no_bl_ids && myData.no_bl_ids.length > 0) {
                            myData.no_bl_ids.forEach(blId => {
                                addBlChipToTruckingSection(sectionIndex, blId);
                            });
                        }
                        
                        section.querySelector('.trucking-subtotal-input').value = new Intl.NumberFormat('id-ID').format(myData.subtotal);
                        calculateTruckingTotals(sectionIndex);
                    }
                })();
            });
        }

        // 8. LABUH TAMBAT SECTIONS
        if (existingLabuhTambatSections.length > 0) {
            clearAllLabuhTambatSections();
            existingLabuhTambatSections.forEach(myData => {
                (async function() {
                    const section = addLabuhTambatSection();
                    const sectionIndex = section.getAttribute('data-section-index');
                    
                    // Set kapal (don't dispatch change to avoid triggering async voyage reload)
                    const kapalSel = section.querySelector('.kapal-select-labuh-tambat');
                    if (kapalSel && myData.kapal) {
                        kapalSel.value = myData.kapal;
                    }
                    
                    // Load voyages async and set saved value
                    const voyageSelect = section.querySelector('.voyage-select-labuh-tambat');
                    if (myData.kapal) {
                        try {
                            const res = await fetch(`{{ url('biaya-kapal/get-voyages') }}/${encodeURIComponent(myData.kapal)}`);
                            const data = await res.json();
                            voyageSelect.disabled = false;
                            let opt = '<option value="">-- Pilih Voyage --</option><option value="DOCK">DOCK</option>';
                            if (data && data.success && data.voyages) {
                                data.voyages.forEach(v => opt += `<option value="${v}">${v}</option>`);
                            }
                            voyageSelect.innerHTML = opt;
                            if (myData.voyage) voyageSelect.value = myData.voyage;
                        } catch(e) {
                            // Fallback: just show saved voyage
                            voyageSelect.innerHTML = `<option value="${myData.voyage}">${myData.voyage}</option>`;
                            voyageSelect.value = myData.voyage;
                            voyageSelect.disabled = false;
                        }
                    }
                    
                    // Set lokasi first, then update vendors
                    if (myData.lokasi) {
                        section.querySelector('.lokasi-select-labuh-tambat').value = myData.lokasi;
                        updateLabuhTambatVendorsForLokasi(sectionIndex, myData.lokasi);
                    }
                    
                    // Set vendor and load types
                    if (myData.vendor) {
                        section.querySelector('.vendor-select-labuh-tambat').value = myData.vendor;
                        loadTypesForLabuhTambatVendor(sectionIndex, myData.vendor);
                    }
                    
                    // Set No. Referensi
                    if (myData.nomor_referensi) section.querySelector('.no-referensi-input-labuh-tambat').value = myData.nomor_referensi;
                    
                    // Set Penerima, Rekening, Invoice date
                    if (myData.penerima) section.querySelector('.penerima-input-labuh-tambat').value = myData.penerima;
                    if (myData.nomor_rekening) section.querySelector('.nomor-rekening-input-labuh-tambat').value = myData.nomor_rekening;
                    if (myData.tanggal_invoice_vendor) section.querySelector('.tanggal-invoice-vendor-input-labuh-tambat').value = myData.tanggal_invoice_vendor;
                    if (myData.biaya_materai) {
                        const materaiField = section.querySelector('.biaya-materai-input-labuh-tambat');
                        if (materaiField) materaiField.value = parseInt(myData.biaya_materai).toLocaleString('id-ID');
                    }
                    
                    // Clear default type row and add the saved types
                    const typesList = section.querySelector('.types-list-labuh-tambat');
                    typesList.innerHTML = '';
                    if (myData.types && myData.types.length > 0) {
                        myData.types.forEach(typeItem => {
                            addTypeToLabuhTambatSectionWithValue(sectionIndex, typeItem.type_id, typeItem.type_keterangan, typeItem.is_lumpsum, typeItem.kuantitas, typeItem.harga);
                        });
                    }
                })();
            });
            calculateTotalFromAllLabuhTambatSections();
        }

        // 9. PERIJINAN SECTIONS
        if (existingPerijinanSections.length > 0) {
            clearAllPerijinanSections();
            existingPerijinanSections.forEach(myData => {
                addPerijinanSection(myData);
            });
        }

        // 10. MERATUS SECTIONS
        if (existingMeratusSections.length > 0) {
            if (typeof clearAllMeratusSections === 'function') clearAllMeratusSections();
            existingMeratusSections.forEach(myData => {
                if (typeof addMeratusSection === 'function') addMeratusSection(myData);
            });
        }
    }

    // New helper for TKBM
    window.addBarangToTkbmSectionWithValue = function(sectionIndex, barangId, jumlah) {
        const section = document.querySelector(`[data-tkbm-section-index="${sectionIndex}"]`);
        const container = section.querySelector('.tkbm-barang-container');
        const barangIndex = container.children.length;
        
        // Use TKBM pricelist
        let barangOptions = '<option value="">Pilih Nama Barang</option>';
        pricelistTkbmData.forEach(pricelist => {
            const selected = pricelist.id == barangId ? 'selected' : '';
            barangOptions += `<option value="${pricelist.id}" data-tarif="${pricelist.tarif}" ${selected}>${pricelist.nama_barang}</option>`;
        });
        
        const inputGroup = document.createElement('div');
        inputGroup.className = 'flex items-end gap-2 mb-2';
        inputGroup.innerHTML = `
            <div class="flex-1">
                <select name="tkbm_sections[${sectionIndex}][barang][${barangIndex}][barang_id]" class="tkbm-barang-select-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-amber-500" required>
                    ${barangOptions}
                </select>
            </div>
            <div class="w-24">
                <input type="number" step="any" name="tkbm_sections[${sectionIndex}][barang][${barangIndex}][jumlah]" value="${jumlah}" class="tkbm-jumlah-input-item w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-amber-500" placeholder="0" required>
            </div>
            <button type="button" onclick="removeBarangFromTkbmSection(this)" class="px-2 py-1.5 bg-red-500 hover:bg-red-600 text-white rounded text-sm transition">
                <i class="fas fa-trash text-xs"></i>
            </button>
        `;
        
        container.appendChild(inputGroup);
        
        // Event listeners
        const barangSelect = inputGroup.querySelector('.tkbm-barang-select-item');
        const jumlahInput = inputGroup.querySelector('.tkbm-jumlah-input-item');
        
        barangSelect.addEventListener('change', function() {
            calculateTotalFromAllTkbmSections();
        });
        
        jumlahInput.addEventListener('input', function() {
            calculateTotalFromAllTkbmSections();
        });
    };

    // Original Script Follows
