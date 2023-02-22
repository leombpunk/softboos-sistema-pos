var tablaFalopa;
var tablaFalopa2;
const carrito = [];
const totales = {
    total: 0.00,
    subtotal: 0.00,
    iva: 0.00
};

$(document).ready(function () {
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
        var data = $(this).serialize();
        console.log(data);
        // $.ajax({
        //     type: "POST",
        //     url: base_url+"Proveedores/setProveedor/",
        //     data: data,
        //     dataType: "json",
        //     success: function (response) {
        //         if (response.status){
        //             tabalFalopa.ajax.reload();
        //             $("#proveedoresModalCenter").modal("hide");
        //             swal("Bien!",response.message,"success");
        //         }
        //         else {
        //             swal("Atención!",response.message,"error");
        //         }
        //     }
        // });
    });
});
function openModal(){
    $("#prductosBuscarModalCenter").modal("show");
}
function agregarProducto(id){
    // console.log({id:id});
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
                $("#detalleVentaTableBody").append("<tr id='item-"+id+"'></tr>");
                //boton
                $("#item-"+id).append("<td class='text-center'><button type='button' onclick='eliminarItem(`"+id+"`)' class='btn btn-sm btn-danger'><i class='fa fa-trash'></i></button></td>");
                //codigo
                $("#item-"+id).append("<td class='text-center align-middle'>"+data.data.cod+"</td>");
                //descripcion - nombre
                $("#item-"+id).append("<td class='align-middle'>"+data.data.nom+"</td>");
                //iva
                $("#item-"+id).append("<td class='text-center align-middle'>"+data.data.ivaporcent+"</td>");
                //cantidad
                $("#item-"+id).append("<td class='text-center align-middle'>"+inputcantidad+"</td>");
                //unidad medida
                $("#item-"+id).append("<td class='text-center align-middle'>"+data.data.rnom+"</td>");
                //precio
                $("#item-"+id).append("<td class='text-center align-middle'>"+data.data.precioventa+"</td>");
                //total (cantidad*precio)
                $("#item-"+id).append("<td class='text-right align-middle'>"+(parseFloat(inputcantidad)*parseFloat(data.data.precioventa))+"</td>");

                //sumar total, subtotal e iva
                totales.total += parseFloat(inputcantidad)*parseFloat(data.data.precioventa);
                totales.iva += (parseFloat(inputcantidad)*parseFloat(data.data.precioventa))/parseFloat(data.data.ivaporcent);
                totales.subtotal = totales.total - totales.iva; 
                // console.log({totales});
                //asignar
                $("#subtotal").children().eq(1).text(totales.subtotal.toFixed(2));
				$("#totaliva").children().eq(1).text(totales.iva.toFixed(2));
				$("#total").children().eq(1).text(totales.total.toFixed(2));

                //armo el carrito
                carrito.push({
                    id: parseInt(id),
                    cod: data.data.cod,
                    cantidad: parseFloat(inputcantidad),
                    iva: (parseFloat(inputcantidad)*parseFloat(data.data.precioventa))/parseFloat(data.data.ivaporcent),
                    total: parseFloat(inputcantidad)*parseFloat(data.data.precioventa)
                });
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
            message: "el input de cantidad esta vacio",
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
    console.log(carrito);
    //usar el get de producto
}
function eliminarItem(itemId){
    // console.log({itemId: itemId});
    //restar total, subtotal, e iva
    const {iva, total} = carrito.find((item) => {
        // console.log(item);
        if (item.id == itemId) {
            // console.log({item:item});
            return item;
        }
    });

    //restar total, subtotal e iva
    totales.total -= parseFloat(total);
    totales.iva -= parseFloat(iva);
    totales.subtotal = totales.total - totales.iva; 
    console.log({totales});
    //asignar
    $("#subtotal").children().eq(1).text(totales.subtotal.toFixed(2));
    $("#totaliva").children().eq(1).text(totales.iva.toFixed(2));
    $("#total").children().eq(1).text(totales.total.toFixed(2));
    //remover
    $("#item-"+itemId).remove();
}
function verVenta(id){
    $("#ventasVerModalCenter").modal("show");
    // console.log({id:id})
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
// function editarProveedor(id){
//     $("#proveedoresModalCenterTitle").html("Editar Proveedor");
//     $(".modal-header").addClass("headerUpdate").removeClass("headerRegister"); 
//     $("#btnText").html("Actualizar");
//     $("#btnGuardar").addClass("btn-info").removeClass("btn-primary");
//     $.ajax({
//     	url: base_url+'Proveedores/getProveedor/'+id,
//     	type: 'GET',
//     	dataType: 'json',
//     	success: function(data){
//     		console.log(data);
//     		if (data.status){
//     			$("#proveedor_id").val(data.data.PROVEEDOR_ID);
//     			$("#proveedorrazonSocial").val(data.data.RAZONSOCIAL);
//     			$("#proveedorcuit").val(data.data.CUIT);
//     			$("#proveedorweb").val(data.data.WEB);
//     			$("#proveedormail").val(data.data.MAIL);
//     			$("#proveedortelefono").val(data.data.TELEFONO);
//     			$("#proveedordireccion").val(data.data.DIRECCION);
//     			$("#proveedoresModalCenter").modal("show");
//     		}
//     		else {
//     			swal("Atención!",data.message,"error");
//     		}
//     	}
//     });
// }
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