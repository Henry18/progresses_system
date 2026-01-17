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
        Schema::table('invests', function (Blueprint $table) {
            $table->boolean('terms_accepted')->default(0)->after('fractional_capital');
            $table->timestamp('terms_accepted_at')->nullable()->after('terms_accepted');
            $table->boolean('project_terms_accepted')->default(0)->after('terms_accepted_at');
            $table->timestamp('project_terms_accepted_at')->nullable()->after('project_terms_accepted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invests', function (Blueprint $table) {
            $table->dropColumn(['terms_accepted', 'terms_accepted_at', 'project_terms_accepted', 'project_terms_accepted_at']);
        });
    }
};
