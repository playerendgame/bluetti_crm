<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFunnelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('funnels', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('campaign_name')->nullable();
            $table->bigInteger('attribution_id')->unsigned()->nullable();
            $table->foreign('attribution_id')->references('id')->on('attributions');
            $table->boolean('is_active')->default(1);
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
        Schema::dropIfExists('funnels');
    }
}
