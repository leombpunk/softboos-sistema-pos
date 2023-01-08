<?php headerAdmin($data);
getModal("modalUdMedidas", $data);
getModal("modalUdMedidasVer",$data); ?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1>
                <i class="fa fa-balance-scale" aria-hidden="true"></i> <?= $data['page_title']; ?> 
            <?php if ($_SESSION["permisos"][0]["AGREGAR"] == 1){ ?>
                <button class="btn btn-primary" type="button" onclick="openModal();" data-toggle="modal" data-target="#udMedidasModalCenter">
                    <i class="fa fa-plus" aria-hidden="true"></i> Nueva Unidad de Medida
                </button>
            <?php } ?>
            </h1>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url(); ?>udMedidas"><?= $data['page_title']; ?></a></li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-sm" id="udMedidasTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th>CODIGO</th>
                                    <th>CANTIDAD</th>
                                    <th>UD MEDIDA</th>
                                    <th>ES IGUAL A</th>
                                    <th>CANTIDAD</th>
                                    <th>UD MEDIDA</th>
                                    <th>TIPO</th>
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