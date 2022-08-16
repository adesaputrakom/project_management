<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Project Management PTPN V | Sign in</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?= base_url() ?>assets/dist/css/adminlte.min.css">
  <link rel="shortcut icon" href="<?= base_url(); ?>assets/dist/img/logo_ptpnv.png" />

  <!--Recaptcha 3 Google -->
  <script src="https://www.google.com/recaptcha/api.js?render=<?= $sitekey; ?>"></script>
  <script>
    grecaptcha.ready(function() {
      grecaptcha.execute('<?= $sitekey; ?>', {
        action: 'contact'
      }).then(function(token = '') {
        var recaptchaResponse = document.getElementById('recaptchaResponse');
        recaptchaResponse.value = token;
      });
    });
  </script>

  <!-- Customise Style -->
  <style>
    .login-page {
      background-image: url(<?= base_url() ?>assets/dist/img/ptpnv.png);
      background-size: cover;
    }
  </style>

</head>

<body class="hold-transition login-page">
  <div class="login-box">
    <!-- /.login-logo -->
    <div class="card card-outline card-primary">
      <div class="card-header text-center">
        <a href="<?= base_url() ?>" class="h1"><b>PM</b> PTPN V</a>
      </div>
      <div class="card-body">
        <p class="login-box-msg">Sign in to start your session</p>

        <?php if ($this->session->flashdata('alert')) { ?>
          <div class="alert alert-<?= $this->session->flashdata('alert') ?> alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5><i class="icon fas fa-info"></i> Alert !</h5>
            <?= $this->session->flashdata('message') ?>
          </div>
        <?php } ?>

        <div class="m-alert m-alert--outline alert alert-warning alert-capslock alert-dismissible animated fadeIn" role="alert" style="display: none;">
          <span>Warning! Caps Lock is on</span>
        </div>

        <form action="<?= base_url() ?>auth/signin" method="POST" id="_form">
          <input type="hidden" name="g-recaptcha_response" id="recaptchaResponse">
          <div class="input-group mb-3">
            <input type="text" class="form-control" name="username" id="username" placeholder="Username/Email">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-user"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="password" class="form-control" name="password" id="password" placeholder="Password">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-8">
              <div class="icheck-primary">
                <input type="checkbox" id="remember">
                <label for="remember">
                  Remember Me
                </label>
              </div>
            </div>
            <!-- /.col -->
            <div class="col-4">
              <button type="submit" class="btn btn-primary btn-block">Sign In</button>
            </div>
            <!-- /.col -->
          </div>
        </form>

        <div class="social-auth-links text-center mt-2 mb-3">
          <a href="<?php echo $login_url; ?>" class="btn btn-block btn-danger">
            <i class="fab fa-google-plus mr-2"></i> Sign in using Google+
          </a>
        </div>
        <!-- /.social-auth-links -->

        <p class="mb-1">
          <a href="<?= base_url() ?>auth/forgot_password">I forgot my password</a>
        </p>
        <p class="mb-0">
          <a href="<?= base_url() ?>auth/signup" class="text-center">Register a new membership</a>
        </p>
      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->
  </div>
  <!-- /.login-box -->

  <!-- jQuery -->
  <script src="<?= base_url() ?>assets/plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="<?= base_url() ?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="<?= base_url() ?>assets/dist/js/adminlte.min.js"></script>
  <script src="<?= base_url() ?>assets/dist/js/jquery.blockUI.min.js"></script>
</body>

</html>

<script type="text/javascript">
  function login() {

    var pass = $('#password').val();
    $.ajax({
      async: true,
      type: 'get',
      beforeSend: function(request) {
        $.blockUI({
          message: '<img src="<?= base_url() ?>assets/dist/gif/ajax-loader.gif" width="50"> Please Wait ...',
          css: {
            border: 'none',
            backgroundColor: 'transparent',
            color: '#fff'
          }
        });
      },
      complete: function(request, json) {},
      url: '<?= base_url() ?>auth/signin',
      type: "POST",
      data: $('#_form').serialize(),
      dataType: "JSON",
      success: function(data) {
        $.unblockUI();
        if (data.data == true) {

        } else {

        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        $.unblockUI();
      }
    });
  }

  var username = document.getElementById("username");
  username.addEventListener("keyup", function(event) {
    // If "caps lock" is pressed, display the warning text
    if (event.getModifierState("CapsLock")) {
      $('.alert-capslock').show();
    } else {
      $('.alert-capslock').hide();
    }
  });

  var pwd = document.getElementById("password");
  pwd.addEventListener("keyup", function(event) {
    // If "caps lock" is pressed, display the warning text
    if (event.getModifierState("CapsLock")) {
      $('.alert-capslock').show();
    } else {
      $('.alert-capslock').hide();
    }
  });
</script>