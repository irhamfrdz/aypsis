const fs = require('fs');
let content = fs.readFileSync('app/Http/Controllers/TandaTerimaTanpaSuratJalanController.php', 'utf8');

// Replacement sets
content = content.replace(/class TandaTerimaTanpaSuratJalanController/g, 'class TandaTerimaTanpaSuratJalanBatamController');
content = content.replace(/TandaTerimaTanpaSuratJalan(?!Export)/g, 'TandaTerimaTanpaSuratJalanBatam'); // Prevent Export mismatch if present
content = content.replace(/tanda-terima-tanpa-surat-jalan/g, 'tanda-terima-tanpa-surat-jalan-batam');

fs.writeFileSync('app/Http/Controllers/TandaTerimaTanpaSuratJalanBatamController.php', content);
console.log('Controller cloned successfully with variables setups!');
