<?php

/**
 * user.php User Page.
 *
 * Copyright (C) 2022 Agronorte <alboresmariano@gmail.com>
 *
 * @package frontend.Agronorte
 * @author  Mariano Alborés <alboresmariano@gmail.com>
 */

namespace Agronorte;

// simple autoloader according standard PSR-0
require_once(realpath(dirname(__FILE__) . '/../tools/Autoload.php'));

use Agronorte\core\Configuration;
use Agronorte\domain\User;
use Agronorte\domain\Report;
use Agronorte\frontend\inc\Menu;
use Agronorte\tools\Additional;
use Agronorte\tools\Categorizations;
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
$params = [
    'id_user'   => !is_null(filter_input(INPUT_GET, 'id')) ? filter_input(INPUT_GET, 'id') : false,
];

// data for edition or creation
$status         = Categorizations::status();
$roles          = Categorizations::roles();
if (false !== $params['id_user'])
{
    $usr        = User::load(['id' => $params['id_user']]);
    $profile    = $usr->name . ' ' . $usr->lastname;
    $id         = $usr->id;
    $name       = $usr->name;
    $lastname   = $usr->lastname;
    $email      = $usr->email;
    $password   = $usr->password;
    $stat       = ucfirst($status[$usr->status]);
    $role       = ucfirst($roles[$usr->role]);
    $created    = $user->created;
}
else
{
    $usr        = new User([]);
    $profile    = 'Nuevo Usuario';
    $id = $name = $lastname = $email = $password = $stat = $role = $created = null;
}
$report     = Report::load(['id_user' => $params['id_user']]);
if(false !== $report)
{
    $id_rep     = $report->id;
    $rep_name   = $report->name;
    $rep_id     = $report->report;
}
else
{
    $rep_name = $rep_id = $id_rep = null;
}

// needed for top
require_once(realpath(dirname(__FILE__) . '/inc/Top.php'));
?>
<body class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<input type="hidden" id="id" value="<?php echo $id;?>" />
<input type="hidden" id="oldemail" value="<?php echo $email;?>" />
<input type="hidden" id="oldpassword" value="<?php echo $password;?>" />
<input type="hidden" id="id-report" value="<?php echo $id_rep;?>" />
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
                <div class="col-sm-6">
                    <h1 class="m-0"><?php echo $profile;?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="users.php" class="btn btn-sm btn-success btn-block"><i class="fas fa-reply mr-2"></i> Volver a usuarios</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3">
                    <!-- Profile Image -->
                    <?php echo Utils::profile([
                        'id'        => $id,
                        'name'      => $name,
                        'lastname'  => $lastname,
                        'email'     => $email,
                        'status'    => $stat,
                        'role'      => $role,
                        'created'   => $created
                    ]);?>
                </div>
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-body">
                            <div class="tab-content">
                                    <div class="card-body">
                                        <?php 
                                            echo Utils::input('name', $name, 'Nombre', 'text');
                                            echo Utils::input('lastname', $lastname, 'Apellido', 'text');
                                            echo Utils::input('email', $email, 'E-mail', 'text');
                                            echo Utils::input('password', $password, 'Password', 'password');
                                            echo Utils::input('status', $usr->status, 'Estado', 'select', $status);
                                            echo Utils::input('role', $usr->role, 'Rol', 'select', $roles);
                                        ?>

                                        <?php if($user->role === Categorizations::roles(true)['superadmin']){ ?>
                                            <div class="row reports-fields">
                                                <div class="col-md-3">
                                                    <?php echo Utils::input('report', $rep_name, 'Nombre Tablero', 'text');?>
                                                </div>
                                                <div class="col-md-8">
                                                    <?php echo Utils::input('report-id', $rep_id, 'ID Tablero', 'text');?>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="form-group">
                                                        <label for="empty">&nbsp;</label>
                                                        <button class="btn btn-danger btn-block" onclick="deleteReport(); return false;">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php 
                                            } else {
                                                echo Utils::input('report', $rep_name, '', 'hidden');
                                                echo Utils::input('report-id', $rep_id, '', 'hidden');
                                            }   
                                        ?>

                                    </div>

                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-success" onclick="storeUser('USER'); return false;">Guardar</button>
                                    </div>
                            </div>
                        </div>
                </div>
            </div>
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
<script src="../plugins/toastr/toastr.min.js"></script>
<script src="js/adminlte.js"></script>

<?php
// needed for bottom
require_once(realpath(dirname(__FILE__) . '/inc/Bottom.php'));