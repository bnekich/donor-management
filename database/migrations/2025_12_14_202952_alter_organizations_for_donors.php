<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->foreignId('donor_id')->nullable()->after('id')->unique()->constrained()->onDelete('cascade');
            $table->boolean('needs_review')->default(true)->after('type');
            $table->dropColumn([
                'email',
                'phone',
                'address_line1',
                'address_line2',
                'city',
                'state',
                'zip',
                'country',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropForeign(['donor_id']);
            $table->dropColumn('needs_review');
            $table->string('email')->nullable()->after('name');
            $table->string('phone')->nullable()->after('email');
            $table->string('address_line1')->nullable()->after('phone');
            $table->string('address_line2')->nullable()->after('address_line1');
            $table->string('city')->nullable()->after('address_line2');
            $table->string('state')->nullable()->after('city');
            $table->string('zip')->nullable()->after('state');
            $table->string('country')->default('USA')->after('zip');
        });
    }
};
