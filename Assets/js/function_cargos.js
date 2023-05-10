var sampleTable;
$(document).ready(function () {
    sampleTable = $("#sampleTable").DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": {
            "url": base_url+"Assets/Spanish.json"
        },
        "ajax": {
            "url": base_url+"Cargos/getCargos",
            "dataSrc":""},
        "columns": [
            { "data": "CARGO_ID" },
            { "data": "CARGO_DESCRIPCION" },
            { "data": "FECHA_ALTA" },
            { "data": "estado" },
            { "data": "actions" }
        ],
        "responsive": true,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0,"asc"]]
    });
    $("#formCargos").submit(function (e) { 
        e.preventDefault();
        var nombre = $("#cargonombre").val();
        var estado = $("#cargoestado").val();
        if (nombre == "" || estado == "") {
            swal("Atención!","Todos los campos son obligatorios","error");
            // return false;
        }
        else {
            var data = $("#formCargos").serialize();
            console.log(data);
            $.ajax({
                type: "POST",
                url: base_url+"Cargos/setCargo",
                data: data,
                dataType: "json",
                success: function (response) {
                    // console.log(response);
                    if (response.status){
                        $("#cargosModalCenter").modal("hide");
                        $("#formCargos").trigger("reset");
                        swal("Resultado",response.message,"success");
                        sampleTable.ajax.reload();
                    }
                    else {
                        swal("Error",response.message+" "+response.expected,"error");
                    }
                },
                error: function (error) {
                    console.log(error);
                }
            });
        }
    });
});
function openModal(){
    $("#cargosModalCenterTitle").html("Nuevo Cargo");
    $(".modal-header").addClass("headerRegister").removeClass("headerUpdate"); 
    $("#btnText").html("Guardar");
    $("#btnGuardar").addClass("btn-primary").removeClass("btn-info");
    $("#formCargos").trigger("reset");
    $("#cargo_id").val("");
    $("#cargosModalCenter").modal("show");
}
function editarCargo(id){
    $("#cargosModalCenterTitle").html("Editar Cargo");
    $(".modal-header").addClass("headerUpdate").removeClass("headerRegister"); 
    $("#btnText").html("Actualizar");
    $("#btnGuardar").addClass("btn-info").removeClass("btn-primary");
    $.ajax({
        type: "GET",
        url: base_url+"Cargos/getCargo/"+id,
        dataType: "json",
        success: function (response) {
            // console.log(response);
            if (response.status){
                $("#cargo_id").val(response.data.CARGO_ID);
                $("#cargonombre").val(response.data.CARGO_DESCRIPCION);
                $("#cargoestado").val(response.data.ESTADO_ID).trigger("change");
                $("#cargosModalCenter").modal("show");
            }
            else {
                swal("Error",response.message+" "+response.expected,"error");
            }
        },
        error: function (error) {
            console.log(error);
        }
    });
}
function borrarCargo(id){
    swal({
        title: "Eliminar Cargo",
        text: "¿Quiere eliminar el cargo?",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then(function(isConfirm){
        if(isConfirm){
            $.ajax({
                type: "POST",
                url: base_url+"Cargos/delCargo/",
                data: "id="+id,
                dataType: "json",
                success: function (response) {
                    if(response.status){
                        swal("Eliminar!",response.message,"success");
                        sampleTable.ajax.reload();
                    }
                    else {
                        swal("Atencion!",response.message,"error");
                    }
                },
                error: function (error) {
                    console.log(error);
                }
            });
        }
    });
}
function verPermisos(id,nombrePermiso){
    $("#spanCargo").html(nombrePermiso);
    $.ajax({
        url: base_url+'Cargos/getPermisos/'+id,
        type: 'GET',
        dataType: 'json',
        success: function(data){
            if (data.status){
                $("#tbodyTablePermisos").html(""); //vacio la tabla equisde
                data.data.forEach(function(element, index) {
                    $("#tbodyTablePermisos").append("<tr><td>"+element.ID+"</td><td>"+element.NOMBRE+"</td><td class='text-center'>"+btnCheck(element.ID,1)+"</td><td class='text-center'>"+btnCheck(element.ID,2)+"</td><td class='text-center'>"+btnCheck(element.ID,3)+"</td><td class='text-center'>"+btnCheck(element.ID,4)+"</td></tr>");
                    if (element.L == "1") $("#"+element.ID+"_1").prop("checked",true);
                    if (element.A == "1") $("#"+element.ID+"_2").prop("checked",true);
                    if (element.M == "1") $("#"+element.ID+"_3").prop("checked",true);
                    if (element.B == "1") $("#"+element.ID+"_4").prop("checked",true);
                });
             }
            else {
                swal("Error!",data.message,"error");
            }
        },
        error: function (error) {
            console.log(error);
        }
    });
    $("#cargo_id_permiso").val(id);
    $("#permisosModalCenter").modal("show");
}
function btnCheck(modulo, boton){
    return "<div class='toggle-flip'><label><input id='"+modulo+"_"+boton+"' name='"+modulo+"_"+boton+"' value='"+modulo+"_"+boton+"' type='checkbox'><span class='flip-indecator' data-toggle-on='ON' data-toggle-off='OFF'></span></label></div>";
}
$("#formPermisos").submit(function(e){
    e.preventDefault();
    var datas = $(this).serialize();
    $.ajax({
        url: base_url+'Cargos/setPermisos',
        type: 'POST',
        dataType: 'json',
        data: datas,
        success: function(data){
            // console.log(data);
            if (data.status){
                swal("Exito!",data.message,"success");
            }
            else {
                swal("Atención!",data.message,"error");
            }
        },
        error: function (error) {
            console.log(error);
        }
    });
});

function restaurarCargo(id){
    swal({
        title: "Restaurar Cargo",
        text: "¿Quiere restaurar el cargo?",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then(function(isConfirm){
        if (isConfirm) {
            $.ajax({
                type: "POST",
                url: base_url+"Cargos/setRestaurar",
                dataType: "json",
                data: "idCargo="+id,
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

function verEmpleados(id){
    console.log(id)
    $("#tableEmpleadosVer").DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": {
            "url": base_url+"Assets/Spanish.json"
        },
        "ajax": {
            "url": base_url+"Cargos/getEmpleados/"+id,
            "dataSrc":"data"},
        "columns": [
            { "data": "CODIGO_SUCURSAL" },
            { "data": "RAZONSOCIAL" },
            { "data": "DNI" },
            { "data": "NOMBRE" },
            { "data": "APELLIDO" },
            { "data": "TELEFONO" },
            { "data": "MAIL" }
        ],
        "responsive": true,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0,"asc"]]
    });
    $("#cargosEmpleadosVerModalCenter").modal("show");
}