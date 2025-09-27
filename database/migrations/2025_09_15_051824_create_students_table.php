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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->nullable()->constrained('class_rooms')->onDelete('set null');
            $table->string('name');
            $table->enum('gender', ['L', 'P']);
            $table->enum('level', ['X', 'XI', 'XII']);
            $table->enum('major', ['TKJ', 'TKR', 'AK', 'AP']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
