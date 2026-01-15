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
        Schema::create('processor_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('processor')->index(); // 'givebutter', 'stripe', etc.
            $table->string('source_field'); // JSON path in webhook payload (e.g., 'data.amount', 'donor.email')
            $table->string('target_field'); // Field in donations table (e.g., 'amount', 'processor_id')
            $table->string('transformation_type')->default('direct'); // 'direct', 'callback', 'lookup', 'computed'
            $table->json('transformation_config')->nullable(); // Additional config for transformations
            $table->boolean('is_required')->default(false);
            $table->integer('priority')->default(0); // Order of processing
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['processor', 'target_field', 'source_field']);
            $table->index(['processor', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('processor_mappings');
    }
};
