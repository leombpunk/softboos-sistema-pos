<div class="modal fade" id="udMedidasModalCenter" tabindex="-1" role="dialog" aria-labelledby="udMedidasModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header headerRegister" id="modalH">
                <h5 class="modal-title" id="udMedidasModalCenterTitle"><i class="fa fa-briefcase" aria-hidden="true"></i> Nuevo Rubro</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formUdMedidas" name="formUdMedidas" class="needs-validation">
                <div class="modal-body">
                    <input type="hidden" id="udMedida_id" name="udMedida_id" value="">
                    <div class="form-row">
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label" for="udMedidanombre">Unidad de Medida</label>
                                <input id="udMedidanombre" name="udMedidanombre" class="form-control" type="text" placeholder="Nombre de la Unidad de Medida" required>
                                <div class="invalid-feedback">Complete este campo!</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label" for="udMedidaabr">Abrevitura</label>
                                <input type="text" name="udMedidaabr" id="udMedidaabr" class="form-control" placeholder="Abreviatura (ej.: Litro = L)" required>
                                <div class="invalid-feedback">Complete este campo!</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <div class="form-group">
                                <label for="udMedidaequal">Equivalencia</label>
                                <select name="udMedidaequal" id="udMedidaequal" class="form-control"></select>
                                <div class="invalid-feedback">Complete este campo!</div>
                            </div>
                            <!-- <p class="form-text text-muted"><i class="fa fa-info"></i> Equivalencia: Si usted desea dar el alta una unidad de medida referenciada seleccione un elemento de la lista. Ej.: 1 cajon de bebidas es = a 8 unidades.</p> -->
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label class="control-label" for="udMedidaval">Cantidad</label>
                                <input id="udMedidaval" name="udMedidaval" class="form-control" type="text">
                                <div class="invalid-feedback">Complete este campo!</div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="form-row">
                        <div class="form-group">
                            <p class="form-text text-muted"><i class="fa fa-info"></i> Equivalencia: Si usted desea dar el alta una unidad de medida referenciada seleccione un elemento de la lista. Ej.: 1 cajon de bebidas es = a 8 unidades.</p>
                        </div>
                    </div> -->
                    <div class="form-group">
                        <label class="control-label" for="udMedidaestado">Estado</label>
                        <select class="form-control" name="udMedidaestado" id="udMedidaestado" required>
                            <option value="1" selected>Activo</option>
                            <option value="2">Inactivo</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <p class="form-text text-muted"><i class="fa fa-info"></i> Equivalencia: Si usted desea dar el alta una unidad de medida referenciada seleccione un elemento de la lista. Ej.: 1 cajon de bebidas es = a 8 unidades.</p>
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