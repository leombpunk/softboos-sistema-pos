var sampleTable;
$(document).ready(function(){
    sampleTable = $("#rubrosTable").DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": {
            "url": base_url+"Assets/Spanish.json"
        },
        "ajax": {
            "url": base_url+"Rubros/getRubros",
            "dataSrc":""},
        "columns": [
            { "data": "id" },
            { "data": "de" },
            { "data": "fa" },
            { "data": "estado" },
            { "data": "actions" }
        ],
        "responsive": true,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0,"asc"]],
        // dom: 'Bfrtip',
        // buttons: [
        //     'copyHtml5',
        //     'excelHtml5',
        //     'csvHtml5',
        //     'pdfHtml5'
        // ]
    });
});
$("#formRubros").submit(function(e){
    e.preventDefault();
    let data = new FormData(this);
    // console.log(data);
    $.ajax({
        type: "POST",
        url: base_url+"Rubros/setRubro/",
        data: data,
        dataType: "json",
        contentType: false,
        cache: false,
        processData: false,
        success: function(response){
            // console.log(response);
            if (response.status){
                sampleTable.ajax.reload();
                $("#rubrosModalCenter").modal("hide");
                swal("Bien hecho!",response.message,"success");
            }
            else {
                swal("Atencion!",response.message,"error");
            }
        },
        error: function (error){
            console.log(error)
        }
    });
});
$("#rubroimg").change(function(e){ 
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
    // if ($("#modalH").hasClass("headerRegister")){
        // $("#load_img").toggle(function(){
            $("#load_img1").slideDown();
        // });
    // }
});
function openModal(){
	$("#rubro_id").val("");
    $("#rubrosModalCenterTitle").html("Nuevo Rubro");
    $(".modal-header").addClass("headerRegister").removeClass("headerUpdate"); 
    $("#btnText").html("Guardar");
    $("#btnGuardar").addClass("btn-primary").removeClass("btn-info");
    // $("#load_img").toggle(function(){
        $("#load_img1").slideUp();
    // });
    $("#formRubros").trigger("reset");
    $("#rubrosModalCenter").modal("show");
}
function verRubro(id){
    $.ajax({
		url: base_url+'Rubros/getRubro/'+id,
		type: 'GET',
		dataType: 'json',
        contentType: false,
        cache: false,
        processData: false,
		success: function(data){
			console.log(data);
			if (data.status){
				$("#tblID").html(data.data.id);
				$("#tblNombre").html(data.data.de);
				$("#tblFechaalta").html(data.data.fa);
				$("#tblImagen").html('<img class="img-fluid" src="Assets/'+data.data.img+'" alt="Rubro">');
                // '<img class="img-fluid" src="<?= media(); ?>'+data.data.img+'" alt="Rubro">'
				$("#rubrosVerModalCenter").modal("show");
			}
			else {
				swal("Atención!",data.message,"error");
			}
		},
        error: function (error){
            console.log(error)
        }
	});
}
function editarRubro(id){
    $("#rubrosModalCenterTitle").html("Editar Rubro");
    $(".modal-header").addClass("headerUpdate").removeClass("headerRegister"); 
    $("#btnText").html("Actualizar");
    $("#btnGuardar").addClass("btn-info").removeClass("btn-primary");
    // $("#load_img").toggle(function(){
        $("#load_img1").slideUp();
    // });
    $.ajax({
        type: "GET",
        url: base_url+"Rubros/getRubro/"+id,
        dataType: "json",
        success: function (response) {
            console.log(response);
            if (response.status){
                $("#rubro_id").val(response.data.id);
                $("#rubronombre").val(response.data.de);
                $("#rubroestado").val(response.data.est).trigger("change");
                $("#load_img1").prop({src: "Assets/"+response.data.img});
                // $("#load_img").toggle(function(){
                    $("#load_img1").slideDown();
                // });
                $("#rubrosModalCenter").modal("show");
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
function borrarRubro(id){
    swal({
        title: "Eliminar Rubro",
        text: "¿Quiere eliminar el Rubro?",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then(function(isConfirm){
        if(isConfirm){
            $.ajax({
                type: "POST",
                url: base_url+"Rubros/delRubro/",
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
function restaurarRubro(id){
    swal({
        title: "Restaurar Rubro",
        text: "¿Quiere restaurar el rubro?",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then(function(isConfirm){
        if (isConfirm) {
            $.ajax({
                type: "POST",
                url: base_url+"Rubro/setRestaurar",
                dataType: "json",
                data: "idRubro="+id,
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