<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->timestamps();
        });

        Schema::table('venues', function (Blueprint $table) {
            $table->foreignId('business_id')->nullable()->after('id')->constrained('businesses')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('venues', function (Blueprint $table) {
            $table->dropConstrainedForeignId('business_id');
        });

        Schema::dropIfExists('businesses');
        Schema::dropIfExists('business_types');
    }
};
