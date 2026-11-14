<style>
    .sugeng-sheet-scroll { position: relative; overflow: auto; height: 75vh; background: #e5e7eb; padding: 16px; }
    .sugeng-sheet-resize { height: 10px; margin-top: -5px; position: relative; z-index: 5; cursor: ns-resize; background: #d1d5db; border-radius: 0 0 6px 6px; }
    .sugeng-sheet-resize:hover { background: #818cf8; }
    .sugeng-sheet { border-collapse: collapse; table-layout: fixed; background: white; color: #000; font: 12px Arial, sans-serif; }
    .sugeng-sheet th, .sugeng-sheet td { border: 1px solid #222; padding: 5px 6px; vertical-align: top; white-space: pre-line; overflow-wrap: anywhere; }
    .sugeng-sheet tbody th, .sugeng-sheet tbody td { box-sizing: border-box; }
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
    .sugeng-sheet tbody td[data-column="C"], .sugeng-sheet tbody td[data-column="D"] { text-align: center; vertical-align: middle; }
    .sugeng-sheet tbody td[data-column="AA"] { text-align: center; vertical-align: middle; }
    .sugeng-sheet tbody td[data-column="AA"].sheet-ayp { background: #ff0000 !important; color: #000; font-weight: bold; }
    .sugeng-sheet tbody td[data-column="F"], .sugeng-sheet tbody td[data-column="I"], .sugeng-sheet tbody td[data-column="L"], .sugeng-sheet tbody td[data-column="AE"] { text-align: right; }
    .sugeng-sheet tbody td[data-column="G"], .sugeng-sheet tbody td[data-column="J"], .sugeng-sheet tbody td[data-column="M"], .sugeng-sheet tbody td[data-column="AF"] { text-align: left; }
    .sugeng-sheet .sheet-cell-content { max-height: 34px; overflow: hidden; white-space: pre-line; }
    .sugeng-sheet td:focus-within .sheet-cell-content { overflow: auto; }
    .sugeng-sheet .sheet-row-number { position: sticky; left: 0; z-index: 2; width: 44px; min-width: 44px; padding: 0; text-align: center; vertical-align: middle; background: #f3f4f6; color: #4b5563; font-weight: normal; cursor: pointer; user-select: none; }
    .sugeng-sheet .sheet-size-heading { position: relative; height: 24px; padding: 2px; background: #f3f4f6; color: #374151; font-size: 10px; user-select: none; }
    .sugeng-sheet .sheet-column-resize { position: absolute; top: 0; right: -4px; z-index: 3; width: 8px; height: 100%; cursor: col-resize; touch-action: none; }
    .sugeng-sheet tbody .sheet-row-number { position: sticky; }
    .sugeng-sheet .sheet-row-resize { position: absolute; bottom: -5px; left: 0; z-index: 4; display: block; width: 100%; height: 12px; cursor: row-resize; touch-action: none; }
    .sugeng-sheet .sheet-row-resize::after { content: ''; position: absolute; bottom: 4px; left: 10px; right: 10px; height: 3px; border-radius: 2px; background: #818cf8; }
    .sugeng-sheet .sheet-row-resize:hover::after { height: 5px; background: #4f46e5; }
    .sugeng-sheet tbody tr[data-selected="true"] td { background: #dbeafe; }
    .sugeng-sheet tbody tr.sheet-incomplete td, .sugeng-sheet tbody tr.sheet-incomplete .sheet-row-number { background: #fef3c7; }
    .sugeng-sheet tbody tr.sheet-incomplete[data-selected="true"] td { background: #dbeafe; }
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
<div class="sugeng-size-controls mb-2 flex flex-wrap items-end gap-3 rounded border border-gray-300 bg-white p-3 text-sm" aria-label="Pengaturan ukuran tabel">
    <label class="flex flex-col gap-1">Kolom
        <select class="sugeng-size-column rounded border border-gray-300 px-2 py-1">
            @foreach(array_merge(range('B', 'Z'), ['AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG']) as $column)<option value="{{ $column }}">{{ $column }}</option>@endforeach
        </select>
    </label>
    <label class="flex flex-col gap-1">Lebar kolom (px)
        <input type="number" class="sugeng-size-width w-28 rounded border border-gray-300 px-2 py-1" min="40" max="1600" step="1">
    </label>
    <label class="flex flex-col gap-1">Tinggi baris (px)
        <input type="number" class="sugeng-size-height w-28 rounded border border-gray-300 px-2 py-1" min="24" max="500" step="1" value="46">
    </label>
    <label class="flex flex-col gap-1">Tinggi area tabel (px)
        <input type="number" class="sugeng-size-table-height w-28 rounded border border-gray-300 px-2 py-1" min="200" max="2000" step="1">
    </label>
    <button type="button" class="sugeng-size-reset rounded border border-gray-300 px-3 py-1 hover:bg-gray-100">Reset ukuran</button>
    <span class="text-gray-600">Tarik batas kanan huruf kolom untuk lebar, batas bawah nomor baris untuk tinggi baris, atau bilah di bawah tabel untuk tinggi area tabel.</span>
</div>
<div class="sugeng-sheet-scroll" role="region" aria-label="Manifest dan perincian, geser untuk melihat seluruh kolom" tabindex="0">
    @php
        $widths = [75,75,105,95,55,65,180,65,65,807,65,65,807,110,110,190,210,160,190,210,160,190,210,160,230,130,120,150,110,65,55,220];
        $columns = array_merge(range('B', 'Z'), ['AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG']);
    @endphp
    <table class="sugeng-sheet" style="width: {{ array_sum($widths) + 44 }}px" data-csrf="{{ csrf_token() }}" data-clear-url="{{ route('manifest-sugeng.rows.clear') }}">
        <colgroup><col style="width: 44px">@foreach($widths as $width)<col style="width: {{ $width }}px">@endforeach</colgroup>
        <thead>
            <tr class="sheet-size-headings"><th class="sheet-size-heading">#</th>@foreach($columns as $column)<th class="sheet-size-heading">{{ $column }}<span class="sheet-column-resize" data-column="{{ $column }}" title="Tarik untuk mengatur lebar kolom"></span></th>@endforeach</tr>
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
                    <tr class="sheet-container" data-item-id="{{ $item->id }}" data-kind="container" data-line="0"><th scope="row" class="sheet-row-number" tabindex="0" aria-label="Pilih baris {{ $sheetRow++ }}">{{ $sheetRow - 1 }}<span class="sheet-row-resize" title="Tarik untuk mengatur tinggi baris"></span></th>@foreach($containerCells as $value)
                        @php $column = $columns[$loop->index]; @endphp
                        <td tabindex="-1" data-column="{{ $column }}" @class(['sheet-ayp' => $column === 'AA' && strtoupper(trim((string) data_get($item->sheet_values, 'container.'.$column, $value))) === 'AYP']) @can('manifest-sugeng-manage') contenteditable="true" data-save-url="{{ route('manifest-sugeng.cell.update', $item) }}" data-kind="container" data-line="0" @endcan><div class="sheet-cell-content">{{ data_get($item->sheet_values, 'container.'.$column, $value) }}</div></td>
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
                    <tr data-item-id="{{ $item->id }}" data-kind="detail" data-line="{{ $line }}" @class(['sheet-incomplete' => $item->belum_selesai])><th scope="row" class="sheet-row-number" tabindex="0" aria-label="Pilih baris {{ $sheetRow++ }}">{{ $sheetRow - 1 }}<span class="sheet-row-resize" title="Tarik untuk mengatur tinggi baris"></span>@if($line === 0)
                        @can('manifest-sugeng-manage')<form method="POST" action="{{ route('manifest-sugeng.set-incomplete', $item) }}" onclick="event.stopPropagation()" class="mt-1">@csrf @method('PUT')<input type="hidden" name="belum_selesai" value="{{ $item->belum_selesai ? '0' : '1' }}"><button type="submit" class="rounded px-1 text-xs {{ $item->belum_selesai ? 'bg-amber-500 text-white' : 'bg-gray-200 text-gray-600' }}" title="{{ $item->belum_selesai ? 'Batalkan tanda belum selesai' : 'Tandai belum selesai' }}" aria-label="{{ $item->belum_selesai ? 'Batalkan tanda belum selesai' : 'Tandai belum selesai' }}"><i class="fas fa-highlighter" aria-hidden="true"></i></button></form>
                        @elsecan('manifest-sugeng-view')
                            @if($item->belum_selesai)<span class="block text-xs text-amber-800" title="Belum selesai">●</span>@endif
                        @endcan
                    @endif</th>@foreach($cells as $value)
                        @php $column = $columns[$loop->index]; @endphp
                        <td tabindex="-1" data-column="{{ $column }}" @class(['sheet-ayp' => $column === 'AA' && strtoupper(trim((string) data_get($item->sheet_values, 'details.'.$line.'.'.$column, $value))) === 'AYP']) @can('manifest-sugeng-manage') contenteditable="true" data-save-url="{{ route('manifest-sugeng.cell.update', $item) }}" data-kind="detail" data-line="{{ $line }}" @endcan><div class="sheet-cell-content">{{ data_get($item->sheet_values, 'details.'.$line.'.'.$column, $value) }}</div></td>
                    @endforeach</tr>
                @endfor
            @empty
                <tr><td colspan="33" style="text-align:center;padding:32px">Belum ada data Manifest Sugeng untuk pilihan ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="sugeng-sheet-resize" title="Tarik untuk mengatur tinggi area tabel" aria-hidden="true"></div>
