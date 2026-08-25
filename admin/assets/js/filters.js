/**
 * Reusable DataTable Filters & Sort
 * Works with filter-bar.php include.
 * Reads data attributes for column mapping - no hardcoding per page.
 */
$(document).ready(function () {

    // ── Column index mapping (read from <table> data attributes) ──
    // Each <table class="datatable"> should have: data-name-col, data-email-col, etc.
    function getColIndex(table, key) {
        return parseInt(table.attr('data-' + key + '-col'), 10) || -1;
    }

    // ── APPLY FILTERS ──
    $(document).on('click', '.filter-apply', function () {
        var wrapper = $(this).closest('.filter-dropdown');
        var pageType = $(this).data('page-type');
        var table = $('table.datatable');

        // Reset all previous column searches
        var dt = table.DataTable();
        dt.columns().search('');

        // Text filters
        wrapper.find('.filter-input').each(function () {
            var val = $(this).val().trim();
            var colKey = $(this).data('filter-column');
            var colIdx = getColIndex(table, colKey);
            if (colIdx >= 0 && val !== '') {
                dt.column(colIdx).search(val);
            }
        });

        // Date range filter
        var dateFrom = wrapper.find('.filter-date-from').val();
        var dateTo   = wrapper.find('.filter-date-to').val();
        var dateCol  = getColIndex(table, 'date');

        if (dateCol >= 0 && (dateFrom || dateTo)) {
            // Store date params on table for custom draw filter
            table.data('filter-date-from', dateFrom);
            table.data('filter-date-to', dateTo);
            table.data('filter-date-col', dateCol);
        } else {
            table.removeData('filter-date-from');
            table.removeData('filter-date-to');
            table.removeData('filter-date-col');
        }

        dt.draw();

        // Close dropdown
        wrapper.removeClass('show');
    });

    // ── RESET FILTERS ──
    $(document).on('click', '.filter-reset', function () {
        var wrapper = $(this).closest('.filter-dropdown');
        var table = $('table.datatable');
        var dt = table.DataTable();

        wrapper.find('.filter-input').val('');
        wrapper.find('.filter-date-from').val('');
        wrapper.find('.filter-date-to').val('');

        dt.columns().search('').draw();

        table.removeData('filter-date-from');
        table.removeData('filter-date-to');
        table.removeData('filter-date-col');
    });

    // ── DATE RANGE CUSTOM FILTER (via DataTables draw callback) ──
    $.fn.dataTable.ext.search.push(function (settings, data) {
        var table = $(settings.nTable);
        var dateFrom = table.data('filter-date-from');
        var dateTo   = table.data('filter-date-to');
        var dateCol  = parseInt(table.data('filter-date-col'), 10);

        if (!dateFrom && !dateTo) return true;
        if (isNaN(dateCol) || dateCol < 0) return true;

        // Parse the cell date value (format: YYYY-MM-DD HH:MM:SS or similar)
        var cellVal = data[dateCol] || '';
        // Extract just the date part for comparison
        var cellDate = cellVal.substring(0, 10);

        if (dateFrom && cellDate < dateFrom) return false;
        if (dateTo && cellDate > dateTo) return false;

        return true;
    });

    // ── SORT DROPDOWN ──
    $(document).on('click', '.sort-dropdown .dropdown-item', function (e) {
        e.preventDefault();
        var sortType = $(this).data('sort');
        var wrapper = $(this).closest('.sort-dropdown');
        var pageType = wrapper.data('page-type');
        var table = $('table.datatable');
        var dt = table.DataTable();

        // Update active state
        wrapper.find('.dropdown-item').removeClass('active');
        $(this).addClass('active');

        // Determine which column index is the "name" or primary display column
        var nameCol = getColIndex(table, 'name');
        if (nameCol < 0) nameCol = 1; // fallback

        var dateCol = getColIndex(table, 'date');
        if (dateCol < 0) dateCol = table.find('th').length - 2; // fallback to second-to-last

        switch (sortType) {
            case 'asc':
                dt.order([nameCol, 'asc']).draw();
                break;
            case 'desc':
                dt.order([nameCol, 'desc']).draw();
                break;
            case 'newest':
                dt.order([dateCol, 'desc']).draw();
                break;
            case 'oldest':
                dt.order([dateCol, 'asc']).draw();
                break;
        }

        // Update button text
        var btnText = $(this).text();
        $(this).closest('.dropdown').find('.dropdown-toggle').html(
            '<i class="ti ti-sort-ascending-2 me-2"></i>' + btnText
        );
    });

    // ── KEYBOARD: Enter key triggers filter apply ──
    $(document).on('keydown', '.filter-input', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            $(this).closest('.filter-dropdown').find('.filter-apply').trigger('click');
        }
    });

});
