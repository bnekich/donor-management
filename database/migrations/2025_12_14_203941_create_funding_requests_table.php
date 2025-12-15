<?php

// 2025_12_14_000009_create_funding_requests_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('funding_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipient_id')->constrained('organizations')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->date('request_date');
            $table->enum('status', ['pending', 'approved', 'rejected', 'funded'])->default('pending');
            $table->decimal('approved_amount', 15, 2)->nullable();
            $table->date('funded_date')->nullable();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funding_requests');
    }
};
