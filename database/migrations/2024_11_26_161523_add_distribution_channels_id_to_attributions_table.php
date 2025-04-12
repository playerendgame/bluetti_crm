<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDistributionChannelsIdToAttributionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attributions', function (Blueprint $table) {
            $table->bigInteger('distribution_channel_id')->unsigned()->nullable()->after('campaign_name');
            $table->foreign('distribution_channel_id')->references('id')->on('attributions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attributions', function (Blueprint $table) {
            $table->dropColumn(['distribution_channel_id']);
            $table->dropForeign(['distribution_channel_id']);
        });
    }
}
