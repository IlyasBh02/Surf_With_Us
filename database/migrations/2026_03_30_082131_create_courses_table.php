<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coach_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('thumbnail')->nullable();
            $table->dateTime('date');
            $table->integer('duration');
            $table->integer('available_places');
            $table->decimal('price', 8, 2)->default(45.00);
            $table->enum('level', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->string('location')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
