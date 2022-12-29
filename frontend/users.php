<?php

/**
 * users.php Main Page.
 *
 * Copyright (C) 2022 Agronorte <alboresmariano@gmail.com>
 *
 * 
 * @package frontend.Agronorte
 * @author  Mariano Alborés <alboresmariano@gmail.com>
 */

namespace Agronorte;

// simple autoloader according standard PSR-0
require_once(realpath(dirname(__FILE__) . '/../tools/Autoload.php'));

use Agronorte\core\Configuration;
use Agronorte\domain\User;
use Agronorte\frontend\inc\Menu;
use Agronorte\tools\Additional;
use Agronorte\tools\Data;
use Agronorte\tools\Secure;
use Agronorte\tools\Utils;

// sessions handler / permissions
session_start();
$user       = User::load(['id' => (int) $_SESSION['session']['id']]);
$validation = Secure::permissions($user);
if (false === $validation['valid'])
{
    header('Location: ' . $validation['url']);
}

// needed data
$params = [];
$table  = Data::users($user);

// needed for top
require_once(realpath(dirname(__FILE__) . '/inc/Top.php'));
?>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">

  <!-- Preloader -->
  <?php echo Utils::preloader();?>

  <!-- Navbar -->
  <?php echo Menu::navigation();?>

  <!-- Main Sidebar Container -->
  <?php echo Menu::aside($user);?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header mb-2">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Usuarios</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="user.php" class="btn btn-sm btn-success btn-block"><i class="fas fa-plus mr-2"></i> Crear usuario</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <?php echo Utils::table($table);?>
        </div>
    </section>
  </div>

  <!-- Main Footer -->
  <?php echo Menu::footer();?>
</div>

<!-- Scripts -->
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="plugins/jszip/jszip.min.js"></script>
<script src="plugins/pdfmake/pdfmake.min.js"></script>
<script src="plugins/pdfmake/vfs_fonts.js"></script>
<script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<script src="js/adminlte.js"></script>
<script>
$(function () {
    // Initialize datatable
    $('#<?php echo $table['id'];?>').DataTable({
        dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>tp",
        lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
        buttons: [
            {extend: 'copy',className: 'btn-sm'},
            {extend: 'csv',title: 'Users', className: 'btn-sm'},
            {extend: 'pdf', title: 'Users', className: 'btn-sm'},
            {extend: 'print',className: 'btn-sm'}
        ],
        order: [[ 0, "desc" ]],
        pageLength: 25,
        lengthChange: true
    });
});
</script>

<?php
// needed for bottom
require_once(realpath(dirname(__FILE__) . '/inc/Bottom.php'));