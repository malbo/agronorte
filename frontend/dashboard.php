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

use Foodtech\core\Configuration;
use Foodtech\tools\Additional;

// sessions handler / permissions
session_start();

// needed data
$params = [];

// needed for top
require_once(realpath(dirname(__FILE__) . '/inc/Top.php'));

var_dump(var_export($_SESSION['session'], true));
?>
<body>
    <p class="mt-5"><a href="logout.php">Logout</a></p>
<?php
// needed for bottom
require_once(realpath(dirname(__FILE__) . '/inc/Bottom.php'));