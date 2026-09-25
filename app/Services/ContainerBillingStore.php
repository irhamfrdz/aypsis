<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ContainerBillingStore
{
    public const STORES = ['masters', 'rates', 'rentals', 'expected', 'invoices', 'detachedInvoices', 'payments', 'pranotas', 'oplog', 'settings', 'rentalOverrides'];

    public function snapshot(): array
    {
        return DB::transaction(function () {
            $revision = DB::table('container_billing_revisions')->where('id', 1)->lockForUpdate()->value('revision');
            $data = array_fill_keys(self::STORES, []);
            foreach (DB::table('container_billing_records')->orderBy('id')->get() as $record) {
                $data[$record->store][] = json_decode($record->payload, true, 512, JSON_THROW_ON_ERROR);
            }

            return ['revision' => (int) $revision, 'data' => $data];
        });
    }

    public function mutate(array $input, int $userId): array
    {
        $operation = $input['operation'];
        $datasets = $operation === 'replace' ? $input['data'] : [$input['store'] => $input['rows'] ?? []];
        foreach ($datasets as $store => $rows) {
            if (! in_array($store, self::STORES, true) || ! is_array($rows) || ! array_is_list($rows)) {
                throw ValidationException::withMessages(['data' => 'Koleksi data tidak valid.']);
            }
            foreach ($rows as $row) {
                if (! is_array($row) || ! isset($row['id']) || (! is_string($row['id']) && ! is_int($row['id'])) || strlen((string) $row['id']) > 1000) {
                    throw ValidationException::withMessages(['data' => 'Setiap record wajib memiliki ID string atau integer (maksimal 1000 karakter).']);
                }
            }
        }
        if ($operation === 'replace' && array_diff(self::STORES, array_keys($datasets))) {
            throw ValidationException::withMessages(['data' => 'Snapshot harus memuat seluruh koleksi.']);
        }

        return DB::transaction(function () use ($input, $operation, $datasets, $userId) {
            $revision = DB::table('container_billing_revisions')->where('id', 1)->lockForUpdate()->first();
            abort_if((int) $revision->revision !== (int) $input['revision'], 409, 'Data sudah berubah di sesi lain. Muat ulang halaman sebelum melanjutkan.');
            if ($operation === 'replace') {
                DB::table('container_billing_records')->delete();
            }
            foreach ($datasets as $store => $rows) {
                if ($operation === 'clear') {
                    DB::table('container_billing_records')->where('store', $store)->delete();
                } elseif ($operation === 'delete') {
                    DB::table('container_billing_records')->where('store', $store)->where('record_key', $this->key($input['id']))->delete();
                } else {
                    $records = array_map(fn ($row) => [
                        'store' => $store,
                        'record_key' => $this->key($row['id']),
                        'payload' => json_encode($row, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
                        'updated_by' => $userId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ], $rows);
                    foreach (array_chunk($records, 100) as $chunk) {
                        DB::table('container_billing_records')->upsert($chunk, ['store', 'record_key'], ['payload', 'updated_by', 'updated_at']);
                    }
                }
            }
            $next = (int) $revision->revision + 1;
            DB::table('container_billing_revisions')->where('id', 1)->update(['revision' => $next, 'updated_at' => now()]);

            return ['revision' => $next];
        });
    }

    private function key(string|int $id): string
    {
        return hash('sha256', json_encode($id, JSON_THROW_ON_ERROR));
    }
}
