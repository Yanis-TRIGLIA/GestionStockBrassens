<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddObservationToSortiesTable extends Migration
{
    public function up()
    {
        Schema::table('sorties', function (Blueprint $table) {
            $table->string('observation')->nullable()->after('number_after_reduce'); 
        });
    }

    public function down()
    {
        Schema::table('sorties', function (Blueprint $table) {
            $table->dropColumn('observation'); 
        });
    }
}
