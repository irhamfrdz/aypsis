import json, collections, html
from reportlab.platypus import SimpleDocTemplate, Paragraph, Table, TableStyle, Spacer, PageBreak
from reportlab.lib import colors
from reportlab.lib.styles import ParagraphStyle
from reportlab.lib.pagesizes import A3, landscape
from reportlab.lib.enums import TA_RIGHT
D=json.load(open('tmp/pdfs/data.json',encoding='utf-8-sig')); rows=D['rows']; groups=collections.defaultdict(list)
for r in rows: groups[(r['plat'].replace(' ', '').upper(),r['tujuan'].strip().upper())].append(r)
diff={k:v for k,v in groups.items() if len({r['uj'] for r in v})>1}
fmt=lambda n:f'{float(n):,.0f}'.replace(',','.')
styles={ 'body':ParagraphStyle('body',fontName='Helvetica',fontSize=9,leading=13,spaceAfter=8), 'title':ParagraphStyle('title',fontName='Helvetica-Bold',fontSize=21,leading=26,spaceAfter=12), 'cell':ParagraphStyle('cell',fontName='Helvetica',fontSize=7.5,leading=10), 'head':ParagraphStyle('head',fontName='Helvetica-Bold',fontSize=7.5,leading=10,textColor=colors.white), 'num':ParagraphStyle('num',fontName='Helvetica',fontSize=7.5,leading=10,alignment=TA_RIGHT)}
def p(t,kind='cell'):return Paragraph(html.escape(str(t)).replace('\n','<br/>'),styles[kind])
def table(data,widths,head=True):
 t=Table(data,colWidths=widths,repeatRows=1 if head else 0,hAlign='LEFT')
 t.setStyle(TableStyle([('BACKGROUND',(0,0),(-1,0),colors.HexColor('#153c52')),('VALIGN',(0,0),(-1,-1),'TOP'),('GRID',(0,0),(-1,-1),.3,colors.HexColor('#c6d2d8')),('LEFTPADDING',(0,0),(-1,-1),5),('RIGHTPADDING',(0,0),(-1,-1),5),('TOPPADDING',(0,0),(-1,-1),6),('BOTTOMPADDING',(0,0),(-1,-1),6),('ROWBACKGROUNDS',(0,1),(-1,-1),[colors.white,colors.HexColor('#f1f5f7')])]))
 return t
out='output/pdf/Analisis_UJ_POT1126000008.pdf'; W,H=landscape(A3); usable=W-56
story=[p('Analisis Perbedaan Uang Jalan','title'),p('POT1126000008 | PT. Alexindo YakinPrima Shipping | Data diperiksa 27 November 2026','body')]
story.append(p(f'Cakupan: seluruh {len(rows)} baris pranota, {len(groups)} kelompok plat + tujuan. Ditemukan {len(diff)} kelompok dengan UJ berbeda ({sum(map(len,diff.values()))} baris). Nomor pada tabel mengikuti urutan data pranota.','body'))
story.append(p('Temuan: uang jalan dasar pada masing-masing kelompok yang berbeda tetap sama. Perbedaan total berasal dari pelancar; khusus B9385UIV tujuan KARAWANG, komponen MEL juga berbeda. Seluruh ukuran pada kelompok tersebut setara 20 kaki (20 / 20ft). Kesamaan plat dan tujuan tidak menjamin kesamaan biaya tambahan.','body'))
sumdata=[[p(x,'head') for x in ['NO PLAT','TUJUAN','NO BARIS ASLI','VARIASI UJ (Rp)','KOMPONEN YANG BERBEDA','SELISIH MIN-MAKS (Rp)']]]
fields=[('jumlah_uang_jalan','Dasar'),('jumlah_mel','MEL'),('jumlah_pelancar','Pelancar'),('jumlah_kawalan','Kawalan'),('jumlah_parkir','Parkir'),('jumlah_penyesuaian','Penyesuaian')]
for k,v in diff.items():
 changes=[]
 for f,label in fields:
  vals=sorted({float((r['components'] or {}).get(f,0) or 0) for r in v})
  if len(vals)>1:changes.append(label+': '+ ' / '.join(fmt(x) for x in vals))
 sumdata.append([p(x) for x in [k[0],k[1],', '.join(str(r['no']) for r in v),' / '.join(fmt(x) for x in sorted({r['uj'] for r in v})),'; '.join(changes),fmt(max(r['uj'] for r in v)-min(r['uj'] for r in v))]])
