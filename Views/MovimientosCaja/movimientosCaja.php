<?php headerAdmin($data);
getModal("modalMovimientos",$data); ?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fa fa-money" aria-hidden="true"></i> <?= $data['page_title']; ?> <button class="btn btn-primary" type="button" onclick="openModal();" data-toggle="modal" data-target="#movimientosModalCenter">
                <i class="fa fa-plus" aria-hidden="true"></i> <?= $data['button_name']; ?>
            </button></h1>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url(); ?>movimientosCaja"><?= $data['page_title']; ?></a></li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert" <?= empty($data['alert_message'])?'hidden':''; ?>>
                        <strong><?= $data['alert_message']; ?></strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-sm" id="sampleTable">
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