<?php

/**
 * upload.php Upload files.
 * Campaign
 *
 * Copyright (C) 2022 Agronorte <alboresmariano@gmail.com>
 *
 * @package frontend.Platform
 * @author  Mariano Alborés <alboresmariano@gmail.com>
 */

namespace Agronorte\frontend;

// simple autoloader according standard PSR-0
require_once(realpath(dirname(__FILE__) . '/../tools/Autoload.php'));

use Agronorte\tools\Additional;

class Upload
{
    /**
     * Upload function
     * 
     * @param array $params data
     * @return string
     */
    public static function upload($params)
    {
        // get id and rename with timestamp to prevent duplicates
        $id         = $params['id'];
        $filename   = $params['filename'];

        if(!is_dir("/tmp/" . $id))
        {
            mkdir("/tmp/" . $id, 0777);
        }

        if($params && move_uploaded_file($params['tmp'], "/tmp/" . $id . "/" . $filename))
        {
            return json_encode([
                'status' => true
            ]);
        }
    }
}

/**
 * File to manage uploads to server
 */
echo Upload::upload([
    'filename'      => $_FILES['file']['name'],
    'filetype'      => $_FILES['file']['type'],
    'filesize'      => $_FILES['file']['size'],
    'tmp'           => $_FILES['file']['tmp_name'],
    'id'            => filter_input(INPUT_POST, 'id')
]);