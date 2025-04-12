<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRegionsCitiesProvincesToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->bigInteger('region_id')->unsigned()->after('email')->nullable();
            $table->foreign('region_id')->references('id')->on('regions');
            $table->bigInteger('province_id')->unsigned()->after('region_id')->nullable();
            $table->foreign('province_id')->references('id')->on('provinces');
            $table->bigInteger('city_id')->unsigned()->after('province_id')->nullable();
            $table->foreign('city_id')->references('id')->on('cities');
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
            $table->dropColumn(['region_id']);
            $table->dropColumn(['province_id']);
            $table->dropColumn(['city_id']);
        });
    }
}
