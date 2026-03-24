<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            
            // Component marks
            $table->integer('uniform')->default(0);       // Max: 20
            $table->integer('club')->default(0);          // Max: 20
            $table->integer('sport')->default(0);         // Max: 20
            $table->integer('competition')->default(0);   // Max: 40
            $table->integer('extra')->default(0);         // Extra curriculum
            
            // Calculated fields
            $table->integer('total')->default(0);         // Sum of components (capped at 100)
            $table->string('grade')->default('E');        // A/B/C/D/E
            
            // Source tracking
            $table->boolean('is_manual_override')->default(false);
            $table->foreignId('last_updated_by')->nullable()->constrained('teachers');
            $table->text('override_reason')->nullable();
            
            $table->timestamps();
            
            // Ensure one mark record per student (latest)
            $table->unique('student_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marks');
    }
};