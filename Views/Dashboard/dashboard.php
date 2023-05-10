<?php headerAdmin($data); ?>
<main class="app-content">
  <div class="app-title">
    <div>
      <h1><i class="fa fa-dashboard"></i> <?= $data['page_title']; ?></h1>
      <p>Start a beautiful journey here</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
      <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
      <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
    </ul>
  </div>
  <div class="row">
    <div class="col-md-6 col-lg-3">
      <div class="widget-small primary coloured-icon"><i class="icon fa fa-money fa-3x"></i>
        <div class="info">
          <h4>Total Efectivo</h4>
          <p><b id="PVCantidad">0.00</b>$</p>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-lg-3">
      <div class="widget-small info coloured-icon"><i class="icon fa fa-credit-card fa-3x"></i>
        <div class="info">
          <h4>Total Mercado Pago</h4>
          <p><b id="PVMasPedido">0.00</b>$</p>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-lg-3">
      <div class="widget-small warning coloured-icon"><i class="icon fa fa-dollar fa-3x"></i>
        <div class="info">
          <h4>Ingresos del dia</h4>
          <p>Pesos: <b id="PVIngresosDia">0.00</b>$</p>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-lg-3">
      <div class="widget-small danger coloured-icon"><i class="icon fa fa-handshake-o fa-3x"></i>
        <div class="info">
          <h4>Total ventas</h4>
          <p><b id="PVTotalVenta">0</b> Productos</p>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="tile">
        <h3 class="tile-title">Cantidad total por producto vendidos en general</h3>
        <div class="embed-responsive embed-responsive-16by9">
          <canvas class="embed-responsive-item" id="myChart"></canvas>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-6">
      <div class="tile">
        <h3 class="tile-title">Monto total por producto vendidos en EFECTIVO</h3>
        <div class="embed-responsive embed-responsive-16by9">
          <canvas class="embed-responsive-item" id="myChart3" style="margin-left: 25%;"></canvas>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="tile">
        <h3 class="tile-title">Monto total por producto vendidos con MERCADOPAGO</h3>
        <div class="embed-responsive embed-responsive-16by9">
          <canvas class="embed-responsive-item" id="myChart2" style="margin-left: 25%;"></canvas>
        </div>
      </div>
    </div>
  </div>
</main>
<?php footerAdmin($data); ?>