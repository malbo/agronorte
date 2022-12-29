<?php

/**
 * index.php login Page.
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
use Agronorte\tools\Additional;

// needed data
$params = [];

// needed for top
require_once(realpath(dirname(__FILE__) . '/inc/Top.php'));
?>
<body class="hold-transition login-page">
    <div class="login-box">
    <div class="card card-outline card-success">
        <div class="card-header text-center">
            <a href="#" class="h1"><?php echo Configuration::COMPANY;?></a>
        </div>
        <div class="card-body">
            <p class="login-box-msg">Ingrese sus datos de sesión</p>

            <form action="../../index3.html" method="post">
                <div class="input-group mb-3">
                    <input type="email" class="form-control" placeholder="Email" id="email" />
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>

                <div class="input-group mb-3">
                    <input type="password" class="form-control" placeholder="Password" id="password" />
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>

                <div class="mb-3 text-center">
                    <div class="g-recaptcha" data-sitekey="6LcrUjsjAAAAAArHJgbN2r7kY9IB0dW8XKcTTxzA" style="display: inline-block;"></div>
                </div>

                <div class="row">
                    <!-- <div class="col-8">
                        <a href="forgot-password.html">Reestablecer contraseña</a>
                    </div> -->

                    <div class="col-12">
                        <button type="submit" class="btn btn-success btn-block" onclick="login(); return false;">Login</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    </div>

<!-- Vendor scripts -->
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="plugins/toastr/toastr.min.js"></script>
<script src="js/adminlte.min.js"></script>
<?php
// needed for bottom
require_once(realpath(dirname(__FILE__) . '/inc/Bottom.php'));