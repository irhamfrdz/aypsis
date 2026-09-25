<?php

namespace App\Http\Controllers;

use App\Services\ContainerBillingStore;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContainerBillingController extends Controller
{
    public function index()
    {
        return response()->view('container-billing.index')->header('Cache-Control', 'no-store');
    }

    public function asset(string $file)
    {
        abort_unless(in_array($file, ['app.js', 'baseline_data.js', 'siklus_reference.js', 'examples.js'], true), 404);

        return response()->file(resource_path('container-billing/'.$file), [
            'Content-Type' => 'application/javascript; charset=UTF-8',
            'Cache-Control' => 'private, no-cache',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function state(ContainerBillingStore $store)
    {
        return response()->json($store->snapshot())->header('Cache-Control', 'no-store');
    }

    public function update(Request $request, ContainerBillingStore $store)
    {
        $input = $request->validate([
            'revision' => ['required', 'integer', 'min:0'],
            'operation' => ['required', Rule::in(['put', 'clear', 'delete', 'replace'])],
            'store' => ['required_unless:operation,replace', Rule::in(ContainerBillingStore::STORES)],
            'rows' => ['required_if:operation,put', 'array', 'max:100000'],
            'data' => ['required_if:operation,replace', 'array'],
            'id' => ['required_if:operation,delete', function ($attribute, $value, $fail) {
                if ((! is_string($value) && ! is_int($value)) || strlen((string) $value) > 1000) {
                    $fail('ID tidak valid.');
                }
            }],
        ]);

        return response()->json($store->mutate($input, (int) $request->user()->id));
    }
}
