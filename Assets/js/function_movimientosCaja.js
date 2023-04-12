var sampleTable;
const today = () => {
    var date = new Date();
    return date.getFullYear() + "-" +((date.getMonth()+1) < 10 ? "0" + (date.getMonth() + 1) : (date.getMonth()+1)) + "-" + (date.getDate() < 10 ? "0" + date.getDate() : date.getDate())
}
$(document).ready(function () {

    sampleTable = $("#sampleTable").DataTable({
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
                        sampleTable.ajax.reload(function(){});
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
        url: base_url+"MovimientosCaja/getTipoMovimiento",
        dataType: "json",
        success: function (response) {
            // console.log(response);
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
    $("#formMovimientos").trigger("reset");
    $("#movimiento_id").val("");
    $("#movimientosModalCenter").modal("show");
}
function borrarMovimiento(id){
    swal({
        title: "Eliminar Movimiento",
        text: "¿Quiere eliminar este Movimiento de Caja?",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then(function(isConfirm){
        if(isConfirm){
            $.ajax({
                type: "POST",
                url: base_url+"MovimientosCaja/delMovimiento/",
                data: "id="+id,
                dataType: "json",
                success: function (response) {
                    if(response.status){
                        swal("Eliminar!",response.message,"success");
                        sampleTable.ajax.reload(function(){
                        
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