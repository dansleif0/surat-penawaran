<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('offers', function (Blueprint $table) {
        $table->decimal('diskon_global', 15, 2)->default(0)->after('total_keseluruhan');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down()
{
    Schema::table('offers', function (Blueprint $table) {
        $table->dropColumn('diskon_global');
    });
}
};
