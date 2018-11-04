<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicNetworkController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function list(Request $request)
    {
        $dir = '../server/php/files/';
        $files = scandir($dir);
        $jsonFiles = [];
        foreach ($files as $file) {
            if (preg_match('/\.csv\.json$/', $file)) {
                $jsonFiles[] = $file;
            }
        }
        return ['network_list' => $jsonFiles];
    }
}