<?php

/**
 * account.php Account Page.
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
$params         = [];
$status         = Categorizations::status();
$roles          = Categorizations::roles();
$stat           = ucfirst($status[$user->status]);
$role           = ucfirst($roles[$user->role]);
$permission     = $user->role !== Categorizations::roles(true)['user'] ? false : true;

// needed for top
require_once(realpath(dirname(__FILE__) . '/inc/Top.php'));
?>
<body class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<input type="hidden" id="id" value="<?php echo $user->id;?>" />
<input type="hidden" id="oldemail" value="<?php echo $user->email;?>" />
<input type="hidden" id="oldpassword" value="<?php echo $user->password;?>" />
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
            <h1 class="m-0">Cuenta</h1>
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
                        'id'        => $user->id,
                        'name'      => $user->name,
                        'lastname'  => $user->lastname,
                        'email'     => $user->email,
                        'status'    => $stat,
                        'role'      => $role,
                        'created'   => $user->created
                    ]);?>
                </div>
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-body">
                            <div class="tab-content">
                                    <div class="card-body">
                                        <?php 
                                            echo Utils::input('name', $user->name, 'Nombre', 'text', null);
                                            echo Utils::input('lastname', $user->lastname, 'Apellido', 'text', null);
                                            echo Utils::input('email', $user->email, 'E-mail', 'text', null, $permission);
                                            echo Utils::input('password', $user->password, 'Password', 'password', null);
                                            echo Utils::input('status', $user->status, 'Estado', 'select', $status, $permission, false);
                                            echo Utils::input('role', $user->role, 'Rol', 'select', $roles, $permission, false);
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
<script src="../plugins/file-upload/jquery.uploadfile.min.js?cb=<?php echo uniqid();?>"></script>
<script src="js/adminlte.js"></script>
<script src="js/ready.js?cb=<?php echo uniqid();?>"></script>
<script>
$(function () {
    // Banner uploader
    $("#pic-upload").uploadFile({
        url:                    "upload.php",
        method:                 "POST",
        multiple:               false,
        dragDrop:               false,
        showFileCounter:        false,
        fileName:               "file",
        maxFileSize:            500 * 1024,
        formData:{
            "id":  "<?php echo $user->id;?>"
        },
        acceptFiles:            ".jpg, .JPG, .jpeg, .JPEG, .png, .PNG, .gif, .GIF",
        showPreview:            false,
        showCancel:             false,
        showStatusAfterSuccess: false,
        showProgress:           false,
        onSelect:function(){
            $("#pic-upload").text('Procesando, guardar perfil...');
        }
    });
});
</script>
<?php
// needed for bottom
require_once(realpath(dirname(__FILE__) . '/inc/Bottom.php'));