<?php
// 2025_12_14_000007_create_donations_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->string('processor')->nullable(); // e.g., 'givebutter', 'stripe'
            $table->string('processor_id')->nullable();
            $table->string('reference_number')->nullable();
            $table->morphs('donor');
            $table->decimal('amount', 15, 2);
            $table->decimal('processor_fee', 15, 2)->nullable();
            $table->decimal('net_amount', 15, 2)->nullable();
            $table->date('transaction_date');
            $table->string('payment_method'); // e.g., 'credit_card', 'check', 'direct'
            $table->string('transaction_id')->nullable();
            $table->foreignId('pledge_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('campaign_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('chapter_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('account_id')->nullable()->constrained('chart_of_accounts')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
