function exportExcel() {
    let downloadLink;
    let dataType = 'application/vnd.ms-excel';
    let dataTable = document.getElementById('table_content');
    let tableHTML = dataTable.outerHTML.replace(/ /g, '%20');

    // Nombre del archivo
    nombreArchivo = USER_NAME.split(" ").join("_").split(".").join("")  + '.xls';

    // Crear el link de descarga
    downloadLink = document.createElement("a");

    document.body.appendChild(downloadLink);

    if (navigator.msSaveOrOpenBlob) {
        let blob = new Blob(['\ufeff', tableHTML], {
            type: dataType
        });
        navigator.msSaveOrOpenBlob(blob, nombreArchivo);
    } else {
        // Crear el link al archivo
        downloadLink.href = 'data:' + dataType + ', ' + tableHTML;

        // Setear el nombre de archivo
        downloadLink.download = nombreArchivo;

        //Ejecutar la función
        downloadLink.click();
    }
}