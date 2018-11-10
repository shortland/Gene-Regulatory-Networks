<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicNetworkController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function list()
    {
        $dir = '../server/php/files/';
        $files = scandir($dir);
        $jsonFiles = [];
        foreach ($files as $file) {
            if (preg_match('/\.csv\.json$/', $file)) {
                $jsonFiles[] = [
                    'name' => $file,
                    'id' => base64_encode(rawurlencode($file)),
                    //'modified' => date("F d, H:i", filemtime(__FULL_FILE_PATH__))
                ];
            }
        }
        return ['network_list' => $jsonFiles];
    }

    public function changesAfterEpoch(Request $request)
    {
        $inputs = $request->all();

        if (!isset($inputs['networkId']) || empty($inputs['networkId'])) {
            return $this->sendCustomResponse(400, 'Required network id');
        }

        if (!isset($inputs['epoch']) || empty($inputs['epoch'])) {
            $inputs['epoch'] = time() - (60 * 60 * 24); // get changes of last 24 hours if not given
        }

        $servername = env('DB_HOST', 'mysql'); 
        $username = env('DB_USERNAME', 'mysql');
        $password = env('DB_PASSWORD', 'mysql');
        $dbname = env('DB_DATABASE', 'mysql');

        try {
            $conn = new \PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            
            $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $timediff = $inputs['epoch'];
            $networkId = $inputs['networkId'];// preg_replace('/=/', '', $inputs['networkId']);
            $sql = "SELECT `nodeCurrentId` as `id`, `nodeNextId` as `next`, `nodeParentId` as `parent` FROM `networks_dynamics` WHERE unix_timestamp(`modifyTimestamp`) > $timediff AND `networkId` = '$networkId'";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute();

            $result = $stmt->setFetchMode(\PDO::FETCH_ASSOC);
            try {
                //var_dump();
                //$clientId = $stmt->fetchAll()[0]['clientId'];
            }
            catch(\Exception $e) {
                return $this->sendCustomResponse(401, 'Unauthorized request');
            }

        }
        catch(PDOException $e) {
            return $e->getMessage();
        }

        $conn = null;
        return $stmt->fetchAll();
    }
}