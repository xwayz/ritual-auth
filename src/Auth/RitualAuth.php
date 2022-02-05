<?php

namespace Admaja\RitualAuth\Auth;

use Admaja\RitualAuth\Apps\SettingApp;
use Admaja\RitualAuth\Database\Models\LoginAttempt;
use Admaja\RitualAuth\Database\Models\LoginInformation;
use BrowserDetect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class RitualAuth{

    public static $unique_code_type = "";
    public static $data = [];
    public static $login_attempt;
    public static $count = 0;
    
    public static function attempt($request,$credentials)
    {
        $setting = SettingApp::all();
        $unique_code_type = $setting->auth->unique_code_type;

        self::$unique_code_type = $unique_code_type;
        self::$data = [
            "ip_address" => Request::ip(),
            "device_name" => BrowserDetect::deviceFamily(),
            "platform_name" => BrowserDetect::platformName(),
            "browser_name" => BrowserDetect::browserName(),
            "user_agent" => BrowserDetect::userAgent(),
        ];

        self::$login_attempt = LoginAttempt::where(self::$data)->where("status","!=","success")->first();

        if(self::$login_attempt != null){

            if(self::$login_attempt->status == "blocked"){
                return self::response(false,"user blocked",self::$login_attempt);
            }
            // check login
            if($login_status = self::login($request,$credentials, "update")){
                return self::response(true,"login success", self::$login_attempt);
            }

            self::$count = self::$login_attempt->attempts + 1;
            // re set unique code
            $update = ["unique_code" => self::uniqueCode($request)];

            $start  = strtotime(self::$login_attempt->updated_at);
            $end = time();
            $diff  = $end - $start;

            $time = floor($diff / (60 * 60 * 24));

            if($time > 0){
                self::$login_attempt->delete();

                self::$login_attempt = self::create($request);
            }else{    
                $update["attempts"] = self::$count;
            }

            if(self::$count == ($setting->auth->attempts)){
                $update["status"] = "blocked";
                $update["blocked_at"] = date("Y-m-d H:i:s");
                $update["attempts"] = 3;
            }

            self::$login_attempt->update($update);
        }else{
            self::$count = 1;
            self::$login_attempt = self::create($request);

            if($login_status = self::login($request,$credentials)){
                return self::response(true,"login success", self::$login_attempt);
            }
        }

        return self::response(false,"login failed", self::$login_attempt);
    }

    public static function uniqueCode($data)
    {
        if(self::$unique_code_type == "email"){
            return $data->email;
        }else{
            return $data->username;
        }
    }

    protected static function create($request)
    {
        $data = self::$data;
        $data["uuid"] = Str::orderedUuid();
        $data["attempts"] = 1;
        $data["unique_code"] = self::uniqueCode($request);
        $data["unique_code_type"] = self::$unique_code_type;

        $login_attempt = LoginAttempt::create($data);

        return $login_attempt;
    }

    protected static function login($request, $credentials, $status = "create")
    {
        if(Auth::attempt($credentials)){
            $request->session()->regenerate();
            
            self::$login_attempt->update([
                "unique_code" => self::uniqueCode($request),
                "status" => "success",
                "attempts" => $status == "create" && self::$count == 1 ? 0 : self::$login_attempt->attempts,
            ]);

            LoginInformation::updateOrCreate([
                "user_id" => auth()->user()->id,
            ],[
                "uuid" => Str::orderedUuid(),
                "login_attempt_id" => self::$login_attempt->id,
                "status" => "login"
            ]);

            return true;
        }

        return false;
    }

    protected static function response($status, $message,$data)
    {
        return [
            "status" => $status,
            "message" => $message,
            "data" => $data 
        ];
    }
}