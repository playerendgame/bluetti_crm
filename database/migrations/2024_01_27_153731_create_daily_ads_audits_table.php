<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyAdsAuditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_ads_audits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date_ad_spent');
            $table->unsignedDecimal('facebook_ad_spent', 19, 2);
            $table->unsignedDecimal('google_ad_spent', 19, 2);
            $table->unsignedDecimal('lazada_ad_spent', 19, 2);
            $table->unsignedDecimal('shopee_ad_spent', 19, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daily_ads_audits');
    }
}
