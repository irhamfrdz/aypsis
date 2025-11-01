<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔧 Creating filtered SQL file (excluding users and permissions)...\n\n";

// Path to SQL files
$originalSqlFile = 'c:\\folder_kerjaan\\aypsis.sql';
$filteredSqlFile = 'c:\\folder_kerjaan\\aypsis\\aypsis\\aypsis_filtered.sql';

if (!file_exists($originalSqlFile)) {
    echo "❌ Original SQL file not found: {$originalSqlFile}\n";
    exit(1);
}

echo "📂 Reading original SQL file...\n";
$content = file_get_contents($originalSqlFile);

if ($content === false) {
    echo "❌ Failed to read SQL file\n";
    exit(1);
}

echo "✅ Original file loaded (" . number_format(strlen($content)) . " bytes)\n";

// Split into lines for better processing
$lines = explode("\n", $content);
$filteredLines = [];
$insideExcludedTable = false;
$skippedLines = 0;
$totalLines = count($lines);

echo "🔄 Filtering content...\n";

foreach ($lines as $lineNum => $line) {
    $line = trim($line);
    
    // Check if we're starting an excluded table
    if (preg_match('/INSERT INTO [`\'"]?(users|permissions|user_permissions)[`\'"]?/i', $line) ||
        preg_match('/CREATE TABLE [`\'"]?(users|permissions|user_permissions)[`\'"]?/i', $line)) {
        $insideExcludedTable = true;
        $skippedLines++;
        continue;
    }
    
    // Check if we're ending an excluded table section
    if ($insideExcludedTable) {
        // Skip until we find a new table or section
        if (preg_match('/^(CREATE TABLE|INSERT INTO|DROP TABLE|ALTER TABLE|\/\*)/i', $line) && 
            !preg_match('/(users|permissions|user_permissions)/i', $line)) {
            $insideExcludedTable = false;
        } else {
            $skippedLines++;
            continue;
        }
    }
    
    // Add line to filtered content
    $filteredLines[] = $line;
    
    // Show progress
    if ($lineNum % 1000 == 0 && $lineNum > 0) {
        $progress = round(($lineNum / $totalLines) * 100, 1);
        echo "   Processing... {$progress}% ({$lineNum}/{$totalLines} lines)\n";
    }
}

echo "✅ Filtering completed\n";
echo "   • Original lines: " . number_format($totalLines) . "\n";
echo "   • Filtered lines: " . number_format(count($filteredLines)) . "\n";
echo "   • Skipped lines: " . number_format($skippedLines) . "\n";

// Write filtered content
echo "\n💾 Writing filtered SQL file...\n";
$filteredContent = implode("\n", $filteredLines);

if (file_put_contents($filteredSqlFile, $filteredContent) === false) {
    echo "❌ Failed to write filtered SQL file\n";
    exit(1);
}

echo "✅ Filtered SQL file created: {$filteredSqlFile}\n";
echo "   Size: " . number_format(strlen($filteredContent)) . " bytes\n";

// Get database connection info
$config = config('database.connections.mysql');
$host = $config['host'];
$database = $config['database'];
$username = $config['username'];
$password = $config['password'];

echo "\n🚀 Importing filtered SQL using MySQL command...\n";

// Construct MySQL command
$mysqlCmd = "mysql -h {$host} -u {$username}";
if (!empty($password)) {
    $mysqlCmd .= " -p{$password}";
}
$mysqlCmd .= " {$database} < \"{$filteredSqlFile}\"";

echo "📝 MySQL command: " . str_replace("-p{$password}", "-p***", $mysqlCmd) . "\n\n";

echo "⚠️  Please run this command manually in your terminal:\n";
echo "---------------------------------------------------\n";
echo $mysqlCmd . "\n";
echo "---------------------------------------------------\n";

echo "\nOr if you prefer, you can use phpMyAdmin or MySQL Workbench to import the filtered file:\n";
echo "📁 File location: {$filteredSqlFile}\n";

echo "\n✅ Preparation completed! Please run the MySQL import command above.\n";