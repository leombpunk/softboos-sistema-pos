<?php headerAdmin($data);
getModal("modalMovimientos",$data); ?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fa fa-bell" aria-hidden="true"></i> <?= $data['page_title']; ?></h1>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url(); ?>movimientosCaja"><?= $data['page_title']; ?></a></li>
        </ul>
    </div>
    <div class="row" <?= empty($data['alert_message'])?'hidden':''; ?>>
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong><?= $data['alert_message']; ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <h2 class="mb-3 line-head">Productos Faltantes</h2>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-sm" id="sampleTable1">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>DESCRIPCION</th>
                                    <th>TIPO</th>
                                    <th>FECHA DE ALTA</th>
                                    <th>MONTO</th>
                                    <th>RESPONSABLE</th>
                                    <th class="text-center">ACCIONES</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <h2 class="mb-3 line-head">Productos Excedentes</h2>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-sm" id="sampleTable2">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>DESCRIPCION</th>
                                    <th>TIPO</th>
                                    <th>FECHA DE ALTA</th>
                                    <th>MONTO</th>
                                    <th>RESPONSABLE</th>
                                    <th class="text-center">ACCIONES</th>
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