from html.parser import HTMLParser
from pathlib import Path
class Check(HTMLParser):
 def __init__(self):
  super().__init__(); self.sections=[]; self.active=None; self.buttons=[]
 def handle_starttag(self,tag,attrs):
  a=dict(attrs)
  if tag=='section':
   assert 'hidden' in a
   self.active={'id':a['id'],'text':''}; self.sections.append(self.active)
  if tag=='button' and 'js-analysis-detail' in a.get('class','').split(): self.buttons.append(a)
 def handle_endtag(self,tag):
  if tag=='section':self.active=None
 def handle_data(self,data):
  if self.active is not None:self.active['text']+=data
c=Check();c.feed(Path('tmp/pdfs/detail-preview.html').read_text(encoding='utf-8'))
assert len(c.sections)==len(c.buttons)==10
first=c.sections[0]['text']
assert all(sj in first for sj in ['JB10036342','JB10036353','JB10036737'])
assert 'JB10036354' not in first and '180.000' in first
assert all(b['aria-controls']==s['id'] and b['aria-expanded']=='false' for b,s in zip(c.buttons,c.sections))
print('PASS: 10 hidden detail panels; plate buttons linked; Cibitung group contains only its 3 rows and comparison.')
