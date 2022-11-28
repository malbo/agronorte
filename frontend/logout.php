<?php

/**
 * logout.php Logout.
 *
 * Copyright (C) 2022 Agronorte <alboresmariano@gmail.com>
 *
 * 
 * @package frontend.Agronorte
 * @author  Mariano Alborés <alboresmariano@gmail.com>
 */

namespace Agronorte;

session_start();
session_unset();
session_destroy();
header('Location: index.php');