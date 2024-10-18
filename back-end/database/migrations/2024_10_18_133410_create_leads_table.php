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
        Schema::create('leads', function (Blueprint $table) {
            $table->id(); // ID column
            $table->string('name'); // Lead's name
            $table->string('email')->unique(); // Lead's email
            $table->string('phone')->nullable(); // Lead's phone
            $table->foreignId('lead_status_id')->constrained('lead_statuses'); // Foreign key to lead_statuses
            $table->timestamps(); // created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
