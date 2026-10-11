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
        Schema::table('categories', function (Blueprint $table) {
            $table->string('home_layout')->default('slider')->after('show_on_home');
            $table->integer('home_max_products')->default(100)->after('home_layout');
            $table->integer('home_per_page')->default(20)->after('home_max_products');
            $table->integer('home_order')->default(0)->after('home_per_page');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['home_layout', 'home_max_products', 'home_per_page', 'home_order']);
        });
    }
};
