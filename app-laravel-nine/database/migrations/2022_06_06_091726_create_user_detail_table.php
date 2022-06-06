<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_detail', function (Blueprint $table) {
            $table->id();
            $table->string('identity_number', 16)->unique();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->char('gender', 1);
            $table->date('birth_date');
            $table->string('birth_place', 60)->nullable();
            $table->string('religion', 20)->nullable();
            $table->string('phone_number', 30)->nullable();
            $table->string('full_address')->nullable();
            $table->string('zip_code', 10)->nullable();
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
        Schema::dropIfExists('user_detail');
    }
};
