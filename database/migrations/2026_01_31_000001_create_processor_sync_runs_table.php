<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('processor_sync_runs', function (Blueprint $table) {
            $table->id();
            $table->string('processor', 64)->index();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->string('status', 32)->default('pending')->index(); // pending, running, success, failed
            $table->unsignedInteger('pages_fetched')->default(0);
            $table->unsignedInteger('contacts_staged')->default(0);
            $table->unsignedInteger('contacts_loaded')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('processor_sync_runs');
    }
};
