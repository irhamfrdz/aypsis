<?php

$content = file_get_contents('resources/views/stock-ban/create.blade.php');
$len = strlen($content);
$stack = [];

$line = 1;
$col = 1;
for ($i = 0; $i < $len; $i++) {
    $char = $content[$i];
    if ($char === "\n") {
        $line++;
        $col = 1;

        continue;
    }

    // Ignore strings
    if ($char === '"' || $char === "'") {
        $quote = $char;
        $i++;
        $col++;
        while ($i < $len && ($content[$i] !== $quote || $content[$i - 1] === '\\')) {
            if ($content[$i] === "\n") {
                $line++;
                $col = 1;
            } else {
                $col++;
            }
            $i++;
        }
        $col++;

        continue;
    }

    if ($char === '[' || $char === '(' || $char === '{') {
        $stack[] = ['char' => $char, 'line' => $line, 'col' => $col];
    } elseif ($char === ']' || $char === ')' || $char === '}') {
        if (empty($stack)) {
            echo "Unmatched closing $char on line $line, col $col\n";
            $col++;

            continue;
        }
        $last = array_pop($stack);
        $expected = '';
        if ($last['char'] === '[') {
            $expected = ']';
        }
        if ($last['char'] === '(') {
            $expected = ')';
        }
        if ($last['char'] === '{') {
            $expected = '}';
        }
        if ($char !== $expected) {
            echo "Mismatched $char on line $line, col $col (expected $expected to match {$last['char']} on line {$last['line']}, col {$last['col']})\n";
        }
    }
    $col++;
}
if (! empty($stack)) {
    echo "Unclosed brackets/braces left:\n";
    foreach ($stack as $s) {
        echo "{$s['char']} on line {$s['line']}, col {$s['col']}\n";
    }
}
