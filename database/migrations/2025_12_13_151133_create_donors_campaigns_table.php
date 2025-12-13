<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donors_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_id')->constrained();
            $table->foreignId('campaign_id')->constrained();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donors_campaigns');
    }
};
