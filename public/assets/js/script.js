$(function () {
    const BASE_URL = window.location.origin + '/';

    // ============ Sidebar toggle (admin) ============
    $('#sidebarToggle').on('click', function () {
        $('.admin-sidebar').toggleClass('show');
    });

    // ============ Refresh cart count badge ============
    function refreshCartCount() {
        if ($('#cartCount').length === 0) return;
        $.get(BASE_URL + 'cart/count', function (res) {
            $('#cartCount').text(res.count);
        });
    }
    refreshCartCount();

    // ============ Add to cart (AJAX) ============
    $(document).on('submit', '.form-add-cart', function (e) {
        e.preventDefault();
        const $form = $(this);
        const $btn = $form.find('button[type=submit]');
        const originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Memproses...');

        $.ajax({
            url: BASE_URL + 'cart/add',
            method: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            success: function (res) {
                $btn.prop('disabled', false).html(originalHtml);
                if (res.success) {
                    $('#cartCount').text(res.cart_count);
                    showToast(res.message, 'success');
                } else {
                    showToast(res.message, 'danger');
                }
            },
            error: function () {
                $btn.prop('disabled', false).html(originalHtml);
                showToast('Terjadi kesalahan, silakan coba lagi.', 'danger');
            }
        });
    });

    // ============ Update qty cart (AJAX) ============
    $(document).on('change', '.cart-qty-input', function () {
        const $input = $(this);
        const id = $input.data('id');
        const qty = $input.val();
        const $row = $input.closest('tr');

        $.post(BASE_URL + 'cart/update', { id: id, qty: qty }, function (res) {
            if (res.success) {
                $row.find('.subtotal-cell').text(res.formatted_subtotal);
                recalcCartTotal();
            }
        }, 'json');
    });

    function recalcCartTotal() {
        let total = 0;
        $('.subtotal-cell').each(function () {
            const val = $(this).text().replace(/[^0-9]/g, '');
            total += parseInt(val || 0);
        });
        $('#cartGrandTotal').text('Rp ' + total.toLocaleString('id-ID'));
    }

    // ============ Live search katalog (AJAX) ============
    let searchTimer;
    $(document).on('keyup', '#searchKatalog', function () {
        clearTimeout(searchTimer);
        const keyword = $(this).val();
        searchTimer = setTimeout(function () {
            loadCatalog();
        }, 400);
    });

    $(document).on('change', '#filterCategory, #filterSort', function () {
        loadCatalog();
    });

    function loadCatalog() {
        const keyword = $('#searchKatalog').val() || '';
        const category = $('#filterCategory').val() || '';
        const sort = $('#filterSort').val() || '';

        $('#catalogGrid').css('opacity', 0.5);

        $.get(BASE_URL + 'katalog', { q: keyword, category: category, sort: sort }, function (res) {
            $('#catalogGrid').html(res).css('opacity', 1);
        });
    }

    // ============ Hitung total sewa otomatis (checkout) ============
    function calcRentalDays() {
        const start = $('#start_date').val();
        const end = $('#end_date').val();
        if (!start || !end) return;

        const d1 = new Date(start);
        const d2 = new Date(end);
        const diffDays = Math.round((d2 - d1) / (1000 * 60 * 60 * 24)) + 1;

        if (diffDays < 1) {
            $('#totalDaysInfo').text('Tanggal selesai harus setelah tanggal mulai.').addClass('text-danger');
            $('#checkoutBtn').prop('disabled', true);
            return;
        }

        $('#totalDaysInfo').text(diffDays + ' hari sewa').removeClass('text-danger');
        $('#checkoutBtn').prop('disabled', false);

        let grandTotal = 0;
        $('.cart-item-row').each(function () {
            const price = parseFloat($(this).data('price'));
            const qty = parseInt($(this).find('.cart-qty-input').val());
            grandTotal += price * qty * diffDays;
        });

        $('#estimatedTotal').text('Rp ' + grandTotal.toLocaleString('id-ID'));
    }

    $(document).on('change', '#start_date, #end_date, .cart-qty-input', calcRentalDays);
    calcRentalDays();

    // ============ Preview gambar upload ============
    $(document).on('change', '.input-image-preview', function () {
        const target = $(this).data('preview');
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                $(target).attr('src', e.target.result).removeClass('d-none');
            };
            reader.readAsDataURL(file);
        }
    });

    // ============ Confirm delete ============
    $(document).on('click', '.btn-confirm-delete', function (e) {
        if (!confirm($(this).data('message') || 'Yakin ingin menghapus data ini?')) {
            e.preventDefault();
        }
    });

    // ============ Simple toast ============
    function showToast(message, type) {
        const id = 'toast-' + Date.now();
        const html = `
            <div id="${id}" class="toast align-items-center text-bg-${type} border-0 position-fixed bottom-0 end-0 m-4" role="alert" style="z-index:9999;">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>`;
        $('body').append(html);
        const toastEl = new bootstrap.Toast(document.getElementById(id), { delay: 3000 });
        toastEl.show();
        setTimeout(() => $('#' + id).remove(), 3500);
    }

    // ============ DataTable-like search sederhana (Admin) ============
    $(document).on('keyup', '.table-search', function () {
        const value = $(this).val().toLowerCase();
        const target = $(this).data('target');
        $(target + ' tbody tr').filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
});
