<?php 
headerAdmin($data);
getModal("modalPerfil",$data);
?>
    <main class="app-content">
      <div class="row user">
        <div class="col-md-12">
          <div class="profile">
            <div class="info"><img class="user-img" src="<?= media(); ?>images/uploads/logo-icon3.png">
              <h4><?= $_SESSION["userDATA"]["APELLIDO"]." ".$_SESSION["userDATA"]["NOMBRE"]; ?></h4>
              <p><?= $_SESSION["userDATA"]["CARGO_DESCRIPCION"]; ?></p>
            </div>
            <div class="cover-image"></div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="tile p-0">
            <ul class="nav flex-column nav-tabs user-tabs">
              <li class="nav-item"><a class="nav-link active" href="#user-timeline" data-toggle="tab">Datos Personales</a></li>
              <li class="nav-item"><a class="nav-link" href="#user-settings" data-toggle="tab">Datos de Sesión</a>
              </li>
              <li class="nav-item"><a class="nav-link" href="#" data-toggle="tab">Datos de la Sucursal</a></li>
              <li class="nav-item"><a class="nav-link" href="#" data-toggle="tab">Entrada/Salida</a></li>
            </ul>
          </div>
        </div>
        <div class="col-md-9">
          <div class="tab-content">
            <div class="tab-pane active" id="user-timeline">
              <div class="timeline-post">
                <div class="post-media">
                  <div class="content">
                    <h5>Datos Personales <button class="btn btn-sm btn-info" type="button" onclick="openModalPerfil();"><i class="fa fa-pencil" aria-hidden="true"></i></button></h5>
                  </div>
                </div>
                <div class="table-responsive">
                	<table class="table table-sm table-bordered" id="dataEmpleadoTable">
	 					<tbody>
	 						<tr>
	 							<td style="width: 150px;">Código:</td>
	 							<td id="celid"><?= $_SESSION["userDATA"]["EMPLEADO_ID"]; ?></td>
	 						</tr>
	 						<tr>
	 							<td>DNI:</td>
	 							<td><?= $_SESSION["userDATA"]["DNI"]; ?></td>
	 						</tr>
	 						<tr>
	 							<td>Nombres:</td>
	 							<td id="celnombre"><?= $_SESSION["userDATA"]["NOMBRE"]; ?></td>
	 						</tr>
	 						<tr>
	 							<td>Apellidos:</td>
	 							<td id="celapellido"><?= $_SESSION["userDATA"]["APELLIDO"]; ?></td>
	 						</tr>
	 						<tr>
	 							<td>Fecha de Nacimiento:</td>
	 							<td id="celapellido"><?= $_SESSION["userDATA"]["FECHA_NACIMIENTO"]; ?></td>
	 						</tr>
	 						<tr>
	 							<td>Cargo:</td>
	 							<td id="celcargo"><?= $_SESSION["userDATA"]["CARGO_DESCRIPCION"]; ?></td>
	 						</tr>
	 						<tr>
	 							<td>E-mail:</td>
	 							<td id="celmail"><?= $_SESSION["userDATA"]["MAIL"]; ?></td>
	 						</tr>
	 						<tr>
	 							<td>Teléfono:</td>
	 							<td id="celtelefono"><?= $_SESSION["userDATA"]["TELEFONO"]; ?></td>
	 						</tr>
	 						<tr>
	 							<td>Dirección:</td>
	 							<td><?= $_SESSION["userDATA"]["DIRECCION"]; ?></td>
	 						</tr>
	 					</tbody>               	
	                </table>
                </div>
              </div>
            </div>
            <div class="tab-pane fade" id="user-settings">
              <div class="tile user-settings">
                <h4 class="line-head">Datos de Sesión</h4>
                <form class="form-horizontal" id="formDataFiscal" name="formDataFiscal">
                  <div class="form-group row">
                  	<div class="col-md-6">
                  		<label for="DFcuil" class="control-label">CUIL (Usuario)</label>
                    <!-- <div class="col-md-8"> -->
                    	<input class="form-control" type="text" id="DFcuil" name="DFcuil" value="<?= $_SESSION['userDATA']['CUIL']; ?>" placeholder="Ingrese su CUIL sin puntos, espacios o  guiones">
                    <!-- </div> -->
                  	</div>
                    
                  </div>
                  <div class="form-group row">
                	<div class="col-md-6">
                		<label class="control-label">Contraseña Actual:</label>
                		<input class="form-control" type="" name="">
                	</div>
                	<div class="col-md-6">
                		<label class="control-label">Contraseña Nueva:</label>
                		<input class="form-control" type="" name="">
                	</div>
                  </div>
                  <div class="row mb-10">
                    <div class="col-md-12">
                      <button class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i> Guardar</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
<?php footerAdmin($data); ?>