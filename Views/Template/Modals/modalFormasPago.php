<div class="modal fade" id="formasPagosModalCenter" tabindex="-1" role="dialog" aria-labelledby="formasPagosModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header headerRegister">
                <h5 class="modal-title" id="formasPagosModalCenterTitle"><i class="fa fa-briefcase" aria-hidden="true"></i> Nueva Forma de Pago</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formFormasPago" name="formFormasPago" class="needs-validation">
                <div class="modal-body">
                    <input type="hidden" id="formasPago_id" name="formasPago_id" value="">
                    <div class="form-group">
                        <label class="control-label" for="formasPagonombre">Forma de Pago</label>
                        <input id="formasPagonombre" name="formasPagonombre" class="form-control" type="text" placeholder="Nombre de la Forma de Pago" required>
                        <!-- <div class="valid-feedback">Looks good!</div> -->
                        <div class="invalid-feedback">Complete este campo!</div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="formasPagoestado">Estado</label>
                        <select class="form-control" name="formasPagoestado" id="formasPagoestado" required>
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