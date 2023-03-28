<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsForMediable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('media', function (Blueprint $table) {
            $table->string('height')->default('50');
            $table->string('width')->default('50');
            $table->string('alt')->default('');
        });

        Schema::table('mediables', function (Blueprint $table) {
            $table->uuid('mediable_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn('height');
            $table->dropColumn('width');
            $table->dropColumn('alt');
        });

        Schema::table('mediables', function (Blueprint $table) {
            $table->dropColumn('mediable_id');
        });
    }
}