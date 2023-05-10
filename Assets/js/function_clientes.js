var sampleTable;
$(document).ready(function () {
    sampleTable = $("#clientesTable").DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": {
            "url": base_url+"Assets/Spanish.json"
        },
        "ajax": {
            "url": base_url+"Clientes/getClientes",
            "dataSrc":""},
        "columns": [
            { "data": "DNI" },
            { "data": "NOMBRE" },
            { "data": "APELLIDO" },
            { "data": "MAIL" },
            { "data": "TELEFONO" },
            { "data": "estado"},
            { "data": "actions" }
        ],
        "responsive": true,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0,"asc"]]
    });
    $("#formClientes").submit(function(e){
        e.preventDefault();
        var data = $(this).serialize();
        console.log(data);
        $.ajax({
            type: "POST",
            url: base_url+"Clientes/setCliente/",
            data: data,
            dataType: "json",
            success: function (response) {
                if (response.status){
                    sampleTable.ajax.reload();
                    $("#clientesModalCenter").modal("hide");
                    swal("Bien!",response.message,"success");
                }
                else {
                    swal("Atención!",response.message,"error");
                }
            },
            error: function (error) {
                console.log(error);
            }
        });
    });
});
function openModal(){
	$("#cliente_id").val("");
    $("#clientesModalCenterTitle").html("Nuevo Cliente");
    $(".modal-header").addClass("headerRegister").removeClass("headerUpdate"); 
    $("#btnText").html("Guardar");
    $("#btnGuardar").addClass("btn-primary").removeClass("btn-info");
    $("#formClientes").trigger("reset");
    $("#clientesModalCenter").modal("show");
}
function verCliente(id){
    $.ajax({
        type: "GET",
        url: base_url+"Clientes/getCliente/"+id,
        dataType: "json",
        success: function (response) {
            console.log(response);
            if (response.status){
                $("#tblDNI").html(response.data.DNI);
                $("#tblNombre").html(response.data.NOMBRE);
                $("#tblApellido").html(response.data.APELLIDO);
                $("#tblFechanac").html(response.data.FECHA_NACIMIENTO);
                $("#tblCUIL").html(response.data.CUIL);
                $("#tblTelefono").html(response.data.TELEFONO);
                $("#tblMail").html(response.data.MAIL);
                $("#tblDireccion").html(response.data.DIRECCION);
                $("#tblFechaalta").html(response.data.FECHA_ALTA);
                $("#tblEstado").html(response.data.ESTADO);
                $("#clientesVerModalCenter").modal("show");
            }
            else {
                swal("Atención!",response.message,"error");
            }
        },
        error: function (error) {
            console.log(error);
        }
    });
}
function editarCliente(id){
    $("#clientesModalCenterTitle").html("Editar Cliente");
    $(".modal-header").addClass("headerUpdate").removeClass("headerRegister"); 
    $("#btnText").html("Actualizar");
    $("#btnGuardar").addClass("btn-info").removeClass("btn-primary");
    $.ajax({
    	url: base_url+'Clientes/getCliente/'+id,
    	type: 'GET',
    	dataType: 'json',
    	success: function(data){
    		console.log(data);
    		if (data.status){
    			$("#cliente_id").val(data.data.CLIENTE_ID);
    			$("#clientedni").val(data.data.DNI);
    			$("#clientefechanac").val(data.data.FECHA_NACIMIENTO);
    			$("#clientenombre").val(data.data.NOMBRE);
    			$("#clienteapellido").val(data.data.APELLIDO);
    			$("#clientecuil").val(data.data.CUIL);
    			$("#clientemail").val(data.data.MAIL);
    			$("#clientetelefono").val(data.data.TELEFONO);
    			$("#clientedireccion").val(data.data.DIRECCION);
    			$("#clientesModalCenter").modal("show");
    		}
    		else {
    			swal("Atención!",data.message,"warning");
    		}
    	},
        error: function (error) {
            console.log(error);
        }
    });
}
function borrarCliente(id){
    swal({
        title: "Eliminar Cliente",
        text: "¿Quiere eliminar al Cliente?",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then(function(isConfirm){
        if(isConfirm){
            $.ajax({
                type: "POST",
                url: base_url+"Clientes/delCliente/",
                data: "id="+id,
                dataType: "json",
                success: function (response) {
                    console.log(response);
                    if(response.status){
                        swal("Eliminar!",response.message,"success");
                        sampleTable.ajax.reload();
                    }
                    else {
                        swal("Atencion!",response.message,"warning");
                    }
                },
                error: function (error){
                    console.log(error);
                    swal("Atencion!",error,"error");
                }
            });
        }
    });
}
function restaurarCliente(id){
    swal({
        title: "Restaurar Cliente",
        text: "¿Quiere restaurar el cliente?",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then(function(isConfirm){
        if (isConfirm) {
            $.ajax({
                type: "POST",
                url: base_url+"Clientes/setRestaurar",
                dataType: "json",
                data: "idCliente="+id,
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