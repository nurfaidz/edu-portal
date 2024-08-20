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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Schedule::class)->index();
            $table->foreignIdFor(\App\Models\User::class, 'lecturer_id')->index()->nullable();
            $table->foreignIdFor(\App\Models\User::class, 'student_id')->index()->nullable();
            $table->string('status');
            $table->string('note')->nullable();
            $table->dateTime('checkin_at');
            $table->dateTime('expired_at');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
