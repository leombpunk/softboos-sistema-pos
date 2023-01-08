<div class="modal fade" id="cargosModalCenter" tabindex="-1" role="dialog" aria-labelledby="cargosModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header headerRegister">
                <h5 class="modal-title" id="cargosModalCenterTitle"><i class="fa fa-briefcase" aria-hidden="true"></i> Nuevo Cargo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formCargos" name="formCargos" class="needs-validation">
                <div class="modal-body">
                    <input type="hidden" id="cargo_id" name="cargo_id" value="">
                    <div class="form-group">
                        <label class="control-label" for="cargonombre">Cargo</label>
                        <input id="cargonombre" name="cargonombre" class="form-control" type="text" placeholder="Nombre del Cargo" required>
                        <!-- <div class="valid-feedback">Looks good!</div> -->
                        <div class="invalid-feedback">Complete este campo!</div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="">Nivel de Acceso</label>
                        <select class="form-control" name="cargonacceso" id="cargonacceso" required>
                        </select>
                        <small><p class="form-text text-muted"><i class="fa fa-info"></i> El campo "Nivel de Acceso" hace referencia a la gerarquia de la empresa con respecto a los cargos de sus empleados. Donde el numero mas alto es el de mayor rango y el numero mas pequeño de menor rango gerarquico.</p></small>
                        <div class="invalid-feedback">Complete este campo!</div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="cargoestado">Estado</label>
                        <select class="form-control" name="cargoestado" id="cargoestado" required>
                            <option value="1" selected>Activo</option>
                            <option value="2">Inactivo</option>
                        </select>
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