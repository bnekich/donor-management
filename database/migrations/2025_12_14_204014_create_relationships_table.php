<?php
// 2025_12_14_000010_create_relationships_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('relationships', function (Blueprint $table) {
            $table->id();
            $table->string('from_type');
            $table->unsignedBigInteger('from_id');
            $table->string('to_type');
            $table->unsignedBigInteger('to_id');
            $table->string('relationship_type'); // e.g., 'spouse', 'employee', 'partner'
            $table->text('notes')->nullable();
            $table->index(['from_type', 'from_id']);
            $table->index(['to_type', 'to_id']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relationships');
    }
};
