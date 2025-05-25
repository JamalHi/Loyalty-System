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
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->boolean('first_login')->default(0);
            $table->string('location');
            $table->string('about');
            $table->double('service')->nullable();
            $table->float('points')->default(0);
            $table->string('id_image')->nullable();
            $table->string('commercial_record')->nullable();
            $table->set('category' , ['clothes','shoes','food'])->nullable();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};
