/**
 * admin-custom.js
 * Shared utility functions for all admin pages.
 * Prefixed with tmc- to avoid conflicts with theme's script.js.
 */

/**
 * Show a SweetAlert2 toast notification.
 * @param {'success'|'error'|'warning'|'info'} type
 * @param {string} message
 * @param {number} [timer=8000]
 */
function tmcToast(type, message, timer) {
    timer = timer || 8000;
    Swal.fire({
        toast: true,
        position: 'bottom-end',
        icon: type,
        title: message,
        showConfirmButton: false,
        timer: timer,
        timerProgressBar: true
    });
}

/**
 * Auto-hide an element after a delay.
 * @param {string} selector - jQuery selector
 * @param {number} [ms=5000]
 */
function tmcAutoHide(selector, ms) {
    ms = ms || 5000;
    setTimeout(function () {
        $(selector).fadeOut('slow');
    }, ms);
}

/**
 * Populate an edit modal from a button's data attributes.
 * @param {jQuery} $btn - The clicked button element
 * @param {Object} fields - Map of { 'dataAttr': '#inputSelector' }
 */
function tmcEditModalPopulate($btn, fields) {
    $.each(fields, function (dataAttr, selector) {
        var val = $btn.attr('data-' + dataAttr) || '';
        $(selector).val(val);
    });
}

/**
 * Collect checked delete checkboxes into a hidden input and show a delete modal.
 * @param {string} checkboxSelector - CSS selector for checkboxes (default: '.delete-checkbox:checked')
 * @param {string} hiddenInputSelector - Selector for the hidden input to store IDs
 * @param {string} modalSelector - Selector for the Bootstrap modal element
 */
function tmcDeleteSelect(checkboxSelector, hiddenInputSelector, modalSelector) {
    checkboxSelector = checkboxSelector || '.delete-checkbox:checked';
    var ids = [];
    $(checkboxSelector).each(function () {
        ids.push($(this).val());
    });
    if (ids.length === 0) {
        Swal.fire({
            toast: true,
            position: 'bottom-end',
            icon: 'warning',
            title: 'Please select at least one item',
            showConfirmButton: false,
            timer: 4000
        });
        return;
    }
    $(hiddenInputSelector).val(ids.join(','));
    var modal = new bootstrap.Modal(document.querySelector(modalSelector));
    modal.show();
}

/**
 * Image file preview using FileReader.
 * @param {Event} event - The input change event
 * @param {string} previewId - ID of the <img> element to preview into
 */
function tmcPreviewImage(event, previewId) {
    var reader = new FileReader();
    reader.onload = function () {
        var preview = document.getElementById(previewId);
        if (preview) {
            preview.src = reader.result;
            preview.style.display = 'block';
        }
    };
    reader.readAsDataURL(event.target.files[0]);
}

/**
 * Password visibility toggle.
 * @param {string} inputId - ID of the password input
 * @param {string} btnId - ID of the toggle button/icon
 */
function tmcPasswordToggle(inputId, btnId) {
    var input = document.getElementById(inputId);
    var btn = document.getElementById(btnId);
    if (input.type === 'password') {
        input.type = 'text';
        btn.classList.remove('ti-eye');
        btn.classList.add('ti-eye-off');
    } else {
        input.type = 'password';
        btn.classList.remove('ti-eye-off');
        btn.classList.add('ti-eye');
    }
}

/**
 * Initialize Summernote editors and wire form submit to copy content to hidden inputs.
 * @param {Array} editors - Array of { editor: '#summernoteId', hidden: '#hiddenInputId', height: 200 }
 */
function tmcInitSummernote(editors) {
    $(document).ready(function () {
        $.each(editors, function (i, cfg) {
            var height = cfg.height || 200;
            $(cfg.editor).summernote({
                height: height,
                tabsize: 2,
                callbacks: {
                    onInit: function () {
                        var existing = $(cfg.hidden).val();
                        if (existing) {
                            $(cfg.editor).summernote('code', existing);
                        }
                    }
                }
            });
        });

        $('form').on('submit', function () {
            $.each(editors, function (i, cfg) {
                var code = $(cfg.editor).summernote('code');
                $(cfg.hidden).val(code);
            });
        });
    });
}

/**
 * Generate a URL-friendly slug from a string.
 * @param {string} str
 * @returns {string}
 */
function tmcSlugify(str) {
    return str
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/[\s-]+/g, '-')
        .replace(/^-+|-+$/g, '');
}

/**
 * Initialize select-all checkbox behavior for DataTable rows.
 * @param {string} [selectAllSelector='#select-all']
 * @param {string} [checkboxSelector='.delete-checkbox']
 */
function tmcInitSelectAll(selectAllSelector, checkboxSelector) {
    selectAllSelector = selectAllSelector || '#select-all';
    checkboxSelector = checkboxSelector || '.delete-checkbox';
    $(document).on('change', selectAllSelector, function () {
        var checked = $(this).prop('checked');
        $(checkboxSelector).prop('checked', checked);
    });
    $(document).on('change', checkboxSelector, function () {
        var total = $(checkboxSelector).length;
        var checked = $(checkboxSelector + ':checked').length;
        $(selectAllSelector).prop('checked', total === checked);
    });
}
