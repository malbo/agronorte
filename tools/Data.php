<?php

/**
 * Data.php  General queris and views.
 *
 * Copyright (C) 2022 Agronorte <alboresmariano@gmail.com>
 *
 * @package domain.Agronorte
 * @author  Mariano Alborés <alboresmariano@gmail.com>
 */

namespace Agronorte\tools;

// simple autoloader according standard PSR-0
require_once(realpath(dirname(__FILE__) . '/../tools/Autoload.php'));

use Agronorte\core\Configuration;
use Agronorte\domain\User;
use Agronorte\tools\Categorizations;
use Agronorte\tools\Additional;

class Data
{
    /**
     * Load users
     * 
     * @param object $usr User that require report
     * @return object/array
     */
    public static function users($usr)
    {
        $users      = User::load([], true);
        $return     = [];
        $body       = [];
        $header     = ['ID', 'Nombre', 'Apellido', 'E-mail', 'Rol', 'Estado', 'Creado', ''];
        $status     = Categorizations::status();
        $roles      = Categorizations::roles();
        
        foreach ($users as $user)
        {
            $id         = $user['id'];
            $goto       = $id === $usr->id ? '<a href="account.php"><i class="fa fa-edit"></i></a>' : '<a href="user.php?id=' . $id . '"><i class="fa fa-edit"></i></a>';
            $body[]     = [$id, $user['name'], $user['lastname'], $user['email'], ucfirst($roles[$user['role']]), ucfirst($status[$user['status']]), $user['created'], $goto];
        }

        $return['id']       = 'users';
        $return['header']   = $header;
        $return['body']     = $body;

        return $return;   
    } 
}