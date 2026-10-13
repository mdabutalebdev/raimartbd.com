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
        Schema::table('products', function (Blueprint $table) {
            // Sales & Sold Count Strategy
            $table->string('sales_metrics_mode')->default('organic')->after('reviews_count');
            $table->unsignedInteger('base_sold_count')->default(0)->after('sales_metrics_mode');
            $table->date('sales_start_date')->nullable()->after('base_sold_count');
            $table->unsignedInteger('daily_sales_volume')->default(2)->after('sales_start_date');

            // Ratings & Reviews Strategy
            $table->string('rating_metrics_mode')->default('organic')->after('daily_sales_volume');
            $table->unsignedInteger('base_reviews_count')->default(0)->after('rating_metrics_mode');
            $table->date('rating_start_date')->nullable()->after('base_reviews_count');
            $table->unsignedInteger('rating_interval_days')->default(4)->after('rating_start_date');
            $table->unsignedInteger('ratings_per_interval')->default(1)->after('rating_interval_days');
            $table->decimal('min_rating', 3, 2)->default(4.50)->after('ratings_per_interval');
            $table->decimal('max_rating', 3, 2)->default(5.00)->after('min_rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'sales_metrics_mode',
                'base_sold_count',
                'sales_start_date',
                'daily_sales_volume',
                'rating_metrics_mode',
                'base_reviews_count',
                'rating_start_date',
                'rating_interval_days',
                'ratings_per_interval',
                'min_rating',
                'max_rating',
            ]);
        });
    }
};
