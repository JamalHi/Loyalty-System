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
        Schema::create('histories', function (Blueprint $table) {
            $table->id();
            $table->string('operation')->nullable();
            $table->float('transfer_points');
            $table->timestamp('transfer_time')->nullable();
            $table->float('invoice')->nullable();
            $table->foreignId('from_user')->constrained('users')->cascadeOnDelete();
            $table->foreignId('to_user')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('histories');
    }
};
