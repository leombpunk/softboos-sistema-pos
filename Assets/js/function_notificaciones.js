var sampleTable1;
var sampleTable2;
$(document).ready(function(){
    sampleTable1 = $('#sampleTable1').DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": {
            "url": base_url+"Assets/Spanish.json"
        },
        "ajax": {
            "url": base_url+"Notificaciones/getFaltantes/"+0,
            "dataSrc":""},
        "columns": [
            { "data": "CODIGO" },
            { "data": "NOMBRE" },
            { "data": "ALERTA_MINCANT" },
            { "data": "CANTIDAD_ACTUAL" },
            { "data": "CODIGO_SUCURSAL" },
            { "data": "RAZONSOCIAL" }
        ],
        "responsive": true,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0,"asc"]],
        dom: 'Blfrtip',
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ]
    });
    sampleTable2 = $('#sampleTable2').DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": {
            "url": base_url+"Assets/Spanish.json"
        },
        "ajax": {
            "url": base_url+"Notificaciones/getExcedentes/"+0,
            "dataSrc":""},
        "columns": [
            { "data": "CODIGO" },
            { "data": "NOMBRE" },
            { "data": "ALERTA_MAXCANT" },
            { "data": "CANTIDAD_ACTUAL" },
            { "data": "CODIGO_SUCURSAL" },
            { "data": "RAZONSOCIAL" }
        ],
        "responsive": true,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0,"asc"]],
        dom: 'Blfrtip',
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ]
    });
})