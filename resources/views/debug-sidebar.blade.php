@extends('layouts.app')

@section('title', 'Debug Sidebar Tujuan Kirim')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Debug Sidebar - Master Tujuan Kirim</h3>
                </div>
                <div class="card-body">
                    @php
                        $user = Auth::user();
                        $hasKaryawan = $user && $user->karyawan;
                        $isAdmin = $user && method_exists($user, 'hasRole') && $user->hasRole('admin');
                        
                        $hasMasterPermissions = $user && (
                            $user->can('master-permission-view') ||
                            $user->can('master-cabang-view') ||
                            $user->can('master-pengirim-view') ||
                            $user->can('master-jenis-barang-view') ||
                            $user->can('master-term-view') ||
                            $user->can('master-coa-view') ||
                            $user->can('master-kode-nomor-view') ||
                            $user->can('master-nomor-terakhir-view') ||
                            $user->can('master-tipe-akun-view') ||
                            $user->can('master-tujuan-view') ||
                            $user->can('master-tujuan-kirim-view') ||
                            $user->can('master-kegiatan-view')
                        );
                        
                        $showMasterSection = $isAdmin || $hasMasterPermissions;
                    @endphp
                    
                    <div class="alert alert-info">
                        <h5>Status Debug:</h5>
                        <ul>
                            <li><strong>User:</strong> {{ $user->username }}</li>
                            <li><strong>Has Karyawan:</strong> {{ $hasKaryawan ? 'Yes' : 'No' }}</li>
                            <li><strong>Is Admin:</strong> {{ $isAdmin ? 'Yes' : 'No' }}</li>
                            <li><strong>Has Master Permissions:</strong> {{ $hasMasterPermissions ? 'Yes' : 'No' }}</li>
                            <li><strong>Show Master Section:</strong> {{ $showMasterSection ? 'Yes' : 'No' }}</li>
                            <li><strong>Can View Tujuan Kirim:</strong> {{ $user->can('master-tujuan-kirim-view') ? 'Yes' : 'No' }}</li>
                        </ul>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h5>Kondisi Menu Master Data:</h5>
                            @if($showMasterSection)
                                <div class="alert alert-success">
                                    ✅ Master Data section AKAN MUNCUL
                                </div>
                                
                                <h6>Menu yang akan tampil:</h6>
                                <ul class="list-group">
                                    @if($user && $user->can('master-permission-view'))
                                        <li class="list-group-item">✅ Master Permission</li>
                                    @endif
                                    @if($user && $user->can('master-cabang-view'))
                                        <li class="list-group-item">✅ Master Cabang</li>
                                    @endif
                                    @if($user && $user->can('master-tujuan-view'))
                                        <li class="list-group-item">✅ Master Tujuan</li>
                                    @endif
                                    @if($user && $user->can('master-tujuan-kirim-view'))
                                        <li class="list-group-item bg-primary text-white">
                                            <strong>✅ TUJUAN KIRIM (TARGET MENU)</strong>
                                        </li>
                                    @endif
                                    @if($user && $user->can('master-kegiatan-view'))
                                        <li class="list-group-item">✅ Master Kegiatan</li>
                                    @endif
                                </ul>
                            @else
                                <div class="alert alert-danger">
                                    ❌ Master Data section TIDAK AKAN MUNCUL
                                </div>
                            @endif
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Instruksi Troubleshooting:</h5>
                            <ol>
                                <li>Pastikan Anda sudah <strong>logout</strong> dan <strong>login kembali</strong></li>
                                <li>Lakukan <strong>hard refresh</strong> browser (Ctrl+F5 atau Cmd+Shift+R)</li>
                                <li>Periksa sidebar di sebelah kiri</li>
                                <li>Cari section <strong>"Master Data"</strong></li>
                                <li><strong>Klik</strong> "Master Data" untuk expand dropdown</li>
                                <li>Menu <strong>"Tujuan Kirim"</strong> harus muncul di dalam dropdown</li>
                            </ol>
                            
                            <div class="alert alert-warning">
                                <strong>Catatan:</strong> Jika menu "Master Data" tidak terlihat, scroll down pada sidebar karena mungkin terpotong.
                            </div>
                            
                            <div class="mt-3">
                                <a href="{{ route('tujuan-kirim.index') }}" class="btn btn-primary">
                                    <i class="fas fa-external-link-alt"></i> Test Direct Link ke Tujuan Kirim
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h5>JavaScript Debug:</h5>
                        <button onclick="debugSidebar()" class="btn btn-info">Check Sidebar Elements</button>
                        <div id="jsDebugOutput" class="mt-2"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function debugSidebar() {
    const output = document.getElementById('jsDebugOutput');
    
    // Check if sidebar exists
    const sidebar = document.getElementById('sidebar');
    const masterToggle = document.getElementById('master-menu-toggle');
    const masterContent = document.getElementById('master-menu-content');
    
    let result = '<div class="alert alert-info">';
    result += '<h6>JavaScript Debug Results:</h6>';
    result += '<ul>';
    result += '<li>Sidebar element: ' + (sidebar ? '✅ Found' : '❌ Not found') + '</li>';
    result += '<li>Master toggle button: ' + (masterToggle ? '✅ Found' : '❌ Not found') + '</li>';
    result += '<li>Master content: ' + (masterContent ? '✅ Found' : '❌ Not found') + '</li>';
    
    if (masterContent) {
        const tujuanKirimLinks = masterContent.querySelectorAll('a[href*="tujuan-kirim"]');
        result += '<li>Tujuan Kirim links: ' + tujuanKirimLinks.length + ' found</li>';
        
        if (tujuanKirimLinks.length > 0) {
            result += '<li>Link text: "' + tujuanKirimLinks[0].textContent.trim() + '"</li>';
            result += '<li>Link href: ' + tujuanKirimLinks[0].href + '</li>';
        }
    }
    
    result += '</ul></div>';
    
    output.innerHTML = result;
}
</script>
@endsection