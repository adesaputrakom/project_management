<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Project Management PTPN V | Dashboard</title>
  <link rel="shortcut icon" href="<?= base_url(); ?>assets/dist/img/logo_ptpnv.png" />

  <!-- include css -->
  <?php include('include_css.php') ?>

</head>
<body class="hold-transition sidebar-mini layout-fixed 
<?php if($page_name=='view_workspaces' || $page_name=='view_kanbanboard' || $page_name=='view_users'){ echo'sidebar-collapse';}?>">

<div class="wrapper">

  <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="<?=base_url()?>assets/dist/img/logo_ptpnv.png" alt="AdminLTELogo" height="100" width="100">
  </div>

  <!-- include view header or topmenu -->
  <?php include('include_header.php') ?>

  <!-- include sidebar menu -->
  <?php include('include_menu.php') ?>

  <!-- content -->
  <?php include "pages/" . $page_name . ".php"; ?>

  <!-- include footer -->
  <?php include('include_footer.php') ?>

  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
</div>
<!-- ./wrapper -->

  <!-- include footer -->
  <?php include('include_js.php') ?>

</body>
</html>
