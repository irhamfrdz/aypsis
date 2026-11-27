from pathlib import Path
for name,cls in [('analysis-preview.blade.php','analysis-money'),('analysis-pdf.blade.php','number')]:
 p=Path('resources/views/pranota-ongkos-truk')/name;s=p.read_text();needle="@foreach(['ongkos','pelancar','uj','nominal'] as $key)<td class=\""+cls+"\">{{ number_format($row[$key], 0, ',', '.') }}</td>@endforeach";assert needle in s
 replacement="""@foreach(['ongkos','pelancar','uj','nominal'] as $key)
<td class="""+'"'+cls+'"'+""">@if($key === 'pelancar')@include('pranota-ongkos-truk.pelancar-badge', ['amount' => $row['pelancar'], 'amounts' => $rows->pluck('pelancar')->unique()->sort()->values()])@else{{ number_format($row[$key], 0, ',', '.') }}@endif</td>
@endforeach"""
 s=s.replace(needle,replacement)
 if name=='analysis-preview.blade.php':s=s.replace('Seluruh plat pada tujuan ini dibandingkan.', 'Warna pelancar yang sama menandakan nominal yang sama; warna berbeda menandakan nominal berbeda. Seluruh plat pada tujuan ini dibandingkan.')
 else:s=s.replace('PELANCAR adalah komponen biaya pelancar dari data Uang Jalan terkait.', 'PELANCAR adalah komponen biaya pelancar dari data Uang Jalan terkait. Nominal pelancar yang sama ditandai dengan warna yang sama.')
 p.write_text(s)
