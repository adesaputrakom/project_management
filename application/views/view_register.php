<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Project Management PTPN V | Sign up</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?= base_url() ?>assets/dist/css/adminlte.min.css">
  <link rel="shortcut icon" href="<?= base_url(); ?>assets/dist/img/logo_ptpnv.png" />

  <!-- Customise Style -->
  <style>
    .register-page {
      background-image: url(<?= base_url() ?>assets/dist/img/ptpnv.png);
      background-size: cover;
    }
  </style>
</head>

<body class="hold-transition register-page">
  <div class="register-box">
    <div class="card card-outline card-primary">
      <div class="card-header text-center">
        <a href="<?= base_url() ?>" class="h1"><b>PM</b> PTPN V</a>
      </div>
      <div class="card-body">

        <!-- message error -->
        <div id="message_error"></div>

        <?php if ($this->session->flashdata('alert')) { ?>
          <div class="alert alert-<?= $this->session->flashdata('alert') ?> alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5><i class="icon fas fa-info"></i> Alert !</h5>
            <?= $this->session->flashdata('message') ?>
          </div>
        <?php } ?>

        <p class="login-box-msg">Register a new membership</p>

        <form action="#" id="form" method="post">
          <div class="input-group mb-3">
            <input type="text" class="form-control" name="nama" placeholder="Full name">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-user"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="email" class="form-control" name="email" placeholder="Email">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-envelope"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="password" class="form-control" name="password" placeholder="Password">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="password" class="form-control" name="repassword" placeholder="Retype password">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-8">
              <div class="icheck-primary">
                <input type="checkbox" id="agreeTerms" name="terms" value="agree">
                <label for="agreeTerms">
                  I agree to the <a href="#">terms</a>
                </label>
              </div>
            </div>
            <!-- /.col -->
            <div class="col-4">
              <button type="button" class="btn btn-primary btn-block" onclick="proccess()">Register</button>
            </div>
            <!-- /.col -->
          </div>
        </form>

        <div class="social-auth-links text-center">
          <a href="<?= $login_url ?>" class="btn btn-block btn-danger">
            <i class="fab fa-google-plus mr-2"></i>
            Sign up using Google+
          </a>
        </div>

        <a href="<?= base_url() ?>" class="text-center">I already have a membership</a>
      </div>
      <!-- /.form-box -->
    </div><!-- /.card -->
  </div>
  <!-- /.register-box -->

  <!-- jQuery -->
  <script src="<?= base_url() ?>assets/plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="<?= base_url() ?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="<?= base_url() ?>assets/dist/js/adminlte.min.js"></script>
</body>

</html>

<script type="text/javascript">
    function proccess(){
        
        $.ajax({
            url : "<?=base_url().'auth/signup/submit'?>",
            type: "POST",
            data:new FormData($('#form')[0]), //this is formData
            processData:false,
            contentType:false,
            cache:false,
            async:false,
            dataType: "JSON",
            success: function(data)
            {
                if(data.status) 
                {
                    html = `<div class="alert alert-${data.status} alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <h5><i class="icon fas fa-info"></i> Alert !</h5>
                                ${data.message}
                            </div>`;

                    $('#message_error').empty().html(html);
                    $('#message_error').show();

                    if(data.status == 'success'){
                      // redirect to login
                      window.setTimeout(function() {
                          window.location.href = '<?=base_url()?>auth';
                      }, 1000);
                    }
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                toastr.error('Gagal', data.message, {timeOut: 2000});
                $('#btnSave').attr('disabled',false); //set button enable 
            }
        });
    }
</script>