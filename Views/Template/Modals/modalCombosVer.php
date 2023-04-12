<div class="modal fade" id="combosModalVerCenter" tabindex="-1" role="dialog" aria-labelledby="combosModalVerCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header headerRegister">
                <h5 class="modal-title" id="combosModalVerCenterTitle"><i class="fa fa-cube" aria-hidden="true"></i> Detalles del Combo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <fieldset disabled>
                    <legend>Informacion general</legend>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="width: 170px;">Atributos</th>
                                    <th>Datos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>ID</td>
                                    <td id="tblIdCombo"></td>
                                </tr>
                                <tr>
                                    <td>Nombre</td>
                                    <td id="tblNombre"></td>
                                </tr>
                                <tr>
                                    <td>Descripcion</td>
                                    <td id="tblDescripcion"></td>
                                </tr>
                                <tr>
                                    <td>Estado</td>
                                    <td id="tblEstado"></td>
                                </tr>
                                <tr>
                                    <td colspan="2"> <strong>Datos del Producto asociado</strong></td>
                                </tr>
                                <tr>
                                    <td>ID</td>
                                    <td id="tblIdMercaderia"></td>
                                </tr>
                                <tr>
                                    <td>Producto</td>
                                    <td id="tblMercaNombre"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </fieldset>
                <fieldset disabled>
                    <legend>Insumos usados</legend>
                    <div class="table-responsive">
                        <table class="table table-striped table-sm" id="TablaInsumoVer">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th style="width: 50%;">Nombre</th>
                                    <th style="width: 25%; text-align: center;">Cantidad</th>
                                    <th style="width: 25%; text-align: center;">Unidad Medida</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyInsumoVer">
                                
                            </tbody>
                        </table>
                    </div>
                </fieldset>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>