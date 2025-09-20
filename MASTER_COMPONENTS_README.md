# Komponen Global Master Pages

Berikut adalah komponen-komponen yang dapat digunakan secara global di halaman master untuk konsistensi UI/UX.

## 1. Alert Messages (`components.alerts`)

Menampilkan pesan success, warning, dan error dari session.

**Penggunaan:**

```blade
@include('components.alerts')
```

**Fitur:**

-   Otomatis mendeteksi session `success`, `warning`, `error`
-   Design konsisten dengan icon dan warna yang sesuai
-   Responsive dan accessible

## 2. Search Section (`components.search-section`)

Section pencarian dengan filter dan pagination controls.

**Penggunaan:**

```blade
@include('components.search-section', [
    'routeName' => 'master.route.index',
    'placeholder' => 'Cari data...',
    'label' => 'Cari Data',
    'showPerPage' => true,
    'perPageOptions' => [15, 50, 100]
])
```

**Parameter:**

-   `routeName`: Nama route untuk form action
-   `placeholder`: Placeholder untuk input search
-   `label`: Label untuk input field
-   `showPerPage`: Tampilkan dropdown per page (default: true)
-   `perPageOptions`: Opsi jumlah data per halaman

## 3. Master Header (`components.master-header`)

Header section dengan judul, subtitle, dan action buttons.

**Penggunaan:**

```blade
@include('components.master-header', [
    'title' => 'Master Title',
    'subtitle' => 'Optional subtitle',
    'createRoute' => 'master.route.create',
    'createText' => 'Tambah Data',
    'buttons' => [
        [
            'route' => 'route.name',
            'text' => 'Button Text',
            'icon' => '<svg>...</svg>',
            'color' => 'blue',
            'type' => 'link'
        ]
    ]
])
```

**Parameter:**

-   `title`: Judul halaman
-   `subtitle`: Subtitle (optional)
-   `createRoute`: Route untuk tombol create
-   `createText`: Teks tombol create
-   `buttons`: Array tombol tambahan

## 4. Table Container (`components.table-container`)

Container untuk table dengan sticky header dan scroll to top.

**Penggunaan:**

```blade
@include('components.table-container', [
    'maxHeight' => 'calc(100vh - 300px)',
    'scrollToTop' => true
])
    <thead class="sticky-table-header">
        <!-- table header -->
    </thead>
    <tbody>
        <!-- table body -->
    </tbody>
@endinclude
```

**Parameter:**

-   `maxHeight`: Tinggi maksimal container (default: calc(100vh - 300px))
-   `scrollToTop`: Tampilkan tombol scroll to top (default: true)

## 5. Modern Pagination (`components.modern-pagination`)

Pagination dengan page jump dan statistik lengkap.

**Penggunaan:**

```blade
@include('components.modern-pagination', [
    'paginator' => $dataPaginator,
    'routeName' => 'master.route.index'
])
```

**Parameter:**

-   `paginator`: Instance paginator Laravel
-   `routeName`: Nama route untuk navigasi

## 6. Delete Modal (`components.delete-modal`)

Modal konfirmasi hapus dengan informasi item.

**Penggunaan:**

```blade
@include('components.delete-modal', [
    'id' => 'deleteModal',
    'title' => 'Konfirmasi Hapus',
    'message' => 'Yakin ingin menghapus data ini?',
    'warning' => 'Tindakan ini tidak dapat dibatalkan',
    'itemInfo' => [
        ['label' => 'Nama', 'value' => $item->nama],
        ['label' => 'ID', 'value' => $item->id]
    ],
    'additionalWarnings' => [
        'Data akan dihapus permanen',
        'Semua riwayat terkait akan hilang'
    ],
    'deleteAction' => 'confirmDelete()',
    'deleteText' => 'Ya, Hapus'
])
```

## Contoh Implementasi Lengkap

Berikut contoh halaman master lengkap menggunakan semua komponen:

```blade
@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header Section --}}
        @include('components.master-header', [
            'title' => 'Master Data',
            'subtitle' => 'Kelola data sistem',
            'createRoute' => 'master.data.create',
            'buttons' => [
                [
                    'route' => 'master.data.import',
                    'text' => 'Import',
                    'icon' => '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>',
                    'color' => 'green'
                ]
            ]
        ])

        {{-- Alert Messages --}}
        @include('components.alerts')

        {{-- Search Section --}}
        @include('components.search-section', [
            'routeName' => 'master.data.index',
            'placeholder' => 'Cari data...',
            'label' => 'Cari Data'
        ])

        {{-- Table Section --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            @include('components.table-container')
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kolom 1</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kolom 2</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($data as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->field1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->field2 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <a href="{{ route('master.data.edit', $item) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                <button onclick="openDeleteModal()" class="ml-4 text-red-600 hover:text-red-900">Hapus</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            @endinclude

            {{-- Pagination --}}
            @include('components.modern-pagination', [
                'paginator' => $data,
                'routeName' => 'master.data.index'
            ])
        </div>

        {{-- Delete Modal --}}
        @include('components.delete-modal', [
            'itemInfo' => [
                ['label' => 'Nama', 'value' => 'Nama Item'],
                ['label' => 'ID', 'value' => 'ID Item']
            ]
        ])

    </div>
</div>
@endsection
```

## Keuntungan Menggunakan Komponen

1. **Konsistensi UI/UX** di seluruh aplikasi
2. **Maintenance** lebih mudah - perubahan di satu tempat
3. **Reusability** - komponen dapat digunakan di berbagai halaman
4. **Standardization** - pola yang sama untuk semua halaman master
5. **Developer Experience** - kode lebih bersih dan mudah dibaca
