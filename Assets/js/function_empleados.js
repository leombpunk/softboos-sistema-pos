var sampleTable;
$(document).ready(function () {
    sampleTable = $("#empleadosTable").DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": {
            "url": base_url+"Assets/Spanish.json"
        },
        "ajax": {
            "url": base_url+"Empleados/getEmpleados",
            "dataSrc":""
		},
        "columns": [
            { "data": "DNI" },
            { "data": "NOMBRE" },
            { "data": "APELLIDO" },
            { "data": "CODIGO_SUCURSAL" },
            { "data": "CARGO_DESCRIPCION" },
            { "data": "MAIL" },
            { "data": "TELEFONO" },
			{ "data": "estado" },
            { "data": "actions" }
        ],
        "responsive": true,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0,"asc"]]
    });

    $.ajax({
    	url: base_url+'Empleados/getCargosDiff',
    	type: 'POST',
    	dataType: 'json',
    	success: function(data){
    		$("#empleadocargo").append("<option value=''></option>");
			if (data.status){
				data.data.forEach(function(element, index) {
					// console.log("elemento: "+element+" indice:"+index);
					$("#empleadocargo").append("<option value='"+element.CARGO_ID+"''>"+element.CARGO_DESCRIPCION+"</option>");
				});
			}
			else {
				swal("Error",data.message,"error");
			}
			// $("#empleadocargo").addClass("selectpicker");
    	},
    	error: function(){
    		swal("Error","Algo malio sal!","error");
    	}
    });	
});

$("#formEmpleados").submit(function(e){
	e.preventDefault();
	var datos = $("#formEmpleados").serialize();
	// console.log(datos);
	$.ajax({
		url: base_url+'Empleados/setEmpleado',
		type: 'POST',
		dataType: 'json',
		data: datos,
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
			}
		}
	});
});
function editarEmpleado(id){
	$("#empleadosModalCenterTitle").html("Editar Empleado");
    $(".modal-header").addClass("headerUpdate").removeClass("headerRegister"); 
    $("#btnText").html("Actualizar");
    $("#btnGuardar").addClass("btn-info").removeClass("btn-primary");
    $.ajax({
    	url: base_url+'Empleados/getEmpleado/'+id,
    	type: 'GET',
    	dataType: 'json',
    	success: function(data){
    		console.log(data);
    		if (data.status){
    			$("#empleado_id").val(data.data.EMPLEADO_ID);
    			$("#empleadodni").val(data.data.DNI);
    			$("#empleadofechanac").val(data.data.FECHA_NACIMIENTO);
    			$("#empleadonombre").val(data.data.NOMBRE);
    			$("#empleadoapellido").val(data.data.APELLIDO);
    			$("#empleadocuil").prop({
    			// 	hidden: 'true',
    				disabled: true,
    				readonly: true
    			});
    			$("#empleadocuil").val(data.data.CUIL);
    			$("#empleadopassword").prop({
    			// 	hidden: 'true',
    				disabled: true,
    				readonly: true
    			});
    			// $("#empleadopassword").val(data.data.CONTRASENA);
    			$("#empleadomail").val(data.data.MAIL);
    			$("#empleadotelefono").val(data.data.TELEFONO);
    			$("#empleadocargo").val(data.data.CARGO_ID).trigger("change");
    			$("#empleadodireccion").val(data.data.DIRECCION);
    			$("#empleadosModalCenter").modal("show");
    		}
    		else {
    			swal("Atención!",data.message,"error");
    		}
    	}
    });
}
function verEmpleado(id){
	$.ajax({
		url: base_url+'Empleados/getEmpleado/'+id,
		type: 'GET',
		dataType: 'json',
		success: function(data){
			console.log(data);
			if (data.status){
				$("#tblDNI").html(data.data.DNI);
				$("#tblNombre").html(data.data.NOMBRE);
				$("#tblApellido").html(data.data.APELLIDO);
				$("#tblFechanac").html(data.data.FECHA_NACIMIENTO);
				$("#tblCUIL").html(data.data.CUIL);
				$("#tblTelefono").html(data.data.TELEFONO);
				$("#tblMail").html(data.data.MAIL);
				$("#tblDireccion").html(data.data.DIRECCION);
				$("#tblCargo").html(data.data.CARGO_DESCRIPCION);
				$("#tblSucursal").html(data.data.CODIGO_SUCURSAL);
				$("#empleadosVerModalCenter").modal("show");
			}
			else {
				swal("Atención!",data.message,"error");
			}
		}
	});
}
function borrarEmpleado(id){
	swal({
        title: "Eliminar Empleado",
        text: "¿Quiere eliminar al Empleado?",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then(function(isConfirm){
        if(isConfirm){
            $.ajax({
                type: "POST",
                url: base_url+"Empleados/delEmpleado/",
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
function openModal(){
	$("#empleado_id").val("");
    $("#empleadosModalCenterTitle").html("Nuevo Empleado");
    $(".modal-header").addClass("headerRegister").removeClass("headerUpdate"); 
    $("#btnText").html("Guardar");
    $("#btnGuardar").addClass("btn-primary").removeClass("btn-info");
    $("#empleadocuil").prop({
	// 	hidden: 'true',
		disabled: false,
		readonly: false
	});
    $("#empleadopassword").prop({
	// 	hidden: 'true',
		disabled: false,
		readonly: false
	});
    $("#formEmpleados").trigger("reset");
    $("#empleadosModalCenter").modal("show");
}

function openModalPerfil(){
	$("#perfilModalCenter").modal("show");
}

$("#empleadocuil").inputmask('99-99999999-9', {
  placeholder: '__-________-_'
});

$("#empleadocuil").keyup(function(event) {
	// alert("holi");
	// alert(event.which);
	// let regex = /[1-9]/gi;
	if (event.which == 13){
    	event.preventDefault();
  	}
	if (event.which >= 48 && event.which <=57){
		console.log(event.which);
	}
	else {
		this.value = this.value.replace(/[^0-9]/, '');
	}
});
