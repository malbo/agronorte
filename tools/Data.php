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
use Agronorte\domain\Report;
use Agronorte\domain\User;
use Agronorte\tools\Additional;
use Agronorte\tools\Categorizations;
use Agronorte\tools\Dotenv;

class Data
{
    /**
     * Constants
     */
    const AZURE_TENANT_ID       = 'cf38fd76-cbf5-4129-a362-70bb00cff66a';
    const AZURE_CLIENT_ID       = '5e93a9eb-e2ff-4bfc-bcf9-e1f33123129a';
    const CLIENT_SECRET         = 'TGn8Q~T7gWG2myQ4t2cQ5AI7.vAVx4C1EV~ikcst';
    const PBI_WORKSPACE_ID      = '82640e74-4c8b-414e-b5c7-9504c0e71ee9';

    /**
     * Load report
     * API: https://learn.microsoft.com/en-us/rest/api/power-bi
     * 
     * @param object $usr User that require report
     * @return object/array
     */
    public static function report($usr)
    {
        // Get report data
        $report = Report::load(['id_user' => $usr->id]);

        // Power BI Access Token (Microsoft Access)
        // $body   = [
        //     'tenant'        => self::AZURE_TENANT_ID, 
        //     'client_id'     => self::AZURE_CLIENT_ID, 
        //     'client_secret' => self::CLIENT_SECRET, 
        //     'scope'         => 'https://graph.microsoft.com/.default', 
        //     'grant_type'    => 'client_credentials'
        // ]; 
        // $access = shell_exec("curl -X POST --header 'Content-Type: application/x-www-form-urlencoded' -d '" . http_build_query($body) . "' 'https://login.microsoftonline.com/" . self::AZURE_TENANT_ID . "/oauth2/v2.0/token'");
        // $token  = json_decode($access, true)['access_token'];
        // Additional::log("TOKEN", json_decode($access, true)['access_token']);

        // Power BI Access Token
        Dotenv::load(realpath(dirname(__FILE__) . '/../.env'));
        $body   = [
            'client_id'     => self::AZURE_CLIENT_ID, 
            'client_secret' => self::CLIENT_SECRET, 
            'username'      => getenv('PBIUSERNAME'),
            'password'      => getenv('PBIPASSWORD'),
            'resource'      => 'https://analysis.windows.net/powerbi/api', 
            'grant_type'    => 'password'
        ]; 
        $access = shell_exec("curl -X POST --header 'Content-Type: application/x-www-form-urlencoded' -d '" . http_build_query($body) . "' 'https://login.windows.net/common/oauth2/token'");
        $token  = json_decode($access, true)['access_token'];

        // Refresh Dataset 
        $body   = [
            'notifyOption' => ''
        ];
        $dtset  = shell_exec("curl -X POST --header 'Content-Type: application/x-www-form-urlencoded' --header 'Accept: application/json' --header 'Authorization: Bearer " . $token . "' -d '" . http_build_query($body) . "' 'https://api.powerbi.com/v1.0/myorg/datasets/" . $report->name . "/refreshes'");

        Additional::log("REFRESH_DATASET", $dtset);    

        // Get report
        $query  = shell_exec("curl -X GET --header 'Content-Type: application/x-www-form-urlencoded' --header 'Accept: application/json' --header 'Authorization: Bearer " . $token . "' 'https://api.powerbi.com/v1.0/myorg/reports/" . $report->report . "'");
        $resp   = json_decode($query, true);

        Additional::log("REPORT", $resp);
        
        if(false === empty($report))
        {
            $url    = 'https://app.powerbi.com/reportEmbed?reportId=' . $resp['id'] . '&autoAuth=true&ctid=' . self::AZURE_TENANT_ID;
            $return = [
                'name'      => $resp['name'],
                'report'    => $report->report,
                // 'iframe'    => '<iframe title="' . $resp['name'] . '" src="' . $url . '" width="100%" height="800" frameborder="0" allowfullscreen="allowfullscreen" onload="resizeIframe(this);" style="border:3px solid #377c2c;"></iframe>'
                'iframe'    => '<div style="width:100%;height:calc(100vh - 200px);background-color:yellow"><iframe src="' . $url . '" style="width:100%; height:100%; padding:0; margin:0; border:1px solid #377c2c;"></iframe></div>'
            ]; 
        }
        else
        {
            $return = [
                'name'      => 'Dashboard v1',
                'report'    => null,
                'iframe'    => 'No hay reportes cargados aún.'
            ];  
        }

        return $return;
    } 

    /**
     * Create curl method function
     *
     * @access public
     * @return boolean
     */
    public static function curlSender($method='POST', $url, $data)
    {
        $token      = self::TOKEN;
        $header     = '-H "accept: application/json" -H "Authorization: Token token=' . $token . '" -H "Content-Type: application/json"';
        $response   = shell_exec('curl -X ' . $method . ' "' . self::BASE_URL . $url . '" ' . $header . ' -d \'' . json_encode($data, JSON_HEX_QUOT) . '\'');
        $return     = json_decode($response, true);
        
        return $return;
    }

    /**
     * Manage roles
     * 
     * @param object $usr User that require report
     * @return object/array
     */
    public static function roles($usr)
    {
        $roles  = Categorizations::roles();
        $return = [];
        foreach($roles as $key => $value)
        {
            if($usr->role >= $key)
            {
                $return[$key] = $value;
            }
        }

        return $return;
    } 

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
            if($usr->role >= $user['role'])
            {
                $id         = $user['id'];
                $goto       = $id === $usr->id ? '<a href="account.php"><i class="fa fa-edit"></i></a>' : '<a href="user.php?id=' . $id . '"><i class="fa fa-edit"></i></a>';
                $body[]     = [$id, $user['name'], $user['lastname'], $user['email'], ucfirst($roles[$user['role']]), ucfirst($status[$user['status']]), $user['created'], $goto];
            }
        }

        $return['id']       = 'users';
        $return['header']   = $header;
        $return['body']     = $body;

        return $return;   
    } 
}