<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn('donor_type');
            $table->dropColumn('donor_id');
        });

        Schema::table('donations', function (Blueprint $table) {
            $table->foreignId('donor_id')->nullable()->after('reference_number')->constrained('donors')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropForeign(['donor_id']);
        });

        Schema::table('donations', function (Blueprint $table) {
            $table->unsignedBigInteger('donor_id')->nullable()->after('reference_number');
            $table->string('donor_type')->nullable()->after('donor_id');
        });
    }
};
