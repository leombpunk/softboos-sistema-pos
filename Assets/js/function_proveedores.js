var tabalFalopa;
$(document).ready(function () {
    tabalFalopa = $("#proveedoresTable").DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": {
            "url": base_url+"Assets/Spanish.json"
        },
        "ajax": {
            "url": base_url+"Proveedores/getProveedores",
            "dataSrc":""},
        "columns": [
            { "data": "RAZONSOCIAL" },
            { "data": "CUIT" },
            { "data": "MAIL" },
            { "data": "TELEFONO" },
            { "data": "WEB" },
            { "data": "estado"},
            { "data": "actions" }
        ],
        "responsive": true,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0,"asc"]]
    });
    $("#formProveedores").submit(function(e){
        e.preventDefault();
        var data = $(this).serialize();
        console.log(data);
        $.ajax({
            type: "POST",
            url: base_url+"Proveedores/setProveedor/",
            data: data,
            dataType: "json",
            success: function (response) {
                if (response.status){
                    tabalFalopa.ajax.reload();
                    $("#proveedoresModalCenter").modal("hide");
                    swal("Bien!",response.message,"success");
                }
                else {
                    swal("Atención!",response.message,"error");
                }
            }
        });
    });
});
function openModal(){
	$("#proveedor_id").val("");
    $("#proveedorModalCenterTitle").html("Nuevo Proveedor");
    $(".modal-header").addClass("headerRegister").removeClass("headerUpdate"); 
    $("#btnText").html("Guardar");
    $("#btnGuardar").addClass("btn-primary").removeClass("btn-info");
    $("#formProveedores").trigger("reset");
    $("#proveedoresModalCenter").modal("show");
}
function verProveedor(id){
    console.log({id:id})
    $.ajax({
        type: "GET",
        url: base_url+"Proveedores/getProveedor/"+id,
        dataType: "json",
        success: function (response) {
            console.log(response);
            if (response.status){
                $("#tblRazonSocial").html(response.data.RAZONSOCIAL);
                $("#tblCUIT").html(response.data.CUIT);
                $("#tblTelefono").html(response.data.TELEFONO);
                $("#tblMail").html(response.data.MAIL);
                $("#tblDireccion").html(response.data.DIRECCION);
                $("#tblWeb").html(response.data.WEB);
                $("#tblFechaalta").html(response.data.FECHA_ALTA);
                $("#tblEstado").html(response.data.ESTADO);
                $("#proveedoresVerModalCenter").modal("show");
            }
            else {
                swal("Atención!",response.message,"error");
            }
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
    			$("#proveedor_id").val(data.data.PROVEEDOR_ID);
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
    			swal("Atención!",data.message,"error");
    		}
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