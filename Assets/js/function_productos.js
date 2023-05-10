var sampleTable;
//cargar las opciones del dropzone antes que el documento este listo,
//de lo contrario las opciones son ignoradas.
// Dropzone.options.myDropzonePe = {
//     autoProccessQueue: false, //.processFile(file) need to be called by me
//     autoQueue: false,
//     addRemoveLinks: true,
//     paramName: "imgFile",
//     maxFiles: 5,
//     acceptedFiles: "image/png, image/jpg, image/jpeg",
//     maxFilesize: 2,
//     uploadMultiple: true,
// }
$(document).ready(function(){
    sampleTable = $("#productosTable").DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": {
            "url": base_url+"Assets/Spanish.json"
        },
        "ajax": {
            "url": base_url+"Productos/getProductos",
            "dataSrc":""
		},
        "columns": [
            { "data": "cod" },
            { "data": "nom" },
            { "data": "rnom" },
            { "data": "umnom" },
            { "data": "cant" },
            { "data": "precioventa" },
			{ "data": "est" },
            { "data": "actions" }
        ],
        "responsive": true,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0,"asc"]]
    });
    // CKEDITOR.replace("productodescripcion");
    // var content = CKEDITOR.instances['productodescripcion'].getData();
    // console.log(content);
    loadRubros();
    loadUnMedidas();
    loadIVA();
});
$("#formProductos").submit(function(e){
    e.preventDefault();
    console.log($(this).serialize());
    datos = $(this).serialize();
    // var form = document.getElementById("formProductos");
    // var formData = new FormData(form);
    // formData.set('productodescripcion', CKEDITOR.instances['productodescripcion'].getData());
    // console.log("formulario2 formData: ");
    // console.log(formData.get('productonombre'));
    // console.log(formData.get('productodescripcion'));
    $.ajax({
        type: "POST",
        url: base_url+"Productos/setProducto/",
        data: datos,
        dataType: "json",
        success: function(data){
			// console.log(data);
			if (data.status){
				sampleTable.ajax.reload(function(){});
				$.notify({
		      		title: "Bien! ",
		      		message: data.message,
		      		icon: 'fa fa-check' 
		      	},{
		      		position: "absolute",
		      		type: "success",
		      		placement: {
						from: "bottom",
						align: "right"
					},
					z_index: 3000
		      	});
		      	swal("Bien!",data.message,"success");
                $("#productosModalCenter").modal("hide");
			}
			else {
				$.notify({
		      		title: "Error! ",
		      		message: data.message,
		      		icon: 'fa fa-equis' 
		      	},{
		      		position: "absolute",
		      		type: "danger",
		      		placement: {
						from: "bottom",
						align: "right"
					},
					z_index: 3000
		      	});
                swal("Atención!",data.message,"error");
			}
		},
        error: function(error){
            console.log(error);
            swal("Error!",error,"error");
        }
    });
});
function openModal(){
    $("#producto_id").val("");
    $("#productosModalCenterTitle").html("Nuevo Producto");
    $(".modal-header").addClass("headerRegister").removeClass("headerUpdate"); 
    $("#btnText").html("Guardar");
    $("#btnGuardar").addClass("btn-primary").removeClass("btn-info");
    $("#formProductos").trigger("reset");
    $("#productosModalCenter").modal("show");
}
function editarProducto(id){
    $("#productosModalCenterTitle").html("Editar Producto");
    $(".modal-header").addClass("headerUpdate").removeClass("headerRegister"); 
    $("#btnText").html("Actualizar");
    $("#btnGuardar").addClass("btn-info").removeClass("btn-primary");
    $.ajax({
        type: "GET",
        url: base_url+"Productos/getProducto/"+id,
        dataType: "json",
        success: function(data){
            console.log(data);
            if (data.status){
                $("#producto_id").val(data.data.id);
                $("#productonombre").val(data.data.nom);
                $("#productocodigo").val(data.data.cod);
                $("#productorubro").val(data.data.rid).trigger("change");
                $("#productoudmedida").val(data.data.umid).trigger("change");
                $("#productocantmin").val(data.data.cmin.replace(",","."));
                $("#productocantmax").val(data.data.cmax.replace(",","."));
                $("#productoiva").val(data.data.iva).trigger("change");
                $("#productoestado").val(data.data.est).trigger("change");
                $("#productopventa").val(data.data.precioventa.replace(",","."));
                $("#productopcosto").val(data.data.preciocosto.replace(",","."));
                $("#productoinsumo").val(data.data.esinsumo).trigger("change");
                $("#productoventa").val(data.data.esvendible).trigger("change");
                $("#productosModalCenter").modal("show");
            }
            else {
                swal("Atención!",data.message,"error");
            }
        },
        error: function(error){
            console.log(error);
            swal("Error!",error,"error");
        }
    });
}
function borrarProducto(id){
    swal({
        title: "Eliminar Producto",
        text: "¿Quiere eliminar el Producto?",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then(function(isConfirm){
        if(isConfirm){
            $.ajax({
                type: "POST",
                url: base_url+"Productos/delProducto/",
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
                error: function(error){
                    console.log(error);
                    swal("Error!",error,"error");
                }
            });
        }
    });
}
function verProducto(id){
    $.ajax({
		url: base_url+'Productos/getProducto/'+id,
		type: 'GET',
		dataType: 'json',
		success: function(data){
			console.log(data);
			if (data.status){
				$("#tblCodigo").html(data.data.cod);
				$("#tblDescripcion").html(data.data.nom);
				$("#tblRubro").html(data.data.rnom);
				$("#tblUnidadMedida").html(data.data.umnom);
				$("#tblCMAX").html(data.data.cmax);
				$("#tblCMIN").html(data.data.cmin);
				$("#tblCActual").html(data.data.cant);
				$("#tblIVA").html(data.data.ivanom);
				$("#tblPCosto").html(data.data.preciocosto);
                $("#tblPVenta").html(data.data.precioventa);
                $("#tblInsumo").html((data.data.esinsumo == 1 ? 'Si' : 'No'));
                $("#tblVendible").html((data.data.esvendible == 1 ? 'Si' : 'No'));
				$("#tblEstado").html((data.data.est == 1 ? 'Activo' : 'Inactivo'));
				$("#productosVerModalCenter").modal("show");
			}
			else {
				swal("Atención!",data.message,"error");
			}
		},
        error: function(error){
            console.log(error);
            swal("Error!",error,"error");
        }
	});
}
function loadRubros(){
    $.ajax({
        type: "POST",
        url: base_url+"Productos/getRubros",
        dataType: "json",
        success: function (response) {
            if (response.status){
                response.data.forEach(element => {
                    $("#productorubro").append(element);
                });
            }
            else {
                $("#productorubro").append("<option value=''>Aun no hay Rubros!!!</option>");
            }
        },
        error: function(error){
            console.log(error);
            swal("Error!",error,"error");
        }
    });
}
function loadUnMedidas(){
    $.ajax({
        type: "POST",
        url: base_url+"Productos/getUnMedidas",
        dataType: "json",
        success: function (response) {
            if (response.status){
                response.data.forEach(element => {
                    $("#productoudmedida").append(element);
                });
            }
            else {
                $("#productoudmedida").append("<option value=''>Aun no hay Unidades de Medida!!!</option>");
            }
        },
        error: function(error){
            console.log(error);
            swal("Error!",error,"error");
        }
    });
}
function loadIVA(){
    $.ajax({
        type: "POST",
        url: base_url+"Productos/getIVA",
        dataType: "json",
        success: function (response) {
            if (response.status){
                response.data.forEach(element => {
                    $("#productoiva").append(element);
                });
            }
            else {
                $("#productoiva").append("<option value=''>Aun no hay IVA cargado!!!</option>");
            }
        },
        error: function(error){
            console.log(error);
            swal("Error!",error,"error");
        }
    });
}

function restaurarMovimiento(id){
    swal({
        title: "Restaurar Movimiento",
        text: "¿Quiere restaurar el movimiento?",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then(function(isConfirm){
        if (isConfirm) {
            $.ajax({
                type: "POST",
                url: base_url+"Movimiento/setRestaurar",
                dataType: "json",
                data: "idMovimiento="+id,
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