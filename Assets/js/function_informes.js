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
            "url": base_url+"MovimientosCaja/getMovimientos",
            "dataSrc":""},
        "columns": [
            { "data": "id" },
            { "data": "descripcion" },
            { "data": "tipo" },
            { "data": "alta" },
            { "data": "monto" },
            { "data": "empleado" },
            { "data": "actions" }
        ],
        "responsive": true,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0,"asc"]],
        "search": {
            "search" : today()
        }
    });

    $("#formMovimientos").submit(function (e) { 
        e.preventDefault();
        // var id = $("#cargo_id").val();
        var descripcion = $("#movimientoDescripcion").val();
        var tipo = $("#movimientoTipo").val();
        var monto = $("#movimientoMonto").val();
        if (descripcion == "" || tipo == "" || monto == "") {
            swal("Atención","Todos los campos son obligatorios","error");
            return false;
        }
        else {
            var data = $("#formMovimientos").serialize();
            console.log(data);
            $.ajax({
                type: "POST",
                url: base_url+"MovimientosCaja/setMovimiento",
                data: data,
                dataType: "json",
                success: function (response) {
                    // console.log(response);
                    if (response.status){
                        $("#movimientosModalCenter").modal("hide");
                        $("#formMovimientos").trigger("reset");
                        swal("Resultado",response.message,"success");
                        tabalFalopa.ajax.reload(function(){});
                    }
                    else {
                        swal("Error",response.message+" "+response.expected,"error");
                    }
                }
            });
        }
    });
});