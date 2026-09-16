<button type="button" class="manifest-shipper-open mt-1 text-purple-700 hover:text-purple-900 underline text-[10px] font-medium"
        data-update-url="{{ route('report.manifests.update-shipper', $manifest->id) }}"
        data-manifest="{{ json_encode($manifest->only(['id', 'nomor_bl', 'nomor_kontainer', 'shipper_id', 'pengirim', 'alamat_pengirim', 'penerima', 'notify_party', 'alamat_notify_party'])) }}">
    {{ $manifest->shipper_id ? 'Ubah Shipper' : 'Pilih Shipper' }}
</button>
