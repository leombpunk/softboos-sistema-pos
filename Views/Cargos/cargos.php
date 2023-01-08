<?php headerAdmin($data);
getModal("modalCargos", $data);
getModal("modalPermisos", $data); ?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fa fa-briefcase" aria-hidden="true"></i> <?= $data['page_title']; ?> <button class="btn btn-primary" type="button" onclick="openModal();" data-toggle="modal" data-target="#cargosModalCenter">
                <i class="fa fa-plus" aria-hidden="true"></i> Nuevo Cargo
            </button></h1>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url(); ?>cargos"><?= $data['page_title']; ?></a></li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-sm" id="sampleTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>DESCRIPCION</th>
                                    <th>RANGO</th>
                                    <th>FECHA DE ALTA</th>
                                    <th>ESTADO</th>
                                    <th class="text-center">ACCIONES</th>
                                </tr>
                            </thead>
                            <!-- <tbody>
                            </tbody> -->
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php footerAdmin($data); ?>