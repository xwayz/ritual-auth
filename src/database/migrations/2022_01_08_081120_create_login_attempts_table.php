<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoginAttemptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id();
            $table->uuid("uuid");
            $table->text("unique_code");
            $table->string("unique_code_type")->default("email");
            $table->string("ip_address");
            $table->string("device_name")->nullable();
            $table->string("platform_name")->nullable();
            $table->string("browser_name")->nullable();
            $table->string("user_agent");
            $table->string("attempts")->default(1);
            $table->enum("status",["running","success","blocked"])->default("running");
            $table->timestamp("blocked_at")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('login_attempts');
    }
}
