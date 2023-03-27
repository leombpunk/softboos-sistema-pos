<div class="modal fade" id="combosModalCenter" tabindex="-1" role="dialog" aria-labelledby="combosModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header headerRegister">
                <h5 class="modal-title" id="combosModalCenterTitle"><i class="fa fa-cubes" aria-hidden="true"></i> Nuevo Combo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formCombo" name="formCombo" class="needs-validation">
                <div class="modal-body">
                    <input type="hidden" id="combo_id" name="combo_id" value="">
                    <div class="form-group">
                        <label for="" class="control-label">Producto</label>
                        <input type="text" class="form-control" id="combocodproducto" list="listproductos" required>
                        <datalist id="listproductos">
                            <!-- <option value="0">0 | Vacio</option>
                            <option value="1">1 | Uno</option> -->
                        </datalist>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="combonombre">Nombre</label>
                        <input id="combonombre" name="combonombre" class="form-control" type="text" placeholder="Nombre del combo" disabled required>
                    </div>
                    <div class="form-group">
                        <label for="combodescripcion" class="control-label">Descripcion</label>
                        <textarea name="combodescripcion" id="combodescripcion" class="form-control" disabled></textarea>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="comboestado">Estado</label>
                        <select class="form-control" name="comboestado" id="comboestado" disabled required>
                            <option value="1" selected>Activo</option>
                            <option value="2">Inactivo</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="comboaddingrediente">Ingredientes</label>
                        <div class="input-group mb-3">
                            <input id="comboaddingrediente" name="comboaddingrediente" list="listingredientes" type="text" class="form-control" placeholder="Buscar ingrediente" aria-label="Recipient's username" aria-describedby="button-addon2" disabled>
                            <datalist id="listingredientes">
                                <!-- <option value="0">0 | Vacio</option> -->
                            </datalist>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="button-addon2" disabled>Agregar</button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Unidad Medida</th>
                                        <th>Cantidad</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>mamadas</td>
                                        <td>unidad</td>
                                        <th>5</th>
                                        <th><button>borrar</button></th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button id="btnGuardar" type="submit" class="btn btn-primary" disabled><i class="fa fa-save" aria-hidden="true"></i> <span id="btnText">Guardar</span></button>
                </div>
            </form>
        </div>
    </div>
</div>