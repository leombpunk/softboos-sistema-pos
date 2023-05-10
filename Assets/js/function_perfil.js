$(document).ready(function() {
    cargarDatosPerfil();
});
function enableFormDataUser(){
    var formUser = document.forms["formDataUser"].getElementsByTagName('input');
    for (let index = 0; index < formUser.length; index++) {
        formUser[index].disabled = false;
    }
    $("#btnEditar").attr('hidden', true);
    $("#btnCancelar").removeAttr('hidden');
    $("#btnGuardar").removeAttr('disabled');
}
function disableFormDataUser(){
    var formUser = document.forms["formDataUser"].getElementsByTagName('input');
    for (let index = 0; index < formUser.length; index++) {
        formUser[index].disabled = true;
    }
    $("#btnCancelar").attr('hidden', true);
    $("#btnEditar").removeAttr('hidden');
    $("#btnGuardar").attr('disabled', true);
}
function cargarDatosPerfil(){
    $.ajax({
        type: "GET",
        url: base_url+"Perfil/getPerfil",
        dataType: "json",
        success: function(response){
            console.log(response);
            //datos perfil
            $("#userName").html(response.data.NOMBRE+" "+response.data.APELLIDO);
            $("#nombre").val(response.data.NOMBRE);
            $("#apellido").val(response.data.APELLIDO);
            $("#dni").val(response.data.DNI);
            $("#fechaNac").val(response.data.FECHA_NACIMIENTO);
            $("#email").val(response.data.MAIL);
            $("#telefono").val(response.data.TELEFONO);
            $("#direccion").val(response.data.DIRECCION);
            //datos sucursal
            $("#scodigo").val(response.data.CODIGO_SUCURSAL);
            $("#snombre").val(response.data.RAZONSOCIAL);
            $("#stelefono").val(response.data.STELEFONO);
            $("#semail").val(response.data.SMAIL);
            $("#cuit").val(response.data.SCUIT);
            $("#web").val(response.data.WEB);
            $("#sdireccion").val(response.data.SUC_DIRECCION);
            //datos sesion
            $("#cuil").val(response.data.CUIL);
            $("#cargo").val(response.data.CARGO_DESCRIPCION);
        },
        error: function(error){
            console.log(error);
        }
    })
}
$("#formDataUser").submit(function(e){
    e.preventDefault();
    console.log($(this).serialize());
    $.ajax({
        type: "POST",
        url: base_url+"Perfil/setPerfil",
        dataType: "json",
        data: $(this).serialize(),
        success: function(response){
            console.log(response);
            if (response.status){
                swal("Bien!",response.message,"success");
                disableFormDataUser();
                cargarDatosPerfil();
            }
            else {
                swal("Atención!",response.message,"warning");
            }
        },
        error: function(error){
            console.log(error);
        }
    })
})
$("#formDataSession").submit(function(e){
    e.preventDefault();
    console.log($(this).serialize());
    $.ajax({
        type: "POST",
        url: base_url+"Perfil/setPassword",
        dataType: "json",
        data: $(this).serialize(),
        success: function(response){
            console.log(response);
            if (response.status){
                swal("Bien!",response.message,"success");
                cargarDatosPerfil();
                //limpiar formulario
                //contraseñas
                $("#actualpass").val("");
                $("#newpass").val("");
            }
            else {
                swal("Atención!",response.message,"warning");
            }
        },
        error: function(error){
            console.log(error);
        }
    })
})