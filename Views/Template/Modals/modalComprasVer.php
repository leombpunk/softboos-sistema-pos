<div class="modal fade" id="comprasVerModalCenter" tabindex="-1" role="dialog" aria-labelledby="comprasVerModalCenterTitle" data-backdrop="static" data-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="comprasVerModalCenterTitle"><i class="fa fa-file-text-o" aria-hidden="true"></i> Datos de la Factura de Compra</h5>
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
                        <table class="table" id="comprasCabeceraTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th colspan="5" class="align-middle h4"> Factura de Compra</th>
                                    <th class="h4" >
                                        <span id="fechaEmision"></span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="font-weight-bold align-middle h5">
                                        <label for="proveedorId">Proveedor</label>
                                    </td>
                                    <td class="font-weight-bold align-middle h5">
                                        <input type="text" name="proveedorId" id="proveedorId" list="" autocomplete="off" role="combobox" class="form-control" placeholder="Seleccione un proveedor" disabled>
                                        <datalist id="proveedorList" name="proveedorList" role="listbox">

                                        </datalist>
                                    </td>
                                    <td class="font-weight-bold align-middle">
                                        <label for="numeroFacturaC">Factura N°</label>
                                    </td>
                                    <td class="align-middle">
                                        <input type="number" name="numeroFacturaC" id="numeroFacturaC" class="form-control" placeholder="Numero de factura" disabled>
                                    </td>
                                    <td class="font-weight-bold align-middle">
                                        <label for="formaPago">Forma de Pago</label>
                                    </td>
                                    <td class="align-middle">
                                        <select class="form-control" name="formaPago" id="formaPago" disabled>

                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-center align-middle h5">Sucursal ID:</td>
                                    <td colspan="5">
                                        <input class="form-control" type="text" list="" id="sucursalId" name="sucursalId" autocomplete="off" role="combobox" placeholder="Seleccione una sucursal" disabled>
                                        <datalist id="sucursalList" name="sucursalList" role="listbox">

                                        </datalist>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-right align-middle h6">Sucursal:</td>
                                    <td class="align-middle">
                                        <input type="text" class="form-control" id="numeroSucursal" name="numeroSucursal" disabled>
                                    </td>
                                    <td class="font-weight-bold text-right align-middle h6">Nombre:</td>
                                    <td class="align-middle" colspan="4">
                                        <input type="text" class="form-control" id="nombreSucursal" name="nombreSucursal" disabled>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="comprasDetalleTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th class="align-middle h5" colspan="7">Detalle</th>
                                </tr>
                                <tr>
                                    <th class="text-center align-middle">#Código</th>
                                    <th class="align-middle">Descripcion</th>
                                    <th class="text-center align-middle">IVA</th>
                                    <th class="text-center align-middle">Cantidad</th>
                                    <th class="text-center align-middle">Unidad Medida</th>
                                    <th class="text-center align-middle">Precio</th>
                                    <th class="text-center align-middle">Total</th>
                                </tr>
                            </thead>
                            <tbody id="detalleCompraTableBody">
                                
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