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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\LecturerCourse::class)->index();
            $table->string('classroom');
            $table->date('date');
            $table->time('start');
            $table->time('end');
            $table->json('extras')->nullable();
            $table->boolean('is_reschedule')->default(false);
            $table->text('reschedule_note')->nullable();
            $table->string('class');
            $table->tinyInteger('semester');
            $table->year('academic_year');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
