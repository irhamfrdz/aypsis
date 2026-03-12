const fs = require('fs');
const path = require('path');

function walkSync(currentDirPath, callback) {
    fs.readdirSync(currentDirPath).forEach(function (name) {
        var filePath = path.join(currentDirPath, name);
        var stat = fs.statSync(filePath);
        if (stat.isFile()) {
            callback(filePath, stat);
        } else if (stat.isDirectory()) {
            walkSync(filePath, callback);
        }
    });
}

function replaceInFile(path, replacements) {
    let content = fs.readFileSync(path, 'utf8');
    let original = content;
    replacements.forEach(([search, replace]) => {
        content = content.replace(search, replace);
    });
    if (content !== original) {
        fs.writeFileSync(path, content, 'utf8');
        console.log(`Replaced in ${path}`);
    }
}

// 1. Model
replaceInFile('app/Models/OrderBatam.php', [
    [/class Order extends/g, 'class OrderBatam extends'],
    [/public function suratJalans/g, 'protected $table = \'order_batams\';\n\n    public function suratJalans'],
]);

// 2. Controller
replaceInFile('app/Http/Controllers/OrderBatamController.php', [
    [/class OrderController/g, 'class OrderBatamController'],
    [/use App\\Models\\Order;/g, 'use App\\Models\\OrderBatam;'],
    [/\bOrder::/g, 'OrderBatam::'],
    [/\$order/g, '$orderBatam'],
    [/\$orders/g, '$orderBatams'],
    [/'orders\./g, "'orders-batam."],
    [/'orders'/g, "'orders-batam'"],
    [/'order'/g, "'orderBatam'"],
    [/\(Order /g, '(OrderBatam '],
    [/unique:orders,nomor_order/g, 'unique:order_batams,nomor_order'],
    [/generateOrderNumber/g, 'generateOrderBatamNumber'],
    [/route\('orders/g, "route('orders-batam"],
]);

// 3. Views
walkSync('resources/views/orders-batam', function(filePath) {
    if (filePath.endsWith('.blade.php')) {
        replaceInFile(filePath, [
            [/route\('orders\./g, "route('orders-batam."],
            [/\$order->/g, '$orderBatam->'],
            [/\$order /g, '$orderBatam '],
            [/\$order\[/g, '$orderBatam['],
            [/\$order\)/g, '$orderBatam)'],
            [/\$orders /g, '$orderBatams '],
            [/\$orders\)/g, '$orderBatams)'],
            [/\$orders as/g, '$orderBatams as'],
            [/\('orders /g, "('orders-batam "],
            [/\('orders\./g, "('orders-batam."],
            [/Order::/g, 'OrderBatam::'],
        ]);
    }
});

console.log("Done");
