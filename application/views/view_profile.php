<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Project Management PTPN V | Your Profile</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?= base_url() ?>assets/dist/css/adminlte.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/daterangepicker/daterangepicker.css">

  <!-- Select2 -->
  <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?= base_url() ?>assets/dist/css/adminlte.min.css">
  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">

  <link rel="shortcut icon" href="<?= base_url(); ?>assets/dist/img/logo_ptpnv.png" />
</head>
<body class="hold-transition sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Your Profile</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Your Profile</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Default box -->
      <div class="card">
        <div class="card-body row">
          <div class="col-5 text-center d-flex align-items-top justify-content-center bg-info">
            <div class="pt-5">
              <h2>Wellcome <i class="text-warning"><?= $this->session->userdata('nama')?></i></h2>
              <p class="lead mb-5">
                Silahkan lengkapi data diri anda ..!
              </p>
            </div>
          </div>
          <div class="col-7">
            <?php if ($this->session->flashdata('alert')){ 
                $alert = $this->session->flashdata('alert');
                if($alert == 'success'){
                    $msg = 'Profil anda berhasil diperbarui !';
                }else if($alert == 'error'){
                    $msg = 'Profil anda gagal diperbarui !';
                }else{
                    $msg = 'Semua fill wajib di isi !';
                }?>
                <div class="alert alert-<?= $alert ?> alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-info"></i> Alert !</h5>
                    <?= $msg ?>
                </div>
            <?php } ?>
            <form method="POST" action="<?=base_url()?>dashboard/update_profile" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Nama Lengkap <font class="text-danger">*</font></label>
                    <input type="text" class="form-control" name="nama" placeholder="masukkan nama lengkap" value="<?=$profil->nama?>">
                </div>
                <div class="form-group">
                    <label>Email <font class="text-danger">*</font></label>
                    <input type="email" class="form-control" name="email" placeholder="masukkan email" value="<?=$profil->email?>">
                </div>
                <div class="form-group">
                    <label>Username <font class="text-danger">*</font></label>
                    <input type="text" class="form-control" name="username" placeholder="masukkan username" value="<?=$profil->username?>">
                </div>
                <div class="form-group">
                    <label>Password <font class="text-danger">*</font></label>
                    <input type="text" class="form-control" name="pass" placeholder="masukkan password">
                </div>
                <div class="form-group">
                    <label>Jenis Kelamin <font class="text-danger">*</font></label>
                    <select class="form-control select2" name="jeniskelamin">
                        <option <?php if($profil->jeniskelamin=='')echo'selected'?> value=""> -- Pilih Jenis Kelamin --</option>
                        <option <?php if($profil->jeniskelamin=='Laki-laki')echo'selected'?> value="Laki-laki"> Laki-laki </option>
                        <option <?php if($profil->jeniskelamin=='Perempuan')echo'selected'?> value="Perempuan"> Perempuan </option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Tempat Lahir <font class="text-danger">*</font></label>
                    <input type="text" class="form-control" name="tempatlahir" placeholder="masukkan tempat lahir" value="<?=$profil->tempat_lahir?>">
                </div>
                <div class="form-group">
                    <label>Tanggal Lahir <font class="text-danger">*</font></label>
                    <div class="input-group date" id="reservationdate" data-target-input="nearest">
                        <?php $tgllahir =  $profil->tgl_lahir ? date('d/m/Y', strtotime($profil->tgl_lahir)) : ''; ?>
                        <input type="text" value="<?=$tgllahir?>" class="form-control datetimepicker-input" name="tanggallahir" data-target="#reservationdate" placeholder="pilih tanggal lahir">
                        <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                            <div class="input-group-text bg-primary"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Pendidikan <font class="text-danger">*</font></label>
                    <select class="form-control select2" name="pendidikan">
                        <option <?php if($profil->pendidikan=='')echo'selected'?> value=""> -- Pilih Pendidikan --</option>
                        <option <?php if($profil->pendidikan=='SD')echo'selected'?> value="SD"> SD </option>
                        <option <?php if($profil->pendidikan=='SMP')echo'selected'?> value="SMP"> SMP </option>
                        <option <?php if($profil->pendidikan=='SMA')echo'selected'?> value="SMA"> SMA </option>
                        <option <?php if($profil->pendidikan=='D3')echo'selected'?> value="D3"> D3 </option>
                        <option <?php if($profil->pendidikan=='S1')echo'selected'?> value="S1"> S1 </option>
                        <option <?php if($profil->pendidikan=='S2')echo'selected'?> value="S2"> S2 </option>
                        <option <?php if($profil->pendidikan=='S3')echo'selected'?> value="S3"> S3 </option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Alamat <font class="text-danger">*</font></label>
                    <textarea class="form-control" name="alamat" rows="2" placeholder="masukkan Alamat"><?= $profil->alamat ?></textarea>
                </div>
                <div class="form-group">
                    <label>Foto <small class="text-info"> ( max : 2 mb )</small></label>
                    <input type="file" class="form-control" name="foto" placeholder="upload foto" onchange="checkSize(this)">
                </div>
                <div class="form-group">
                    <label>Company/Organisasi <font class="text-danger">*</font></label>
                    <input type="text" class="form-control" name="company" placeholder="masukkan company/perusahaan" value="<?= $profil->company ?>">
                </div>
                <div class="form-group">
                    <label>Departement/Bidang</label>
                    <select class="form-control select2" name="departement_id">
                        <option value=""> -- Pilih Departement --</option>
                        <?php if($departments){
                            foreach($departments as $departemen){
                                if($profil->departement_id == $departemen->departement_id){
                                    echo '<option value="'.encrypt($departemen->departement_id).'" selected> '.$departemen->departement.' </option>';
                                }else{
                                    echo '<option value="'.encrypt($departemen->departement_id).'"> '.$departemen->departement.' </option>';
                                }
                            }
                        }?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Posisi <font class="text-danger">*</font></label>
                    <input type="text" class="form-control" name="posisi" placeholder="masukkan posisi/jabatan" value="<?= $profil->position ?>">
                </div>
                <div class="form-group">
                    <label>Agama <font class="text-danger">*</font></label>
                    <select class="form-control select2" name="agama">
                        <option <?php if($profil->agama=='')echo'selected'?> value=""> -- Pilih Agama --</option>
                        <option <?php if($profil->agama=='Islam')echo'selected'?> value="Islam"> Islam </option>
                        <option <?php if($profil->agama=='Kristen')echo'selected'?> value="Kristen"> Kristen </option>
                        <option <?php if($profil->agama=='Katholik')echo'selected'?> value="Katholik"> Katholik </option>
                        <option <?php if($profil->agama=='Konghucu')echo'selected'?> value="Konghucu"> Konghucu </option>
                        <option <?php if($profil->agama=='Budha')echo'selected'?> value="Budha"> Budha </option>
                        <option <?php if($profil->agama=='Hindu')echo'selected'?> value="Hindu"> Hindu </option>
                    </select>
                </div>
                <hr>
                <b>Note :</b>
                Tanda (<font class="text-danger">*</font>) artinya wajib di isi !

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="btn_simpan">Save</button>
                </div>
            </form>
          </div>
        </div>
      </div>

    </section>
    <!-- /.content -->

    <footer class="main-footer">
        <strong>Copyright &copy; 2021 <a href="#">PTPN V Pekanbaru</a>.</strong>
        All rights reserved.
        <div class="float-right d-none d-sm-inline-block">
        <b>Version</b> 1.0
        </div>
  </footer>
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="<?= base_url() ?>assets/plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?= base_url() ?>assets/plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="<?= base_url() ?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- daterangepicker -->
<script src="<?= base_url() ?>assets/plugins/moment/moment.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="<?= base_url() ?>assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- AdminLTE App -->
<script src="<?= base_url() ?>assets/dist/js/adminlte.js"></script>
<!-- select2 -->
<script src="<?= base_url() ?>assets/plugins/select2/js/select2.full.min.js"></script>
<!-- SweetAlert2 -->
<script src="<?= base_url() ?>assets/plugins/sweetalert2/sweetalert2.min.js"></script>

</body>
</html>

<script type="text/javascript">    
    function checkSize(t) {
        if(typeof t.files[0] !== 'undefined'){
            size = t.files[0].size/1024;
            ext = t.files[0].type;
            if(ext == 'image/png' || ext == 'image/jpeg' || ext == 'image/jpg'){
                if(size > 2300){
                    $(t).val('');;

                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning !',
                        text: 'Ukuran file yang diupload melebihi dari 2 Mb !'
                    });
                }
            }else{
                $(t).val('');

                Swal.fire({
                    icon: 'warning',
                    title: 'Warning !',
                    text: 'File yang diupload bukan JPEG,JPG,PNG !'
                });
            }    
        }
    }

    document.addEventListener("DOMContentLoaded", function() {

        $('.date').datetimepicker({
            format: 'DD/MM/YYYY'
        });

        //Initialize Select2 Elements
        $('.select2').select2({width: "100%"});

        <?php if ($this->session->flashdata('alert')=='success'){ ?>
            window.setTimeout(function(){
                window.location.href = '<?=base_url()?>dashboard';
            },1000);
        <?php } ?>
    });
</script>
