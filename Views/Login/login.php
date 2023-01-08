<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="Author" content="Leandro Boos">
    <meta name="theme-color" content="#009688">
    <link rel="stylesheet" type="text/css" href="<?= media();?>css/main.css">
    <link rel="stylesheet" type="text/css" href="<?= media();?>css/style.css">
    <link rel="stylesheet" type="text/css" href="<?= media();?>css/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="shortcut icon" type="image/x-icon" href="<?= media();?>images/uploads/logo-icon3.png">
    <title><?= $data['page_tag'];?></title>
  </head>
  <body>
    <section class="material-half-bg"></section>
    <section class="login-content">
      <div class="logo">
        <h1>SoftBoos</h1>
      </div>
      <div class="login-box">
        <form id="formLogin" name="formLogin" class="login-form" action="">
          <h3 class="login-head"><i class="fa fa-lg fa-fw fa-user"></i> CONECTARSE</h3>
          <div class="form-group">
            <label class="control-label" for="user">USUARIO</label>
            <input id="user" name="user" class="form-control" type="text" placeholder="Usuario" autofocus autocomplete="off" maxlength="11" required>
          </div>
          <div class="form-group">
            <label class="control-label" for="password">CONTRASEÑA</label>
            <input id="password" name="password" class="form-control" type="password" placeholder="Contraseña" maxlength="16" required>
          </div>
          <div class="form-group">
            <div class="utility">
              <p class="semibold-text mb-2"><a href="#" data-toggle="flip">¿Olvidaste tu contraseña?</a></p>
            </div>
          </div>
          <div class="form-group btn-container">
            <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-sign-in"></i> INGRESAR</button>
          </div>
        </form>
        <form id="formForget" name="formForget" class="forget-form" action="">
          <h3 class="login-head"><i class="fa fa-lock"></i> ¿Olvidaste tu contraseña?</h3>
          <div class="form-group">
            <label class="control-label" for="mailreset">EMAIL</label>
            <input id="mailreset" name="mailreset" class="form-control" type="mail" placeholder="Email" autocomplete="off" required>
          </div>
          <div class="form-group btn-container">
            <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-unlock"></i> Reestrablecer</button>
          </div>
          <div class="form-group mt-3">
            <p class="semibold-text mb-0"><a href="#" data-toggle="flip"><i class="fa fa-angle-left"></i>  Volver al Login</a></p>
          </div>
        </form>
      </div>
    </section>
    <script>
        const base_url = "<?= base_url();?>";
    </script>
    <!-- Essential javascripts for application to work-->
    <script src="<?= media();?>js/jquery-3.3.1.min.js"></script>
    <script src="<?= media();?>js/popper.min.js"></script>
    <script src="<?= media();?>js/bootstrap.min.js"></script>
    <script src="<?= media(); ?>js/plugins/sweetalert.min(2).js"></script>
    <script src="<?= media();?>js/main.js"></script>
    <!-- The javascript plugin to display page loading on top-->
    <script src="<?= media();?>js/plugins/pace.min.js"></script>
    <script src="<?= media();?>js/<?= $data["page_filejs"];?>"></script>
  </body>
</html>