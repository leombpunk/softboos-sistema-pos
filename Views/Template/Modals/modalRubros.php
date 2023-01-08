<div class="modal fade" id="rubrosModalCenter" tabindex="-1" role="dialog" aria-labelledby="rubrosModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header headerRegister" id="modalH">
                <h5 class="modal-title" id="rubrosModalCenterTitle"><i class="fa fa-briefcase" aria-hidden="true"></i> Nuevo Rubro</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formRubros" name="formRubros" class="needs-validation">
                <div class="modal-body">
                    <input type="hidden" id="rubro_id" name="rubro_id" value="">
                    <div class="form-group">
                        <label class="control-label" for="rubronombre">Rubro</label>
                        <input id="rubronombre" name="rubronombre" class="form-control" type="text" placeholder="Nombre del Rubro" required>
                        <div class="invalid-feedback">Complete este campo!</div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="rubroestado">Estado</label>
                        <select class="form-control" name="rubroestado" id="rubroestado" required>
                            <option value="1" selected>Activo</option>
                            <option value="2">Inactivo</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="rubroimg" class="control-label">Seleccione una imagen para el rubro</label>
                        <!-- <input type="file" name="rubroimg" id="rubroimg" class="filestyle" data-buttonText="Imagen" accept="image/*">
                        <div id="load_img">
	                        <img class="img-responsive" alt="Imagen para el Rubro">
				        </div> -->
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="rubroimg" id="rubroimg" accept="image/*">
                            <label class="custom-file-label" for="rubroimg" data-browse="Buscar">Seleccione una imagen</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div id="load_img">
                            <img id="load_img1" class="img-fluid" src="<?= media(); ?>images/uploads/album-icon.jpg" alt="Imagen para el Rubro">
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