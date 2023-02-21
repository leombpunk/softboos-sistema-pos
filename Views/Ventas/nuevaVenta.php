<?php headerAdmin($data);
getModal("modalVentasVer",$data); ?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fa fa-shopping-cart" aria-hidden="true"></i> <?= $data['page_title']; ?></h1>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url(); ?>nuevaVenta"><?= $data['page_title']; ?></a></li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <form id="formNuevaVenta" name="formNuevaVenta">
                        <div class="table-responsive">
                            <table class="table table-hover" id="ventasCabeceraTable">
                                <thead class="thead-dark">
                                    <tr>
                                        <th colspan="5" class="h4"> Factura de Venta</th>
                                        <th class="h4"><?= date('d/m/Y'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><img class="img-fluid img-thumbnail" src="<?= media();?>/images/uploads/logo-icon2.png" alt="logo" style="width: 120px; heigth: 120px;"></td>
                                        <td class="font-weight-bold align-middle h5">Misiones Yogurt</td>
                                        <td class="font-weight-bold align-middle">Factura N°</td>
                                        <td class="align-middle">0000000000014</td>
                                        <td class="font-weight-bold align-middle">Forma de Pago</td>
                                        <td class="align-middle">
                                            <select class="form-control" name="asd" id="asd">
                                                <option value="1" selected>Efectivo</option>
                                                <option value="2">Debito</option>
                                                <option value="3">Credito</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold text-center align-middle h5">Cliente:</td>
                                        <td colspan="5">
                                            <input class="form-control" type="text" list="clientList" value="Varios">
                                            <datalist id="clientList">
                                                <option value="Varios"></option>
                                                <option value="Cliente 1"></option>
                                                <option value="Cliente 2"></option>
                                            </datalist>
                                        </td>
                                    </tr>
                                    <tr hidden>
                                        <td class="font-weight-bold text-right align-middle h6">DNI:</td>
                                        <td class="align-middle">99999999</td>
                                        <td class="font-weight-bold text-right align-middle h6">Nombre:</td>
                                        <td class="align-middle" colspan="4">XXXXXXXXXXXXX</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover" id="ventasDetalleTable">
                                <!-- <caption>Detalle</caption> -->
                                <thead class="thead-dark">
                                    <tr>
                                        <th class="align-middle h5" colspan="7">Detalle</th>
                                        <th class="text-right align-middle h5"><button type="button" class="btn btn-primary"><i class="fa fa-plus"></i> Añadir</button></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th class="text-center align-middle">Acciones</th>
                                        <th class="text-center align-middle">#</th>
                                        <th class="align-middle">Descripcion</th>
                                        <th class="text-center align-middle">IVA</th>
                                        <th class="text-center align-middle">Cantidad</th>
                                        <th class="text-center align-middle">Unidad Medida</th>
                                        <th class="text-center align-middle">Precio</th>
                                        <th class="text-center align-middle">Total</th>
                                    </tr>
                                    <tr>
                                        <td class="text-center align-middle"><button class="btn btn-danger"><i class="fa fa-trash"></i></button></td>
                                        <td class="text-center align-middle">1</td>
                                        <td class="align-middle">item 1</td>
                                        <td class="text-center align-middle">10,5</td>
                                        <td class="text-center align-middle">3</td>
                                        <td class="text-center align-middle">unidad</td>
                                        <td class="text-center align-middle">10</td>
                                        <td class="text-right align-middle">30</td>
                                        
                                    </tr>
                                    <tr>
                                        <td class="text-center align-middle"><button class="btn btn-danger"><i class="fa fa-trash"></i></button></td>
                                        <td class="text-center align-middle">2</td>
                                        <td class="align-middle">item 2</td>
                                        <td class="text-center align-middle">21</td>
                                        <td class="text-center align-middle">5</td>
                                        <td class="text-center align-middle">unidad</td>
                                        <td class="text-center align-middle">15</td>
                                        <td class="text-right align-middle">75</td>
                                        
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td class="font-weight-bold text-right align-middle h6" colspan="7">Subtotal: $</td>
                                        <td class="text-right align-middle  h6">9999</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold text-right align-middle h6" colspan="7">T. IVA: $</td>
                                        <td class="text-right align-middle  h6">999</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold text-right align-middle h6" colspan="7">Total: $</td>
                                        <td class="text-right align-middle  h6">999999</td>
                                    </tr>
                                    <tr>
                                        <td colspan="8" class="text-right align-middle"><button type="submit" class="btn btn-primary">Finalizar</button></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- <div class="table-responsive" hidden>
                            <table class="table table-hover table-striped table-sm" id="ventasFPagoTable">
                                <thead class="thead-dark">
                                    <tr>
                                        <th class="h5" colspan="6">Forma de Pago</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="2" class="font-weight-bold text-right align-middle h6">Efectivo</td>
                                        <td class="text-right align-middle">Cant. Pagos
                                            <select name="" id="">
                                                <option value="1">1 cuota</option>
                                                <option value="1">3 cuotas</option>
                                                <option value="1">6 cuotas</option>
                                                <option value="1">9 cuotas</option>
                                                <option value="1">12 cuotas</option>
                                                <option value="1">18 cuotas</option>
                                                <option value="1">24 cuotas</option>
                                                <option value="1">36 cuotas</option>
                                            </select>
                                        </td>
                                        <td class="text-right align-middle">
                                            $ <input type="number" value="0.0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="font-weight-bold text-right align-middle h6">Debito</td>
                                        <td class="text-right align-middle">Cant. Pagos
                                            <select name="" id="">
                                                <option value="1">1 cuota</option>
                                                <option value="1">3 cuotas</option>
                                                <option value="1">6 cuotas</option>
                                                <option value="1">9 cuotas</option>
                                                <option value="1">12 cuotas</option>
                                                <option value="1">18 cuotas</option>
                                                <option value="1">24 cuotas</option>
                                                <option value="1">36 cuotas</option>
                                            </select>
                                        </td>
                                        <td class="text-right align-middle">
                                            $ <input type="number" value="0.0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="font-weight-bold text-right align-middle h6">Credito</td>
                                        <td class="text-right align-middle">Cant. Pagos
                                            <select name="" id="">
                                                <option value="1">1 cuota</option>
                                                <option value="1">3 cuotas</option>
                                                <option value="1">6 cuotas</option>
                                                <option value="1">9 cuotas</option>
                                                <option value="1">12 cuotas</option>
                                                <option value="1">18 cuotas</option>
                                                <option value="1">24 cuotas</option>
                                                <option value="1">36 cuotas</option>
                                            </select>
                                        </td>
                                        <td class="text-right align-middle">
                                            $ <input type="number" value="0.0">
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="6" class="text-right align-middle"><button type="submit" class="btn btn-primary">Finalizar</button></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div> -->
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
</main>
<?php footerAdmin($data); ?>