                        {{-- Master Umum Sub-Dropdown --}}
                        @php
                            $isUmumRoute = Request::routeIs('master.cabang.*') || Request::routeIs('master.kode-nomor.*') || Request::routeIs('master.nomor-terakhir.*') || Request::routeIs('master.kegiatan.*') || Request::routeIs('master-pelabuhan.*') || Request::routeIs('klasifikasi-biaya.*') || Request::routeIs('master-dokumen-perijinan-kapal.*') || Request::routeIs('master.lwbp-lama.*') || Request::routeIs('master.item-kwitansi.*') || Request::routeIs('master.mesin.*') || Request::routeIs('master.shipper-consignee.*') || Request::routeIs('master.wa-templates.*') || Request::routeIs('master.wa-broadcast.*') || Request::routeIs('master.wa-gateway.*') || Request::routeIs('master-customer-buruh.*');
                            $hasUmumPermissions = $user && ($user->can('master-cabang-view') || $user->can('master-kode-nomor-view') || $user->can('master-nomor-terakhir-view') || $user->can('master-kegiatan-view') || $user->can('master-pelabuhan-view') || $user->can('master-klasifikasi-biaya-view') || $user->can('master-dokumen-perijinan-kapal-view') || $user->can('master-lwbp-lama-view') || $user->can('mesin-view') || $user->can('master-shipper-consignee-view') || $user->can('master-wa-templates-view') || $user->can('master-wa-broadcast-view') || $user->can('master-customer-buruh-view') || $isAdmin);
                        @endphp

                        @if($hasUmumPermissions)
                        <div class="mx-2 mb-3">
                            <button id="master-umum-toggle" class="w-full flex justify-between items-center py-2 px-3 rounded-lg text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 group {{ $isUmumRoute ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                                <span class="text-xs font-medium">Master Umum</span>
                                <svg class="w-3 h-3 transition-transform duration-200 dropdown-arrow {{ $isUmumRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div id="master-umum-content" class="dropdown-content ml-4 mt-2 space-y-1" @if($isUmumRoute) style="display: block;" @endif>
                                @if($user && $user->can('mesin-view'))
                                    <a href="{{ route('master.mesin.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('master.mesin.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Kelola Mesin</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-cabang-view'))
                                    <a href="{{ route('master.cabang.index') }}" target="_blank" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('master.cabang.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Master Cabang</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-kode-nomor-view'))
                                    <a href="{{ route('master.kode-nomor.index') }}" target="_blank" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('master.kode-nomor.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Kode Penomoran</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-nomor-terakhir-view'))
                                    <a href="{{ route('master.nomor-terakhir.index') }}" target="_blank" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('master.nomor-terakhir.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Nomor Terakhir</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-kegiatan-view'))
                                    <a href="{{ route('master.kegiatan.index') }}" target="_blank" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('master.kegiatan.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Jenis Kegiatan</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-pelabuhan-view'))
                                    <a href="{{ route('master-pelabuhan.index') }}" target="_blank" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('master-pelabuhan.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Pelabuhan</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-dokumen-perijinan-kapal-view'))
                                    <a href="{{ route('master-dokumen-perijinan-kapal.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('master-dokumen-perijinan-kapal.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Dokumen Perijinan Kapal</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-sertifikat-kapal-view'))
                                    <a href="{{ route('master-sertifikat-kapal.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('master-sertifikat-kapal.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Master Sertifikat Kapal</span>
                                    </a>
                                @endif

                                @if($user && $user->can('master-klasifikasi-biaya-view'))
                                    <a href="{{ route('klasifikasi-biaya.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('klasifikasi-biaya.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Master Klasifikasi Biaya</span>
                                    </a>
                                @endif

                                @if($user && $user->can('master-lwbp-lama-view'))
                                    <a href="{{ route('master.lwbp-lama.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('master.lwbp-lama.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Master LWBP Lama</span>
                                    </a>
                                @endif
                                @if($isAdmin || ($user && $user->can('master-item-kwitansi-view')))
                                    <a href="{{ route('master.item-kwitansi.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('master.item-kwitansi.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs font-semibold">Item Barang Kwitansi</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-shipper-consignee-view'))
                                    <a href="{{ route('master.shipper-consignee.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('master.shipper-consignee.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs font-semibold">Shipper / Consignee</span>
                                    </a>
                                @endif

                                @if($user && $user->can('master-customer-buruh-view'))
                                    <a href="{{ route('master-customer-buruh.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('master-customer-buruh.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs font-semibold">Customer Buruh</span>
                                    </a>
                                @endif
                                @if($isAdmin || ($user && $user->can('master-wa-templates-view')))
                                    <a href="{{ route('master.wa-templates.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('master.wa-templates.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs font-semibold">Master Template WA</span>
                                    </a>
                                @endif
                                
                                @if($isAdmin || ($user && $user->can('master-wa-broadcast-view')))
                                    <a href="{{ route('master.wa-broadcast.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('master.wa-broadcast.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs font-semibold">Broadcast WA</span>
                                    </a>
                                    <a href="{{ route('master.wa-gateway.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-green-50 hover:text-green-700 transition-all duration-200 {{ Request::routeIs('master.wa-gateway.*') ? 'bg-green-50 text-green-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs font-semibold">Koneksi WA Gateway</span>
                                    </a>
                                @endif
                                </div>
                            </div>
                            @endif

                            {{-- Tipe Akun Sub-Dropdown --}}
                            @php
                                $isAkunRoute = Request::routeIs('master.tipe-akun.*') || Request::routeIs('master-coa-*');
                                $hasAkunPermissions = $user && ($user->can('master-tipe-akun-view') || $user->can('master-coa-view'));
                            @endphp

                        @if($hasAkunPermissions)
                        <div class="mx-2 mb-3">
                            <button id="master-akun-toggle" class="w-full flex justify-between items-center py-2 px-3 rounded-lg text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 group {{ $isAkunRoute ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                                <span class="text-xs font-medium">Tipe Akun</span>
                                <svg class="w-3 h-3 transition-transform duration-200 dropdown-arrow {{ $isAkunRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div id="master-akun-content" class="dropdown-content ml-4 mt-2 space-y-1" @if($isAkunRoute) style="display: block;" @endif>
                                @if($user && $user->can('master-coa-view'))
                                    <a href="{{ route('master-coa-index') }}" target="_blank" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('master-coa-*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">COA</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-tipe-akun-view'))
                                    <a href="{{ route('master.tipe-akun.index') }}" target="_blank" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 {{ Request::routeIs('master.tipe-akun.*') ? 'bg-purple-50 text-purple-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Tipe Akun</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                        @endif

                            {{-- Master Penjualan Sub-Dropdown --}}
                            @php
                                $isPenjualanRoute = Request::routeIs('pengirim.*') || Request::routeIs('penerima.*') || Request::routeIs('master-pengirim-penerima.*') || Request::routeIs('jenis-barang.*') || Request::routeIs('term.*') || Request::routeIs('master.tujuan-kegiatan-utama.*') || Request::routeIs('tujuan-kirim.*') || Request::routeIs('master.tujuan.*') || Request::routeIs('master.nama-barang-amprahan.*');
                                $hasPenjualanPermissions = $user && ($isAdmin || $user->can('master-pengirim-view') || $user->can('master-penerima-view') || $user->can('master-pengirim-penerima-view') || $user->can('master-jenis-barang-view') || $user->can('master-term-view') || $user->can('master-tujuan-kirim-view') || $user->can('master-tujuan-view'));
                            @endphp

                        @if($hasPenjualanPermissions)
                        <div class="mx-2 mb-3">
                            <button id="master-penjualan-toggle" class="w-full flex justify-between items-center py-2 px-3 rounded-lg text-xs hover:bg-orange-50 hover:text-orange-700 transition-all duration-200 group {{ $isPenjualanRoute ? 'bg-orange-50 text-orange-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                                <span class="text-xs font-medium">Master Penjualan</span>
                                <svg class="w-3 h-3 transition-transform duration-200 dropdown-arrow {{ $isPenjualanRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div id="master-penjualan-content" class="dropdown-content ml-4 mt-2 space-y-1" @if($isPenjualanRoute) style="display: block;" @endif>
                                @if($user && $user->can('master-pengirim-view'))
                                    <a href="{{ route('pengirim.index') }}" target="_blank" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-orange-50 hover:text-orange-700 transition-all duration-200 {{ Request::routeIs('pengirim.*') ? 'bg-orange-50 text-orange-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Master Pengirim</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-penerima-view'))
                                    <a href="{{ route('penerima.index') }}" target="_blank" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-orange-50 hover:text-orange-700 transition-all duration-200 {{ Request::routeIs('penerima.*') ? 'bg-orange-50 text-orange-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Master Penerima</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-pengirim-penerima-view'))
                                    <a href="{{ route('master-pengirim-penerima.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-orange-50 hover:text-orange-700 transition-all duration-200 {{ Request::routeIs('master-pengirim-penerima.*') ? 'bg-orange-50 text-orange-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Master Pengirim/Penerima</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-jenis-barang-view'))
                                    <a href="{{ route('jenis-barang.index') }}" target="_blank" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-orange-50 hover:text-orange-700 transition-all duration-200 {{ Request::routeIs('jenis-barang.*') ? 'bg-orange-50 text-orange-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Jenis Barang</span>
                                    </a>
                                @endif
                                    <a href="{{ route('master.nama-barang-amprahan.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-orange-50 hover:text-orange-700 transition-all duration-200 {{ Request::routeIs('master.nama-barang-amprahan.*') ? 'bg-orange-50 text-orange-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Nama Barang Amprahan</span>
                                    </a>
                                @if($user && $user->can('master-term-view'))
                                    <a href="{{ route('term.index') }}" target="_blank" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-orange-50 hover:text-orange-700 transition-all duration-200 {{ Request::routeIs('term.*') ? 'bg-orange-50 text-orange-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Terms</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-tujuan-kirim-view'))
                                    <a href="{{ route('master.tujuan-kegiatan-utama.index') }}" target="_blank" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-orange-50 hover:text-orange-700 transition-all duration-200 {{ Request::routeIs('master.tujuan-kegiatan-utama.*') ? 'bg-orange-50 text-orange-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Master Transportasi</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-tujuan-kirim-view'))
                                    <a href="{{ route('tujuan-kirim.index') }}" target="_blank" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-orange-50 hover:text-orange-700 transition-all duration-200 {{ Request::routeIs('tujuan-kirim.*') ? 'bg-orange-50 text-orange-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Master Tujuan Kirim</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-tujuan-view'))
                                    <a href="{{ route('master.tujuan.index') }}" target="_blank" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-orange-50 hover:text-orange-700 transition-all duration-200 {{ Request::routeIs('master.tujuan.*') ? 'bg-orange-50 text-orange-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Master Tujuan</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                        @endif

                        {{-- Master Karyawan Sub-Dropdown --}}
                            @php
                                $isUserRoute = Request::routeIs('master.user.*') || Request::routeIs('master.karyawan.*') || Request::routeIs('master.jam-kerja.*') || Request::routeIs('karyawan-tidak-tetap.*') || Request::routeIs('master-group-bp-jamsostek.*') || Request::routeIs('master.divisi.*') || Request::routeIs('master.pekerjaan.*') || Request::routeIs('master.pajak.*') || Request::routeIs('master.tunjangan.*') || Request::routeIs('admin.user-approval.*') || Request::routeIs('master-bank-*') || Request::routeIs('master.permission.*') || Request::routeIs('master.karyawan.approval.*') || Request::routeIs('master.pelamar-karyawan.*') || Request::routeIs('master-rumus-bpjs.*');
                                $hasUserPermissions = $user && ($user->can('master-user-view') || $user->can('master-karyawan-view') || $user->can('master-jam-kerja-view') || $user->can('karyawan-tidak-tetap-view') || $user->can('master-divisi-view') || $user->can('master-pekerjaan-view') || $user->can('master-pajak-view') || $user->can('master-tunjangan') || $user->can('master-bank-view') || $user->can('master-permission-view'));
                                $hasUserApprovalAccess = $isAdmin ||
                                    auth()->user()->can('master-user') ||
                                    auth()->user()->can('user-approval') ||
                                    auth()->user()->can('user-approval.view') ||
                                    auth()->user()->can('user-approval.create') ||
                                    auth()->user()->can('user-approval.update') ||
                                    auth()->user()->can('user-approval.delete') ||
                                    auth()->user()->can('user-approval.approve') ||
                                    auth()->user()->can('user-approval.print') ||
                                    auth()->user()->can('user-approval.export');
                                $showUserSection = $hasUserPermissions || $hasUserApprovalAccess;
                            @endphp

                        @if($showUserSection)
                        <div class="mx-2 mb-3">
                            <button id="master-karyawan-toggle" class="w-full flex justify-between items-center py-2 px-3 rounded-lg text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 group {{ $isUserRoute ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                                <span class="text-xs font-medium">Master Karyawan</span>
                                <svg class="w-3 h-3 transition-transform duration-200 dropdown-arrow {{ $isUserRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div id="master-karyawan-content" class="dropdown-content ml-4 mt-2 space-y-1" @if($isUserRoute) style="display: block;" @endif>
                                @if($user && $user->can('master-jam-kerja-view'))
                                    <a href="{{ route('master.jam-kerja.index') }}" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ Request::routeIs('master.jam-kerja.*') ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Jam Kerja (Shift)</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-user-view'))
                                    <a href="{{ route('master.user.index') }}" target="_blank" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ Request::routeIs('master.user.index') || Request::routeIs('master.user.create') || Request::routeIs('master.user.edit') ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Master User</span>
                                    </a>
                                @endif
                                @if($user && (strtolower($user->username ?? '') === 'kiky' || strtolower($user->name ?? '') === 'kiky'))
                                    <a href="{{ route('master.user.online') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ Request::routeIs('master.user.online') ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">User Online</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-karyawan-view'))
                                    <a href="{{ route('master.karyawan.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ Request::routeIs('master.karyawan.index') ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Data Karyawan</span>
                                    </a>
                                    <a href="{{ route('master.pelamar-karyawan.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ Request::routeIs('master.pelamar-karyawan.*') ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-600 font-bold' }}">
                                        <span class="text-xs">Data Pelamar</span>
                                    </a>
                                    @if($user && $user->can('master-karyawan-abk-view'))
                                        <a href="{{ route('master.karyawan.abk-index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ Request::routeIs('master.karyawan.abk-index') ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                            <span class="text-xs text-blue-600 font-bold">Data Karyawan (ABK)</span>
                                        </a>
                                    @endif
                                @endif
                                @if($user && $user->can('master-karyawan-approval'))
                                    @php
                                        $pendingApprovalCount = \App\Models\KaryawanApprovalRequest::where('status', 'pending')->count();
                                    @endphp
                                    <a href="{{ route('master.karyawan.approval.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-amber-50 hover:text-amber-700 transition-all duration-200 {{ Request::routeIs('master.karyawan.approval.*') ? 'bg-amber-50 text-amber-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs font-bold uppercase italic text-amber-600">Persetujuan Edit Karyawan</span>
                                        @if($pendingApprovalCount > 0)
                                            <span class="ml-auto bg-amber-500 text-white text-[10px] px-1.5 py-0.5 rounded-full font-bold ml-1">{{ $pendingApprovalCount }}</span>
                                        @endif
                                    </a>
                                @endif
                                @if($user && $user->can('karyawan-tidak-tetap-view'))
                                    <!-- Karyawan Tidak Tetap -->
                                    <a href="{{ route('karyawan-tidak-tetap.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ Request::routeIs('karyawan-tidak-tetap.*') ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Karyawan Tidak Tetap</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-karyawan-view'))
                                    <!-- Master Group BP Jamsostek -->
                                    <a href="{{ route('master-group-bp-jamsostek.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ Request::routeIs('master-group-bp-jamsostek.*') ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Master Group BP Jamsostek</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-divisi-view'))
                                    <a href="{{ route('master.divisi.index') }}" target="_blank" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ Request::routeIs('master.divisi.*') ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Master Divisi</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-pekerjaan-view'))
                                    <a href="{{ route('master.pekerjaan.index') }}" target="_blank" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ Request::routeIs('master.pekerjaan.*') ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Master Pekerjaan</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-pajak-view'))
                                    <a href="{{ route('master.pajak.index') }}" target="_blank" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ Request::routeIs('master.pajak.*') ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Master Pajak</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-tunjangan'))
                                    <a href="{{ route('master.tunjangan.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ Request::routeIs('master.tunjangan.*') ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Master Tunjangan</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-karyawan-view'))
                                    <a href="{{ route('master-rumus-bpjs.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ Request::routeIs('master-rumus-bpjs.*') ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Master Rumus BPJS</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-bank-view'))
                                    <a href="{{ route('master-bank-index') }}" target="_blank" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ Request::routeIs('master-bank-*') ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Master Bank</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-permission-view'))
                                    <a href="{{ route('master.permission.index') }}" target="_blank" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ Request::routeIs('master.permission.*') ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Izin (buat Programmer)</span>
                                    </a>
                                @endif

                                @if($hasUserApprovalAccess)
                                    @php
                                        $pendingUsersCount = \App\Models\User::where('status', 'pending')->count();
                                    @endphp
                                    <a href="{{ route('admin.user-approval.index') }}" target="_blank" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ Request::routeIs('admin.user-approval.*') ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Persetujuan User</span>
                                        @if($pendingUsersCount > 0)
                                            <span class="ml-auto bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full">{{ $pendingUsersCount }}</span>
                                        @endif
                                    </a>
                                @endif

                                @can('master-buruh-view')
                                <a href="{{ route('master.buruh.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 {{ Request::routeIs('master.buruh.*') ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-600 font-bold text-indigo-600' }}">
                                    <span class="text-xs font-bold text-indigo-600 italic">Data Buruh</span>
                                </a>
                                @endcan


                            </div>
                        </div>
                        @endif
                        {{-- Master Tarif --}}
                        @php
                            $tarifItems = collect(config('master_tarif'));
                            $hasTarifPermissions = $user && $tarifItems->contains(fn ($item) =>
                                $item['permission'] && ($user->can($item['permission']) || (!empty($item['admin']) && $isAdmin))
                            );
                            $tarifItems = $hasTarifPermissions ? $tarifItems->filter(fn ($item) =>
                                !$item['permission'] || $user->can($item['permission']) || (!empty($item['admin']) && $isAdmin)
                            ) : collect();
                        @endphp
                        @if($hasTarifPermissions)
                            <div class="mx-2 mb-3">
                                <button id="master-tarif-toggle" class="w-full flex justify-between items-center py-2 px-3 rounded-lg text-xs hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 group text-gray-600 hover:shadow-sm">
                                    <span class="text-xs font-medium">Master Tarif</span>
                                    <svg class="w-3 h-3 transition-transform duration-200 dropdown-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div id="master-tarif-content" class="dropdown-content ml-4 mt-2 space-y-1">
                                    @foreach($tarifItems as $item)
                                        <a href="{{ route($item['route']) }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs text-gray-600 hover:bg-purple-50 hover:text-purple-700 transition-all duration-200">
                                            <span class="text-xs">{{ $item['label'] }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Master Aktiva --}}
                        @php
                            $hasMasterAktivaPermissions = $user && (
                                $user->can('master-mobil-view') ||
                                $user->can('master-kapal.view') ||
                                $user->can('master-alat-berat-view') ||
                                $user->can('asset-view') ||
                                $user->can('master-asset-view') ||
                                $user->can('master-stock-kontainer-view') ||
                                $user->can('master-kontainer-view') ||
                                $user->can('master-chasis-batam-view') ||
                                $user->can('master-gudang-view') ||
                                $user->can('stock-ban-view') ||
                                $user->can('master-gudang-ban-view') ||
                                $user->can('master-gudang-amprahan-view') ||
                                $user->can('master-type-bon-amprahan-view') ||
                                $user->can('master-kartu-bensin-batam-view') ||
                                $isAdmin
                            );
                        @endphp
                        @if($hasMasterAktivaPermissions)
                        <div class="mx-2 mb-3">
                            <button id="master-aktiva-toggle" class="w-full flex justify-between items-center py-2 px-3 rounded-lg text-xs text-gray-600 hover:bg-green-50 hover:text-green-700 hover:shadow-sm transition-all duration-200 group">
                                <span class="text-xs font-medium">Master Aktiva</span>
                                <svg class="w-3 h-3 transition-transform duration-200 dropdown-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div id="master-aktiva-content" class="dropdown-content ml-4 mt-2 space-y-1">
                                @if($user && $user->can('master-mobil-view'))
                                    <a href="{{ route('master.mobil.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs text-gray-600 hover:bg-green-50 hover:text-green-700 transition-all duration-200"><span class="text-xs">Master Kendaraan</span></a>
                                @endif
                                @if($user && $user->can('master-kapal.view'))
                                    <a href="{{ route('master-kapal.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs text-gray-600 hover:bg-green-50 hover:text-green-700 transition-all duration-200"><span class="text-xs">Master Kapal</span></a>
                                @endif
                                @if($user && $user->can('master-alat-berat-view'))
                                    <a href="{{ route('master.alat-berat.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs text-gray-600 hover:bg-green-50 hover:text-green-700 transition-all duration-200"><span class="text-xs">Master Alat Berat</span></a>
                                @endif
                                @if($isAdmin || ($user && ($user->can('asset-view') || $user->can('master-asset-view'))))
                                    <a href="{{ route('asset.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs text-gray-600 hover:bg-green-50 hover:text-green-700 transition-all duration-200"><span class="text-xs">Master Asset</span></a>
                                @endif
                                @if($user && $user->can('master-stock-kontainer-view'))
                                    <a href="{{ route('master.stock-kontainer.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs text-gray-600 hover:bg-green-50 hover:text-green-700 transition-all duration-200"><span class="text-xs">Master Kontainer (Stok Kontainer)</span></a>
                                @endif
                                @if($user && $user->can('master-kontainer-view'))
                                    <a href="{{ route('master.kontainer.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs text-gray-600 hover:bg-green-50 hover:text-green-700 transition-all duration-200"><span class="text-xs">Master Kontainer Sewa</span></a>
                                @endif
                                @if($user && $user->can('master-chasis-batam-view'))
                                    <a href="{{ route('master.chasis-batam.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs text-gray-600 hover:bg-green-50 hover:text-green-700 transition-all duration-200"><span class="text-xs">Master Chasis Batam</span></a>
                                @endif
                                @if($user && $user->can('master-gudang-view'))
                                    <a href="{{ route('master-gudang.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs text-gray-600 hover:bg-green-50 hover:text-green-700 transition-all duration-200"><span class="text-xs">Master Gudang</span></a>
                                @endif
                                @if($isAdmin || ($user && ($user->can('stock-ban-view') || $user->can('master-gudang-ban-view'))))
                                    <a href="{{ route('master-gudang-ban.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs text-gray-600 hover:bg-green-50 hover:text-green-700 transition-all duration-200"><span class="text-xs">Master Gudang Ban</span></a>
                                @endif
                                @if($user && $user->can('master-gudang-amprahan-view'))
                                    <a href="{{ route('master.gudang-amprahan.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs text-gray-600 hover:bg-green-50 hover:text-green-700 transition-all duration-200"><span class="text-xs">Master Gudang Amprahan</span></a>
                                @endif
                                @if($user && $user->can('master-type-bon-amprahan-view'))
                                    <a href="{{ route('master.type-bon-amprahan.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs text-gray-600 hover:bg-green-50 hover:text-green-700 transition-all duration-200"><span class="text-xs">Master Type Bon Amprahan</span></a>
                                @endif
                                @if($isAdmin || ($user && $user->can('stock-ban-view')))
                                    <a href="{{ route('master.merk-ban.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs text-gray-600 hover:bg-green-50 hover:text-green-700 transition-all duration-200"><span class="text-xs">Master Merk Ban</span></a>
                                    <a href="{{ route('master.tipe-stock-ban.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs text-gray-600 hover:bg-green-50 hover:text-green-700 transition-all duration-200"><span class="text-xs">Master Tipe Stock Ban</span></a>
                                @endif
                                @if($user && ($user->can('master-kartu-bensin-batam-view') || $isAdmin))
                                    <a href="{{ route('master-kartu-bensin-batam.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs text-gray-600 hover:bg-green-50 hover:text-green-700 transition-all duration-200"><span class="text-xs">Master Kartu Bensin Batam</span></a>
                                @endif
                            </div>
                        </div>
                        @endif

                        {{-- Master Pemasok Sub-Dropdown --}}
                        @php
                            $isPemasokRoute = Request::routeIs('master.vendor-bengkel.*') || Request::routeIs('vendor-kontainer-sewa.*') || Request::routeIs('master.vendor-supir.*') || Request::routeIs('master.vendor-asuransi.*') || Request::routeIs('master.vendor-amprahan.*');
                            $hasPemasokPermissions = $user && ($user->can('master-vendor-bengkel.view') || $user->can('vendor-kontainer-sewa-view') || $user->can('master-vendor-supir-view') || $user->can('master-vendor-asuransi-view') || $user->can('master-vendor-amprahan-view'));
                        @endphp

                        @if($hasPemasokPermissions)
                        <div class="mx-2 mb-3">
                            <button id="master-pemasok-toggle" class="w-full flex justify-between items-center py-2 px-3 rounded-lg text-xs hover:bg-teal-50 hover:text-teal-700 transition-all duration-200 group {{ $isPemasokRoute ? 'bg-teal-50 text-teal-700 font-medium shadow-sm' : 'text-gray-600 hover:shadow-sm' }}">
                                <span class="text-xs font-medium">Master Pemasok</span>
                                <svg class="w-3 h-3 transition-transform duration-200 dropdown-arrow {{ $isPemasokRoute ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div id="master-pemasok-content" class="dropdown-content ml-4 mt-2 space-y-1" @if($isPemasokRoute) style="display: block;" @endif>
                                @if($user && $user->can('master-vendor-amprahan-view'))
                                    <a href="{{ route('master.vendor-amprahan.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-teal-50 hover:text-teal-700 transition-all duration-200 {{ Request::routeIs('master.vendor-amprahan.*') ? 'bg-teal-50 text-teal-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Vendor Amprahan</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-vendor-bengkel.view'))
                                    <a href="{{ route('master.vendor-bengkel.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-teal-50 hover:text-teal-700 transition-all duration-200 {{ Request::routeIs('master.vendor-bengkel.*') ? 'bg-teal-50 text-teal-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Vendor/Bengkel</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-vendor-asuransi-view'))
                                    <a href="{{ route('master.vendor-asuransi.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-teal-50 hover:text-teal-700 transition-all duration-200 {{ Request::routeIs('master.vendor-asuransi.*') ? 'bg-teal-50 text-teal-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Vendor Asuransi</span>
                                    </a>
                                @endif
                                @if($user && $user->can('vendor-kontainer-sewa-view'))
                                    <a href="{{ route('vendor-kontainer-sewa.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-teal-50 hover:text-teal-700 transition-all duration-200 {{ Request::routeIs('vendor-kontainer-sewa.*') ? 'bg-teal-50 text-teal-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                        <span class="text-xs">Vendor Kontainer Sewa</span>
                                    </a>
                                @endif
                                @if($user && $user->can('master-vendor-supir-view'))
                                <a href="{{ route('master.vendor-supir.index') }}" target="_blank" class="flex items-center py-1.5 px-3 mx-1 rounded-md text-xs hover:bg-teal-50 hover:text-teal-700 transition-all duration-200 {{ Request::routeIs('master.vendor-supir.*') ? 'bg-teal-50 text-teal-700 font-medium shadow-sm' : 'text-gray-600' }}">
                                    <span class="text-xs">Vendor Supir</span>
                                </a>
                                @endif

                            </div>
                        </div>
                        @endif
