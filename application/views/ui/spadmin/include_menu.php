  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?=base_url()?>dashboard" class="brand-link">
      <img src="<?=base_url()?>assets/dist/img/logo_ptpnv.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">PM PTPN V</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="<?=base_url()?>assets/uploads/foto/<?= $this->session->userdata('foto');?>" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block"><?= $this->session->userdata('nama');?></a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item menu-open">
            <a href="<?=base_url()?>dashboard" class="nav-link <?php if($page_name=='view_dashboard')echo'active'?> ">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
              </p>
            </a>
          </li>

          <li class="nav-item <?php if($page_name=='view_users' || $page_name=='view_departement' || $page_name=='view_board' || $page_name=='view_label')echo'menu-open'?>">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-cog"></i>
              <p>
                Setting
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="<?=site_url('spadmin/users')?>" class="nav-link <?php if($page_name=='view_users')echo'active'?> ">
                  <i class="far fa-user nav-icon"></i>
                  <p>Users</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?=site_url('spadmin/departement')?>" class="nav-link <?php if($page_name=='view_departement')echo'active'?>">
                    <i class="fab fa-black-tie nav-icon"></i>
                  <p>Departement</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?=site_url('spadmin/boards')?>" class="nav-link <?php if($page_name=='view_board')echo'active'?>">
                    <i class="fab fa-buffer nav-icon"></i>
                  <p>Board</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?=site_url('spadmin/label')?>" class="nav-link <?php if($page_name=='view_label')echo'active'?>">
                 <i class="fas fa-tag nav-icon"></i>
                  <p>Label</p>
                </a>
              </li>
            <li class="nav-item">
                <a href="<?=site_url('spadmin/unitkerja')?>" class="nav-link <?php if($page_name=='view_unitkerja')echo'active'?>">
                 <i class="fas fa-tag nav-icon"></i>
                  <p>Unit Kerja</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="<?=site_url('spadmin/workspaces')?>" class="nav-link <?php if($page_name=='view_workspaces')echo'active'?>">
              <i class="nav-icon fas fa-briefcase"></i>
              <p>
                Workspaces
              </p>
            </a>
          </li>

          <li class="nav-header">OTHER</li>
          <!-- <li class="nav-item">
            <a href="<?=site_url('spadmin/notif')?>" class="nav-link">
              <i class="far fa-bell nav-icon"></i>
              <p>
                Notifikasi
                <span class="right badge badge-danger">12</span>
              </p>
            </a>
          </li> -->

          <li class="nav-item">
            <a href="<?= site_url('auth/signout')?>" class="nav-link">
              <i class="fas fa-sign-out-alt nav-icon"></i>
              <p>
                Sign Out
              </p>
            </a>
          </li>

        </ul>
      </nav>
    </div>
  </aside>