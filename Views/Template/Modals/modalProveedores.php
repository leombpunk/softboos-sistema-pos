<div class="modal fade" id="proveedoresModalCenter" tabindex="-1" role="dialog" aria-labelledby="proveedoresModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header headerRegister">
                <h5 class="modal-title" id="proveedoresModalCenterTitle"><i class="fa fa-user" aria-hidden="true"></i> Nuevo Proveedor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formProveedores" name="formProveedores" class="needs-validation">
                <div class="modal-body">
                    <p class="form-text text-muted"><i class="fa fa-info"></i> Los campos con asteriscos (<span class="required">*</span>) son obligatorios.</p>
                    <input type="hidden" id="proveedor_id" name="proveedor_id" value="">
                    <div class="form-row">
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label" for="proveedorrazonSocial">Razon Social <span class="required">*</span></label>
                                <input id="proveedorrazonSocial" name="proveedorrazonSocial" class="form-control" type="text" placeholder="Razon Social" required>
                                <div class="invalid-feedback">Complete este campo!</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label" for="proveedorcuit">CUIT <span class="required">*</span></label>
                                <input id="proveedorcuit" name="proveedorcuit" class="form-control" type="text" placeholder="Clave Única de Identificación Tributaria" required>
                                <div class="invalid-feedback">Complete este campo!</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label" for="proveedormail">E-mail</label>
                                <input id="proveedormail" name="proveedormail" class="form-control" type="text" placeholder="Correo Electrónico">
                                <div class="invalid-feedback">Complete este campo!</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label" for="proveedortelefono">Telefono</label>
                                <input id="proveedortelefono" name="proveedortelefono" class="form-control" type="text" placeholder="Telefono/Celular">
                                <div class="invalid-feedback">Complete este campo!</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label" for="proveedorweb">Web</label>
                                <input id="proveedorweb" name="proveedorweb" class="form-control" type="text" placeholder="Direccion Web">
                                <div class="invalid-feedback">Complete este campo!</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label" for="proveedorestado">Estado</label>
                                <select class="form-control" name="proveedorestado" id="proveedorestado" required>
                                    <option value="1" selected>Activo</option>
                                    <option value="2">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label" for="proveedordireccion">Direccion</label>
                                <textarea id="proveedordireccion" name="proveedordireccion" class="form-control" maxlength="100" placeholder="Escriba el domicilio aquí por favor..."></textarea>
                                <div class="invalid-feedback">Complete este campo!</div>
                            </div>
                        </div> 
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button id="btnGuardar" type="submit" class="btn btn-primary"><i class="fa fa-save" aria-hidden="true"></i> <span id="btnText">Guardar</span></button>
                </div>
            </form>
        </div>
    </div>
</div>