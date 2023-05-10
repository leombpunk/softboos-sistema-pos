<div class="modal fade" id="combosAgregarModalCenter" tabindex="-1" role="dialog" aria-labelledby="combosAgregarModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header headerRegister">
                <h5 class="modal-title" id="combosAgregarModalCenterTitle"><i class="fa fa-cube" aria-hidden="true"></i> Actualizar Cantidad Combo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formComboActualizar" name="formComboAActualizar" class="needs-validation">
                <div class="modal-body">
                    <input type="text" id="comboActualizarId" name="comboActualizarId" value="" hidden required>
                    <input type="text" id="comboMercaderiaId" name="comboMercaderiaId" value="" hidden required>
                    <input type="text" id="comboUnidadMeidaId" name="comboUnidadMeidaId" value="" hidden required>
                    <div class="form-group">
                        <label for="combitoNombre" class="control-label">Combo</label>
                        <input type="text" class="form-control" id="combitoNombre" name="combitoNombre" disabled>
                    </div>
                    <div class="form-group">
                        <label for="combitoCantidad" class="control-label">Cantidad Actual</label>
                        <input type="text" class="form-control" id="combitoCantidad" name="combitoCantidad" disabled>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="combitoAgregar">Agregar Cantidad</label>
                        <input min="1" id="combitoAgregar" name="combitoAgregar" class="form-control" type="number" placeholder="Cantidad a Agregar" required disabled>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="combitoQuitar">Quitar Cantidad</label>
                        <input min="1" id="combitoQuitar" name="combitoQuitar" class="form-control" type="number" placeholder="Cantidad a Eliminar" required disabled>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-check" aria-hidden="true"></i> Confirmar</button>
                </div>
            </form>
        </div>
    </div>
</div>