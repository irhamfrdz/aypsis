<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Throwable;

class CleanupDuplicateBlAp14Bj26 extends Command
{
    protected $signature = 'bl:hapus-duplikat-ap14bj26 {--execute : Hapus duplikat; tanpa opsi ini hanya pratinjau}';

    protected $description = 'Periksa dan hapus baris BL identik pada voyage AP14BJ26, menyisakan satu baris per nomor BL';

    private const VOYAGE = 'AP14BJ26';

    private const REFERENCE_TABLES = [
        'tagihan_ob',
        'surat_jalan_bongkarans',
        'surat_jalan_bongkaran_batams',
        'biaya_kapal_meratus',
        'biaya_kapal_temas',
        'biaya_kapal_tanto',
    ];

    private const JSON_REFERENCE_COLUMNS = [
        'biaya_kapal_buruh_batams' => 'kontainer_ids',
        'biaya_kapal_buruh_bongkars' => 'kontainer_ids',
        'biaya_kapal_demurrages' => 'kontainer_ids',
        'biaya_kapal_freights' => 'kontainer_ids',
        'biaya_kapal_klaims' => 'kontainer_ids',
        'biaya_kapal_lolos' => 'kontainer_ids',
        'biaya_kapal_nota_returs' => 'kontainer_ids',
        'biaya_kapal_storages' => 'kontainer_ids',
        'biaya_kapal_thcs' => 'kontainer_ids',
        'invoice_aktivitas_lain' => 'bl_details',
    ];

    public function handle(): int
    {
        if (! Schema::hasTable('bls') || ! Schema::hasColumn('bls', 'nomor_bl')) {
            $this->error('Tabel bls atau kolom nomor_bl tidak ditemukan.');

            return self::FAILURE;
        }

        $execute = (bool) $this->option('execute');

        try {
            $result = DB::transaction(function () use ($execute) {
                $query = DB::table('bls')->where('no_voyage', self::VOYAGE)->orderBy('id');
                $rows = ($execute ? $query->lockForUpdate() : $query)->get();
                $references = $this->referenceCounts($rows->pluck('id')->all());
                $groups = $rows->filter(fn ($row) => ! in_array(trim((string) $row->nomor_bl), ['', '-'], true))
                    ->groupBy(fn ($row) => trim((string) $row->nomor_bl))
                    ->filter(fn ($group) => $group->count() > 1);

                $report = [];
                $deleteIds = [];
                $replacements = [];
                $blocked = 0;

                foreach ($groups as $number => $group) {
                    $first = $group->first();
                    $sameData = $group->every(fn ($row) => $this->businessFields($row) === $this->businessFields($first));
                    $linked = $group->filter(fn ($row) => ($references[$row->id] ?? 0) > 0);
                    $reason = null;

                    if (! $sameData) {
                        $reason = 'Isi berbeda';
                    }

                    if ($reason !== null) {
                        $blocked++;
                        $report[] = [$number, $group->pluck('id')->implode(', '), '-', '-', $reason];

                        continue;
                    }

                    $keep = $linked->first() ?? $first;
                    $remove = $group->pluck('id')->reject(fn ($id) => (int) $id === (int) $keep->id)->values();
                    $deleteIds = array_merge($deleteIds, $remove->all());
                    foreach ($remove as $id) {
                        $replacements[(int) $id] = (int) $keep->id;
                    }
                    $report[] = [$number, $group->pluck('id')->implode(', '), $keep->id, $remove->implode(', '), $linked->count() > 1 ? 'Relasi dialihkan' : 'Siap'];
                }

                if ($execute && $blocked > 0) {
                    throw new RuntimeException("Ada {$blocked} kelompok ambigu. Tidak ada BL yang dihapus; periksa laporan pratinjau.");
                }

                $deleted = 0;
                if ($execute && $deleteIds !== []) {
                    $this->replaceReferences($replacements);
                    $deleted = DB::table('bls')->where('no_voyage', self::VOYAGE)->whereIn('id', $deleteIds)->delete();
                    if ($deleted !== count($deleteIds)) {
                        throw new RuntimeException('Jumlah baris yang terhapus tidak sesuai dengan pratinjau.');
                    }
                }

                return compact('rows', 'groups', 'report', 'deleteIds', 'blocked', 'deleted');
            });
        } catch (Throwable $e) {
            $this->error('Tidak ada perubahan yang disimpan: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->info('Voyage: '.self::VOYAGE);
        $this->info('Total baris BL: '.$result['rows']->count());
        $this->info('Nomor BL berulang: '.$result['groups']->count());
        if ($result['report'] !== []) {
            $this->table(['Nomor BL', 'Semua ID', 'ID disimpan', 'ID dihapus', 'Status'], $result['report']);
        }
        $this->info('Calon baris dihapus: '.count($result['deleteIds']).'; kelompok ambigu: '.$result['blocked']);
        $this->info($execute ? 'Baris terhapus: '.$result['deleted'] : 'Pratinjau saja. Jalankan dengan --execute untuk menghapus.');

        return self::SUCCESS;
    }

    private function businessFields(object $row): array
    {
        $fields = (array) $row;
        unset($fields['id'], $fields['created_at'], $fields['updated_at'], $fields['created_by'], $fields['updated_by']);
        $fields['nomor_bl'] = trim((string) $fields['nomor_bl']);
        ksort($fields);

        return $fields;
    }

    private function referenceCounts(array $ids): array
    {
        if ($ids === []) {
            return [];
        }

        $counts = [];
        foreach (self::REFERENCE_TABLES as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'bl_id')) {
                continue;
            }

            $linked = DB::table($table)->whereIn('bl_id', $ids)
                ->select('bl_id')->selectRaw('COUNT(*) AS jumlah')->groupBy('bl_id')->get();
            foreach ($linked as $item) {
                $counts[$item->bl_id] = ($counts[$item->bl_id] ?? 0) + (int) $item->jumlah;
            }
        }

        $targetIds = array_fill_keys(array_map('intval', $ids), true);
        foreach (self::JSON_REFERENCE_COLUMNS as $table => $column) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
                continue;
            }

