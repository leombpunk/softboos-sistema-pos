<?php
headerAdmin($data);
?>
<main class="app-content">
  <div class="row user">
    <div class="col-md-12">
      <div class="profile">
        <div class="info" style="width:25%;"><img class="user-img" src="<?= media(); ?><?= $_SESSION["userDATA"]["LOGO_URL"]; ?>">
          <h4 id="userName"></h4>
          <p><?= $_SESSION["userDATA"]["CARGO_DESCRIPCION"]; ?></p>
        </div>
        <div class="cover-image"></div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="tile p-0">
        <ul class="nav flex-column nav-tabs user-tabs">
          <li class="nav-item"><a class="nav-link active" href="#userData" data-toggle="tab">Datos Personales</a></li>
          <li class="nav-item"><a class="nav-link" href="#userSession" data-toggle="tab">Datos de Sesión</a></li>
          <li class="nav-item"><a class="nav-link" href="#userSucursal" data-toggle="tab">Datos de la Sucursal</a></li>
        </ul>
      </div>
    </div>
    <div class="col-md-9">
      <div class="tab-content">
        <div class="tab-pane active" id="userData">
        <div class="tile user-settings">
            <h4 class="line-head">Datos Personales 
              <button class="btn btn-sm btn-info" id="btnEditar" type="button" onclick="enableFormDataUser();"><i class="fa fa-pencil" aria-hidden="true"></i></button>
              <button class="btn btn-sm btn-danger" id="btnCancelar" type="button" onclick="disableFormDataUser();" hidden><i class="fa fa-times" aria-hidden="true"></i></button>
            </h4>
            <form class="form-horizontal" id="formDataUser" name="formDataUser">
              <div class="form-group row">
                <div class="col-md-6">
                  <label for="nombre" class="control-label">Nombre</label>
                  <input class="form-control" type="text" id="nombre" name="nombre" placeholder="Ingrese su nombre completo" disabled required>
                </div>
                <div class="col-md-6">
                  <label for="apellido" class="control-label">Apellido</label>
                  <input class="form-control" type="text" id="apellido" name="apellido" placeholder="Ingrese su apellido" disabled required>
                </div>
              </div>
              <div class="form-group row">
                <div class="col-md-6">
                  <label class="control-label" for="dni">DNI</label>
                  <input class="form-control" type="text" name="dni" id="dni" disabled required>
                </div>
                <div class="col-md-6">
                  <label class="control-label" for="fechaNac">Fecha de nacimiento</label>
                  <input class="form-control" type="date" name="fechaNac" id="fechaNac" disabled>
                </div>
              </div>
              <div class="form-group row">
                <div class="col-md-6">
                  <label class="control-label" for="email">Correo electronico</label>
                  <input class="form-control" type="email" name="email" id="email" disabled>
                </div>
                <div class="col-md-6">
                  <label class="control-label" for="telefono">Telefono</label>
                  <input class="form-control" type="phone" name="telefono" id="telefono" disabled>
                </div>
              </div>
              <div class="form-group row">
                <div class="col-md-12">
                  <label class="control-label" for="direccion">Direccion</label>
                  <input type="text" name="direccion" id="direccion" class="form-control" disabled>
                </div>
              </div>
              <div class="row mb-10">
                <div class="col-md-12">
                  <button class="btn btn-primary" id="btnGuardar" type="submit" disabled><i class="fa fa-fw fa-lg fa-check-circle"></i> Guardar</button>
                </div>
              </div>
            </form>
          </div>
        </div>
        <div class="tab-pane fade" id="userSession">
          <div class="tile user-settings">
            <h4 class="line-head">Datos de Sesión</h4>
            <form class="form-horizontal" id="formDataSession" name="formDataSession">
              <div class="form-group row">
                <div class="col-md-6">
                  <label for="cuil" class="control-label">CUIL (Usuario)</label>
                  <input class="form-control" type="text" id="cuil" name="cuil" required>
                </div>
                <div class="col-md-6">
                  <label for="cargo" class="control-label">Cargo</label>
                  <input type="text" name="cargo" id="cargo" class="form-control" disabled>
                </div>
              </div>
              <div class="form-group row">
                <div class="col-md-6">
                  <label class="control-label" for="">Contraseña Actual:</label>
                  <input class="form-control" type="password" name="actualpass" id="actualpass" required>
                </div>
                <div class="col-md-6">
                  <label class="control-label" for="">Contraseña Nueva:</label>
                  <input class="form-control" type="password" name="newpass" id="newpass" required>
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
        <div class="tab-pane fade" id="userSucursal">
          <div class="tile user-settings">
            <h4 class="line-head">Datos de la Sucursal</h4>
              <div class="form-group row">
                <div class="col-md-3">
                  <label for="scodigo" class="control-label">Codigo</label>
                </div>
                <div class="col-md-9">
                  <input class="form-control" type="text" id="scodigo" name="scodigo" disabled>
                </div>
              </div>
              <div class="form-group row">
                <div class="col-md-3">
                  <label for="snombre" class="control-label">Sucursal</label>
                </div>
                <div class="col-md-9">
                  <input class="form-control" type="text" id="snombre" name="snombre" disabled>
                </div>
              </div>
              <div class="form-group row">
                <div class="col-md-3">
                  <label class="control-label" for="stelefono">Telefono</label>
                  </div>
                <div class="col-md-9">
                  <input class="form-control" type="text" name="stelefono" id="stelefono" disabled>
                </div>
              </div>
              <div class="form-group row">
                <div class="col-md-3">
                  <label class="control-label" for="semail">Correo electronico</label>
                  </div>
                <div class="col-md-9">
                  <input class="form-control" type="text" name="semail" id="semail" disabled>
                </div>
              </div>
              
              <div class="form-group row">
                <div class="col-md-3">
                  <label class="control-label" for="cuit">CUIT</label>
                  </div>
                <div class="col-md-9">
                  <input class="form-control" type="text" name="cuit" id="cuit" disabled>
                </div>
                </div>
              <div class="form-group row">
                <div class="col-md-3">
                  <label class="control-label" for="web">Web</label>
                  </div>
                <div class="col-md-9">
                  <input class="form-control" type="text" name="web" id="web" disabled>
                </div>
              </div>
              <div class="form-group row">
                <div class="col-md-3">
                  <label class="control-label" for="sdireccion">Direccion</label>
                </div>
                <div class="col-md-9">
                  <input type="text" name="sdireccion" id="sdireccion" class="form-control" disabled>
                </div>
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
<?php footerAdmin($data); ?>