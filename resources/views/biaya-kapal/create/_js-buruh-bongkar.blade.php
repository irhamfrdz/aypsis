// --- BIAYA BURUH BONGKAR SCRIPT ---

$(document).ready(function() {
    function initBuruhBongkarSelect2() {
        if (typeof jQuery.fn.select2 !== 'undefined') {
            $('#buruh_bongkar_pengirim').select2({
                placeholder: '-- Pilih Pengirim --',
                allowClear: true,
                ajax: {
                    url: "{{ route('biaya-kapal.search-pengirim') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term, // search term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        };
                    },
                    cache: true
                }
            });
        } else {
            setTimeout(initBuruhBongkarSelect2, 100);
        }
    }

    // Initialize Select2 for Pengirim
    initBuruhBongkarSelect2();

    // Handle Cari Data Button
    $('#btn_cari_buruh_bongkar').on('click', function() {
        const pengirim = $('#buruh_bongkar_pengirim').val();
        const startDate = $('#buruh_bongkar_start_date').val();
        const endDate = $('#buruh_bongkar_end_date').val();

        if (!pengirim || !startDate || !endDate) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Harap isi Nama Pengirim dan Range Tanggal terlebih dahulu.'
            });
            return;
        }

        // Show loading state
        const originalBtnText = $(this).html();
        $(this).html('<i class="fas fa-spinner fa-spin"></i> Mencari...').prop('disabled', true);

        $.ajax({
            url: "{{ route('biaya-kapal.get-manifest-buruh-bongkar') }}",
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content') || "{{ csrf_token() }}",
                pengirim: pengirim,
                start_date: startDate,
                end_date: endDate
            },
            success: function(response) {
                const tbody = $('#buruh_bongkar_table_body');
                tbody.empty();

                if (response.data && response.data.length > 0) {
                    response.data.forEach(function(item) {
                        let tipeBadge = '';
                        if (item.surat_jalan_tipe === 'batam') {
                            tipeBadge = ' <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">Batam</span>';
                        }
                        
                        const tr = `
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <input type="checkbox" name="buruh_bongkar_manifest_ids[]" value="${item.id}" class="buruh-bongkar-checkbox w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 cursor-pointer" checked>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    ${item.nomor_kontainer || '-'}
                                </td>
                                <td class="px-6 py-4">
                                    ${item.no_voyage || '-'}
                                </td>
                                <td class="px-6 py-4">
                                    ${item.surat_jalan} ${tipeBadge}
                                </td>
                                <td class="px-6 py-4">
                                    ${item.tanggal_berangkat}
                                </td>
                            </tr>
                        `;
                        tbody.append(tr);
                    });

                    $('#buruh_bongkar_results_container').removeClass('hidden');
                    updateSelectedCount();
                } else {
                    $('#buruh_bongkar_results_container').addClass('hidden');
                    Swal.fire({
                        icon: 'info',
                        title: 'Tidak Ditemukan',
                        text: 'Tidak ada data manifest untuk pengirim dan range tanggal tersebut.'
                    });
                }
            },
            error: function(xhr) {
                console.error(xhr);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat mengambil data.'
                });
            },
            complete: function() {
                // Restore button
                $('#btn_cari_buruh_bongkar').html(originalBtnText).prop('disabled', false);
            }
        });
    });

    // Select All Checkbox logic
    $('#buruh_bongkar_select_all').on('change', function() {
        const isChecked = $(this).prop('checked');
        $('.buruh-bongkar-checkbox').prop('checked', isChecked);
        updateSelectedCount();
    });

    // Individual Checkbox logic
    $(document).on('change', '.buruh-bongkar-checkbox', function() {
        const total = $('.buruh-bongkar-checkbox').length;
        const checked = $('.buruh-bongkar-checkbox:checked').length;
        
        $('#buruh_bongkar_select_all').prop('checked', total === checked && total > 0);
        updateSelectedCount();
    });

    function updateSelectedCount() {
        const checkedCount = $('.buruh-bongkar-checkbox:checked').length;
        $('#buruh_bongkar_selected_count').text(checkedCount);
    }
});
