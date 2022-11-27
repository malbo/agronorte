<?php

/**
 * index.php Main Page.
 *
 * Copyright (C) 2022 Foodtech <alboresmariano@gmail.com>
 *
 * 
 * @package frontend.Foodtech
 * @author  Mariano Alborés <alboresmariano@gmail.com>
 */

namespace Foodtech;

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
<img src="images/logo.png" class="logo" />
<!-- end Home -->


<!-- Scripts -->

<!-- end Scripts -->

<?php
// needed for bottom
require_once(realpath(dirname(__FILE__) . '/inc/Bottom.php'));