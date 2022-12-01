<?php

/**
 * dashboard.php Main Page.
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
$report = Data::report($user);

// needed for top
require_once(realpath(dirname(__FILE__) . '/inc/Top.php'));
?>
<body class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
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
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-12">
            <h1 class="m-0"><?php echo $report['name'];?></h1>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <section class="content">
          <div class="container-fluid text-center">
          <?php echo $report['iframe'];?>
          </div>
    </section>
  </div>

  <!-- Main Footer -->
  <?php echo Menu::footer();?>
</div>

<!-- Scripts -->
<script src="../plugins/jquery/jquery.min.js"></script>
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<script src="js/adminlte.js"></script>

<?php
// needed for bottom
require_once(realpath(dirname(__FILE__) . '/inc/Bottom.php'));