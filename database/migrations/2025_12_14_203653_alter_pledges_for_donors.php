<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pledges', function (Blueprint $table) {
            $table->dropMorphs('donor');
        });

        Schema::table('pledges', function (Blueprint $table) {
            $table->foreignId('donor_id')->nullable()->after('id')->constrained('donors')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('pledges', function (Blueprint $table) {
            $table->dropForeign(['donor_id']);
        });

        Schema::table('pledges', function (Blueprint $table) {
            $table->morphs('donor');
        });
    }
};
