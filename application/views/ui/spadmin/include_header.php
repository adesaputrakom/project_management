  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="<?= base_url()?>dashboard" class="nav-link">Home</a>
      </li>
      <!-- <li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link">Contact</a>
      </li> -->
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>

      <?php if($this->session->userdata('double_akses') == 'Yes') { ?>
        <!-- Switch -->
        <li class="nav-item dropdown">
          <a class="nav-link" data-toggle="dropdown" href="#" title="switch hak akses">
            <i class="fas fa-toggle-off"></i>
          </a>
          <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right mt-2">
            <?php if($this->session->userdata('is_admin') == false) { ?>
              <div class="dropdown-divider"></div>
              <a href="<?=base_url()?>auth/switch/<?=kodeswitchakses('administrator');?>" class="dropdown-item bg-success">
                <i class="fas fa-user-shield mr-2"></i> Administrator
              </a>
            <?php }else{ ?>
              <div class="dropdown-divider"></div>
              <a href="<?=base_url()?>auth/switch/<?=kodeswitchakses('user');?>" class="dropdown-item bg-warning text-white">
                <i class="fas fa-user-tie mr-2"></i> User
              </a>
            <?php } ?>
        </li>
      <?php } ?>

      <li class="nav-item">
        <a class="nav-link" data-widget="control-sidebar" data-controlsidebar-slide="true" href="#" role="button">
          <i class="fas fa-th-large"></i>
        </a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->