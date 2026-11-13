<style>
    .sugeng-sheet-scroll { overflow: auto; max-height: 75vh; background: #e5e7eb; padding: 16px; }
    .sugeng-sheet { border-collapse: collapse; table-layout: fixed; background: white; color: #000; font: 12px Arial, sans-serif; }
    .sugeng-sheet th, .sugeng-sheet td { border: 1px solid #222; padding: 5px 6px; vertical-align: top; white-space: pre-line; overflow-wrap: anywhere; }
    .sugeng-sheet thead th { text-align: center; vertical-align: middle; font-size: 11px; font-weight: bold; background: white; }
    .sugeng-sheet .sheet-title td { border: 0; padding: 4px 8px; }
    .sugeng-sheet .sheet-company { font: bold 22px 'Book Antiqua', Georgia, serif; }
    .sugeng-sheet .sheet-heading { text-align: center; font-size: 28px; font-weight: bold; padding: 14px 0; }
    .sugeng-sheet .sheet-section th { border: 0; text-align: left; }
    .sugeng-sheet .sheet-section .sheet-manifest { background: #92d050; }
    .sugeng-sheet .sheet-section .sheet-detail { background: #00b0f0; }
    .sugeng-sheet .sheet-container td { font-weight: bold; border-top: 2px solid #222; }
    .sugeng-sheet a { color: #1d4ed8; text-decoration: underline; }
    .sugeng-sheet td[contenteditable="true"] { cursor: text; }
    .sugeng-sheet td[contenteditable="true"]:focus { outline: 2px solid #4f46e5; outline-offset: -2px; background: #eef2ff; }
    .sugeng-sheet td[data-state="saving"] { background: #fef3c7; }
    .sugeng-sheet td[data-state="saved"] { background: #dcfce7; }
    .sugeng-sheet td[data-state="error"] { background: #fee2e2; }
    .sugeng-sheet tbody td:focus { outline: 2px solid #4f46e5; outline-offset: -2px; background: #eef2ff; }
    .sugeng-sheet .sheet-cell-content { max-height: 34px; overflow: hidden; white-space: pre-line; }
    .sugeng-sheet td:focus-within .sheet-cell-content { overflow: auto; }
    .sugeng-sheet .sheet-row-number { position: sticky; left: 0; z-index: 2; width: 44px; min-width: 44px; padding: 0; text-align: center; vertical-align: middle; background: #f3f4f6; color: #4b5563; font-weight: normal; cursor: pointer; user-select: none; }
    .sugeng-sheet tbody tr[data-selected="true"] td { background: #dbeafe; }
    .sugeng-sheet tbody tr[data-selected="true"] .sheet-row-number { background: #4f46e5; color: white; }
    .sugeng-sheet tbody tr[data-selected="true"] td:focus { background: #eef2ff; }
</style>
<div class="sugeng-formula-bar mb-2 flex items-start gap-2 rounded border border-gray-300 bg-gray-100 p-2" aria-label="Isi sel terpilih">
    <output class="sugeng-cell-address mt-1 w-16 shrink-0 rounded border border-gray-300 bg-white px-2 py-1 text-sm" aria-label="Alamat sel">—</output>
    <span class="mt-1 text-lg italic text-gray-500" aria-hidden="true">fx</span>
    <label class="sr-only" for="sugeng-formula-input">Isi lengkap sel terpilih</label>
    <textarea id="sugeng-formula-input" class="sugeng-formula-input min-h-16 flex-1 resize-y rounded border border-gray-300 bg-white px-3 py-2 text-sm" rows="3" placeholder="Pilih sel untuk melihat isi lengkapnya" @cannot('manifest-sugeng-manage') readonly @endcannot></textarea>
    @can('manifest-sugeng-manage')<button type="button" class="sugeng-formula-save rounded bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-700" disabled>Simpan</button>@endcan
</div>
<div class="sugeng-sheet-scroll" role="region" aria-label="Manifest dan perincian, geser untuk melihat seluruh kolom" tabindex="0">
    @php
        $widths = [140,95,135,95,55,65,180,65,65,807,65,65,807,110,110,190,210,160,190,210,160,190,210,160,230,130,120,150,110,65,55,220];
        $columns = array_merge(range('B', 'Z'), ['AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG']);
    @endphp
    <table class="sugeng-sheet" style="width: {{ array_sum($widths) + 44 }}px" data-csrf="{{ csrf_token() }}" data-clear-url="{{ route('manifest-sugeng.rows.clear') }}">
        <colgroup><col style="width: 44px">@foreach($widths as $width)<col style="width: {{ $width }}px">@endforeach</colgroup>
        <thead>
            <tr class="sheet-title"><td colspan="33" class="sheet-company">PT. ALEXINDO YAKIN PRIMA</td></tr>
            <tr class="sheet-title"><td colspan="33">Perusahaan Pelayaran Nasional</td></tr>
            <tr class="sheet-title"><td colspan="33" class="sheet-heading">M A N I F E S T &nbsp; O F &nbsp; C A R G O</td></tr>
            <tr class="sheet-title"><td colspan="33">{{ request('nama_kapal') }}{{ request('no_voyage') ? ' / '.request('no_voyage') : '' }}</td></tr>
            <tr class="sheet-section"><th></th><th colspan="7"></th><th colspan="3" class="sheet-manifest">MANIFEST</th><th colspan="3" class="sheet-detail">PERINCIAN</th><th colspan="19"></th></tr>
            <tr>
                <th rowspan="2" class="sheet-row-number">#</th>
                @foreach(['B/L NO.', 'HS CODE', 'MARK AND NUMBERS', 'SEAL NO.'] as $heading)<th rowspan="2">{{ $heading }}</th>@endforeach
                <th colspan="6" rowspan="2">DESCRIPTION OF GOODS</th>
                <th colspan="3" rowspan="2">DESCRIPTION OF GOODS</th>
                @foreach(['WEIGHT', 'MEASS', 'SHIPPER', 'ADDRESS', 'NO. IDENTITAS (NPWP SHIPPER)', 'CONSIGNEE', 'ADDRESS', 'NO. IDENTITAS (NPWP CONSIGNEE)', 'NOTIFY PARTY (CONSIGNEE)', 'ADDRESS', 'NO. IDENTITAS (NPWP NOTIFY PARTY CONSIGNEE)', 'DELIVERY ADDRESS & CONTACT PERSON', 'DOCUMENT PPFTZ-03', 'CONDITION'] as $heading)<th rowspan="2">{{ $heading }}</th>@endforeach
                <th colspan="2">RECEIPT SIGNS & ROAD LETTERS</th>
                <th colspan="3" rowspan="2">K E T E R A N G A N<br>PERHITUNGAN M3 / KGS</th>
            </tr>
            <tr><th>NUMBER</th><th>DATE</th></tr>
        </thead>
        <tbody>
            @php $sheetRow = 1; @endphp
            @php $lastContainer = null; @endphp
            @forelse($items as $item)
                @php
                    $isLcl = $item->source_type === 'lcl' || str_contains(strtoupper((string) $item->tipe_kontainer), 'LCL');
                    $containerKey = implode('|', [$item->nama_kapal, $item->no_voyage, strtoupper(trim((string) $item->nomor_kontainer))]);
                    $containerType = strtoupper($item->tipe_kontainer ?? $item->source_type ?? '');
                    $containerDescription = trim('Container'.($item->size_kontainer ? ' '.$item->size_kontainer.' feet' : '').($containerType ? ' / '.$containerType : ''));
                    $containerCells = array_fill(0, 32, '');
                    $containerCells[2] = $item->nomor_kontainer;
                    $containerCells[3] = $item->no_seal;
                    $containerCells[4] = 1;
                    $containerCells[5] = 'Unit';
                    $containerCells[6] = $containerDescription.' stc :';
                    $containerCells[29] = 1;
                    $containerCells[30] = 'Unit';
                    $containerCells[31] = $containerDescription;
                    $manifestGoods = collect($item->manifest_goods ?? []);
                    $details = collect($item->perincian_barang ?? []);
                    $shipper = $item->shipperJb;
                @endphp
                @if($isLcl && $item->nomor_kontainer && $lastContainer !== $containerKey)
                    <tr class="sheet-container" data-item-id="{{ $item->id }}" data-kind="container" data-line="0"><th scope="row" class="sheet-row-number" tabindex="0" aria-label="Pilih baris {{ $sheetRow++ }}">{{ $sheetRow - 1 }}</th>@foreach($containerCells as $value)
                        @php $column = $columns[$loop->index]; @endphp
                        <td tabindex="-1" @can('manifest-sugeng-manage') contenteditable="true" data-save-url="{{ route('manifest-sugeng.cell.update', $item) }}" data-kind="container" data-line="0" data-column="{{ $column }}" @endcan><div class="sheet-cell-content">{{ data_get($item->sheet_values, 'container.'.$column, $value) }}</div></td>
                    @endforeach</tr>
                @endif
                @php $lastContainer = $containerKey; @endphp
                @for($line = 0; $line < max(1, $manifestGoods->count(), $details->count()); $line++)
                    @php
                        $good = $manifestGoods->get($line, []);
                        $detail = $details->get($line, []);
                        $hsCodes = (array) (data_get($good, 'hs_codes') ?: (data_get($good, 'hs_code') ? [data_get($good, 'hs_code')] : []));
                        $cells = [
                            $line === 0 ? $item->nomor_bl : '', implode("\n", $hsCodes),
                            !$isLcl && $line === 0 ? $item->nomor_kontainer : '',
                            !$isLcl && $line === 0 ? $item->no_seal : '',
                            !$isLcl && $line === 0 ? 1 : '',
                            !$isLcl && $line === 0 ? 'Unit' : '',
                            !$isLcl && $line === 0 ? $containerDescription.' stc :' : '',
                            data_get($good, 'jumlah'), data_get($good, 'satuan'), data_get($good, 'nama_barang'),
                            data_get($detail, 'jumlah'), data_get($detail, 'satuan'), data_get($detail, 'nama_barang'),
                            data_get($good, 'tonase'), data_get($good, 'meter_kubik'),
                            $shipper?->shipper ?: $item->pengirim, $shipper?->alamat_shipper, $shipper?->npwp_shipper,
                            $shipper?->consignee, $shipper?->alamat_consignee, $shipper?->npwp_consignee,
                            $shipper?->notify_party_consignee, $shipper?->alamat_notify_party_consignee, $shipper?->npwp_notify_party_consignee,
                            $shipper?->delivery_address_contact_person, $shipper?->document_ppftz_03, $shipper?->condition,
                            $item->nomor_tanda_terima, $item->tanggal_tanda_terima?->format('d/m/Y'),
                            !$isLcl && $line === 0 ? 1 : '',
                            !$isLcl && $line === 0 ? 'Unit' : '',
                            !$isLcl && $line === 0 ? $containerDescription : '',
                        ];
                    @endphp
                    <tr data-item-id="{{ $item->id }}" data-kind="detail" data-line="{{ $line }}"><th scope="row" class="sheet-row-number" tabindex="0" aria-label="Pilih baris {{ $sheetRow++ }}">{{ $sheetRow - 1 }}</th>@foreach($cells as $value)
                        @php $column = $columns[$loop->index]; @endphp
                        <td tabindex="-1" @can('manifest-sugeng-manage') contenteditable="true" data-save-url="{{ route('manifest-sugeng.cell.update', $item) }}" data-kind="detail" data-line="{{ $line }}" data-column="{{ $column }}" @endcan><div class="sheet-cell-content">{{ data_get($item->sheet_values, 'details.'.$line.'.'.$column, $value) }}</div></td>
                    @endforeach</tr>
                @endfor
            @empty
                <tr><td colspan="33" style="text-align:center;padding:32px">Belum ada data Manifest Sugeng untuk pilihan ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
