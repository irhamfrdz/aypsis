from pathlib import Path
p=Path('app/Services/PranotaOngkosTrukAnalysisService.php');s=p.read_text();s=s.replace("$parts[] = $label.' '.number_format", "$parts[] = $label.' Rp'.number_format")
s=s.replace("($row['uj_number'] ?? 'UJ tanpa nomor').': '.(implode(' + ', $parts) ?: '0').' = '.number_format($row['uj'], 0, ',', '.').'.'", "(implode(' + ', $parts) ?: 'Rp0').' = UJ Rp'.number_format($row['uj'], 0, ',', '.').'.'")
s=s.replace("            } else {\n                $note .= $group->count() > 1 ? ' UJ sama pada plat dan tujuan ini.' : ' Tidak ada pembanding plat dan tujuan yang sama.';\n", "")
# Preserve closing brace for the comparison block.
s=s.replace("$row['keterangan'] = 'Pengirim: '.$row['pengirim'].'. '.$note;", "$row['keterangan'] = $note;")
p.write_text(s)
for name in ['analysis-preview.blade.php','analysis-pdf.blade.php']:
 p=Path('resources/views/pranota-ongkos-truk')/name;s=p.read_text();s=s.replace("'NO PLAT','SIZE'", "'NO PLAT','NAMA PENGIRIM','SIZE'").replace("'sj','plat','size'", "'sj','plat','pengirim','size'")
 s=s.replace('colspan="5"><strong>Subtotal plat', 'colspan="6"><strong>Subtotal plat')
 s=s.replace('[3,11,9,4,12,8,8,8,8,29]', '[3,9,8,14,4,10,7,7,7,7,24]')
 p.write_text(s)
