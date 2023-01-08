<div class="modal fade" id="productosModalCenter" tabindex="-1" role="dialog" aria-labelledby="productosModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header headerRegister">
                <h5 class="modal-title" id="productosModalCenterTitle"><i class="fa fa-user" aria-hidden="true"></i> Nuevo Producto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label>Imagenes <span class="required">*</span></label>
                            <form id="myDropzonePe" class="dropzone text-center" method="POST" enctype="multipart/form-data" action="asd">
                                <div class="dz-message">Arrastre las imagenes aqui o haga click para subirlas<br><small class="text-info">(Formatos de imagenes soportadas: .png, .jpg, .jpeg)</small></div>
                            </form>
                        </div>
                    </div>
                </div> -->
                <form id="formProductos" name="formProductos">
                    <input class="formPro" type="hidden" id="producto_id" name="producto_id" value="">
                    <!-- <div class="row"> -->
                        <!-- <div class="col-6"> -->
                            <div class="row">
                                <div class="form-group col">
                                    <label class="control-label">Nombre <span class="required">*</span></label>
                                    <input class="form-control formPro" type="text" id="productonombre" name="productonombre" placeholder="Ingrese nombre del producto">
                                </div>
                                <div class="form-group col">
                                    <label class="control-label" for="productocodigo">Codigo <span class="required">*</span></label>
                                    <input id="productocodigo" name="productocodigo" class="form-control formPro" type="text" placeholder="Ingrese el codigo del producto" maxlength="8" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col">
                                    <label class="control-label">Rubro <span class="required">*</span></label>
                                    <select class="form-control formPro" name="productorubro" id="productorubro"></select>
                                </div>
                                <div class="form-group col">
                                    <label class="control-label" for="productoudmedida">Unidad de Medida (Base) <span class="required">*</span></label>
                                    <select class="form-control formPro" name="productoudmedida" id="productoudmedida"></select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label class="control-label" for="productocantmin">Cantidad Minima <span class="required">*</span></label>
                                        <input id="productocantmin" name="productocantmin" class="form-control formPro" type="text" placeholder="Nombres" maxlength="50" required>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label class="control-label" for="productocantmax">Cantidad Maxima <span class="required">*</span></label>
                                        <input id="productocantmax" name="productocantmax" class="form-control formPro" type="text" placeholder="Apellidos" maxlength="50" required>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label class="control-label" for="productopcosto">Precio de Costo <span class="required">*</span></label>
                                        <input id="productopcosto" name="productopcosto" class="form-control formPro" type="text" placeholder="Ej.: 1000.00" maxlength="8" required>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label class="control-label" for="productopventa">Precio de Venta <span class="required">*</span></label>
                                        <input id="productopventa" name="productopventa" class="form-control formPro" type="text" placeholder="Ej.: 1000.00" maxlength="8" required>
                                    </div>
                                </div>
                            </div> -->
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label class="control-label" for="productoiva">IVA (%) <span class="required">*</span></label>
                                        <select name="productoiva" id="productoiva" class="form-control formPro"></select>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label class="control-label" for="productoestado">Estado <span class="required">*</span></label>
                                        <select class="form-control formPro" name="productoestado" id="productoestado" required>
                                            <option value="1" selected>Activo</option>
                                            <option value="2">Inactivo</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="">Se usará como un Insumo/Combo? <span class="required">*</span></label>
                                        <div class="col-md-9">
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    <input class="form-check-input formPro" type="radio" name="productoinsumo" required>Si
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    <input class="form-check-input" type="radio" name="productoinsumo">No
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                        <!-- </div> -->
                        <!-- <div class="col-6">
                            <div class="row">
                                <div class="form-group col">
                                    <label class="control-label" for="productodescripcion">Descripcion <span class="required">*</span></label>
                                    <textarea id="productodescripcion" name="productodescripcion" class="form-control formPro" maxlength="100" placeholder="Desarrolle la descripcion del producto"></textarea>
                                </div>
                            </div>
                        </div> -->
                    <!-- </div> -->
                    <small>
                        <p class="form-text text-muted"><i class="fa fa-info"></i> Los campos con asteriscos (<span class="required">*</span>) son obligatorios.</p>
                    </small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button id="btnGuardar" type="submit" class="btn btn-primary"><i class="fa fa-save" aria-hidden="true"></i> <span id="btnText">Guardar</span></button>
            </div>
            </form>
        </div>
    </div>
</div>