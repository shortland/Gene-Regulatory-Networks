<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PrivateNetworkController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    private function generateNewCode() 
    {
        return str_random('32');;
    }

    /**
    *   THIS NEEDS TO APPEND TO THE FILE TOO!!!
    */
    public function modify(Request $request)
    {
        $inputs = $request->all();

        if (!isset($inputs['token']) || empty($inputs['token'])) {
            return $this->sendCustomResponse(401, 'Unauthorized request');
        }

        $clientId = $this->authenticateUser($inputs['token']);

        if (!isset($inputs['networkId']) || empty($inputs['networkId'])) {
            return $this->sendCustomResponse(400, 'Required network id');
        }

        if (!isset($inputs['edgeList']) || empty($inputs['edgeList'])) {
            return $this->sendCustomResponse(400, 'Required edgelist data');
        }

        $edgeDataList = json_decode(htmlspecialchars_decode($inputs['edgeList']));
        $edgeType = gettype($edgeDataList);

        if ($edgeType !== "array") {
            return $this->sendCustomResponse(400, 'Edgelist must be array');
        }

        foreach ($edgeDataList as $edgeNode) {
            $this->insertNetworkDynamics($clientId, $inputs['networkId'], $edgeNode[0], $edgeNode[1], $edgeNode[2]);
        }
    }

    private function insertNetworkDynamics($clientId, $networkId, $currentNode, $nextNode, $parentNode)
    {
        $servername = env('DB_HOST', 'mysql'); 
        $username = env('DB_USERNAME', 'mysql');
        $password = env('DB_PASSWORD', 'mysql');
        $dbname = env('DB_DATABASE', 'mysql');

        try {
            $conn = new \PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            
            $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $sql = "INSERT INTO `networks_dynamics` (`networkId`, `nodeCurrentId`, `nodeNextId`, `nodeParentId`, `modifierClientId`) VALUES ('$networkId', '$currentNode', '$nextNode', '$parentNode', '$clientId')";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute();

            $result = $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        }
        catch(PDOException $e) {
            return $e->getMessage();
        }

        $conn = null;
        return "ok";    
    }

    private function authenticateUser($token) 
    {
        $servername = env('DB_HOST', 'mysql'); 
        $username = env('DB_USERNAME', 'mysql');
        $password = env('DB_PASSWORD', 'mysql');
        $dbname = env('DB_DATABASE', 'mysql');

        try {
            $conn = new \PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            
            $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $sql = "SELECT `clientId` FROM `networks_users` WHERE `userToken` = '$token'";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute();

            $result = $stmt->setFetchMode(\PDO::FETCH_ASSOC);
            try {
                $clientId = $stmt->fetchAll()[0]['clientId'];
            }
            catch(\Exception $e) {
                return $this->sendCustomResponse(401, 'Unauthorized request');
            }

        }
        catch(PDOException $e) {
            return $e->getMessage();
        }

        $conn = null;
        return $clientId;
    }

    /**
    *   TODO: TEMPORARY!!!
    */ 
    public function setNewToken($clientId, $clientSecret, $code, $token) 
    {
        $servername = env('DB_HOST', 'mysql'); 
        $username = env('DB_USERNAME', 'mysql');
        $password = env('DB_PASSWORD', 'mysql');
        $dbname = env('DB_DATABASE', 'mysql');

        try {
            $conn = new \PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            
            $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            
            $sql = "UPDATE `networks_users` SET `clientCode` = NULL, `userToken` = '$token' WHERE `clientId` = '$clientId' AND `userSecret` = '$clientSecret' AND `clientCode` = '$code'";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute();
        }
        catch(PDOException $e) {
            return $e->getMessage();
        }

        try {
            $sql = "SELECT `userToken` FROM `networks_users` WHERE `clientId` = '$clientId' AND `userSecret` = '$clientSecret'";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute();

            $result = $stmt->setFetchMode(\PDO::FETCH_ASSOC); 
            $currentToken = $stmt->fetchAll()[0]['userToken'];

        }
        catch(PDOException $e) {
            return $e->getMessage();
        }

        $conn = null;
        return $currentToken;
    }

}