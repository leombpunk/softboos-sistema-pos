var tablaFalopa;
var tablaFalopa2;
var indiceDetalle = 0;
const factura = {
    clienteId: 0,
    formaPagoId: 0,
    total: 0.00,
    iva: 0.00,
    subtotal: 0.00,
    detalle: []
}
$(document).ready(function () {
    //faltan cargar los clientes, traer el siguiente numero de factura y el nombre del negocio (traerlo de la tabla sucursal 1 y fue)
    tablaFalopa = $("#ventasTable").DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": {
            "url": base_url+"Assets/Spanish.json"
        },
        "ajax": {
            "url": base_url+"Ventas/getVentas",
            "dataSrc": ""
        },
        "columns": [
            { "data": "NUMERO_FACTURA" },
            { "data": "FECHA_EMISION" },
            { "data": "FORMAPAGO" },
            { "data": "TOTAL" },
            { "data": "actions" }
        ],
        "responsive": true,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0,"asc"]]
    });

    tablaFalopa2 = $("#buscadorProductoTable").DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": {
            "url": base_url+"Assets/Spanish.json"
        },
        "ajax": {
            "url": base_url+"Productos/getProductosFacturaVenta",
            "dataSrc": ""
        },
        "columns": [
            { "data": "cod" },
            { "data": "nom" },
            { "data": "umnom" },
            { "data": "precioventa" },
            { "data": "cant" },
            { "data": "action" }
        ],
        "responsive": true,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0,"asc"]]
    });

    $("#formNuevaVenta").submit(function(e){
        e.preventDefault();
        // var data = $(this).serialize();
        factura.clienteId = cliente.value;
        factura.formaPagoId = formaPago.value;
        // console.log(factura);
        //completar la constante factura con los datos que falten, cliente-formapago-etc
        $.ajax({
            type: "POST",
            url: base_url+"Ventas/setVenta/",
            data: factura,
            dataType: "json",
            success: function (response) {
                if (response.status){
                    console.log(response);
                    swal("Bien!",response.message,"success").then(function(isConfirm){
                        window.location = base_url+"ventas";
                    });
                }
                else {
                    swal("Atención!",response.message,"error");
                    console.log(response.message);
                }
            }
        });
    });
    // sucursalData(1);
    numeroFactura();
    cargarClientes();
    cargaFormasPago();
});
function cargaFormasPago(){
    let formaPagoSelect = document.getElementById("formaPago");
    $.ajax({
        type: "GET",
        dataType: "json",
        url: base_url+"FormasPago/getFormasPagos",
        success: function(response){
            response.forEach(element => {
                formaPagoSelect.innerHTML += "<option value='"+element.FORMAPAGO_ID+"'>"+element.FORMA_PAGO +"</option>";
            });
        },
        error: function(error){
            swal("Atención!",error,"error");
        }
    });
}
function cargarClientes(){
    //vamos a probar
    let clienteDataList = document.getElementById("clientList");
    console.log({clienteDataList});
    $.ajax({
        type: "GET",
        url: base_url+"Clientes/getClientes",
        dataType: "json",
        success: function(response){
            console.log(response);
            response.forEach(element => {
                clienteDataList.innerHTML += "<option value='"+element.CLIENTE_ID+"'>"+element.NOMBRE +" "+element.APELLIDO+" (ID: "+element.CLIENTE_ID+")</option>";
            });

            for (let option of clientList.options) {
                option.onclick = function () {
                  cliente.value = option.value
                  clientList.style.display = "none"
                  cliente.style.borderRadius = "5px"
                }
            }
        }
    });
}
function numeroFactura(){
    let numeroFactura = document.getElementById("numeroFacturaV");
    $.ajax({
        type: "GET",
        url: base_url+"Ventas/getNumeroFactura",
        dataType: "json",
        success: function(response){
            console.log(response);
            numeroFactura.innerHTML = response.numFactura.toString().padStart(11-parseInt(response.numFactura),'0');
        }
    });
}
function sucursalData(sucursalId){
    let nombreSucursal = document.getElementById("nombreSucursal");
    nombreSucursal.innerHTML = "Negocio de Falopa";
    //mostrar los datos desde las variables de sesion de php
}
function openModal(){
    $("#prductosBuscarModalCenter").modal("show");
}
function agregarProducto(id){
    // console.log({id:id});
    if(factura.detalle.find((item) => {
        // console.log({item:item});
        if (item.productoId == id) {
            // console.log({item:item});
            return true;
        }
    })){
        swal("Atención!","El producto ya fue agregado en el detalle","error");
        $("#prodcutoCant"+id).val("0.0");
    } 
    else {
        var inputcantidad = $("#prodcutoCant"+id).val();
        if (inputcantidad > 0 || inputcantidad < 0) {
            //ajax aqui
            $.ajax({
                type: "GET",
                url: base_url+"Productos/getProducto/"+id,
                dataType: "json",
                success: function(data){
                    // console.log({data:data});
                    // console.log({cantidad:inputcantidad});
                    $("#prodcutoCant"+id).val("0.0");
                    $("#prductosBuscarModalCenter").modal("hide");
                    //hacer el append
                    $("#detalleVentaTableBody").append("<tr id='item-"+indiceDetalle+"'></tr>");
                    //boton
                    $("#item-"+indiceDetalle).append("<td class='text-center'><button type='button' onclick='eliminarItem(`"+id+"`,`"+indiceDetalle+"`)' class='btn btn-sm btn-danger'><i class='fa fa-trash'></i></button></td>");
                    //codigo
                    $("#item-"+indiceDetalle).append("<td class='text-center align-middle'>"+data.data.cod+"</td>");
                    //descripcion - nombre
                    $("#item-"+indiceDetalle).append("<td class='align-middle'>"+data.data.nom+"</td>");
                    //iva
                    $("#item-"+indiceDetalle).append("<td class='text-center align-middle'>"+data.data.ivaporcent+"</td>");
                    //cantidad
                    $("#item-"+indiceDetalle).append("<td class='text-center align-middle'>"+inputcantidad+"</td>");
                    //unidad medida
                    $("#item-"+indiceDetalle).append("<td class='text-center align-middle'>"+data.data.umnom+"</td>");
                    //precio
                    $("#item-"+indiceDetalle).append("<td class='text-center align-middle'>"+data.data.precioventa+"</td>");
                    //total (cantidad*precio)
                    $("#item-"+indiceDetalle).append("<td class='text-right align-middle'>"+(parseFloat(inputcantidad)*parseFloat(data.data.precioventa)).toFixed(2)+"</td>");
                    //sumar total, subtotal e iva
                    factura.total += parseFloat(inputcantidad)*parseFloat(data.data.precioventa);
                    factura.iva += (parseFloat(inputcantidad)*parseFloat(data.data.precioventa))/parseFloat(data.data.ivaporcent);
                    factura.subtotal = factura.total - factura.iva; 
                    //asignar
                    $("#subtotal").children().eq(1).text(factura.subtotal.toFixed(2));
                    $("#totaliva").children().eq(1).text(factura.iva.toFixed(2));
                    $("#total").children().eq(1).text(factura.total.toFixed(2));
                    //armo el carrito
                    factura.detalle.push({
                        productoId: parseInt(id),
                        unidadMedidaId: parseInt(data.data.umid),
                        cantidad: parseFloat(inputcantidad),
                        precio: parseFloat(data.data.precioventa),
                        iva: (parseFloat(inputcantidad)*parseFloat(data.data.precioventa))/parseFloat(data.data.ivaporcent),
                        total: parseFloat(inputcantidad)*parseFloat(data.data.precioventa)
                    });
                    indiceDetalle++;
                }
            });
            $.notify({
                title: "Bien! ",
                message: "El item se agrego a la lista",
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
                message: "El input de cantidad esta vacio",
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
    // console.log({indiceDetalle:indiceDetalle});
    // console.log(factura);
}
function eliminarItem(itemId, iDetalle){
    // console.log({itemId: itemId});
    //restar total, subtotal, e iva
    const {iva, total} = factura.detalle.find((item) => {
        // console.log({item:item});
        if (item.productoId == itemId) {
            // console.log({item:item});
            return item;
        }
    });
    factura.total -= parseFloat(total);
    factura.iva -= parseFloat(iva);
    factura.subtotal = factura.total - factura.iva; 
    // console.log({factura});
    //asignar
    $("#subtotal").children().eq(1).text(factura.subtotal.toFixed(2));
    $("#totaliva").children().eq(1).text(factura.iva.toFixed(2));
    $("#total").children().eq(1).text(factura.total.toFixed(2));
    //remover
    $("#item-"+iDetalle).remove();
    //remover de la constante factura
    indice = factura.detalle.findIndex((value, index, obj) => {
        // console.log({value: value, index: index, obj: obj});
        if (value.productoId == itemId) return (index+1);
        // return (value.productoId == itemId) && index;
    });
    // console.log({indice:indice});
    factura.detalle.splice(indice,1); //para quitar un elemento no me sirve el iDetalle
    // console.log(factura);
    // console.log(indiceDetalle);
}
function verVenta(id){
    $("#loaderDiv").show();
    $("#dataDivTables").hide();
    $("#ventasVerModalCenter").modal("show");
    //cabecera de la factura de venta
    $.ajax({
        type: "GET",
        url: base_url+"Ventas/getVenta/"+id,
        dataType: "json",
        success: function (response) {
            console.log(response);
            if (response.status){
                //poner los datos el los lugares especificos de la factura
                //cabecera
                $("#fechaEmision").html(response.data.cabecera.FECHA_EMISION);
                $("#numeroFacturaV").html(response.data.cabecera.NUMERO_FACTURA);
                $("#cliente").val(response.data.cabecera.CLIENTE_ID);
                $("#dniCliente").val(response.data.cabecera.DNI);
                $("#nombreCliente").val(response.data.cabecera.NOMBRE+" "+response.data.cabecera.APELLIDO);
                //forma pago
                $("#formaPago").val(response.data.formaPago[0].FORMAPAGO_ID).trigger("change");
                //detalles
                $("#detalleVentaTableBody").html('');
                response.data.detalle.forEach((element, index, array) => {
                    console.log(element);
                    $("#detalleVentaTableBody").append("<tr id='item-"+index+"'></tr>");
                    $("#item-"+index).append("<td class='text-center align-middle'>"+element.CODIGO+"</td>");
                    $("#item-"+index).append("<td class='align-middle'>"+element.DESCRIPCION+"</td>");
                    $("#item-"+index).append("<td class='text-center align-middle'>"+element.IVA_PORCENTAJE+"</td>");
                    $("#item-"+index).append("<td class='text-center align-middle'>"+element.CANTIDAD+"</td>");
                    $("#item-"+index).append("<td class='text-center align-middle'>"+element.UNIMEDIDA+"</td>");
                    $("#item-"+index).append("<td class='text-center align-middle'>"+element.PRECIO+"</td>");
                    $("#item-"+index).append("<td class='text-right align-middle'>"+(parseFloat(element.CANTIDAD)*parseFloat(element.PRECIO)).toFixed(2)+"</td>");
                });
                //totales
                $("#total").children().eq(1).text(parseInt(response.data.cabecera.TOTAL).toFixed(2));
                $("#totaliva").children().eq(1).text(parseInt(response.data.cabecera.IVA_TOTAL).toFixed(2));
                $("#subtotal").children().eq(1).text((response.data.cabecera.TOTAL-response.data.cabecera.IVA_TOTAL).toFixed(2));
                //crear un efecto para transicionar entro un div y otro                
                setTimeout(() => { 
                    $("#loaderDiv").hide('slow');
                    $("#dataDivTables").show('slow');
                }, 1000);
                
            }
            else {
                swal("Atención!",response.message,"error");
            }
        }
    });
}
function anularVenta(id){
    swal({
        title: "Eliminar Proveedor",
        text: "¿Quiere eliminar al Proveedor?",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then(function(isConfirm){
        if(isConfirm){
            alert("equisde, coming son");
            // $.ajax({
            //     type: "POST",
            //     url: base_url+"Proveedores/delProveedor/",
            //     data: "id="+id,
            //     dataType: "json",
            //     success: function (response) {
            //         if(response.status){
            //             swal("Eliminar!",response.message,"success");
            //             tabalFalopa.ajax.reload(function(){});
            //         }
            //         else {
            //             swal("Atencion!",response.message,"error");
            //         }
            //     }
            // });
        }
    });
}