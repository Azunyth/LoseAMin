<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_tables', function (Blueprint $table) {


            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')
                  ->on('users')->onDelete('cascade');

            $table->integer('table_id')->unsigned();
            $table->foreign('table_id')->references('id')
                  ->on('tables')->onDelete('cascade');

            $table->unique(array('user_id', 'table_id'));

            $table->timestamps();
        });

        Schema::table('tables', function (Blueprint $table) {
            $table->integer('max_seat')->default(7)->change();
            $table->integer('seats_available')->default(7)->change();
            $table->boolean('is_closed')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_tables');
    }
}
