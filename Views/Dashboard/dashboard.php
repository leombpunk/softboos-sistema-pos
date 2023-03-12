<?php headerAdmin($data);?>
    <main class="app-content">
      <div class="app-title">
        <div>
          <h1><i class="fa fa-dashboard"></i> <?= $data['page_title'];?></h1>
          <p>Start a beautiful journey here</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
          <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
          <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
        </ul>
      </div>
      <div class="row">
        <div class="col-md-6 col-lg-3">
          <div class="widget-small primary coloured-icon"><i class="icon fa fa-users fa-3x"></i>
            <div class="info">
              <h4>En venta</h4>
              <p><b>5</b> Productos</p>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="widget-small info coloured-icon"><i class="icon fa fa-thumbs-o-up fa-3x"></i>
            <div class="info">
              <h4>Más pedido</h4>
              <p>Producto 1</p>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="widget-small warning coloured-icon"><i class="icon fa fa-files-o fa-3x"></i>
            <div class="info">
              <h4>Ingresos del dia</h4>
              <p>Pesos: <b>15.000</b>$</p>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="widget-small danger coloured-icon"><i class="icon fa fa-star fa-3x"></i>
            <div class="info">
              <h4>Total de ventas</h4>
              <p><b>1268</b> Productos</p>
            </div>
          </div>
        </div>
      </div>
      <!-- <div class="row">
        <div class="col-md-6">
          <div class="tile">
            <h3 class="tile-title">Mas vendidos del mes (guita)</h3>
            <div class="embed-responsive embed-responsive-16by9">
              <canvas class="embed-responsive-item" id="lineChartDemo"></canvas>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="tile">
            <h3 class="tile-title">Mas vendidos del mes (cantidad)</h3>
            <div class="embed-responsive embed-responsive-16by9">
              <canvas class="embed-responsive-item" id="pieChartDemo"></canvas>
            </div>
          </div>
        </div>
      </div> -->
      <div class="row">
        <div class="col-md-6">
          <div class="tile">
            <h3 class="tile-title">Cantidad de ventas por horas</h3>
            <div class="embed-responsive embed-responsive-16by9">
              <canvas class="embed-responsive-item" id="myChart"></canvas>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="tile">
            <h3 class="tile-title">Cantidad de ventas por producto</h3>
            <div class="embed-responsive embed-responsive-16by9">
              <canvas class="embed-responsive-item" id="myChart2" style="margin-left: 25%;"></canvas>
            </div>
          </div>
      </div>
      <!-- <div class="row">
        <div class="col-md-6">
          <div class="tile">
            <h3 class="tile-title">Mas vendidos del mes (guita)</h3>
            <div class="embed-responsive embed-responsive-16by9">
              <canvas class="embed-responsive-item" id="barChartDemo"></canvas>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="tile">
            <h3 class="tile-title">Mas vendidos del mes (cantidad)</h3>
            <div class="embed-responsive embed-responsive-16by9">
              <canvas class="embed-responsive-item" id="doughnutChartDemo"></canvas>
            </div>
          </div>
        </div>
      </div> -->
      <!-- <div class="row">
        <div class="col-md-12">
          <div class="tile">
            <div class="tile-body">Create a beautiful dashboard</div>
            <?php //dep($_SESSION['userDATA']); ?>
          </div>
        </div>
      </div> -->
    </main>
<?php footerAdmin($data);?>   