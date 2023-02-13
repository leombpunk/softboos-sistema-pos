<div class="modal fade" id="clientesModalCenter" tabindex="-1" role="dialog" aria-labelledby="clientesModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header headerRegister">
                <h5 class="modal-title" id="clientesModalCenterTitle"><i class="fa fa-user" aria-hidden="true"></i> Nuevo Empleado</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formClientes" name="formClientes" class="needs-validation">
                <div class="modal-body">
                    <p class="form-text text-muted"><i class="fa fa-info"></i> Los campos con asteriscos (<span class="required">*</span>) son obligatorios.</p>
                    <input type="hidden" id="cliente_id" name="cliente_id" value="">
                    <div class="form-row">
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label" for="clientedni">DNI <span class="required">*</span></label>
                                <input id="clientedni" name="clientedni" class="form-control" type="text" placeholder="Documento Nacional de Identidad" required>
                                <div class="invalid-feedback">Complete este campo!</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label" for="clientefechanac">Fecha Nacimiento <span class="required">*</span></label>
                                <input id="clientefechanac" name="clientefechanac" class="form-control" type="date" placeholder="dd/mm/aaaa" required>
                                <div class="invalid-feedback">Complete este campo!</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label" for="clientenombre">Nombres <span class="required">*</span></label>
                                <input id="clientenombre" name="clientenombre" class="form-control" type="text" placeholder="Nombres" required>
                                <div class="invalid-feedback">Completclienteapellidoe este campo!</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label" for="clienteapellido">Apellidos <span class="required">*</span></label>
                                <input id="clienteapellido" name="clienteapellido" class="form-control" type="text" placeholder="Apellidos" required>
                                <div class="invalid-feedback">Complete este campo!</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label" for="clientemail">E-mail</label>
                                <input id="clientemail" name="clientemail" class="form-control" type="text" placeholder="Correo Electrónico">
                                <div class="invalid-feedback">Completclienteapellidoe este campo!</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label" for="clientetelefono">Telefono</label>
                                <input id="clientetelefono" name="clientetelefono" class="form-control" type="text" placeholder="Telefono/Celular">
                                <div class="invalid-feedback">Complete este campo!</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label" for="clientecuil">CUIL</label>
                                <input id="clientecuil" name="clientecuil" class="form-control" type="text" placeholder="Código Único de Identificación Laboral">
                                <div class="invalid-feedback">Complete este campo!</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label" for="clienteestado">Estado</label>
                                <select class="form-control" name="clienteestado" id="clienteestado" required>
                                    <option value="1" selected>Activo</option>
                                    <option value="2">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label" for="">Direccion</label>
                                <textarea id="clientedireccion" name="clientedireccion" class="form-control" maxlength="100" placeholder="Escriba el domicilio aquí por favor..."></textarea>
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