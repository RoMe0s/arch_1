<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->uuid('id');
            $table->primary('id');
            $table->uuid('owner_id');
            $table->foreign('owner_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->string('owner_name')->nullable();
            $table->uuid('competitor_id')->nullable();
            $table->foreign('competitor_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->string('competitor_name')->nullable();
            $table->uuid('winner_id')->nullable();
            $table->foreign('winner_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('games');
    }
}
