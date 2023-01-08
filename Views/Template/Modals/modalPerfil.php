<div class="modal fade" id="perfilModalCenter" tabindex="-1" role="dialog" aria-labelledby="perfilModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header headerUpdate">
                <h5 class="modal-title" id="perfilModalCenterTitle"><i class="fa fa-user" aria-hidden="true"></i> Modificar Datos Personales</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formPerfil" name="formPerfil" class="needs-validation">
                <div class="modal-body">
                	<p>Los campos con asteríco (<span class="required">*</span>) son obligatorios.</p>
                    <input type="hidden" id="perfil_id" name="perfil_id" value="">
                    <div class="form-row">
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label" for="perfildni">DNI <span class="required">*</span></label>
                                <input id="perfildni" name="perfildni" class="form-control" type="text" placeholder="Documento Nacional de Identidad" value="<?= $_SESSION["userDATA"]["DNI"]; ?>" required>
                                <div class="invalid-feedback">Complete este campo!</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label" for="perfilfechanac">Fecha Nacimiento <span class="required">*</span></label>
                                <input id="perfilfechanac" name="perfilfechanac" class="form-control" type="date" placeholder="dd/mm/aaaa" value="<?= $_SESSION["userDATA"]["FECHA_NACIMIENTO"]; ?>" required>
                                <div class="invalid-feedback">Complete este campo!</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label" for="perfilnombre">Nombres <span class="required">*</span></label>
                                <input id="perfilnombre" name="perfilnombre" class="form-control" type="text" placeholder="Nombres" value="<?= $_SESSION["userDATA"]["NOMBRE"]; ?>" required>
                                <div class="invalid-feedback">Completperfilapellidoe este campo!</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label" for="perfilapellido">Apellidos <span class="required">*</span></label>
                                <input id="perfilapellido" name="perfilapellido" class="form-control" type="text" placeholder="Apellidos" value="<?= $_SESSION["userDATA"]["APELLIDO"]; ?>" required>
                                <div class="invalid-feedback">Complete este campo!</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label" for="perfilmail">E-mail</label>
                                <input id="perfilmail" name="perfilmail" class="form-control" type="text" placeholder="Correo Electrónico" value="<?= $_SESSION["userDATA"]["MAIL"]; ?>">
                                <div class="invalid-feedback">Completperfilapellidoe este campo!</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label" for="perfiltelefono">Telefono</label>
                                <input id="perfiltelefono" name="perfiltelefono" class="form-control" type="text" placeholder="Telefono/Celular" value="<?= $_SESSION["userDATA"]["TELEFONO"]; ?>">
                                <div class="invalid-feedback">Complete este campo!</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label" for="">Direccion</label>
                                <textarea id="perfildireccion" name="perfildireccion" class="form-control" maxlength="100" placeholder="Escriba el domicilio aquí por favor..."><?= $_SESSION["userDATA"]["DIRECCION"]; ?></textarea>
                                <div class="invalid-feedback">Complete este campo!</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button id="btnGuardar" type="submit" class="btn btn-primary"><i class="fa fa-save" aria-hidden="true"></i> <span id="btnText">Actualizar</span></button>
                </div>
            </form>
        </div>
    </div>
</div>