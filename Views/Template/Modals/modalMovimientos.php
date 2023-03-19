<div class="modal fade" id="movimientosModalCenter" tabindex="-1" role="dialog" aria-labelledby="movimientosModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header headerRegister">
                <h5 class="modal-title" id="movimientosModalCenterTitle"><i class="fa fa-briefcase" aria-hidden="true"></i> Nuevo Gasto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formMovimientos" name="formMovimientos" class="needs-validation">
                <div class="modal-body">
                    <input type="hidden" id="movimiento_id" name="movimiento_id" value="">
                    <div class="form-group">
                        <label class="control-label" for="movimientoDescripcion">Descripcion</label>
                        <input id="movimientoDescripcion" name="movimientoDescripcion" class="form-control" type="text" placeholder="Breve descripcion del movimiento" required>
                        <!-- <div class="valid-feedback">Looks good!</div> -->
                        <div class="invalid-feedback">Complete este campo!</div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="movimientoTipo">Tipo</label>
                        <select class="form-control" name="movimientoTipo" id="movimientoTipo" required>
                            <option value="0" selected>Seleccionar...</option>
                            <option value="3" hidden>SALDO INICIAL</option>
                            <option value="1">INGRESO</option>
                            <option value="2">EGRESO</option>
                        </select>
                        <div class="invalid-feedback">Complete este campo!</div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="movimientoMonto">Monto</label>
                        <input class="form-control" name="movimientoMonto" id="movimientoMonto" type="number" placeholder="Ingrese un Monto (dinero)" required>
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