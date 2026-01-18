<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('individuals', 'donor_details');

        Schema::table('donor_details', function (Blueprint $table) {
            $table->foreignId('donor_id')->nullable()->after('id')->unique()->constrained()->onDelete('cascade');
            $table->dropColumn([
                'email',
                'phone',
                'address_line1',
                'address_line2',
                'city',
                'county',
                'state',
                'zip',
                'country',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('donor_details', function (Blueprint $table) {
            $table->dropForeign(['donor_id']);
            $table->dropColumn('donor_id');
            $table->string('email')->nullable()->after('last_name');
            $table->string('phone')->nullable()->after('email');
            $table->string('address_line1')->nullable()->after('phone');
            $table->string('address_line2')->nullable()->after('address_line1');
            $table->string('city')->nullable()->after('address_line2');
            $table->string('county')->nullable()->after('city');
            $table->string('state')->nullable()->after('county');
            $table->string('zip')->nullable()->after('state');
            $table->string('country')->default('USA')->after('zip');
        });

        Schema::rename('donor_details', 'individuals');
    }
};
