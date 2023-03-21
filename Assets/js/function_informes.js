var tabalFalopa;
const today = () => {
    var date = new Date();
    return date.getFullYear() + "-" +((date.getMonth()+1) < 10 ? "0" + (date.getMonth() + 1) : (date.getMonth()+1)) + "-" + (date.getDate() < 10 ? "0" + date.getDate() : date.getDate())
}
$(document).ready(function () {
    tabalFalopa = $("#sampleTable").DataTable({
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
    $.ajax({
        type: "GET",
        url: base_url+"Informes/getinformeDelDia",
        dataType: "json",
        success: function(response) {
            console.log(response);
        }
    });
});