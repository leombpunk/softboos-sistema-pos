<div class="modal fade" id="permisosModalCenter" tabindex="-1" role="dialog" aria-labelledby="permisosModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header headerRegister">
                <h5 class="modal-title" id="permisosModalCenterTitle"><i class="fa fa-briefcase" aria-hidden="true"></i> Permisos del Cargo "<span id="spanCargo" name="spanCargo"></span>"</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formPermisos" name="formPermisos" class="needs-validation">
                <div class="modal-body">
                    <input type="hidden" id="cargo_id_permiso" name="cargo_id_permiso" value="">
                    <table class="table table-sm table-bordered" id="tablePermisos">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Modulo</th>
                                <th class="text-center">Leer/Ver</th>
                                <th class="text-center">Agregar</th>
                                <th class="text-center">Modificar</th>
                                <th class="text-center">Eliminar</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyTablePermisos">
                            
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button id="btnGuardarPermiso" type="submit" class="btn btn-primary"><i class="fa fa-save" aria-hidden="true"></i> Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>