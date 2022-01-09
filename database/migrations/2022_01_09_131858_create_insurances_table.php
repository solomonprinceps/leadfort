<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInsurancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('insurances', function (Blueprint $table) {
            $table->id();
            $table->string("status")->default("0")->nullable();
            $table->string("policy_id")->nullable();
            $table->string("insurance_id")->nullable();
            $table->string("customer_id")->nullable();
            $table->string("attach_policies_id")->nullable();
            $table->string("value_of_assets")->nullable();
            $table->string("state")->nullable();
            $table->string("lga")->nullable();
            $table->string("description")->nullable();
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
        Schema::dropIfExists('insurances');
    }
}
