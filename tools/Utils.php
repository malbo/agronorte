<?php

/**
 * Utils.php  General utilities.
 *
 * Copyright (C) 2022 Agronorte <alboresmariano@gmail.com>
 *
 * @package tools.Agronorte
 * @author  Mariano Alborés <alboresmariano@gmail.com>
 */

namespace Agronorte\tools;

// simple autoloader according standard PSR-0
require_once(realpath(dirname(__FILE__) . '/Autoload.php'));

use Agronorte\core\Configuration;

class Utils
{
    /**
     * Preloader for pages
     * 
     * @return mixed
     */
    public static function preloader()
    {
        return '<div class="preloader flex-column justify-content-center align-items-center">
                    <img class="animation__wobble" src="img/logo.png" alt="AgronorteLogo" height="60" width="60">
                </div>';
    }

    /**
     * Individual fields for forms.
     * Support input hidden, input text, input password, select, textarea.
     * 
     * @param string $id Name for id
     * @param string $value Value for id
     * @param string $name Name for label and default placeholder
     * @param string $type Input(text)|Input(password)|Select|Radio|Checkbox
     * @param array $data Data to construct select, radio or checkboxes
     * @param boolean $readonly Specify is data is readonly
     * @param boolean $zero Default 0 value
     * @param string $onevent Callback JS function
     * @param mixed $rows Height for textarea
     * @param mixed $placeholder Placeholder
     * @param mixed $min Minimum for number
     * @param mixed $max Maximum for number
     * @param mixed $maxlenght Maximum for text and textarea
     * @return mixed
     */
    public static function input($id, $value, $name=null, $type, $data=null, $readonly=false, $zero=true, $onevent=false, $rows=false, $placeholder=null, $min=0,  $max=100, $maxlenght=null)
    {
        $mode   = true === boolval($readonly) ? 'readonly' : '';
        $style  = true === boolval($readonly) ? 'readonly' : '';

        $html = null;
        $html.= '<div class="form-group">';
        $html.= '<label for="name">' . $name . '</label>';

        switch($type)
        {
            case 'password':
                $html.= '<input type="password" class="form-control ' . $style . '" id="' . $id . '" value="' . $value . '" ' . $mode . ' />';
                break;

            case 'select':
                $read = $mode === 'readonly' ? 'disabled="disabled"' : null;
                $html.= '<select id="' . $id . '" class="form-control ' . $style . '" ' . $read . '  ' . $onevent . '>';
                if (true === $zero)
                {
                    $html.= '<option value="0">Seleccionar..</option>';
                }
                foreach ($data as $key => $val)
                {
                    $html.= '<option value="' . $key . '" ';
                    if ($key === $value)
                    {
                        $html.= 'selected ';
                    }
                    $html.= '>' . ucfirst($val) . '</option>';
                }
                $html.= '</select>';               
                break;

            case 'text':
                $html.= '<input type="text" class="form-control ' . $style . '" id="'. $id . '" value="' . $value . '" ' . $mode . ' ' . $onevent . ' placeholder="' . $placeholder . '" autocomplete="never" ' . $maxlenght . ' />';
                break;

            default:
                break;
        }

        $html.= '</div>';

        return $html;
    }

    /**
     * Profile.
     * 
     * @param array $user Data
     * @return string
     */
    public static function profile($user)
    {
        $html = null;
        $html.= '<div class="card card-outline">
                    <div class="card-body box-profile">';

        $html.= '<div class="text-center">
                    <img class="profile-user-img img-fluid img-circle profile-pic" src="img/user.jpg" alt="User profile picture">
                    <div class="p-image">
                        <i class="fa fa-camera upload-button"></i>
                        <input class="file-upload" type="file" accept="image/*"/>
                    </div>
                </div>';

        $html.= '<h3 class="profile-username text-center" id="resume-name">' . $user['name'] . ' ' . $user['lastname'] . '</h3>
                        <p class="text-muted text-center" id="resume-email">' . $user['email'] . '</p>

                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <b>Estado</b> <a class="float-right" id="resume-status">' . $user['status'] . '</a>
                            </li>
                            <li class="list-group-item">
                                <b>Rol</b> <a class="float-right" id="resume-role">' . $user['role'] . '</a>
                            </li>
                            <li class="list-group-item">
                                <b>Creado</b> <a class="float-right">' . $user['created'] . '</a>
                            </li>
                        </ul>
                    </div>
                </div>';
        
        return $html;

    }

    /**
     * Table header.
     * 
     * @param string $table Data for table
     * @return string
     */
    public static function table($table)
    {
        $html = null;
        $html.= '<table id="' . $table['id'] . '" class="table table-striped table-bordered table-hover" style="width:100%">';
        
        $html.= '<thead>';
        $html.= '<tr>';
        
        foreach ($table['header'] as $header)
        {
            $html.= '<th>' . $header . '</th>';
        }  
        
        $html.= '</tr>';
        $html.= '</thead>';
        
        $html.= '<tbody>';
        
        foreach ($table['body'] as $values)
        {
            $html.= '<tr>';
            foreach ($values as $body)
            {  
                $html.= '<td>' . $body . '</td>';
            }
            $html.= '</tr>';
        }  
        
        $html.= '</tbody>';
        
        $html.= '</table>';
        
        return $html;

    }
}