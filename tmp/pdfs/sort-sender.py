from pathlib import Path
for name in ['analysis-preview.blade.php','analysis-pdf.blade.php']:
 p=Path('resources/views/pranota-ongkos-truk')/name;s=p.read_text();s=s.replace("'SIZE','NO BUKTI','TUJUAN'","'SIZE','TUJUAN'").replace("'size','bukti','tujuan'","'size','tujuan'")
 s=s.replace('[3,9,8,4,10,10,7,7,7,7,28]','[3,11,9,4,12,8,8,8,8,29]')
 s=s.replace('colspan="6"><strong>Subtotal plat', 'colspan="5"><strong>Subtotal plat')
 if name=='analysis-preview.blade.php':
  s=s.replace("$detailRows = $rows->filter(fn ($row) => in_array($row['no'], $rowNumbers, true));", "$detailRows = $rows->filter(fn ($row) => in_array($row['no'], $rowNumbers, true))\n        ->sortBy(fn ($row) => mb_strtoupper(trim($row['pengirim'])))->values();")
  s=s.replace('baris untuk plat dan tujuan yang dipilih.', 'baris untuk plat dan tujuan yang dipilih, diurutkan berdasarkan nama pengirim. Nomor tetap mengacu pada baris asli pranota.')
 else:
  s=s.replace('@foreach($rows as $row)', "@foreach($rows->sortBy(fn ($row) => mb_strtoupper(trim($row['pengirim'])))->values() as $row)")
  s=s.replace('Rincian Seluruh Baris Pranota', 'Rincian Seluruh Baris Pranota Berdasarkan Pengirim')
  s=s.replace('Seluruh nilai uang dalam rupiah.', 'Rincian diurutkan berdasarkan nama pengirim; nomor tetap mengacu pada baris asli pranota. Seluruh nilai uang dalam rupiah.')
 p.write_text(s)
