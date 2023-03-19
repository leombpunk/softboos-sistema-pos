var tabalFalopa;
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
        "order": [[0,"asc"]]
    });

    $("#formGastosVarios").submit(function (e) { 
        e.preventDefault();
        // var id = $("#cargo_id").val();
        var nombre = $("#cargonombre").val();
        var nivelacceso = $("#cargonacceso").val();
        var estado = $("#cargoestado").val();
        if (nombre == "" || nivelacceso == "" || estado == "") {
            swal("Atención","Todos los campos son obligatorios","error");
            return false;
        }
        else {
            var data = $("#formGastosVarios").serialize();
            console.log(data);
            $.ajax({
                type: "POST",
                url: base_url+"GastosVarios/setFormasPago",
                data: data,
                dataType: "json",
                success: function (response) {
                    // console.log(response);
                    if (response.status){
                        $("#cargosModalCenter").modal("hide");
                        $("#formGastosVarios").trigger("reset");
                        swal("Resultado",response.message,"success");
                        tabalFalopa.ajax.reload(function(){});
                        cargarTipoMovimiento();
                    }
                    else {
                        swal("Error",response.message+" "+response.expected,"error");
                    }
                }
            });
        }
    });
    cargarTipoMovimiento();
    
});
function cargarTipoMovimiento() {
    $("#movimientoTipo").html('');
    $.ajax({
        type: "GET",
        url: base_url+"movimientosCaja/getTipoMovimiento",
        dataType: "json",
        success: function (response) {
            console.log(response);
            if(response.status){
                response.data.forEach(element => {
                    $("#movimientoTipo").append('<option value="'+element.id+'">'+element.descripcion+'</option>');
                });
            } else {
                swal("Error","Surgió un error al cargar el tipo de movimiento!","error");
            }
            
            
        }
    });
}
function movimientoCaja(){
    //ver si existe una apertura
    //si no existe obligarme a hacerla
    //si existe puedo agregar ingresos u egresos
}
function openModal(){
    $("#movimientosModalCenterTitle").html("Nueva movimiento");
    $(".modal-header").addClass("headerRegister").removeClass("headerUpdate"); 
    $("#btnText").html("Guardar");
    $("#btnGuardar").addClass("btn-primary").removeClass("btn-info");
    $("#formGastosVarios").trigger("reset");
    $("#formasPago_id").val("");
    $("#mcmovimientosModalCenter").modal("show");
}
// function editarFormasPago(id){
//     $("#movimientosModalCenterTitle").html("Editar Forma de Pago");
//     $(".modal-header").addClass("headerUpdate").removeClass("headerRegister"); 
//     $("#btnText").html("Actualizar");
//     $("#btnGuardar").addClass("btn-info").removeClass("btn-primary");
//     $.ajax({
//         type: "GET",
//         url: base_url+"GastosVarios/getFormasPago/"+id,
//         dataType: "json",
//         success: function (response) {
//             // console.log(response);
//             if (response.status){
//                 $("#formasPago_id").val(response.data.FORMAPAGO_ID);
//                 $("#formasPagonombre").val(response.data.FORMA_PAGO);
//                 $("#formasPagoestado").val(response.data.ESTADO_ID).trigger("change");
//                 $("#mcmovimientosModalCenter").modal("show");
//             }
//             else {
//                 swal("Error",response.message+" "+response.expected,"error");
//             }
//         }
//     });
// }
function borrarFormasPago(id){
    swal({
        title: "Eliminar Gasto",
        text: "¿Quiere eliminar este Gasto?",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then(function(isConfirm){
        if(isConfirm){
            $.ajax({
                type: "POST",
                url: base_url+"GastosVarios/delFormasPago/",
                data: "id="+id,
                dataType: "json",
                success: function (response) {
                    if(response.status){
                        swal("Eliminar!",response.message,"success");
                        tabalFalopa.ajax.reload(function(){
                        
                        });
                    }
                    else {
                        swal("Atencion!",response.message,"error");
                    }
                }
            });
        }
    });
}