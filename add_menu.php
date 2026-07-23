<?php
$file = 'resources/views/layouts/app.blade.php';
$content = file_get_contents($file);

$menuToInsert = <<<HTML
                {{-- Rekap Bongkaran Kontainer --}}
                @if(\$user && \$user->can('bl-view'))
                    <a href="{{ route('bl.rekap-bongkaran-kontainer.select') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('bl.rekap-bongkaran-kontainer*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                        <span class="text-xs">Rekap Bongkar/Muat Kontainer</span>
                    </a>
                @endif
HTML;

$search = "{{-- Rekap Bongkaran Perincian --}}";

// Insert the new menu
$content = str_replace($search, $menuToInsert . "\n\n                " . $search, $content);

// Update active state logic of original rekap-bongkaran to exclude kontainer as well
$content = str_replace(
    "!Request::routeIs('bl.rekap-bongkaran-perincian*')", 
    "!Request::routeIs('bl.rekap-bongkaran-perincian*') && !Request::routeIs('bl.rekap-bongkaran-kontainer*')", 
    $content
);

file_put_contents($file, $content);
echo "Added menu in app.blade.php";
