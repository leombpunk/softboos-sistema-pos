<?php headerAdmin($data);
getModal("modalProveedores", $data);
getModal("modalProveedoresVer",$data); ?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fa fa-user" aria-hidden="true"></i> <?= $data['page_title']; ?> <button class="btn btn-primary" type="button" onclick="openModal();" data-toggle="modal" data-target="#proveedoresModalCenter">
                <i class="fa fa-plus" aria-hidden="true"></i> Nuevo Proveedor
            </button></h1>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url(); ?>proveedores"><?= $data['page_title']; ?></a></li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-sm" id="proveedoresTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th>RAZON SOCIAL</th>
                                    <th>CUIT</th>
                                    <th>EMAIL</th>
                                    <th>TELEFONO</th>
                                    <th>WEB</th>
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