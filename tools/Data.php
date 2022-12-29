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
        /**
         * Get report
         */
        $report = Report::load(['id_user' => $usr->id]);

        // Prevent to consume API for null|empty values
        if(true === empty($report))
        {
            return [
                'name'      => 'Dashboard v1',
                'report'    => null,
                'iframe'    => 'No hay reportes cargados aún.'
            ];  
        }

        /**
         * Power BI Access Token (Microsoft Access)
         * For general pourposes
         */
        // $data   = [
        //     'tenant'        => self::AZURE_TENANT_ID, 
        //     'client_id'     => self::AZURE_CLIENT_ID, 
        //     'client_secret' => self::CLIENT_SECRET, 
        //     'scope'         => 'https://graph.microsoft.com/.default', 
        //     'grant_type'    => 'client_credentials'
        // ]; 
        // $response       = shell_exec("curl -X POST --header 'Content-Type: application/x-www-form-urlencoded' -d '" . http_build_query($data) . "' 'https://login.microsoftonline.com/" . self::AZURE_TENANT_ID . "/oauth2/v2.0/token'");
        // $access_token   = json_decode($response, true)['access_token'];

        /**
         * Power BI Access Token
         * Specific for Power BI service
         */
        Dotenv::load(realpath(dirname(__FILE__) . '/../.env'));
        $data   = [
            'client_id'     => self::AZURE_CLIENT_ID, 
            'client_secret' => self::CLIENT_SECRET, 
            'username'      => getenv('PBIUSERNAME'),
            'password'      => getenv('PBIPASSWORD'),
            'resource'      => 'https://analysis.windows.net/powerbi/api', 
            'grant_type'    => 'password'
        ]; 
        $response       = shell_exec("curl -X POST --header 'Content-Type: application/x-www-form-urlencoded' -d '" . http_build_query($data) . "' 'https://login.microsoftonline.com/" . self::AZURE_TENANT_ID . "/oauth2/token'");
        $access_token   = json_decode($response, true)['access_token'];

        /**
         * Refresh Dataset
         * https://learn.microsoft.com/en-us/rest/api/power-bi/datasets/refresh-dataset
         */
        // $body   = [
        //     'notifyOption' => ''
        // ];
        // $dtset  = shell_exec("curl -X POST --header 'Content-Type: application/x-www-form-urlencoded' --header 'Accept: application/json' --header 'Authorization: Bearer " . $access_token . "' -d '" . http_build_query($body) . "' 'https://api.powerbi.com/v1.0/myorg/datasets/" . $report->name . "/refreshes'");
            
        /**
         * Power BI Embed Token
         * https://learn.microsoft.com/es-es/rest/api/power-bi/embed-token/generate-token
         */
        $data   = json_encode([
            'datasets'          => [['id' => $report->dataset_id]],
            'reports'           => [['id' => $report->report_id]],
            'lifetimeInMinutes' => 60
        ]); 
        $response       = shell_exec("curl -X POST --header 'Content-Type: application/json; odata.metadata=minimal' --header 'Accept: application/json' --header 'Authorization: Bearer " . $access_token . "' -d '" . $data . "' 'https://api.powerbi.com/v1.0/myorg/GenerateToken'"); 
        $embed_token    = json_decode($response, true)['token']; 
        
        /**
         * Get report API method
         * https://learn.microsoft.com/en-us/rest/api/power-bi/reports/get-report
         */
        $response       = shell_exec("curl -X GET --header 'Content-Type: application/x-www-form-urlencoded' --header 'Accept: application/json' --header 'Authorization: Bearer " . $access_token . "' 'https://api.powerbi.com/v1.0/myorg/reports/" . $report->report_id . "'");
        $report_values  = json_decode($response, true);
        
        if(false === empty($report))
        {
            $url    = $report_values['embedUrl'];
            $return = [
                'name'          => $report_values['name'],
                'iframe'        => '<div style="width:100%; height:calc(100vh - 200px);" id="embedContainer" class="embedContainer"></div>',
                'embedToken'    => $embed_token,
                'embedUrl'      => $report_values['embedUrl'],
                'reportId'      => $report->report_id
            ]; 
        }
        else
        {
            $return = [
                'name'      => 'Dashboard v1',
                'iframe'    => 'No hay reportes cargados aún.'
            ];  
        }

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