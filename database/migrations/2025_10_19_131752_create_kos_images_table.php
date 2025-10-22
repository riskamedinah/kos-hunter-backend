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
    Schema::create('kos_images', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('kos_id');
        $table->string('file');
        $table->timestamps();

        $table->foreign('kos_id')->references('id')->on('kos')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kos_images');
    }
};
