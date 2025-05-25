<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('verefication_code')->nullable();
            $table->timestamp('verefi_code_exp_date')->nullable();
            $table->string('password');
            $table->string('prof_img')->nullable();
            $table->boolean('active')->default(0);
            $table->integer('password_counter')->default(0);
            $table->string('device_token')->nullable();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
