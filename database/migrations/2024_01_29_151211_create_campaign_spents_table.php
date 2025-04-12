<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignSpentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaign_spents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('attribution_id')->unsigned();
            $table->foreign('attribution_id')->references('id')->on('attributions');
            $table->date('date_spent');
            $table->decimal('ads_spent', 19, 2)->nullable();
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
        Schema::dropIfExists('campaign_spents');
    }
}
