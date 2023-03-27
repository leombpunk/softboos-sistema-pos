<div class="modal fade" id="combosAgregarInsumoModalCenter" tabindex="-1" role="dialog" aria-labelledby="combosAgregarInsumoModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header headerRegister">
                <h5 class="modal-title" id="combosAgregarInsumoModalCenterTitle"><i class="fa fa-cube" aria-hidden="true"></i> Agregar Insumo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formInsumo" name="formInsumo" class="needs-validation">
                <div class="modal-body">
                    <input type="hidden" id="insumo_id" name="insumo_id" value="">
                    <div class="form-group">
                        <label for="insumonombre" class="control-label">Insumo</label>
                        <input type="text" class="form-control" id="insumonombre" disabled>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="insumounidadmedida">Unidad de Medida</label>
                        <select class="form-control" name="insumounidadmedida" id="insumounidadmedida" disabled required>
                            
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="insumocantidad">Cantidad</label>
                        <input id="insumocantidad" name="insumocantidad" class="form-control" type="number" placeholder="cantidad de insumo" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button id="btnAgregarInsumo" type="submit" class="btn btn-primary"><i class="fa fa-plus" aria-hidden="true"></i> Agregar</button>
                </div>
            </form>
        </div>
    </div>
</div>