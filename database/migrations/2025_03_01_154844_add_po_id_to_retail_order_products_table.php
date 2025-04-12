<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPoIdToRetailOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('retail_order_products', function (Blueprint $table) {
            $table->bigInteger('po_id')->unsigned()->nullable()->after('cogs');
            $table->foreign('po_id')->references('id')->on('purchase_orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('retail_order_products', function (Blueprint $table) {
            $table->dropForeign(['po_id']);
            $table->dropColumn(['po_id']);
        });
    }
}
