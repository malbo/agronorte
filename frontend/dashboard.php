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

// needed data
$params = [];

// needed for top
require_once(realpath(dirname(__FILE__) . '/inc/Top.php'));
?>
<!-- Home -->

<!-- end Home -->


<!-- Scripts -->

<!-- end Scripts -->

<?php
// needed for bottom
require_once(realpath(dirname(__FILE__) . '/inc/Bottom.php'));