from pathlib import Path
p=Path('resources/views/pranota-ongkos-truk/analysis-preview.blade.php');s=p.read_text();end=s.index('@if($selectedDestination !== null)');s='''<form class="js-analysis-destination analysis-destination-card" action="{{ route('pranota-ongkos-truk.analysis-preview', $pranota->id) }}" method="GET">
    <div class="analysis-destination-intro">
        <span class="analysis-destination-icon"><i class="fas fa-location-dot" aria-hidden="true"></i></span>
        <div><h3>Analisis berdasarkan tujuan</h3><p>Pilih tujuan untuk membandingkan uang jalan seluruh plat dalam pranota ini.</p></div>
    </div>
    <div class="analysis-destination-controls">
        <div class="analysis-destination-field">
            <label for="analysis-destination">Tujuan <span aria-hidden="true">*</span></label>
            <select id="analysis-destination" name="tujuan" required aria-describedby="analysis-destination-help" @disabled(empty($destinations))>
                <option value="">{{ empty($destinations) ? 'Tidak ada tujuan tersedia' : 'Pilih tujuan pengiriman' }}</option>
                @foreach($destinations as $destination)<option value="{{ $destination }}" @selected($selectedDestination === $destination)>{{ $destination }}</option>@endforeach
            </select>
            <p id="analysis-destination-help">{{ count($destinations) }} tujuan tersedia dalam pranota ini.</p>
        </div>
        <button type="submit" class="analysis-action analysis-action-primary analysis-destination-submit" @disabled(empty($destinations))>Analisis</button>
    </div>
</form>
'''+s[end:];p.write_text(s)
p=Path('resources/views/pranota-ongkos-truk/analysis-modal.blade.php');s=p.read_text();idx=s.index('@media(max-width:700px)');s=s[:idx]+'''#analysis-dialog .analysis-destination-card { background:white; border:1px solid #dbe4ed; border-radius:14px; padding:22px; margin-bottom:24px; }
#analysis-dialog .analysis-destination-intro { display:flex; align-items:flex-start; gap:12px; margin-bottom:20px; }
#analysis-dialog .analysis-destination-icon { display:grid; place-items:center; background:#edf3fa; color:#254a78; width:40px; height:40px; border-radius:10px; flex-shrink:0; }
#analysis-dialog .analysis-destination-intro h3 { margin:0 0 5px; font-size:15px; font-weight:700; color:#18384d; }
#analysis-dialog .analysis-destination-intro p { margin:0; font-size:12px; color:#64748b; line-height:1.6; }
#analysis-dialog .analysis-destination-controls { display:flex; align-items:flex-start; gap:12px; }
#analysis-dialog .analysis-destination-field { flex:1; min-width:0; max-width:600px; }
#analysis-dialog .analysis-destination-field label { display:block; font-size:12px; font-weight:600; margin-bottom:8px; color:#334155; }
#analysis-dialog .analysis-destination-field label span { color:#b45309; }
#analysis-dialog .analysis-destination-field select { display:block; width:100%; min-height:46px; border:1px solid #cbd5e1; border-radius:9px; background-color:#fff; color:#1e293b; font-size:14px; padding:10px 36px 10px 13px; cursor:pointer; }
#analysis-dialog .analysis-destination-field select:focus { outline:3px solid #dbeafe; outline-offset:1px; border-color:#4877a8; }
#analysis-dialog .analysis-destination-field select:disabled { background-color:#f1f5f9; cursor:not-allowed; color:#94a3b8; }
#analysis-dialog .analysis-destination-field p { font-size:11px; margin:7px 0 0; color:#64748b; }
#analysis-dialog .analysis-destination-submit { margin-top:26px; min-height:46px; justify-content:center; padding:10px 24px; }
'''+s[idx:];s=s.replace(' #analysis-dialog .analysis-toolbar { align-items:flex-start;', ''' #analysis-dialog .analysis-destination-card { padding:16px; }
 #analysis-dialog .analysis-destination-controls { flex-direction:column; }
 #analysis-dialog .analysis-destination-field { width:100%; max-width:none; }
 #analysis-dialog .analysis-destination-submit { margin-top:0; width:100%; }
 #analysis-dialog .analysis-toolbar { align-items:flex-start;''');p.write_text(s)
