var tablaFalopa;
$(document).ready(function(){
    tablaFalopa = $("#udMedidasTable").DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": {
            "url": base_url+"Assets/Spanish.json"
        },
        "ajax": {
            "url": base_url+"UdMedidas/getUdMedidas",
            "dataSrc":"" 
        },
        "columns": [
            { "data": "id" },
            { "data": "cantidad1" },
            { "data": "de" },
            { "data": "equal" },
            { "data": "val" },
            { "data": "de2" },
            { "data": "tipo" },
            { "data": "estado" },
            { "data": "actions" }
        ],
        "responsive": true,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0,"asc"]]
        // dom: 'Blfrtip',
        // buttons: [
        //     'copyHtml5',
        //     'excelHtml5',
        //     'csvHtml5',
        //     'pdfHtml5'
        // ]
    });
    getUdMedidaBase();
});
function getUdMedidaBase(){
    $.ajax({
        type: "POST",
        url: base_url+"UdMedidas/getUMBase",
        dataType: "json",
        success: function (response) {
            $("#udMedidaequal").html("");
            console.log(response);
            if (response.status){
                $("#udMedidaequal").append("<option value='' selected>Seleccione...</option>");
                response.data.forEach(function(element, index) {
                    $("#udMedidaequal").append("<option value='"+element.id+"'>"+element.de+"</option>");
                });
            }
            else {
                $("#udMedidaequal").append("<option value=''>No hay unidades de medida</option>");
            }
        }
    });
}
$("#formUdMedidas").submit(function(e){
    e.preventDefault();
    var data = $(this).serialize();
    console.log(data);
    $.ajax({
        type: "POST",
        url: base_url+"UdMedidas/setUdMedida/",
        data: data,
        dataType: "json",
        success: function(response){
            // console.log(response);
            if (response.status){
                tablaFalopa.ajax.reload(function(){
                    getUdMedidaBase();
                });
                $("#udMedidasModalCenter").modal("hide");
                swal("Bien hecho!",response.message,"success");
                
            }
            else {
                swal("Atencion!",response.message,"error");
            }
        }
    });
});
function openModal(){
	$("#udMedida_id").val("");
    $("#udMedidasModalCenterTitle").html("Nueva Unidad de Medida");
    $(".modal-header").addClass("headerRegister").removeClass("headerUpdate"); 
    $("#btnText").html("Guardar");
    $("#btnGuardar").addClass("btn-primary").removeClass("btn-info");
    $("#formUdMedidas").trigger("reset");
    $("#udMedidasModalCenter").modal("show");
}
function verUdMedida(id){
    $.ajax({
		url: base_url+'UdMedidas/getUdMedida/'+id,
		type: 'GET',
		dataType: 'json',
        // contentType: false,
        // cache: false,
        // processData: false,
		success: function(data){
			// console.log(data);
			if (data.status){
				$("#udMedidasVerModalCenter").modal("show");
			}
			else {
				swal("Atención!",data.message,"error");
			}
		}
	});
}
function editarUdMedida(id){
    $("#udMedidasModalCenterTitle").html("Editar Unidad de Medida");
    $(".modal-header").addClass("headerUpdate").removeClass("headerRegister"); 
    $("#btnText").html("Actualizar");
    $("#btnGuardar").addClass("btn-info").removeClass("btn-primary");
    $.ajax({
        type: "GET",
        url: base_url+"UdMedidas/getUdMedida/"+id,
        dataType: "json",
        success: function (response) {
            // console.log(response);
            if (response.status){
                $("#udMedida_id").val(response.data.id);
                $("#udMedidanombre").val(response.data.de);
                $("#udMedidaabr").val(response.data.abr);
                // $("#udMedidaequal").html("");
                $("#udMedidaequal").val(response.data.unid).trigger("change");
                $("#udMedidaval").val(response.data.val);
                $("#udMedidaestado").val(response.data.estado).trigger("change");
                $("#udMedidasModalCenter").modal("show");
            }
            else {
                swal("Atencion!",response.message,"error");
            }
        }
    });
}
function borrarUdMedida(id){
    swal({
        title: "Eliminar Unidad de Medida",
        text: "¿Quiere eliminar la Unidad de Medida?",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then(function(isConfirm){
        if(isConfirm){
            $.ajax({
                type: "POST",
                url: base_url+"UdMedidas/delUdMedida/",
                data: "id="+id,
                dataType: "json",
                success: function (response) {
                    if(response.status){
                        swal("Eliminar!",response.message,"success");
                        tablaFalopa.ajax.reload(function(){
                            getUdMedidaBase();
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