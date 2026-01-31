<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('givebutter_contacts_staging', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('givebutter_contact_id')->unique();
            $table->foreignId('sync_run_id')->nullable()->constrained('processor_sync_runs')->onDelete('set null');
            $table->json('payload'); // raw JSONB-style: full contact object from API
            $table->timestamp('loaded_at')->nullable(); // set when loaded into donors/donor_details
            $table->timestamps();
        });

        Schema::table('givebutter_contacts_staging', function (Blueprint $table) {
            $table->index('sync_run_id');
            $table->index('loaded_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('givebutter_contacts_staging');
    }
};
