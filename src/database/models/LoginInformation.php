<?php

namespace Admaja\RitualAuth\Database\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginInformation extends Model
{
    use HasFactory;

    protected $table = "login_informations";
    protected $guarded = [];
}
