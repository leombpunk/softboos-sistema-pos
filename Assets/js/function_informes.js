var sampleTable;
$(document).ready(function () {
    sampleTable = $("#sampleTable").DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": {
            "url": base_url+"Assets/Spanish.json"
        },
        "ajax": {
            "url": base_url+"Informes/getinformeDelDia",
            "dataSrc":""},
        "columns": [
            { "data": "descripcion" },
            { "data": "NOMBRE" },
            { "data": "cantidad" },
            { "data": "PRECIO" },
            { "data": "monto" },
            { "data": "FORMA_PAGO" },
        ],
        "responsive": true,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0,"asc"]],
    });
});