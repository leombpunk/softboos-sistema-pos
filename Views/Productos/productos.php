<?php headerAdmin($data);
getModal("modalProductos", $data);
getModal("modalProductosVer", $data); ?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fa fa-barcode" aria-hidden="true"></i> <?= $data['page_title']; ?> <button class="btn btn-primary" type="button" onclick="openModal();" data-toggle="modal" data-target="#productosModalCenter">
                <i class="fa fa-plus" aria-hidden="true"></i> Nuevo Producto
            </button></h1>
            
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url(); ?>productos"><?= $data['page_title']; ?></a></li>
        </ul>
    </div>
    <!-- <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <p>
                        <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                        Filtros avanzados</button>
                    </p>
                    <div class="collapse" id="collapseExample">
                      <div class="card card-body">
                        <form>
                            <div class="form-group">
                                <label class="control-label" for="fecha_alta">Fecha de Alta: </label>
                                <input type="date" name="fecha_alta" id="fecha_alta" class="form-control">
                            </div>
                        </form>
                      </div>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-sm" id="productosTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th>CODIGO</th>
                                    <th>NOMBRE</th>
                                    <th>RUBRO</th>
                                    <th>U. MEDIDA</th>
                                    <th>CANTIDAD</th>
                                    <th>PRECIO VENTA</th>
                                    <th>ESTADO</th>
                                    <th>ACCIONES</th>
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