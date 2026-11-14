<?php

namespace App\Services;

use App\Models\PembayaranAktivitasLain;
use Illuminate\Support\Collection;

class VehiclePaymentUsageService
{
    public function forVehicles(Collection $vehicles, string $fromDate, string $toDate): Collection
    {
        $plateToIds = [];
        foreach ($vehicles as $vehicle) {
            foreach ([$vehicle->nomor_polisi, $vehicle->no_kir] as $number) {
                $key = $this->normalize($number);
                if ($key !== '') {
                    $plateToIds[$key][] = $vehicle->id;
                }
            }
        }

        if ($plateToIds === []) {
            return collect();
        }

        $payments = PembayaranAktivitasLain::query()
            ->where('jenis_aktivitas', 'Pembayaran Kendaraan')
            ->whereBetween('tanggal', [$fromDate, $toDate])
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get();

        $byVehicle = collect();
        foreach ($payments as $payment) {
            $details = $payment->kendaraan_details;
            $entries = is_array($details) && $details !== []
                ? $details
                : collect($payment->nomor_polisi_list ?: [$payment->nomor_polisi])
                    ->filter()
                    ->map(fn ($number) => ['nomor_polisi' => $number, 'nominal' => null, 'memo' => null])
                    ->all();

            $seenIds = [];
            foreach ($entries as $entry) {
                $key = $this->normalize($entry['nomor_polisi'] ?? null);
                foreach (array_unique($plateToIds[$key] ?? []) as $vehicleId) {
                    if (isset($seenIds[$vehicleId])) {
                        continue;
                    }
                    $seenIds[$vehicleId] = true;
                    $byVehicle[$vehicleId] ??= collect();
                    $byVehicle[$vehicleId]->push([
                        'tanggal' => $payment->tanggal,
                        'nomor' => $payment->nomor,
                        'jenis' => $payment->sub_jenis_kendaraan ?: 'Lain Lain',
                        'biaya' => $entry['nominal'] ?? (count($entries) === 1 ? $payment->jumlah : null),
                        'memo' => $entry['memo'] ?? null,
                        'keterangan' => $payment->keterangan,
                    ]);
                }
            }
        }

        return $byVehicle;
    }

    private function normalize(?string $number): string
    {
        return preg_replace('/[^A-Z0-9]/', '', strtoupper($number ?? ''));
    }
}
