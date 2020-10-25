<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('w_games', function (Blueprint $table) {
            $table->string('tracking_status',20)->default('Untracked');
            $table->string('tracking_details',500)->default('');
            $table->string('is_responsive',10)->default('');
            $table->string('fn_status',10)->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('w_games', function (Blueprint $table) {
            $table->dropColumn(['tracking_status', 'tracking_details','is_responsive','fn_status']);
        });
    }
}
