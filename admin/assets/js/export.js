/**
 * Universal Export (PDF via pdfMake + Word) for admin DataTables
 * Same process as admin-contact.php - uses pdfMake for real PDF generation.
 *
 * Usage: Include pdfMake CDN + this script. Add id="export-pdf" and id="export-word" to links.
 * Auto-detects: page title, table headers, data rows.
 * Skips: checkbox, Sr. No./SN, image-only, and action columns.
 */
$(document).ready(function () {

    function getReportTitle() {
        var h = document.querySelector('.page-title');
        if (h) return h.textContent.trim();
        var h4 = document.querySelector('.card-header h4');
        if (h4) return h4.textContent.trim();
        return document.title.split(' - ')[0].trim();
    }

    function getFileName() {
        return getReportTitle().replace(/[^a-zA-Z0-9 ]/g, '').replace(/\s+/g, '_');
    }

    function isSkippableHeader(thText) {
        var t = thText.toLowerCase().trim();
        return t === '' || t === 'action' || t === 'actions' || t === 'sr. no.' || t === 'sn' || t === 'sr no' || t === 's.no' || t === 's. no.';
    }

    function getVisibleHeaders() {
        var headers = [];
        $('table.datatable thead th').each(function (i, th) {
            var txt = $(th).text().trim();
            if (!isSkippableHeader(txt)) {
                headers.push(txt);
            }
        });
        return headers;
    }

    function getVisibleColumnIndices() {
        var indices = [];
        $('table.datatable thead th').each(function (i, th) {
            var txt = $(th).text().trim();
            if (!isSkippableHeader(txt)) {
                indices.push(i);
            }
        });
        return indices;
    }

    function getTableData() {
        var colIndices = getVisibleColumnIndices();
        var rows = [];
        $('table.datatable tbody tr').each(function () {
            if (!$(this).is(':visible')) return;
            var cells = [];
            var tds = $(this).find('td');
            colIndices.forEach(function (idx) {
                var td = tds.eq(idx);
                if (td.length === 0) return;
                if (td.find('img').length > 0) {
                    cells.push('Image');
                } else if (td.find('.badge').length > 0) {
                    cells.push(td.find('.badge').first().text().trim());
                } else if (td.find('input, select, .form-check').length > 0) {
                    cells.push('');
                } else {
                    cells.push(td.text().trim());
                }
            });
            if (cells.length > 0) rows.push(cells);
        });
        return rows;
    }

    function buildPdfTableBody(headers, data) {
        var body = [];
        var headerRow = [{ text: 'Sr. No.', bold: true, color: '#ffffff' }];
        headers.forEach(function (h) {
            headerRow.push({ text: h, bold: true, color: '#ffffff' });
        });
        body.push(headerRow);

        data.forEach(function (row, i) {
            var r = [i + 1];
            row.forEach(function (cell) { r.push(cell || ''); });
            body.push(r);
        });
        return body;
    }

    function buildPdfWidths(colCount) {
        var widths = [40];
        for (var c = 1; c < colCount; c++) {
            widths.push('*');
        }
        return widths;
    }

    // ── Export as PDF (pdfMake - same as admin-contact.php) ──
    $(document).on('click', '#export-pdf', function (e) {
        e.preventDefault();
        var headers = getVisibleHeaders();
        var data = getTableData();
        if (data.length === 0) {
            Swal.fire({ toast: true, position: 'bottom-end', icon: 'warning', title: 'No data to export', showConfirmButton: false, timer: 3000 });
            return;
        }

        var title = getReportTitle();
        var tableBody = buildPdfTableBody(headers, data);
        var colCount = headers.length + 1;

        var docDefinition = {
            pageOrientation: 'landscape',
            pageMargins: [30, 30, 30, 30],
            content: [
                {
                    text: title + ' Report',
                    fontSize: 18,
                    bold: true,
                    margin: [0, 0, 0, 5]
                },
                {
                    text: 'Generated: ' + new Date().toLocaleString(),
                    fontSize: 10,
                    color: '#666666',
                    margin: [0, 0, 0, 15]
                },
                {
                    table: {
                        headerRows: 1,
                        widths: buildPdfWidths(colCount),
                        body: tableBody
                    },
                    layout: {
                        fillColor: function (rowIndex) {
                            if (rowIndex === 0) return '#007bff';
                            return rowIndex % 2 === 0 ? '#f9f9f9' : null;
                        },
                        hLineWidth: function () { return 0.5; },
                        vLineWidth: function () { return 0.5; },
                        hLineColor: function () { return '#333333'; },
                        vLineColor: function () { return '#333333'; },
                        paddingLeft: function () { return 8; },
                        paddingRight: function () { return 8; },
                        paddingTop: function () { return 6; },
                        paddingBottom: function () { return 6; }
                    }
                }
            ],
            defaultStyle: { fontSize: 9 }
        };

        pdfMake.createPdf(docDefinition).open();
    });

    // ── Export as Word (.doc via HTML blob) ──
    $(document).on('click', '#export-word', function (e) {
        e.preventDefault();
        var headers = getVisibleHeaders();
        var data = getTableData();
        if (data.length === 0) {
            Swal.fire({ toast: true, position: 'bottom-end', icon: 'warning', title: 'No data to export', showConfirmButton: false, timer: 3000 });
            return;
        }

        var title = getReportTitle();
        var fileName = getFileName();

        var headerRow = '<tr><th>Sr. No.</th>';
        headers.forEach(function (h) { headerRow += '<th>' + h + '</th>'; });
        headerRow += '</tr>';

        var tableRows = '';
        data.forEach(function (row, i) {
            tableRows += '<tr><td>' + (i + 1) + '</td>';
            row.forEach(function (cell) { tableRows += '<td>' + (cell || '') + '</td>'; });
            tableRows += '</tr>';
        });

        var html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">' +
            '<head><meta charset="utf-8">' +
            '<style>table{border-collapse:collapse;width:100%}th,td{border:1px solid #999;padding:8px 12px;text-align:left}th{background:#007bff;color:#fff;font-weight:bold}</style>' +
            '</head><body>' +
            '<h2>' + title + ' Report</h2>' +
            '<p style="color:#666">Generated: ' + new Date().toLocaleString() + ' | Total Records: ' + data.length + '</p>' +
            '<table><thead>' + headerRow + '</thead><tbody>' + tableRows + '</tbody></table></body></html>';

        var blob = new Blob(['\ufeff', html], { type: 'application/msword' });
        var url = URL.createObjectURL(blob);
        var link = document.createElement('a');
        link.href = url;
        link.download = fileName + '_' + new Date().toISOString().slice(0, 10) + '.doc';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    });

});
