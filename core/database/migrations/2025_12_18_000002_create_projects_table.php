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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('image');
            $table->string('pdf')->nullable();
            $table->decimal('minimum_investment', 28, 8)->default(0);
            $table->decimal('maximum_investment', 28, 8)->default(0);
            $table->integer('days_to_init')->default(1);
            $table->boolean('featured')->default(0);
            $table->boolean('testing')->default(0);
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
