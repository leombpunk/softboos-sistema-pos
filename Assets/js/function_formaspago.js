var sampleTable;
$(document).ready(function () {
    sampleTable = $("#sampleTable").DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": {
            "url": base_url+"Assets/Spanish.json"
        },
        "ajax": {
            "url": base_url+"FormasPago/getFormasPagos",
            "dataSrc":""},
        "columns": [
            { "data": "FORMAPAGO_ID" },
            { "data": "FORMA_PAGO" },
            { "data": "FECHA_ALTA" },
            { "data": "estado" },
            { "data": "actions" }
        ],
        "responsive": true,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0,"asc"]]
    });

    $("#formFormasPago").submit(function (e) { 
        e.preventDefault();
        // var id = $("#cargo_id").val();
        var nombre = $("#formasPagonombre").val();
        var estado = $("#formasPagoestado").val();
        if (nombre == "" || estado == "") {
            swal("Atención","Todos los campos son obligatorios","error");
            return false;
        }
        else {
            var data = $("#formFormasPago").serialize();
            console.log(data);
            $.ajax({
                type: "POST",
                url: base_url+"FormasPago/setFormasPago",
                data: data,
                dataType: "json",
                success: function (response) {
                    // console.log(response);
                    if (response.status){
                        $("#formasPagosModalCenter").modal("hide");
                        $("#formFormasPago").trigger("reset");
                        swal("Resultado",response.message,"success");
                        sampleTable.ajax.reload(function(){});
                    }
                    else {
                        swal("Error",response.message+" "+response.expected,"error");
                    }
                },
                error: function (error){
                    console.log(error)
                }
            });
        }
    });
});
function openModal(){
    $("#formasPagosModalCenterTitle").html("Nueva Forma de Pago");
    $(".modal-header").addClass("headerRegister").removeClass("headerUpdate"); 
    $("#btnText").html("Guardar");
    $("#btnGuardar").addClass("btn-primary").removeClass("btn-info");
    $("#formFormasPago").trigger("reset");
    $("#formasPago_id").val("");
    $("#formasPagosModalCenter").modal("show");
}
function editarFormasPago(id){
    $("#formasPagosModalCenterTitle").html("Editar Forma de Pago");
    $(".modal-header").addClass("headerUpdate").removeClass("headerRegister"); 
    $("#btnText").html("Actualizar");
    $("#btnGuardar").addClass("btn-info").removeClass("btn-primary");
    $.ajax({
        type: "GET",
        url: base_url+"FormasPago/getFormasPago/"+id,
        dataType: "json",
        success: function (response) {
            // console.log(response);
            if (response.status){
                $("#formasPago_id").val(response.data.FORMAPAGO_ID);
                $("#formasPagonombre").val(response.data.FORMA_PAGO);
                $("#formasPagoestado").val(response.data.ESTADO_ID).trigger("change");
                $("#formasPagosModalCenter").modal("show");
            }
            else {
                swal("Error",response.message+" "+response.expected,"error");
            }
        },
        error: function (error){
            console.log(error)
        }
    });
}
function borrarFormasPago(id){
    swal({
        title: "Eliminar Forma de Pago",
        text: "¿Quiere eliminar la forma de pago?",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then(function(isConfirm){
        if(isConfirm){
            $.ajax({
                type: "POST",
                url: base_url+"FormasPago/delFormasPago/",
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
                },
                error: function (error){
                    console.log(error)
                }
            });
        }
    });
}

function restaurarFormasPago(id){
    swal({
        title: "Restaurar Forma de Pago",
        text: "¿Quiere restaurar la forma de pago?",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then(function(isConfirm){
        if (isConfirm) {
            $.ajax({
                type: "POST",
                url: base_url+"FormasPago/setRestaurar",
                dataType: "json",
                data: "idFormasPago="+id,
                success: function (response){
                    // console.log(response)
                    if(response.status){
                        swal("Restaurado!",response.message,"success");
                        sampleTable.ajax.reload();
                    }
                    else {
                        swal("Atencion!",response.message,"error");
                    }
                },
                error: function (error){
                    console.log(error)
                }
            });
        }
    });
}