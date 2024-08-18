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
        Schema::create('lecturer_salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\User::class)->index();
            $table->foreignIdFor(\App\Models\Schedule::class)->index();
            $table->integer('amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lecturer_salaries');
    }
};
