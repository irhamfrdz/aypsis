<?php
$dir = __DIR__ . '/resources/views';
$iter = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

$count = 0;
foreach ($iter as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        $original = $content;

        // Match fetch('{{ route(...) }}' or url: '{{ route(...) }}'
        // or axios.get('{{ route(...) }}' or $.get('{{ route(...) }}'
        // Pattern logic: find typical js ajax call patterns, then find {{ route('something') }} inside it.
        // Actually, let's just replace all `route('something')` inside `<script>` blocks with `route('something', [], false)`
        // Wait, finding <script> blocks is easy: `/<script>(.*?)<\/script>/is`
        
        $replaced = preg_replace_callback('/<script\b[^>]*>(.*?)<\/script>/is', function($scriptMatch) {
            $scriptContent = $scriptMatch[1];
            
            // Inside the script content, find {{ route(...) }} and replace
            // {{ route('name') }} => {{ route('name', [], false) }}
            // {{ route('name', ['param' => $val]) }} => {{ route('name', ['param' => $val], false) }}
            
            $newScriptContent = preg_replace_callback('/\{\{\s*route\(([\'"][^\'"]+[\'"])(?:,\s*(.*?))?\)\s*\}\}/i', function($routeMatch) {
                $routeName = $routeMatch[1];
                $paramsStr = isset($routeMatch[2]) && strlen(trim($routeMatch[2])) > 0 ? trim($routeMatch[2]) : '[]';
                
                // If it already ends with false, or has false as a param at the end, don't change it.
                if (preg_match('/false\s*$/i', $paramsStr)) {
                    return $routeMatch[0];
                }
                
                // Add false parameter
                return "{{ route($routeName, $paramsStr, false) }}";
            }, $scriptContent);
            
            return str_replace($scriptMatch[1], $newScriptContent, $scriptMatch[0]);
        }, $content);
        
        if ($replaced !== $original) {
            file_put_contents($file->getPathname(), $replaced);
            $count++;
            echo "Updated: " . $file->getPathname() . "\n";
        }
    }
}
echo "Total files updated: $count\n";
