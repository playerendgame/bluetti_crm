<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddModeOfPaymentIdToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->bigInteger('mode_of_payment_id')->unsigned()->nullable()->after('admin_id');
            $table->foreign('mode_of_payment_id')->references('id')->on('mode_of_payments');
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
            $table->dropForeign(['mode_of_payment_id']);
            $table->dropColumn(['mode_of_payment_id']);
        });
    }
}
