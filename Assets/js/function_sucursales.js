var sampleTable;
$(document).ready(function(){
    sampleTable = $("#sucursalesTable").DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": {
            "url": base_url+"Assets/Spanish.json"
        },
        "ajax": {
            "url": base_url+"Sucursales/getSucursales",
            "dataSrc":""},
        "columns": [
            { "data": "codigo" },
            { "data": "nombre" },
            { "data": "telefono" },
            { "data": "fechaAlta" },
            { "data": "estado" },
            { "data": "actions" }
        ],
        "responsive": true,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0,"asc"]],
    });
});
$("#formSucursales").submit(function(e){
    e.preventDefault();
    let data = new FormData(this);
    // console.log(data);
    $.ajax({
        type: "POST",
        url: base_url+"Sucursales/setSucursal/",
        data: data,
        dataType: "json",
        contentType: false,
        cache: false,
        processData: false,
        success: function(response){
            console.log(response);
            if (response.status){
                sampleTable.ajax.reload();
                $("#sucursalesModalCenter").modal("hide");
                swal("Bien hecho!",response.message,"success");
            }
            else {
                swal("Atencion!",response.message,"warning");
            }
        },
        error: function(error){
            console.log(error);
            swal("Error!",error,"error");
        }
    });
});
$("#sucursalimg").change(function(e){ 
    e.preventDefault();
    // console.log($(this));
    let reader = new FileReader();
    reader.onload = function(){
        // let preview = document.getElementById('preview');
        // let image = document.createElement('img');
        let image = document.getElementById('load_img1');
        image.src = reader.result;
        // preview.innerHTML = '';
        // preview.append(image);
    };
    reader.readAsDataURL(e.target.files[0]);
    $("#load_img1").slideDown();
});
function openModal(){
	$("#sucursal_id").val("");
    $("#sucursalesModalCenterTitle").html("Nueva Sucursal");
    $(".modal-header").addClass("headerRegister").removeClass("headerUpdate"); 
    $("#btnText").html("Guardar");
    $("#btnGuardar").addClass("btn-primary").removeClass("btn-info");
    $("#load_img1").slideUp();
    $("#formSucursales").trigger("reset");
    $("#sucursalesModalCenter").modal("show");
}
function verSucursal(id){
    $.ajax({
		url: base_url+'Sucursales/getSucursal/'+id,
		type: 'GET',
		dataType: 'json',
        contentType: false,
        cache: false,
        processData: false,
		success: function(data){
			console.log(data);
			if (data.status){
				$("#tblID").html(data.data.SUCURSAL_ID);
				$("#tblNombre").html(data.data.RAZONSOCIAL);
                $("#tblWeb").html(data.data.WEB);
                $("#tblTelefono").html(data.data.TELEFONO);
                $("#tblEstado").html(data.data.DESCRIPCION);
                $("#tblMail").html(data.data.MAIL);
                $("#tblCUIT").html(data.data.CUIT);
                $("#tblCodigo").html(data.data.CODIGO_SUCURSAL);
                $("#tblDireccion").html(data.data.DIRECCION);
				$("#tblFechaAlta").html(data.data.FECHA_ALTA);
                $("#tblFechaBaja").html(data.data.FECHA_BAJA);
                $("#tblImagen").html('<img class="img-thumbnail" style="max-width: 200px; height: auto;" src="Assets/'+data.data.LOGO_URL+'" alt="Logo Sucursal">');
				$("#sucursalesVerModalCenter").modal("show");
			}
			else {
				swal("Atención!",data.message,"warning");
			}
		},
        error: function(error){
            console.log(error);
            swal("Error!",error,"error");
        }
	});
}
function editarSucursal(id){
    $("#sucursalesModalCenterTitle").html("Editar Sucursal");
    $(".modal-header").addClass("headerUpdate").removeClass("headerRegister"); 
    $("#btnText").html("Actualizar");
    $("#btnGuardar").addClass("btn-info").removeClass("btn-primary");
    $.ajax({
        type: "GET",
        url: base_url+"Sucursales/getSucursal/"+id,
        dataType: "json",
        success: function (response) {
            console.log(response);
            if (response.status){
                $("#sucursal_id").val(response.data.SUCURSAL_ID);
                $("#sucursalcodigo").val(response.data.CODIGO_SUCURSAL);
                $("#sucursalnombre").val(response.data.RAZONSOCIAL);
                $("#sucursalcuit").val(response.data.CUIT);
                $("#sucursaltelefono").val(response.data.TELEFONO);
                $("#sucursaldireccion").val(response.data.DIRECCION);
                $("#sucursalmail").val(response.data.MAIL);
                $("#sucursalweb").val(response.data.WEB);
                $("#sucursalestado").val(response.data.ESTADO_ID).trigger("change");
                $("#load_img1").prop({src: "Assets/"+response.data.LOGO_URL});
                $("#load_img1").slideDown();
                $("#sucursalesModalCenter").modal("show");
            }
            else {
                swal("Atencion!",response.message,"warning");
            }
        },
        error: function(error){
            console.log(error);
            swal("Error!",error,"error");
        }
    });
}
function borrarSucursal(id){
    swal({
        title: "Eliminar Sucursal",
        text: "¿Quiere eliminar la Sucursal?",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then(function(isConfirm){
        if(isConfirm){
            $.ajax({
                type: "POST",
                url: base_url+"Sucursales/delSucursal/",
                data: "id="+id,
                dataType: "json",
                success: function (response) {
                    if(response.status){
                        swal("Eliminar!",response.message,"success");
                        sampleTable.ajax.reload();
                    }
                    else {
                        swal("Atencion!",response.message,"warning");
                    }
                },
                error: function(error){
                    console.log(error);
                    swal("Error!",error,"error");
                }
            });
        }
    });
}

function verSucursalEmpleados(id, nombre){
    $("#nombreSucursal").html(nombre);
    $("#sucursalEmpleadosTable").DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": {
            "url": base_url+"Assets/Spanish.json"
        },
        "ajax": {
            "type": "GET",
            "url": base_url+"Sucursales/getSucursalesEmpleados/"+id,
            "dataSrc":"data"},
        "columns": [
            { "data": "DNI" },
            { "data": "NOMBRE" },
            { "data": "APELLIDO" }
        ],
        "responsive": true,
        "bDestroy": true,
        "iDisplayLength": 25,
        "order": [[0,"asc"]],
    });
    $("#sucursalesEmpleadosVerModalCenter").modal("show");
}
function restaurarSucursal(id){
    swal({
        title: "Restaurar Sucursal",
        text: "¿Quiere restaurar la sucursal?",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then(function(isConfirm){
        if (isConfirm) {
            $.ajax({
                type: "POST",
                url: base_url+"Sucursal/setRestaurar",
                dataType: "json",
                data: "idSucursal="+id,
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