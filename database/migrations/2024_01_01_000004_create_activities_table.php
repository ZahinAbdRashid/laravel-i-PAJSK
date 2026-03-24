<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['uniform', 'club', 'sport', 'competition', 'extra']);
            $table->string('name');
            $table->enum('level', ['school', 'district', 'state', 'national', 'international']);
            $table->enum('achievement', ['participation', 'third', 'second', 'first']);
            $table->date('activity_date');
            $table->text('description')->nullable();
            
            // Approval status
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('teacher_comment')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('teachers');
            $table->timestamp('approved_at')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};