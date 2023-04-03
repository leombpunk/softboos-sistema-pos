<?php headerAdmin($data);
getModal("modalProductosBuscar",$data); ?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fa fa-shopping-cart" aria-hidden="true"></i> <?= $data['page_title']; ?></h1>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url(); ?>nuevaVenta"><?= $data['page_title']; ?></a></li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <form id="formNuevaVenta" name="formNuevaVenta">
                        <div class="table-responsive">
                            <table class="table" id="ventasCabeceraTable">
                                <thead class="thead-dark">
                                    <tr>
                                        <th colspan="5" class="h4"> Factura de Venta</th>
                                        <th class="h4"><?= date('d/m/Y'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><img class="img-fluid img-thumbnail" src="<?= media();?>/images/uploads/logo-icon2.png" alt="logo" style="width: 120px; heigth: 120px;"></td>
                                        <td class="font-weight-bold align-middle h5" id="nombreSucursal"><?= $_SESSION['userDATA']['RAZONSOCIAL']; ?></td>
                                        <td class="font-weight-bold align-middle">Factura N°</td>
                                        <td class="align-middle" id="numeroFacturaV"></td>
                                        <td class="font-weight-bold align-middle">Forma de Pago</td>
                                        <td class="align-middle">
                                            <select class="form-control" name="formaPago" id="formaPago">
                                                <!-- <option value="1" selected>Efectivo</option>
                                                <option value="2">Debito</option>
                                                <option value="3">Credito</option> -->
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold text-center align-middle h5">Cliente ID:</td>
                                        <td colspan="5">
                                            <input class="form-control" type="text" list="" id="cliente" name="cliente" autocomplete="off" role="combobox">
                                            <datalist id="clientList" name="clientList" role="listbox">
                                                <!-- <option value="99">Varios</option>
                                                <option value="1">Cliente 1</option>
                                                <option value="2">Cliente 2</option> -->
                                            </datalist>
                                        </td>
                                    </tr>
                                    <tr hidden>
                                        <td class="font-weight-bold text-right align-middle h6">DNI:</td>
                                        <td class="align-middle">99999999</td>
                                        <td class="font-weight-bold text-right align-middle h6">Nombre:</td>
                                        <td class="align-middle" colspan="4">XXXXXXXXXXXXX</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="table-responsive">
                            <table class="table" id="ventasDetalleTable">
                                <!-- <caption>Detalle</caption> -->
                                <thead class="thead-dark">
                                    <tr>
                                        <th class="align-middle h5" colspan="7">Detalle</th>
                                        <th class="text-right align-middle h5"><button onclick="openModal();" type="button" class="btn btn-primary"><i class="fa fa-plus"></i> Añadir</button></th>
                                    </tr>
                                </thead>
                                <tbody id="detalleVentaTableBody">
                                    <tr>
                                        <th class="text-center align-middle">Acciones</th>
                                        <th class="text-center align-middle">#</th>
                                        <th class="align-middle">Descripcion</th>
                                        <th class="text-center align-middle">IVA</th>
                                        <th class="text-center align-middle">Cantidad</th>
                                        <th class="text-center align-middle">Unidad Medida</th>
                                        <th class="text-center align-middle">Precio</th>
                                        <th class="text-center align-middle">Total</th>
                                    </tr>
                                    <!-- <tr>
                                        <td class="text-center align-middle"><button type="button" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button></td>
                                        <td class="text-center align-middle">1</td>
                                        <td class="align-middle">item 1</td>
                                        <td class="text-center align-middle">10,5</td>
                                        <td class="text-center align-middle">3</td>
                                        <td class="text-center align-middle">unidad</td>
                                        <td class="text-center align-middle">10</td>
                                        <td class="text-right align-middle">30</td>
                                    </tr> -->
                                </tbody>
                                <tfoot>
                                    <tr id="subtotal">
                                        <td class="font-weight-bold text-right align-middle h6" colspan="7">Subtotal: $</td>
                                        <td class="text-right align-middle h6">0,00</td>
                                    </tr>
                                    <tr id="totaliva">
                                        <td class="font-weight-bold text-right align-middle h6" colspan="7">T. IVA: $</td>
                                        <td class="text-right align-middle h6">0,00</td>
                                    </tr>
                                    <tr id="total">
                                        <td class="font-weight-bold text-right align-middle h6" colspan="7">Total: $</td>
                                        <td class="text-right align-middle h6">0,00</td>
                                    </tr>
                                    <tr>
                                        <td colspan="8" class="text-right align-middle"><button type="submit" class="btn btn-primary">Finalizar</button></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
<?php footerAdmin($data); ?>