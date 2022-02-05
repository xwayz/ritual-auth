<?php

namespace Admaja\RitualAuth\Apps;

use App\Models\Setting;

class SettingApp{

    public static function all($to_array = false)
    {
        $data = Setting::first();
        $data = json_decode($data["rules"]);
        if($to_array == true){
            $data = (array) $data;
            return $data;
        }else{
            return $data;
        }
    }
}