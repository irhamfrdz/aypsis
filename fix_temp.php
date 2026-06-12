path = r"c:\folder_joki\aypsis\aypsis\routes\web.php"
with open(path, "r", encoding="utf-8") as f:
    for idx, line in enumerate(f):
        if "biaya-kapal" in line.lower():
            print(f"{idx+1}: {line.strip()}")