            DB::table($table)->where($column, 'like', '%bl_id%')
                ->select('id', $column)->orderBy('id')
                ->chunkById(500, function ($records) use ($table, $column, $targetIds, &$counts) {
                    foreach ($records as $record) {
                        $data = json_decode($record->{$column}, true);
                        if (json_last_error() !== JSON_ERROR_NONE) {
                            throw new RuntimeException("JSON {$table}.{$column} ID {$record->id} tidak valid.");
                        }

                        $referencedIds = [];
                        $this->findBlIds($data, $targetIds, $referencedIds);
                        foreach ($referencedIds as $id => $_) {
                            $counts[$id] = ($counts[$id] ?? 0) + 1;
                        }
                    }
                });
        }

        return $counts;
    }

    private function findBlIds(mixed $value, array $targetIds, array &$found): void
    {
        if (! is_array($value)) {
            return;
        }

        foreach ($value as $key => $item) {
            if ($key === 'bl_id' && is_numeric($item) && isset($targetIds[(int) $item])) {
                $found[(int) $item] = true;
            } elseif (is_array($item)) {
                $this->findBlIds($item, $targetIds, $found);
            }
        }
    }

    private function replaceReferences(array $replacements): void
    {
        foreach (self::REFERENCE_TABLES as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'bl_id')) {
                continue;
            }

            foreach ($replacements as $oldId => $keepId) {
                DB::table($table)->where('bl_id', $oldId)->update(['bl_id' => $keepId]);
            }
        }

        foreach (self::JSON_REFERENCE_COLUMNS as $table => $column) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
                continue;
            }

            DB::table($table)->where($column, 'like', '%bl_id%')
                ->select('id', $column)->orderBy('id')
                ->chunkById(500, function ($records) use ($table, $column, $replacements) {
                    foreach ($records as $record) {
                        $data = json_decode($record->{$column}, true);
                        if (json_last_error() !== JSON_ERROR_NONE) {
                            throw new RuntimeException("JSON {$table}.{$column} ID {$record->id} tidak valid.");
                        }

                        if ($this->replaceBlIds($data, $replacements)) {
                            DB::table($table)->where('id', $record->id)->update([
                                $column => json_encode($data, JSON_THROW_ON_ERROR),
                            ]);
                        }
                    }
                });
        }
    }

    private function replaceBlIds(mixed &$value, array $replacements): bool
    {
        if (! is_array($value)) {
            return false;
        }

        $changed = false;
        foreach ($value as $key => &$item) {
            if ($key === 'bl_id' && is_numeric($item) && isset($replacements[(int) $item])) {
                $item = $replacements[(int) $item];
                $changed = true;
            } elseif (is_array($item)) {
                $changed = $this->replaceBlIds($item, $replacements) || $changed;
            }
        }

        return $changed;
    }
}