story += [table(sumdata,[90,120,180,210,usable-700,100]),Spacer(1,14)]
story.append(p('Dasar pembacaan: kolom UJ pada aplikasi memakai jumlah_total dari data uang jalan yang terhubung ke SJ, termasuk biaya tambahan dan penyesuaian. ONGKOS mengikuti master tujuan dan size saat pemeriksaan; NOMINAL adalah nilai item pranota yang tersimpan. NOMINAL disalin apa adanya, tidak dihitung ulang. TGL TT dan NO BUKTI mengikuti relasi tanda terima dan nomor Accurate pembayaran sebagaimana tampilan cetak aplikasi. Tanda - berarti tidak tersedia.','body'))
story.append(p('Batas kesimpulan: memo dan alasan penyesuaian pada kelompok yang UJ-nya berbeda kosong. Laporan membuktikan sumber selisih angka; alasan operasional mengapa pelancar/MEL ditetapkan berbeda memerlukan konfirmasi petugas atau bukti pendukung. Perbedaan angka ini sendiri belum membuktikan kesalahan input.','body'))
story.append(PageBreak()); story.append(p('Rincian Seluruh Baris Pranota','title')); story.append(p('Seluruh nilai uang dalam rupiah. Keterangan menyebut komposisi UJ dan pembanding pada plat + tujuan yang sama.','body'))
heads=['NO','TGL SJ','TGL TT','NO SURAT JALAN','NO PLAT','SIZE','RIT S','RIT K','NO BUKTI','TUJUAN','ONGKOS','UJ','NOMINAL','KETERANGAN']
data=[[p(x,'head') for x in heads]]
for r in rows:
 key=(r['plat'].replace(' ','').upper(),r['tujuan'].strip().upper()); v=groups[key]; c=r['components'] or {}; base=min(v,key=lambda x:(x['uj'],x['no']))
 parts=[label+' '+fmt(c.get(f,0) or 0) for f,label in fields if float(c.get(f,0) or 0)!=0]
 note=(c.get('nomor_uang_jalan','UJ tidak tersedia')+'\n'+' + '.join(parts)+' = '+fmt(r['uj'])+'.')
 if key in diff:
  changing=[label for f,label in fields if len({float((z['components'] or {}).get(f,0) or 0) for z in v})>1]
  note+='\nBerbeda pada '+', '.join(changing)+'. '
  if r['no']==base['no']:note+='Acuan UJ terendah kelompok.'
  else:note+='Selisih '+fmt(r['uj']-base['uj'])+' terhadap no. '+str(base['no'])+'.'
 elif len(v)>1:note+='\nUJ sama dengan baris lain pada plat + tujuan ini.'
 else:note+='\nSatu baris pada plat + tujuan ini; tidak ada pembanding.'
 if c.get('memo'):note+=' Memo: '+c['memo']
 vals=[r[x] for x in ['no','tgl_sj','tgl_tt','sj','plat','size','rit_s','rit_k','bukti','tujuan']]+[fmt(r[x]) for x in ['ongkos','uj','nominal']]+[note]
 data.append([p(x,'num' if j in (10,11,12) else 'cell') for j,x in enumerate(vals)])
widths=[25,57,57,78,64,30,29,29,85,90,65,65,65]; widths +=[usable-sum(widths)]
story.append(table(data,widths));story.append(Spacer(1,12))
story.append(p('Jumlah seluruh baris: ONGKOS Rp'+fmt(sum(r['ongkos'] for r in rows))+' | UJ Rp'+fmt(sum(r['uj'] for r in rows))+' | NOMINAL item Rp'+fmt(sum(r['nominal'] for r in rows))+'. Total pranota tersimpan Rp'+fmt(D['pranota']['total_nominal'])+'.','body'))
story.append(p('Sumber: database aplikasi AYPSIS (pranota_ongkos_truks, pranota_ongkos_truk_items, surat jalan muat/bongkaran, uang_jalans, master tujuan, tanda terima dan pembayaran terkait); pemetaan kolom mengikuti resources/views/pranota-ongkos-truk/print.blade.php. Pembacaan data saja; tidak ada perubahan transaksi.','body'))
story.append(p('Penyesuaian pranota: '+ '; '.join(str(a.get('keterangan','-'))+' (Rp'+fmt(a['nominal'])+')' for a in D['pranota'].get('adjustments',[]))+'. Total penyesuaian Rp'+fmt(D['pranota']['adjustment'])+'. Nilai dan keterangan penyesuaian disalin sesuai data tersimpan.', 'body'))
def footer(c,doc):
 c.setStrokeColor(colors.HexColor('#c6d2d8'));c.line(28,28,W-28,28);c.setFont('Helvetica',8);c.setFillColor(colors.HexColor('#526773'));c.drawString(28,16,'AYPSIS | Analisis UJ POT1126000008 | 27/11/2026');c.drawRightString(W-28,16,'Halaman '+str(doc.page))
SimpleDocTemplate(out,pagesize=(W,H),leftMargin=28,rightMargin=28,topMargin=28,bottomMargin=40).build(story,onFirstPage=footer,onLaterPages=footer)
print(out)

