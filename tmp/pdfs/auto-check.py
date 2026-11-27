from pathlib import Path
s=Path('tmp/pdfs/verify-destination.php').read_text(encoding='utf-8-sig')
s=s.replace("str_contains($html,'<table')||!str_contains($html,'Pilih tujuan')", "!str_contains($html,'<table')||!str_contains($html,'Pilih tujuan di atas')||str_contains($html,'JB10036598')")
s=s.replace('initial view has no results','initial view has empty table')
Path('tmp/pdfs/verify-auto-destination.php').write_text(s)
