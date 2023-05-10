var sampleTable;
var search;
var fechita;
var agruparPor;
$(document).ready(function () {
    // search = $("#search").val();
    // fechita = $("#fechita").val();
    // agruparPor = $("#agrupar").val();
    sampleTable = $("#sampleTable").DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": {
            "url": base_url+"Assets/Spanish.json"
        },
        "ajax": {
            "url": base_url+"Informes/getinformeDelDia",
            "dataSrc": "",
            "data": function(){ 
                return {"fecha" : $("#fechita").val(), "agrupar": $("#agrupar").val()} 
            },
            "type": "POST",
        },
        "columns": [
            { "data": "descripcion" },
            { "data": "NOMBRE" },
            { "data": "cantidad" },
            { "data": "PRECIO" },
            { "data": "monto" },
            { "data": "movimiento"},
            { "data": "FORMA_PAGO" },
        ],
        "responsive": true,
        "bDestroy": true,
        "iDisplayLength": 50,
        "order": [],
    });
    $("#searchForm").submit(function(event) {
        event.preventDefault();
        // fechita = $("#fechita").val();
        // agruparPor = $("#agrupar").val();
        // console.log($(this).serialize())
        // console.log({fecha: fechita, agrupar: agruparPor})
        sampleTable.ajax.reload(function(){});
        console.log(sampleTable);
    })
});