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
            "url": base_url+"Productos/getProductosFactura",
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
        console.log(factura);
        //completar la constante factura con los datos que falten, cliente-formapago-etc
        $.ajax({
            type: "POST",
            url: base_url+"Ventas/setVenta/",
            data: factura,
            dataType: "json",
            success: function (response) {
                if (response.status){
                    console.log(response);
                    swal("Bien!",response.message,"success");
                }
                else {
                    // swal("Atención!",response.message,"error");
                    console.log(response.message);
                }
            }
        });
    });
    nombreSucursal(1);
    numeroFactura();
    cargarClientes();
});
function cargarClientes(){
    //vamos a probar
    let clienteDataList = document.getElementById('clientList');
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
    let numeroFactura = document.getElementById('numeroFacturaV');
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
function nombreSucursal(sucursalId){
    let nombreSucursal = document.getElementById("nombreSucursal");
    nombreSucursal.innerHTML = "Negocio de Falopa";

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
    console.log({indiceDetalle:indiceDetalle});
    console.log(factura);
}
function eliminarItem(itemId, iDetalle){
    // console.log({itemId: itemId});
    //restar total, subtotal, e iva
    const {iva, total} = factura.detalle.find((item) => {
        console.log({item:item});
        if (item.productoId == itemId) {
            console.log({item:item});
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
        console.log({value: value, index: index, obj: obj});
        if (value.productoId == itemId) return (index+1);
        // return (value.productoId == itemId) && index;
    });
    console.log({indice:indice});
    factura.detalle.splice(indice,1); //para quitar un elemento no me sirve el iDetalle
    console.log(factura);
    // indiceDetalle--;
    // console.log(indiceDetalle);
}
function verVenta(id){
    $("#ventasVerModalCenter").modal("show");
    console.log({id:id});
    //cabecera de la factura de venta
    // $.ajax({
    //     type: "GET",
    //     url: base_url+"Ventas/getVenta/"+id,
    //     dataType: "json",
    //     success: function (response) {
    //         console.log(response);
    //         if (response.status){
    //             $("#tblRazonSocial").html(response.data.RAZONSOCIAL);
    //             $("#tblCUIT").html(response.data.CUIT);
    //             $("#tblTelefono").html(response.data.TELEFONO);
    //             $("#tblMail").html(response.data.MAIL);
    //             $("#tblDireccion").html(response.data.DIRECCION);
    //             $("#tblWeb").html(response.data.WEB);
    //             $("#tblFechaalta").html(response.data.FECHA_ALTA);
    //             $("#tblEstado").html(response.data.ESTADO);
    //             $("#proveedoresVerModalCenter").modal("show");
    //         }
    //         else {
    //             swal("Atención!",response.message,"error");
    //         }
    //     }
    // });
    //para el detalle de la factura
    // $.ajax({
    //     type: "GET",
    //     url: base_url+"Ventas/getVenta/"+id,
    //     dataType: "json",
    //     success: function (response) {
    //         console.log(response);
    //         if (response.status){
    //             $("#tblRazonSocial").html(response.data.RAZONSOCIAL);
    //             $("#tblCUIT").html(response.data.CUIT);
    //             $("#tblTelefono").html(response.data.TELEFONO);
    //             $("#tblMail").html(response.data.MAIL);
    //             $("#tblDireccion").html(response.data.DIRECCION);
    //             $("#tblWeb").html(response.data.WEB);
    //             $("#tblFechaalta").html(response.data.FECHA_ALTA);
    //             $("#tblEstado").html(response.data.ESTADO);
    //             $("#proveedoresVerModalCenter").modal("show");
    //         }
    //         else {
    //             swal("Atención!",response.message,"error");
    //         }
    //     }
    // });
}
// function borrarVenta(id){
//     swal({
//         title: "Eliminar Proveedor",
//         text: "¿Quiere eliminar al Proveedor?",
//         icon: "warning",
//         buttons: true,
//         dangerMode: true,
//     }).then(function(isConfirm){
//         if(isConfirm){
//             $.ajax({
//                 type: "POST",
//                 url: base_url+"Proveedores/delProveedor/",
//                 data: "id="+id,
//                 dataType: "json",
//                 success: function (response) {
//                     if(response.status){
//                         swal("Eliminar!",response.message,"success");
//                         tabalFalopa.ajax.reload(function(){});
//                     }
//                     else {
//                         swal("Atencion!",response.message,"error");
//                     }
//                 }
//             });
//         }
//     });
// }