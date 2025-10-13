@extends('layouts.app')

@section('title', 'Test Menu Tujuan Kirim')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Test Menu Tujuan Kirim</h3>
                </div>
                <div class="card-body">
                    @php
                        $user = Auth::user();
                    @endphp
                    
                    <h4>User Information:</h4>
                    <p><strong>Username:</strong> {{ $user->username }}</p>
                    <p><strong>Has Karyawan:</strong> {{ $user->karyawan ? 'Yes' : 'No' }}</p>
                    
                    <h4>Permission Checks:</h4>
                    <ul>
                        <li><strong>master-tujuan-kirim-view:</strong> {{ $user->can('master-tujuan-kirim-view') ? '✅ YES' : '❌ NO' }}</li>
                        <li><strong>master-tujuan-kirim-create:</strong> {{ $user->can('master-tujuan-kirim-create') ? '✅ YES' : '❌ NO' }}</li>
                        <li><strong>master-tujuan-kirim-update:</strong> {{ $user->can('master-tujuan-kirim-update') ? '✅ YES' : '❌ NO' }}</li>
                        <li><strong>master-tujuan-kirim-delete:</strong> {{ $user->can('master-tujuan-kirim-delete') ? '✅ YES' : '❌ NO' }}</li>
                    </ul>
                    
                    <h4>Master Section Conditions:</h4>
                    @php
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
                        $isAdmin = $user && method_exists($user, 'hasRole') && $user->hasRole('admin');
                        $showMasterSection = $isAdmin || $hasMasterPermissions;
                    @endphp
                    
                    <ul>
                        <li><strong>Has Master Permissions:</strong> {{ $hasMasterPermissions ? '✅ YES' : '❌ NO' }}</li>
                        <li><strong>Is Admin:</strong> {{ $isAdmin ? '✅ YES' : '❌ NO' }}</li>
                        <li><strong>Show Master Section:</strong> {{ $showMasterSection ? '✅ YES' : '❌ NO' }}</li>
                    </ul>
                    
                    <h4>Routes Test:</h4>
                    <p><strong>Tujuan Kirim Index URL:</strong> {{ route('tujuan-kirim.index') }}</p>
                    
                    <div class="mt-4">
                        <a href="{{ route('tujuan-kirim.index') }}" class="btn btn-primary">
                            Go to Master Tujuan Kirim
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection