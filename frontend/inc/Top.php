<?php

/**
 * Top.php  Top.
 *
 * Copyright (C) 2022 Agronorte <alboresmariano@gmail.com>
 *
 * @package inc.frontend.Agronorte
 * @author  Mariano Alborés <alboresmariano@gmail.com>
 */
    
namespace Agronorte\frontend\inc;

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// simple autoloader according standard PSR-0
require_once(realpath(dirname(__FILE__) . '/../../tools/Autoload.php'));

use Agronorte\core\Configuration;

Configuration::secure();
?>
<!DOCTYPE html>
<html lang="en">
<head>   
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo Configuration::FANTASY;?></title>

    <!-- Vendor styles -->
    <link rel="stylesheet" href="../plugins/toastr/toastr.min.css" />
    <link rel="stylesheet" href="../plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="../plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="../plugins/file-upload/uploadfile.css?cb=<?php echo uniqid();?>" />
    <link rel="stylesheet" href="../plugins/sweetalert/lib/sweet-alert.css" />

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="css/adminlte.css">
    <link href="css/main.css?cb=<?php echo uniqid();?>" rel="stylesheet">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <!-- Scripts JS -->
    <script src="js/scripts.js?cb=<?php echo uniqid();?>"></script>
    <!-- recaptcha -->
    <script src="https://www.google.com/recaptcha/api.js"></script>
</head>
<body>