function downloadCSV(csv, filename) {
    var csvFile;
    var downloadLink;

    // CSV file
    csvFile = new Blob(["\uFEFF"+csv], {type: 'text/csv; charset=utf-18'});

    // Download link
    downloadLink = document.createElement("a");

    // File name
    downloadLink.download = filename;

    // Create a link to the file
    downloadLink.href = window.URL.createObjectURL(csvFile);

    // Hide download link
    downloadLink.style.display = "none";

    // Add the link to DOM
    document.body.appendChild(downloadLink);

    // Click download link
    downloadLink.click();
}

function exportTableToCSV(filename) {
    var csv = [];
    var rows = document.querySelectorAll("table tr");
    
    jQuery('#ik-loansys-application-list tr').each(function() {
        if (jQuery(this).hasClass('ik-loansys-application-id-selected')){
            jQuery('#ik-loansys-application-list').attr('export', 'selected');
        }
    });
  
    if (jQuery('#ik-loansys-application-list').attr('export') == 'selected'){
        var tdToExport = '.ik-loansys-application-id-selected td.ik_loansys_data_to_export, th.ik_loansys_data_to_export';
    } else {
        var tdToExport = 'td.ik_loansys_data_to_export, th.ik_loansys_data_to_export';
    }
    
    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = rows[i].querySelectorAll(tdToExport);
        
        for (var j = 0; j < cols.length; j++) 
            row.push(cols[j].innerText);
        
        csv.push(row.join(","));        
    }

    // Download CSV file
    downloadCSV(csv.join("\n"), filename);
    jQuery('#ik-loansys-application-list').removeAttr('export');
}