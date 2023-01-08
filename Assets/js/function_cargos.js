var tabalFalopa;
$(document).ready(function () {
    tabalFalopa = $("#sampleTable").DataTable({
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
            { "data": "NIVELACCESO_ID" },
            { "data": "FECHA_ALTA" },
            { "data": "estado" },
            { "data": "actions" }
        ],
        "responsive": true,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0,"asc"]]
    });
    // console.log(tabalFalopa);
    //ajax carga de elementos al select
    $.ajax({
        url: base_url+'Cargos/getNivelesAcceso',
        type: 'POST',
        dataType: 'json',
        success: function(data){
            $("#cargonacceso").append("<option value=''></option>");
            if (data.status){
                data.data.forEach(function(element, index) {
                    // console.log("elemento: "+element+" indice:"+index);
                    $("#cargonacceso").append("<option value='"+element.NIVELACCESO_ID+"''>"+element.NIVEL_ACCESO+"</option>");
                });
            }
            else {
                swal("Error",data.message,"error");
            }
            // $("#cargonacceso").addClass("selectpicker");
        },
        error: function(){
            swal("Error","Algo malio sal al cargar los niveles de acceso!","error");
        }
    }); 
    //---------------------------------
    $("#formCargos").submit(function (e) { 
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
                        tabalFalopa.ajax.reload(function(){
                            // editarCargo();
                            // borrarCargo();
                        });
                    }
                    else {
                        swal("Error",response.message+" "+response.expected,"error");
                    }
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
// window.addEventListener("load",function(){
//     editarCargo();
//     borrarCargo();
// },false);
function editarCargo(id){
    // console.log("hijo de puta");
    // var btnEditar = document.querySelectorAll(".btnEditarCargo");
    // console.log(btnEditar);
    // btnEditar.forEach(function(btnEditar){
        // btnEditar.addEventListener("click",function(){
            $("#cargosModalCenterTitle").html("Editar Cargo");
            $(".modal-header").addClass("headerUpdate").removeClass("headerRegister"); 
            $("#btnText").html("Actualizar");
            $("#btnGuardar").addClass("btn-info").removeClass("btn-primary");
            // var data = $(this).attr("rl");
            // console.log(data);
            $.ajax({
                type: "GET",
                url: base_url+"Cargos/getCargo/"+id,
                dataType: "json",
                success: function (response) {
                    console.log(response);
                    if (response.status){
                        $("#cargo_id").val(response.data.CARGO_ID);
                        $("#cargonombre").val(response.data.CARGO_DESCRIPCION);
                        $("#cargonacceso").val(response.data.NIVELACCESO_ID).trigger("change");
                        $("#cargoestado").val(response.data.ESTADO_ID).trigger("change");
                        $("#cargosModalCenter").modal("show");
                    }
                    else {
                        swal("Error",response.message+" "+response.expected,"error");
                    }
                }
            });
    //     });
    // });
}
function borrarCargo(id){
    // var btnBorrar = document.querySelectorAll(".btnBorrarCargo");
    // btnBorrar.forEach(function(btnBorrar){
        // btnBorrar.addEventListener("click",function(){
            // var id = this.getAttribute("rl");
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
    //     });
    // });
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
            console.log(data);
            if (data.status){
                swal("Exito!",data.message,"success");
            }
            else {
                swal("Atención!",data.message,"error");
            }
        }
    });
});
// $("#cargoestado").select2();