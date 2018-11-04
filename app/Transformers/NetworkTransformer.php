<?php

namespace App\Transformers;

use App\Models\NetworkUser;
use League\Fractal\TransformerAbstract;

class NetworkTransformer extends TransformerAbstract
{
    public function transform(NetworkUser $user)
    {
        $formattedUser = [
            'userId'        => $user->userId,
            'firstName'     => $user->firstName,
            'lastName'      => $user->lastName,
            'clientId'      => $user->clientId,
            'userSecret'    => $user->userSecret,
            'clientCode'    => $user->clientCode,
            'userToken'     => $user->userToken,
            'tokenExpire'   => $user->tokenExpire,
        ];

        return $formattedUser;
    }
}