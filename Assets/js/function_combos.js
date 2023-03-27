var tabalFalopa;
//estructura de los ingredientes
const ingredientesList = [{
    idInsumo: 0,
    idUnidadMedida: 0,
    cantidad: 0.00
},];
//estructura del combo
const combo = {
    idCombo: 0,
    idMercaderia: 0,
    nombre: "",
    descripcion: "",
    estado: 0,
    ingredientes: ingredientesList
}
const insumosList = [];
$(document).ready(function () {
    tabalFalopa = $("#sampleTable").DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": {
            "url": base_url+"Assets/Spanish.json"
        },
        "ajax": {
            "url": base_url+"Combos/getCombos",
            "dataSrc":""},
        "columns": [
            { "data": "RECETA_ID" },
            { "data": "NOMBRE" },
            { "data": "FECHA_ALTA" },
            { "data": "estado" },
            { "data": "actions" }
        ],
        "responsive": true,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0,"asc"]]
    });
    $("#formCombo").submit(function (e) { 
        e.preventDefault();
        //varificar que todos los campos tienen datos
        let idCombo = $("#combo_id").val();
        let idProducto = $("#combocodproducto").val();
        let nombre = $("#combonombre").val();
        let estado = $("#comboestado").val();
        let descripcion = $("#combodescripcion").val();//puede ser vacio
        if (idProducto == "" || nombre == "" || estado == "" || ingredientesList.length == 0) {
            swal("Atención","Verifique los campos obligatorios","error");
            return false;
        }
        else {
            combo.idCombo = idCombo;
            combo.idMercaderia = idProducto;
            combo.nombre = nombre;
            combo.estado = estado;
            combo.descripcion = descripcion;
            console.log(combo);
            $.ajax({
                type: "POST",
                url: base_url+"Combos/setCombo",
                data: combo,
                dataType: "json",
                success: function (response) {
                    // console.log(response);
                    if (response.status){
                        $("#combosModalCenter").modal("hide");
                        $("#formCombo").trigger("reset");
                        swal("Resultado",response.message,"success");
                        tabalFalopa.ajax.reload(function(){});
                    }
                    else {
                        swal("Error",response.message+" "+response.expected,"error");
                    }
                }
            });
        }
    });
    //cambia el valor del input de codigo
    $("#combocodproducto").on("change", function(){
        let value = $("#combocodproducto").val();
        console.log("ola q ase!");
        if(value != 0 || value > 0){
            console.log("no está vacío")
            $("#combonombre").removeAttr("disabled")
            $("#combodescripcion").removeAttr("disabled")
            $("#comboestado").removeAttr("disabled")
            $("#comboaddingrediente").removeAttr("disabled")
            $("#button-addon2").removeAttr("disabled")
            $("#btnGuardar").removeAttr("disabled")
        }
        else {
            console.log("yo que se")
            $("#combonombre").attr("disabled",true)
            $("#combodescripcion").attr("disabled",true)
            $("#comboestado").attr("disabled",true)
            $("#comboaddingrediente").attr("disabled",true)
            $("#button-addon2").attr("disabled",true)
            $("#btnGuardar").attr("disabled",true)
        }
    });
    $.ajax({
        type: "GET",
        dataType: "json",
        url: base_url+"Productos/getSoloInsumos",
        success: function (response) {
            // console.log(response);
            response.forEach(element => {
                // console.log(element);
                $("#listingredientes").append('<option value="'+element.id+'">'+element.id+' | '+element.nom+'</option>');
                insumosList.push(element);
            });
            // insumosList.push(response[0]);
            // console.log(insumosList);
        }
    });
    $.ajax({
        type: "GET",
        dataType: "json",
        url: base_url+"Productos/getSoloProductos",
        success: function (response) {
            // console.log(response);
            response.forEach(element => {
                // console.log(element);
                $("#listproductos").append('<option value="'+element.id+'">'+element.id+' | '+element.nom+'</option>');
            });
        }
    });
});
function openModal(){
    $("#combosModalCenterTitle").html("Nuevo Combo");
    $(".modal-header").addClass("headerRegister").removeClass("headerUpdate"); 
    $("#btnText").html("Guardar");
    $("#btnGuardar").addClass("btn-primary").removeClass("btn-info");
    $("#formCombo").trigger("reset");
    $("#combo_id").val("");
    $("#combosModalCenter").modal("show");
}
function editarComboo(id){
    $("#combosModalCenterTitle").html("Editar Combo");
    $(".modal-header").addClass("headerUpdate").removeClass("headerRegister"); 
    $("#btnText").html("Actualizar");
    $("#btnGuardar").addClass("btn-info").removeClass("btn-primary");
    $.ajax({
        type: "GET",
        url: base_url+"Combos/getCombo/"+id,
        dataType: "json",
        success: function (response) {
            // console.log(response);
            if (response.status){
                $("#combo_id").val(response.data.FORMAPAGO_ID);
                $("#combonombre").val(response.data.FORMA_PAGO);
                $("#comboestado").val(response.data.ESTADO_ID).trigger("change");
                $("#combosModalCenter").modal("show");
            }
            else {
                swal("Error",response.message+" "+response.expected,"error");
            }
        }
    });
}
function borrarComboo(id){
    swal({
        title: "Eliminar Combo",
        text: "¿Quiere eliminar el Combo?",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then(function(isConfirm){
        if(isConfirm){
            $.ajax({
                type: "POST",
                url: base_url+"Combos/delCombo/",
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