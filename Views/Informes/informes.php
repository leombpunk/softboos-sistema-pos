<?php headerAdmin($data);
getModal("modalMovimientos",$data); ?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fa fa-line-chart" aria-hidden="true"></i> <?= $data['page_title']; ?></h1>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url(); ?>informes"><?= $data['page_title']; ?></a></li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <form id="searchForm" name="searchForm">
                        <fieldset>
                            <legend>Buscar por</legend>
                            <div class="row">
                                <div class="col-6">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <label class="input-group-text" for="fechita">Fecha</label>
                                        </div>
                                        <input type="date" id="fechita" name="fechita" class="form-control" aria-label="Selector de fecha" aria-describedby="Selector-de-fecha" required>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <label class="input-group-text" for="agrupar">Agrupar por</label>
                                        </div>
                                        <select class="form-control" name="agrupar" id="agrupar" required>
                                            <option value="">Seleccione una opción</option>
                                            <option value="1">Día</option>
                                            <option value="2">Semana</option>
                                            <option value="3">Mes</option>
                                            <option value="4">Año</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <button type="submit" title="Buscar" class="btn btn-primary"><i class="fa fa-search"></i>Buscar</button>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-sm" id="sampleTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th>DESCRIPCION</th>
                                    <th>UNIDAD MEDIDA</th>
                                    <th>CANTIDAD</th>
                                    <th>PRECIO</th>
                                    <th>MONTO</th>
                                    <th>MOVIMIENTO</th>
                                    <th>FORMA DE PAGO</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php footerAdmin($data); ?>