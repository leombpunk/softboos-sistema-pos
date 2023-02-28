<div class="modal fade" id="ventasVerModalCenter" tabindex="-1" role="dialog" aria-labelledby="ventasVerModalCenterTitle" data-backdrop="static" data-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ventasVerModalCenterTitle"><i class="fa fa-file-text-o" aria-hidden="true"></i> Datos de la Factura de Venta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center" id="loaderDiv">
                    <span class="h5"><img src="<?= media(); ?>/images/uploads/loader.gif" alt="Cargando"> <em>Cargando...</em></span>
                </div>
                <div id="dataDivTables" style="display: none;">
                    <div class="table-responsive">
                        <table class="table" id="ventasCabeceraTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th colspan="5" class="h4"> Factura de Venta</th>
                                    <th class="h4">Emitida: <span>[No date]</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><img class="img-fluid img-thumbnail" src="<?= media(); ?>/images/uploads/logo-icon2.png" alt="logo" style="width: 120px; heigth: 120px;"></td>
                                    <td class="font-weight-bold align-middle h5" id="nombreSucursal">[No name]</td>
                                    <td class="font-weight-bold align-middle">Factura N°</td>
                                    <td class="align-middle" id="numeroFacturaV">[No number]</td>
                                    <td class="font-weight-bold align-middle">Forma de Pago</td>
                                    <td class="align-middle">
                                        <select class="form-control" name="formaPago" id="formaPago" disabled>
                                            <option value="1" selected>Efectivo</option>
                                            <option value="2">Debito</option>
                                            <option value="3">Credito</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-right align-middle h5">Cliente ID:</td>
                                    <td colspan="5">
                                        <input class="form-control" type="text" list="" id="cliente" name="cliente" autocomplete="off" role="combobox" disabled>
                                        <datalist id="clientList" role="listbox">
                                        </datalist>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-right align-middle h6">DNI:</td>
                                    <td class="align-middle">
                                        <input type="text" class="form-control" id="dniCliente" name="dniCliente" disabled>
                                    </td>
                                    <td class="font-weight-bold text-right align-middle h6">Nombre:</td>
                                    <td class="align-middle" colspan="4">
                                        <input type="text" class="form-control" id="nombreCliente" name="nombreCliente" disabled>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="ventasDetalleTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th class="align-middle h5" colspan="7">Detalle</th>
                                </tr>
                            </thead>
                            <tbody id="detalleVentaTableBody">
                                <tr>
                                    <th class="text-center align-middle">#</th>
                                    <th class="align-middle">Descripcion</th>
                                    <th class="text-center align-middle">IVA</th>
                                    <th class="text-center align-middle">Cantidad</th>
                                    <th class="text-center align-middle">Unidad Medida</th>
                                    <th class="text-center align-middle">Precio</th>
                                    <th class="text-center align-middle">Total</th>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr id="subtotal">
                                    <td class="font-weight-bold text-right align-middle h6" colspan="6">Subtotal: $</td>
                                    <td class="text-right align-middle h6">0,00</td>
                                </tr>
                                <tr id="totaliva">
                                    <td class="font-weight-bold text-right align-middle h6" colspan="6">T. IVA: $</td>
                                    <td class="text-right align-middle h6">0,00</td>
                                </tr>
                                <tr id="total">
                                    <td class="font-weight-bold text-right align-middle h6" colspan="6">Total: $</td>
                                    <td class="text-right align-middle h6">0,00</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>