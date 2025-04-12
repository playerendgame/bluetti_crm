<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeliveryStatusToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('delivery_status')->default(0)->after('attribution_id');
            $table->date('target_delivery_date')->nullable()->after('delivery_status');
            $table->date('dispatch_date')->nullable()->after('target_delivery_date');
            $table->date('date_delivered')->nullable()->after('dispatch_date');
            $table->text('notes')->nullable()->after('date_delivered');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['delivery_status', 'target_delivery_date', 'dispatch_date', 'date_delivered', 'notes']);
        });
    }
}
