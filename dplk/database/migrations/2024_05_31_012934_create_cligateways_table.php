<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCligatewaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::create('cligateways', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->string('route_name');
            $table->text('parameter');
            $table->integer('access_status');
            $table->date('access_date');
            $table->string('create_by');
            $table->string('last_change_by');
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
        Schema::dropIfExists('cligateways');
    }
}
