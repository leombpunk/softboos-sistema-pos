var tabalFalopa;
//estructura de los ingredientes
const ingredientesList = [];
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
var indiceInsumo = 0;
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
                    console.log(response);
                    if (response.status){
                        $("#combosModalCenter").modal("hide");
                        $("#formCombo").trigger("reset");
                        swal("Resultado",response.message,"success");
                        tabalFalopa.ajax.reload(function(){});
                    }
                    else {
                        swal("Error",response.message+" "+response.expected,"error");
                    }
                },
                error: function(error){
                    console.log(error);
                }
            });
        }
    });
    $("#formInsumo").submit(function(e){
        e.preventDefault();
        //capturar y validar!!!
        var idInsumo = $("#insumo_id").val();
        var nombreInsumo = $("#insumonombre").val();
        var idUniMedida = $("#insumounidadmedida").val();
        var unidadNombre = $("#insumounidadmedida option:selected").text();
        var cantidad = $("#insumocantidad").val();
        console.log(unidadNombre);
        if (cantidad > 0 || cantidad < 0) {
            //hacer el append
            $("#tbodyInsumo").append("<tr id='item-"+indiceInsumo+"'></tr>");
            //descripcion - nombre
            $("#item-"+indiceInsumo).append("<td class='align-middle'>"+nombreInsumo+"</td>");
            //unidad medida
            $("#item-"+indiceInsumo).append("<td class='text-center align-middle'>"+unidadNombre+"</td>");
            //cantidad
            $("#item-"+indiceInsumo).append("<td class='text-center align-middle'>"+cantidad+"</td>");
            //boton
            $("#item-"+indiceInsumo).append("<td class='text-center'><button type='button' onclick='eliminarItem(`"+idInsumo+"`,`"+indiceInsumo+"`)' class='btn btn-sm btn-danger'><i class='fa fa-trash'></i></button></td>");
            ingredientesList.push({
                idInsumo: parseInt(idInsumo),
                nombreInsumo: nombreInsumo,
                idUnidadMedida: parseInt(idUniMedida),
                nombreUnidadMedida: "",
                cantidad: cantidad
            });
            indiceInsumo++;
            $("#combosAgregarInsumoModalCenter").modal("hide");
            $.notify({
                title: "Bien! ",
                message: "El insumo se agrego a la lista",
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
        }
        else {
            $.notify({
                title: "Error! ",
                message: "La cantidad esta vacia",
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
    });
    //cambia el valor del input de codigo producto (idproducto)
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
    $("#combonombre").attr("disabled",true)
    $("#combodescripcion").attr("disabled",true)
    $("#comboestado").attr("disabled",true)
    $("#comboaddingrediente").attr("disabled",true)
    $("#button-addon2").attr("disabled",true)
    $("#btnGuardar").attr("disabled",true)
    //vaciar tabla cuerpo
    $("#tbodyInsumo").html("")
    //vaciar array de ingredientes
    ingredientesList.splice(0,ingredientesList.length)
    
    $("#combosModalCenter").modal("show");
}
function editarCombo(id){
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
function borrarCombo(id){
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

function openModal2(){ //abre el modal de agregar insumo
    // console.log(this);
    //evaluar que "comboaddingrediente" tenga un valor
    let ingredienteId = $("#comboaddingrediente").val();
    if (ingredienteId != ""){
        if(ingredientesList.find((item) => item.idInsumo == ingredienteId)){
            swal("Atención!","El insumo ya fue agregado a la lista","error");
        }
        else {
            //darle los datos al modal "combosAgregarInsumoModalCenter"
            const insumo = insumosList.find((item) => item.id == ingredienteId);
            //como unidad de medida no es un array voy a simular uno
            const unidadMedida = [];
            unidadMedida.push({'id':insumo.umid, 'nombre':insumo.umnom});
            //fin simular
            console.log(insumo);
            $("#formInsumo").trigger("reset");
            $("#insumo_id").val(insumo.id);
            $("#insumonombre").val(insumo.nom);
            unidadMedida.forEach(element => {
                $("#insumounidadmedida").append('<option value="'+element.id+'">'+element.nombre+'</option>');
            });
            $("#combosAgregarInsumoModalCenter").modal("show");
            // $("#insumocantidad").focus();
        }
    } 
    else {
        swal("Atención!","No hay ningun insumo para agregar a la lista","error");
    }
}
function eliminarItem(itemId, iDetalle){
    $("#item-"+iDetalle).remove();
    indice = ingredientesList.findIndex((value, index, obj) => {
        if (value.productoId == itemId) return (index+1);
    });
    ingredientesList.splice(indice,1);
}
function verCombo(id){
    // $("#combosModalVerCenter").modal("show");
    $.ajax({
        type: "GET",
        url: base_url+"Combos/getComboEInsumos/"+id,
        dataType: "json",
        success: function (response) {
            console.log(response);
            if (response.status){
                $("#tblIdCombo").html(response.data.RECETA_ID);
                $("#tblNombre").html(response.data.NOMBRE);
                $("#tblDescripcion").html(response.data.DESCRIPCION);
                $("#tblIdMercaderia").html(response.data.MERCADERIA_ID);
                $("#tblMercaNombre").html(response.data.mercanom);
                $("#tblEstado").html(response.data.estado);
                //foreach y append
                $("#tbodyInsumoVer").html("");
                response.data.insumos.forEach(element => {
                    console.log(element);
                    $("#tbodyInsumoVer").append("<tr><td>"+element.id+"</td><td>"+element.nombre+"</td><td style='text-align: center;'>"+parseFloat(element.cantidad)+"</td><td style='text-align: center;'>"+element.umnombre+"</td></tr>");
                });
                $("#combosModalVerCenter").modal("show");
            }
            else {
                swal("Atención!",response.message,"error");
            }
        }
    });
}