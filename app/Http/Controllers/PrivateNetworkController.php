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
     * TODO
     */
    public function nodeDiffs(Request $request)
    {
        $inputs = $request->all();

        $this->logApiAction($request);

        if (!isset($inputs['token']) || empty($inputs['token'])) {
            return $this->sendCustomResponse(401, 'Unauthorized request');
        }

        $clientId = $this->authenticateUser($inputs['token']);

        if (strlen($clientId) !== 32) {
            return "Invalid authentication";
        }

        if (!isset($inputs['oldNetworkId']) || empty($inputs['oldNetworkId'])) {
            return $this->sendCustomResponse(400, 'Required oldNetworkId');
        }

        if (!isset($inputs['newNetworkId']) || empty($inputs['newNetworkId'])) {
            return $this->sendCustomResponse(400, 'Required newNetworkId');
        }

        // Source data
        $srcNetworkData = $this->csvDirectExport($inputs['oldNetworkId']);

        // Destination data
        $destNetworkData = $this->csvDirectExport($inputs['newNetworkId']);

        /**
         * 1.) iterating through the destination edges[] array
         * this will update new edges.
         * 
         * 2.) it then needs to iterate through destination nodes[] array to add any new nodes
         * 
         * X.) The above two rules don't account for when nodes are completely removed and not existent in destNetwork
         */

        /**
         * Antonio: nodes are never removed or added
         * 
         * ... Only edges are updated/changes 
         * BUT Node labels/titles would change... (w.e. leave for later since 'label' support is already spottys)
         */

        /**
         * This only works if the networks are the same size,
         * AND
         * This only works if nodes (ids) are preserved
         * AND
         * This only works if the only change is the second and/or third column of a node (to,group)
         */
        if (sizeof($destNetworkData) === sizeof($srcNetworkData)) {
            $differences = [];
            for ($i = 0; $i < sizeof($destNetworkData); $i++) {
                if ($destNetworkData[$i] !== $srcNetworkData[$i]) {
                    $tempData = explode(",", $destNetworkData[$i]);
                    $tempData = array_map('intval', $tempData); 
                    $differences[] = $tempData;
                }
            }
        }
        else {
            return ['error' => 'network size differences'];
        }

        return $differences;
    }

    public function jsonExport(Request $request)
    {
        $inputs = $request->all();

        $this->logApiAction($request);

        if (!isset($inputs['token']) || empty($inputs['token'])) {
            return $this->sendCustomResponse(401, 'Unauthorized request');
        }

        $clientId = $this->authenticateUser($inputs['token']);

        if (strlen($clientId) !== 32) {
            return "Invalid authentication";
        }

        if (!isset($inputs['networkId']) || empty($inputs['networkId'])) {
            return $this->sendCustomResponse(400, 'Required network id');
        }

        $fullPath = $this->getNetworkFilePath($inputs['networkId']);

        $fileData = json_decode(file_get_contents($fullPath), TRUE);

        return $fileData;
    }

    public function csvExport(Request $request)
    {
        $inputs = $request->all();

        $this->logApiAction($request);

        if (!isset($inputs['token']) || empty($inputs['token'])) {
            return $this->sendCustomResponse(401, 'Unauthorized request');
        }

        $clientId = $this->authenticateUser($inputs['token']);

        if (strlen($clientId) !== 32) {
            return "Invalid authentication";
        }

        if (!isset($inputs['networkId']) || empty($inputs['networkId'])) {
            return $this->sendCustomResponse(400, 'Required network id');
        }

        return $this->csvDirectExport($inputs['networkId']);
    }

    private function csvDirectExport($networkId)
    {
        $fullPath = $this->getNetworkFilePath($networkId);

        $fileData = json_decode(file_get_contents($fullPath), TRUE);
        $csvArr = [];
        $count = 0;
        foreach ($fileData['edges'] as $edgeNode) {
            $csvArr[] = $edgeNode['from'] . "," . $edgeNode['to'] . "," . $fileData['nodes'][$count]['group'];
            $count++;
        }
        return $csvArr;
    }

    public function renameFile(Request $request)
    {
        $inputs = $request->all();

        $this->logApiAction($request);

        if (!isset($inputs['token']) || empty($inputs['token'])) {
            return $this->sendCustomResponse(401, 'Unauthorized request');
        }

        $clientId = $this->authenticateUser($inputs['token']);

        if (strlen($clientId) !== 32) {
            return "Invalid authentication";
        }

        if (!isset($inputs['networkId']) || empty($inputs['networkId'])) {
            return $this->sendCustomResponse(400, 'Required network id');
        }

        if (!isset($inputs['networkNewName']) || empty($inputs['networkNewName'])) {
            return $this->sendCustomResponse(400, 'Required new network name');
        }

        if ($this->renameFileById($inputs['networkId'], $inputs['networkNewName'])) {
            return ['message' => 'Network rename successful'];
        }
        else {
            return ['error' => 'Network rename unsuccessful'];
        }
    }

    public function createFile(Request $request)
    {
        $inputs = $request->all();

        $this->logApiAction($request);

        if (!isset($inputs['token']) || empty($inputs['token'])) {
            return $this->sendCustomResponse(401, 'Unauthorized request');
        }

        $clientId = $this->authenticateUser($inputs['token']);

        if (strlen($clientId) !== 32) {
            return "Invalid authentication";
        }

        if (!isset($inputs['networkName']) || empty($inputs['networkName'])) {
            return $this->sendCustomResponse(400, 'Required network name');
        }

        return $this->createNetworkFile($inputs['networkName']);
    }

    public function deleteFile(Request $request)
    {
        $inputs = $request->all();

        $this->logApiAction($request);

        if (!isset($inputs['token']) || empty($inputs['token'])) {
            return $this->sendCustomResponse(401, 'Unauthorized request');
        }

        $clientId = $this->authenticateUser($inputs['token']);

        if (strlen($clientId) !== 32) {
            return "Invalid authentication";
        }

        if (!isset($inputs['networkId']) || empty($inputs['networkId'])) {
            return $this->sendCustomResponse(400, 'Required network id');
        }

        return $this->removeNetworkFile($inputs['networkId']);
    }

    public function modify(Request $request)
    {
        $inputs = $request->all();

        $this->logApiAction($request);

        if (!isset($inputs['token']) || empty($inputs['token'])) {
            return $this->sendCustomResponse(401, 'Unauthorized request');
        }

        $clientId = $this->authenticateUser($inputs['token']);

        if (strlen($clientId) !== 32) {
            return "Invalid authentication";
        }

        if (!isset($inputs['networkId']) || empty($inputs['networkId'])) {
            return $this->sendCustomResponse(400, 'Required network id');
        }

        if (!isset($inputs['edgeList']) || empty($inputs['edgeList'])) {
            return $this->sendCustomResponse(400, 'Required edgelist data');
        }

        if (!isset($inputs['save']) || empty($inputs['save'])) {
            $inputs['save'] = 'false';
        }

        $edgeDataList = json_decode(htmlspecialchars_decode($inputs['edgeList']));
        $edgeType = gettype($edgeDataList);

        if ($edgeType !== "array") {
            return $this->sendCustomResponse(400, 'Edgelist must be array');
        }

        if ($inputs['save'] == 'true') {
            $this->appendNetworkFile($inputs['networkId'], $edgeDataList);
        }

        foreach ($edgeDataList as $edgeNode) {
            $this->insertNetworkDynamics($clientId, $inputs['networkId'], $edgeNode[0], $edgeNode[1], $edgeNode[2]);
        }
        
        return ['message' => 'Network successfully updated'];
    }

    private function logApiAction(Request $request)
    {
        $servername = env('DB_HOST', 'mysql'); 
        $username = env('DB_USERNAME', 'mysql');
        $password = env('DB_PASSWORD', 'mysql');
        $dbname = env('DB_DATABASE', 'mysql');

        try {
            $conn = new \PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            
            $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $sql = "INSERT INTO `networks_api_log` (`rawData`) VALUES ('$request')";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute();

            $result = $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        }
        catch(PDOException $e) {
            return $e->getMessage();
        }

        $conn = null;
        return;  
    }

    private function renameFileById($networkId, $networkNewName)
    {
        $fullPath = $this->getNetworkFilePath($networkId);
        $rawName = rawurlencode($networkNewName);
        $newNetworkId = base64_encode($rawName);
        $newNetworkFullPath = $this->getNetworkFilePath($newNetworkId) . '.csv.json';
        try {
            return rename($fullPath, $newNetworkFullPath);
        }
        catch (\Exception $e) {
            return false;
        }
    }

    private function appendNetworkFile($networkId, $edgeDataList)
    {
        $fullPath = $this->getNetworkFilePath($networkId);
        $fileData = json_decode(file_get_contents($fullPath), TRUE);
        foreach ($edgeDataList as $edgeNode) {
            $fileData['nodes'][] = [
                "group" => (string)$edgeNode[2],
                "id" => (string)$edgeNode[0],
                "title" => "Node Id: ".$edgeNode[0]."<br>Destination Id: " . $edgeNode[1]
            ];

            $fileData['edges'][] = [
                "from" => (string)$edgeNode[0],
                "to" => (string)$edgeNode[1]
            ];
        }

        file_put_contents($fullPath, json_encode($fileData));
    }

    private function createNetworkFile($networkName)
    {
        $networkName .= ".csv.json";
        $urlEncodeName = rawurlencode($networkName);
        $encodedName = base64_encode($urlEncodeName);
        $cwdPath = preg_replace('/\/api$/', '', getcwd());

        try {
            if (file_exists($cwdPath . "/server/php/files/" . $networkName)) {
                return ["error" => "File already exists with given name"];
            }
            $file = fopen($cwdPath . "/server/php/files/" . $networkName, "w");
            fwrite($file, '{"nodes":[], "edges":[]}');
            fclose($file);
            chmod($cwdPath . "/server/php/files/" . $networkName, 0777);
        }
        catch (\Exception $e) {
            return ["error" => "Unable to create '" . $networkName . "'"];
        }
        return ["message" => "Sucessfully created network.", "networkId" => $encodedName];
    }

    private function removeNetworkFile($networkId)
    {
        $fullPath = $this->getNetworkFilePath($networkId);
        try {
            unlink($fullPath);
        }
        catch (\Exception $e) {
            return ["error" => "Unable to delete '" . $networkId . "'"];
        }
        return ["message" => "Sucessfully deleted " . $networkId];
    }

    private function getNetworkFilePath($networkId)
    {
        $encodedName = base64_decode($networkId);
        $rawName = rawurldecode($encodedName);
        $cwdPath = preg_replace('/\/api$/', '', getcwd());
        return rawurldecode($cwdPath . "/server/php/files/" . $rawName);
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