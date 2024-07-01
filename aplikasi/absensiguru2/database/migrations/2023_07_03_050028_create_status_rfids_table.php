<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateStatusRfidsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('status_rfids', function (Blueprint $table) {
            $table->id();
            $table->enum('scan', ['Y', 'T']);
            $table->timestamps();
        });

        DB::table('status_rfids')->insert([
            'id' => 1,
            'scan' => 'T'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('status_rfids');
    }
}
