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
        Schema::table('steadfast_integrations', function (Blueprint $table) {
            if (!Schema::hasColumn('steadfast_integrations', 'base_url')) {
                $table->string('base_url')->default('https://portal.packzy.com/api/v1')->after('secret_key');
            }
            if (!Schema::hasColumn('steadfast_integrations', 'webhook_token')) {
                $table->string('webhook_token')->nullable()->after('base_url');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'steadfast_consignment_id')) {
                $table->string('steadfast_consignment_id')->nullable()->after('tracking_number');
            }
            if (!Schema::hasColumn('orders', 'steadfast_tracking_code')) {
                $table->string('steadfast_tracking_code')->nullable()->after('steadfast_consignment_id');
            }
            if (!Schema::hasColumn('orders', 'steadfast_status')) {
                $table->string('steadfast_status')->nullable()->after('steadfast_tracking_code');
            }
            if (!Schema::hasColumn('orders', 'steadfast_response')) {
                $table->text('steadfast_response')->nullable()->after('steadfast_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('steadfast_integrations', function (Blueprint $table) {
            $table->dropColumn(['base_url', 'webhook_token']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['steadfast_consignment_id', 'steadfast_tracking_code', 'steadfast_status', 'steadfast_response']);
        });
    }
};
