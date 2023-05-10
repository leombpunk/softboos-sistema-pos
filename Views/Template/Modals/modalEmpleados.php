<div class="modal fade" id="empleadosModalCenter" tabindex="-1" role="dialog" aria-labelledby="empleadosModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header headerRegister">
                <h5 class="modal-title" id="empleadosModalCenterTitle"><i class="fa fa-user" aria-hidden="true"></i> Nuevo Empleado</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEmpleados" name="formEmpleados" class="needs-validation">
                <div class="modal-body">
                    <p class="form-text text-muted"><i class="fa fa-info"></i> Los campos con asteriscos (<span class="required">*</span>) son obligatorios.</p>
                    <input type="hidden" id="empleado_id" name="empleado_id" value="">
                    <div class="form-row">
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label" for="empleadodni">DNI <span class="required">*</span></label>
                                <input id="empleadodni" name="empleadodni" class="form-control" type="text" placeholder="Documento Nacional de Identidad" maxlength="8" required>
                                <div class="invalid-feedback">Complete este campo!</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label" for="empleadofechanac">Fecha de Nacimiento <span class="required">*</span></label>
                                <input id="empleadofechanac" name="empleadofechanac" class="form-control" type="date" placeholder="dd/mm/aaaa" required>
                                <div class="invalid-feedback">Complete este campo!</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label" for="empleadonombre">Nombres <span class="required">*</span></label>
                                <input id="empleadonombre" name="empleadonombre" class="form-control" type="text" placeholder="Nombres" maxlength="50" required>
                                <div class="invalid-feedback">Completempleadoapellidoe este campo!</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label" for="empleadoapellido">Apellidos <span class="required">*</span></label>
                                <input id="empleadoapellido" name="empleadoapellido" class="form-control" type="text" placeholder="Apellidos" maxlength="50" required>
                                <div class="invalid-feedback">Complete este campo!</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label" for="empleadocuil">CUIL (Usuario) <span class="required">*</span></label>
                                <input id="empleadocuil" name="empleadocuil" class="form-control" type="text" placeholder="Código Único de Identificación Laboral" maxlength="13" required>
                                <div class="invalid-feedback">Complete este campo!</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label" for="empleadopassword">Contraseña <span class="required">*</span></label>
                                <input id="empleadopassword" name="empleadopassword" class="form-control" type="password" placeholder="Contraseña" maxlength="16" required>
                                <div class="invalid-feedback">Complete este campo!</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label" for="empleadomail">E-mail</label>
                                <input id="empleadomail" name="empleadomail" class="form-control" type="email" placeholder="Correo Electrónico" maxlength="50">
                                <div class="invalid-feedback">Completempleadoapellidoe este campo!</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label" for="empleadotelefono">Telefono</label>
                                <input id="empleadotelefono" name="empleadotelefono" class="form-control" type="text" placeholder="Telefono/Celular" maxlength="20">
                                <div class="invalid-feedback">Complete este campo!</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label" for="empleadocargo">Cargo <span class="required">*</span></label>
                                <select class="form-control" id="empleadocargo" name="empleadocargo" required>
                                </select>
                                <div class="invalid-feedback">Complete este campo!</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label" for="empleadoestado">Estado <span class="required">*</span></label>
                                <select class="form-control" name="empleadoestado" id="empleadoestado" required>
                                    <option value="1" selected>Activo</option>
                                    <option value="2">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label" for="empleadodireccion">Direccion</label>
                                <input  type="text" id="empleadodireccion" name="empleadodireccion" class="form-control" maxlength="100" placeholder="Escriba el domicilio aquí por favor..." />
                                <div class="invalid-feedback">Complete este campo!</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label" for="empleadosucursal">Sucursal</label>
                                <select name="empleadosucursal" id="empleadosucursal" class="form-control" required>

                                </select>
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