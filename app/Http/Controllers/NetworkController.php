<?php

namespace App\Http\Controllers;

use App\Models\NetworkUser;
use App\Models\Network;
use App\Repositories\Contracts\NetworkRepository;
use Illuminate\Http\Request;
use App\Transformers\NetworkTransformer;

class NetworkController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function newCode(Request $request)
    {
        $inputs = $request->all();

        if (!isset($inputs['response_type']) || empty($inputs['response_type']) || $inputs['response_type'] !== 'code') {
            return $this->sendCustomResponse(401, 'Invalid request; required response_type');
        }

        if (!isset($inputs['client_id']) || empty($inputs['client_id'])) {
            return $this->sendCustomResponse(401, 'Invalid request; required client_id');
        }

        $clientCode = $this->generateNewCode();

        $this->setNewCode($inputs['client_id'], $clientCode);

        return $this->sendCustomResponse(200, ['code' => $clientCode]);
    }

    public function newToken(Request $request)
    {
        $inputs = $request->all();

        if (!isset($inputs['grant_type']) || empty($inputs['grant_type']) || $inputs['grant_type'] !== 'authorization_code') {
            return $this->sendCustomResponse(401, 'Invalid request; required grant_type');
        }

        if (!isset($inputs['client_id']) || empty($inputs['client_id'])) {
            return $this->sendCustomResponse(401, 'Invalid request; required client_id');
        }

        if (!isset($inputs['client_secret']) || empty($inputs['client_secret'])) {
            return $this->sendCustomResponse(401, 'Invalid request; required client_secret');
        }

        if (!isset($inputs['code']) || empty($inputs['code'])) {
            return $this->sendCustomResponse(401, 'Invalid request; required code');
        }

        $clientToken = $this->generateNewCode();

        $this->setNewToken($inputs['client_id'], $inputs['client_secret'], $clientToken);

        return $this->sendCustomResponse(200, ['token' => $clientToken]);
    }

    public function list(Request $request)
    {
        return "test list";
    }

    private function generateNewCode() 
    {
        return str_random('32');;
    }

    /**
    *   TODO: TEMPORARY!!!
    */ 
    public function setNewCode($clientId, $code) 
    {
        $servername = env('DB_HOST', 'mysql'); 
        $username = env('DB_USERNAME', 'mysql');
        $password = env('DB_PASSWORD', 'mysql');
        $dbname = env('DB_DATABASE', 'mysql');

        try {
            $conn = new \PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            
            $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            
            $sql = "UPDATE `networks_users` SET `clientCode` = '$code' WHERE `clientId` = '$clientId'";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute();
        }
        catch(PDOException $e) {
            return $e->getMessage();
        }

        $conn = null;

        // $user = $this->networkUserRepository->findOneBy(['clientId' => $clientId]);

        // if (!$user instanceof NetworkUser) {
        //     return $this->sendNotFoundResponse("The user with clientId {$clientId} doesn't exist");
        // }

        // // Authorization
        // $this->authorize('update', $user);


        // $user = $this->networkUserRepository->update($user, ['clientId' => $code]);

        // return $this->respondWithItem($user, $this->networkUserTransformer);
    }

    /**
    *   TODO: TEMPORARY!!!
    */ 
    public function setNewToken($clientId, $clientSecret, $code) 
    {
        $servername = env('DB_HOST', 'mysql'); 
        $username = env('DB_USERNAME', 'mysql');
        $password = env('DB_PASSWORD', 'mysql');
        $dbname = env('DB_DATABASE', 'mysql');

        try {
            $conn = new \PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            
            $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            
            $sql = "UPDATE `networks_users` SET `clientCode` = '$code' WHERE `clientId` = '$clientId' AND `userSecret` = '$clientSecret'";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute();
        }
        catch(PDOException $e) {
            return $e->getMessage();
        }

        $conn = null;
    }

}