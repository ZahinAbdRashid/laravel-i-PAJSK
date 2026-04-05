<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->string('academic_session');  //example - 2024/2025
            $table->enum('semester', ['1', '2', '3']);
            
            // Activity participation (as per teacher input)
            $table->string('sports')->nullable();
            $table->string('club')->nullable();
            $table->string('uniform')->nullable();
            $table->string('position')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};