<div class="modal fade" id="sucursalesModalCenter" tabindex="-1" role="dialog" aria-labelledby="sucursalesModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header headerRegister" id="modalH">
                <h5 class="modal-title" id="sucursalesModalCenterTitle"><i class="fa fa-building" aria-hidden="true"></i> Nueva Sucursal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formSucursales" name="formSucursales" class="needs-validation">
                <div class="modal-body">
                    <input type="hidden" id="sucursal_id" name="sucursal_id" value="">
                    <div class="row">
                        <div class="form-group col">
                            <label class="control-label" for="sucursalcodigo">Código</label>
                            <input id="sucursalcodigo" name="sucursalcodigo" class="form-control" type="text" placeholder="Código de la Sucursal" required>
                            <div class="invalid-feedback">Complete este campo!</div>
                        </div>
                        <div class="form-group col">
                            <label class="control-label" for="sucursalnombre">Sucursal</label>
                            <input id="sucursalnombre" name="sucursalnombre" class="form-control" type="text" placeholder="Nombre de la Sucursal" required>
                            <div class="invalid-feedback">Complete este campo!</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label class="control-label" for="sucursalcuit">CUIT</label>
                            <input id="sucursalcuit" name="sucursalcuit" class="form-control" type="text" placeholder="CUIT de la Sucursal">
                            <div class="invalid-feedback">Complete este campo!</div>
                        </div>
                        <div class="form-group col">
                            <label class="control-label" for="sucursaldireccion">Dirección</label>
                            <input id="sucursaldireccion" name="sucursaldireccion" class="form-control" type="text" placeholder="Dirección de la Sucursal">
                            <div class="invalid-feedback">Complete este campo!</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label class="control-label" for="sucursaltelefono">Telefono</label>
                            <input id="sucursaltelefono" name="sucursaltelefono" class="form-control" type="text" placeholder="Telefono de la Sucursal">
                            <div class="invalid-feedback">Complete este campo!</div>
                        </div>
                        <div class="form-group col">
                            <label class="control-label" for="sucursalmail">E-Mail</label>
                            <input id="sucursalmail" name="sucursalmail" class="form-control" type="email" placeholder="E-mail de la Sucursal">
                            <div class="invalid-feedback">Complete este campo!</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label class="control-label" for="sucursalweb">Web</label>
                            <input id="sucursalweb" name="sucursalweb" class="form-control" type="text" placeholder="Sitio Web de la Sucursal">
                            <div class="invalid-feedback">Complete este campo!</div>
                        </div>
                        <div class="form-group col">
                            <label class="control-label" for="sucursalestado">Estado</label>
                            <select class="form-control" name="sucursalestado" id="sucursalestado" required>
                                <option value="1" selected>Activo</option>
                                <option value="2">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="sucursalimg" class="control-label">Seleccione una imagen para el logo de la sucursal</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="sucursalimg" id="sucursalimg" accept="image/*">
                                <label class="custom-file-label" for="sucursalimg" data-browse="Buscar">Seleccione una imagen</label>
                            </div>
                        </div>
                        <div class="form-group col">
                            <div id="load_img" class="align-self-center">
                                <img id="load_img1" class="img-thumbnail" style="max-width: 200px; height: auto;" src="<?= media(); ?>images/uploads/album-icon.jpg" alt="Imagen para la Sucursal">
                            </div>
                        </div>
                    </div>
                    <p class="form-text text-muted">
                        <i class="fa fa-info"></i> 
                        Nota: si el Logo de la Sucursal no cambia, no es necesario que selecciones un logo. El sistema lo hará por tí.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button id="btnGuardar" type="submit" class="btn btn-primary"><i class="fa fa-save" aria-hidden="true"></i> <span id="btnText">Guardar</span></button>
                </div>
            </form>
        </div>
    </div>
</div>