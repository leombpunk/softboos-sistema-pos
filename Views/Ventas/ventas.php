<?php headerAdmin($data);
getModal("modalVentasVer",$data); ?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fa fa-shopping-cart" aria-hidden="true"></i> <?= $data['page_title']; ?> 
            <a class="btn btn-primary" <?= ($_SESSION["permisos"][0]["AGREGAR"] == 1)?'title="Registrar Venta" href="'.base_url().'ventas/nuevaVenta"':'disabled title="No tienes permiso" href="#"' ?>>
                <i class="fa fa-plus" aria-hidden="true"></i> Nueva Venta
            </a></h1>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url(); ?>ventas"><?= $data['page_title']; ?></a></li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-sm" id="ventasTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th>SUCURSAL</th>
                                    <th>N° FACTURA</th>
                                    <th>FECHA</th>
                                    <th>FORMA PAGO</th>
                                    <th>ESTADO</th>
                                    <th>TOTAL</th>
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