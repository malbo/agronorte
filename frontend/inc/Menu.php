<?php

/**
 * Menu.php  Menu.
 *
 * Copyright (C) 2022 Agronorte<alboresmariano@gmail.com>
 *
 * @package inc.frontend.Agronorte
 * @author  Mariano Alborés <alboresmariano@gmail.com>
 */

namespace Agronorte\frontend\inc;

// simple autoloader according standard PSR-0
require_once(realpath(dirname(__FILE__) . '/../../tools/Autoload.php'));

use Agronorte\core\Configuration;
use Agronorte\tools\Additional;
use Agronorte\tools\Categorizations;

class Menu
{
    
    /**
     * Panel for lateral menu
     * 
     * @param array $user User data
     * @return string
     */
    public static function aside($user)
    {
        // prepare data
        $name = $user->name . ' ' . $user->lastname;

        $html = null;
        $html.= '<aside class="main-sidebar sidebar-dark-primary elevation-4">';

        $html.= '<a href="index3.html" class="brand-link">
                    <img src="img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3">
                <span class="brand-text font-weight-light">Agronorte</span>
                </a>';
    
        $html.= '<div class="sidebar">
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img src="img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
                    </div>
                    <div class="info">
                        <a href="#" class="d-block">' . $name . '</a>
                    </div>
                </div>';

          $html.= '<nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-header">EXAMPLES</li>
            
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon far fa-envelope"></i>
                            <p>Mailbox <i class="fas fa-angle-left right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="pages/mailbox/mailbox.html" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Inbox</p>
                            </a>
                        </li>
                        </ul>
                    </li>
                    </ul>
                </nav>
                </div>
            </aside>';
        
        return $html;
    }

    /**
     * Footer panel for bottom
     * @return string
     */
    public static function footer()
    {
        $html = null;
        $html.= '<footer class="main-footer">
                    <strong>Copyright &copy; 2022-' . date('Y') . ' ' . Configuration::COMPANY . '.</strong>
                    All rights reserved.
                    <div class="float-right d-none d-sm-inline-block">
                        <b>Version</b> 1.0.0
                    </div>
                </footer>';
        
        return $html;
    }
    
    /**
     * Panel for top
     * 
     * @return string
     */
    public static function navigation()
    {
        $html = null;
        $html.= '<nav class="main-header navbar navbar-expand navbar-dark">
                    <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                    </li>
                    </ul>
                
                    <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                        <i class="fas fa-expand-arrows-alt"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php" role="button">
                        <i class="fas fa-dot-circle"></i>
                        </a>
                    </li>
                    </ul>
                </nav>';
        
        return $html;
    }
}