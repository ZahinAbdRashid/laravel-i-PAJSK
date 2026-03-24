<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Students tak perlu email - remove dari seeder sahaja
            // Tak perlu drop column sebab column ada dalam users table
        });
    }

    public function down(): void
    {
        // Nothing to revert
    }
};