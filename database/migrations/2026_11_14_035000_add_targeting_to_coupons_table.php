<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('coupons')) {
            Schema::table('coupons', function (Blueprint $table) {
                if (! Schema::hasColumn('coupons', 'applies_to')) {
                    $table->enum('applies_to', ['full_store', 'specific_products', 'specific_categories'])
                        ->default('full_store')
                        ->after('value');
                }
                if (! Schema::hasColumn('coupons', 'product_ids')) {
                    $table->json('product_ids')->nullable()->after('applies_to');
                }
                if (! Schema::hasColumn('coupons', 'category_ids')) {
                    $table->json('category_ids')->nullable()->after('product_ids');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('coupons')) {
            Schema::table('coupons', function (Blueprint $table) {
                $columns = [];
                if (Schema::hasColumn('coupons', 'category_ids')) {
                    $columns[] = 'category_ids';
                }
                if (Schema::hasColumn('coupons', 'product_ids')) {
                    $columns[] = 'product_ids';
                }
                if (Schema::hasColumn('coupons', 'applies_to')) {
                    $columns[] = 'applies_to';
                }
                if (! empty($columns)) {
                    $table->dropColumn($columns);
                }
            });
        }
    }
};

