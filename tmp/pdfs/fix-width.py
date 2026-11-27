from pathlib import Path
p=Path('resources/views/pranota-ongkos-truk/analysis-pdf.blade.php');s=p.read_text(encoding='utf-8-sig');start=s.index('<colgroup>');end=s.index('<tbody>',start);s=s[:start]+'''<thead><tr>
@foreach(['NO','TGL SJ','TGL TT','NO SURAT JALAN','NO PLAT','SIZE','RIT S','RIT K','NO BUKTI','TUJUAN','ONGKOS','UJ','NOMINAL','KETERANGAN'] as $index => $heading)
<th style="width: {{ [2.5,5,5,7,6,3,3,3,8,8,6,6,6,31.5][$index] }}%">{{ $heading }}</th>
@endforeach
</tr></thead>
'''+s[end:];p.write_text(s,encoding='utf-8')
