<?php

namespace App\Utils;

class VeezeeUserAccessHelper
{
    public static function getTrialAccess()
    {
        return [
            'type' => 'Trial',
            'playsAllowedPerDay' => 3,
            'expiresIn' => strtotime('+24 hours')
        ];
    }
}