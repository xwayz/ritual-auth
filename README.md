# ritual-auth (Under Development)

- Instalation => composer require admaja/ritual-auth
- Configuration
    - for laravel:
       Open file config/app.php, add provider Admaja\RitualAuth\RitualAuthServiceProvider::class
    - for lumen: 
       Open file bootstrap/app.php, add provider $app->register(Admaja\RitualAuth\RitualAuthServiceProvider::class);
 
 Then, Publish package => php artisan ritualauth:publish.
 
 Next Step:
 1. Delete File Migration User (Laravel)
 2. Make Seeder For Table SeetingTable:
    
    ```
    $rules = [
        "auth" => [
            "unique_code_type" => "email",
            "can_register" => true,
            "attempts" => 3,
        ],
    ];

    Setting::create([
        "last_updated_by" => "master",
        "rules" => json_encode($rules),
        "status" => "active" 
    ]);
    
    ```
      And Seed This Class, You can Custom value in SettingTable according to the needs, but the code above is mandatory.
  3. Install composer require hisorange/browser-detect, [Browser Detection](https://github.com/hisorange/browser-detect)
  
          Example to Use:
          
          

          ```
            public function authenticate(Request $request)
            {
                $credentials = $this->validateAuth($request);

                $ritual = RitualAuth::attempt($request, $credentials);

                if($ritual["data"]->status == "blocked"){
                    return back()->withErrors([
                        'email' => 'Maaf anda tidak bisa login untuk beberapa saat',
                    ]);
                }

                if($ritual["status"] == true){
                    return "success";
                }

                return back()->withErrors([
                    'email' => 'Email atau Password salah, silahkan cek kembali email dan password anda',
                ]);
            }

            protected function validateAuth($request)
            {
                return $request->validate([
                    $this->username() => 'required|email',
                    'password' => 'required',
                ]);
            }

            protected function username()
            {
                return "email";
            }

            public function logout(Request $request)
            {
                LoginInformation::where("user_id", auth()->user()->id)->update([
                    "status" => "logout"
                ]);

                $request->session()->invalidate();

                return $request->wantsJson() ? new JsonResponse([], 204) : redirect()->route('auth.login.index');
            }
