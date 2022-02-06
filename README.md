# ritual-auth

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
    
    And Seeder This Class, You can Custom value in SettingTable according to the needs, but the code above is mandatory.
