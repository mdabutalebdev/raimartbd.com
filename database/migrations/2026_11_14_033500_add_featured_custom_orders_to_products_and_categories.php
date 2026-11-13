<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('products') && ! Schema::hasColumn('products', 'featured_order')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedInteger('featured_order')->default(0)->after('is_featured');
            });
        }

        if (Schema::hasTable('categories')) {
            Schema::table('categories', function (Blueprint $table) {
                if (! Schema::hasColumn('categories', 'is_featured_tab')) {
                    $table->boolean('is_featured_tab')->default(false)->after('show_on_home');
                }
                if (! Schema::hasColumn('categories', 'featured_tab_order')) {
                    $table->unsignedInteger('featured_tab_order')->default(0)->after('is_featured_tab');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'featured_order')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('featured_order');
            });
        }

        if (Schema::hasTable('categories')) {
            Schema::table('categories', function (Blueprint $table) {
                if (Schema::hasColumn('categories', 'featured_tab_order')) {
                    $table->dropColumn('featured_tab_order');
                }
                if (Schema::hasColumn('categories', 'is_featured_tab')) {
                    $table->dropColumn('is_featured_tab');
                }
            });
        }
    }
};

