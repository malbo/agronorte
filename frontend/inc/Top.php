<?php

/**
 * Top.php  Top.
 *
 * Copyright (C) 2022 Foodtech <alboresmariano@gmail.com>
 *
 * @package inc.frontend.Platform
 * @author  Mariano Alborés <alboresmariano@gmail.com>
 */
    
namespace Foodtech\frontend\inc;

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// simple autoloader according standard PSR-0
require_once(realpath(dirname(__FILE__) . '/../../tools/Autoload.php'));

use Foodtech\core\Configuration;

Configuration::secure();
?>
<!DOCTYPE html>
<html lang="en">
<head>   
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo Configuration::FANTASY;?> :: Programmatic Platform</title>

    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,800italic,800,700italic,700,600italic,400italic,600,300italic,300|Oswald:400,300,700' rel='stylesheet' type='text/css'>
    <!-- Bootstrap -->
    <link href="styles/main.css?cb=<?php echo uniqid();?>" rel="stylesheet">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
</head>
<body>