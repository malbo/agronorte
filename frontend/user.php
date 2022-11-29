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
$permissions    = Categorizations::permissions();
if (false !== $params['id_user'])
{
    $user       = User::load(['id' => $params['id_user']]);
    $profile    = $user->name . ' ' . $user->lastname;
    $id         = $user->id;
    $name       = $user->name;
    $lastname   = $user->lastname;
    $email      = $user->email;
    $password   = $user->password;
    $stat       = ucfirst($status[$user->status]);
    $role       = ucfirst($roles[$user->role]);
    $created    = $user->created;
}
else
{
    $user       = new User([]);
    $profile    = 'Nuevo Usuario';
    $id = $name = $lastname = $email = $password = $stat = $role = $created = null;
}

// needed for top
require_once(realpath(dirname(__FILE__) . '/inc/Top.php'));
?>
<body class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<input type="hidden" id="id" value="<?php echo $id;?>" />
<input type="hidden" id="oldemail" value="<?php echo $email;?>" />
<input type="hidden" id="oldpassword" value="<?php echo $password;?>" />
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
                                            echo Utils::input('status', $user->status, 'Estado', 'select', $status);
                                            echo Utils::input('role', $user->role, 'Role', 'select', $roles);
                                            echo Utils::input('permissions', $user->permissions, 'Permisos', 'select', $permissions);
                                        ?>
                                    </div>

                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-success" onclick="storeUser(); return false;">Guardar</button>
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