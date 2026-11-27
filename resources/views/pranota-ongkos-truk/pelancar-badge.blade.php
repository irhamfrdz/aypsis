@php
    // Spread distinct amounts across the color wheel, independently of row order.
    $index = $amounts->search(fn ($value) => (float) $value === (float) $amount);
    $hue = (($index === false ? 0 : $index) / max(1, $amounts->count())) * 6;
    $sector = (int) floor($hue);
    $fraction = $hue - $sector;
    $rgb = match ($sector % 6) {
        0 => [1, $fraction, 0], 1 => [1 - $fraction, 1, 0],
        2 => [0, 1, $fraction], 3 => [0, 1 - $fraction, 1],
        4 => [$fraction, 0, 1], default => [1, 0, 1 - $fraction],
    };
    $background = sprintf('#%02x%02x%02x', ...array_map(fn ($c) => (int) round(225 + 25 * $c), $rgb));
    $foreground = sprintf('#%02x%02x%02x', ...array_map(fn ($c) => (int) round(35 + 65 * $c), $rgb));
@endphp
<span style="display:inline-block;padding:3px 6px;border-radius:5px;background-color:{{ $background }};color:{{ $foreground }};border:1px solid {{ $foreground }};font-weight:bold;">{{ number_format($amount, 0, ',', '.') }}</span>
