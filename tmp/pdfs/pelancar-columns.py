from pathlib import Path
p=Path('app/Services/PranotaOngkosTrukAnalysisService.php');s=p.read_text();needle="'nominal' => (float) $item->nominal, 'components' => $values,";assert needle in s;s=s.replace(needle,needle+"\n                'pelancar' => $values['jumlah_pelancar'],");p.write_text(s)
for name in ['analysis-preview.blade.php','analysis-pdf.blade.php']:
 p=Path('resources/views/pranota-ongkos-truk')/name;s=p.read_text();s=s.replace("['NO','TGL SJ','TGL TT','NO SURAT JALAN','NO PLAT','SIZE','RIT S','RIT K','NO BUKTI','TUJUAN','ONGKOS','UJ','NOMINAL','KETERANGAN']","['NO','NO SURAT JALAN','NO PLAT','SIZE','NO BUKTI','TUJUAN','ONGKOS','PELANCAR','UJ','NOMINAL','KETERANGAN']")
 s=s.replace("['no','tgl_sj','tgl_tt','sj','plat','size','rit_s','rit_k','bukti','tujuan']","['no','sj','plat','size','bukti','tujuan']")
 s=s.replace("['ongkos','uj','nominal']","['ongkos','pelancar','uj','nominal']")
 s=s.replace('colspan="10"','colspan="6"').replace('[2.5,5,5,7,6,3,3,3,8,8,6,6,6,31.5]','[3,9,8,4,10,10,7,7,7,7,28]')
 s=s.replace('RIT S = rit supir; RIT K = rit kenek.', 'PELANCAR adalah komponen biaya pelancar dari data Uang Jalan terkait.')
 p.write_text(s)
