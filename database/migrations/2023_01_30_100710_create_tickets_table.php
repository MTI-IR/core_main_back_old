<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->text('explanation')->nullable();
            $table->boolean('checked')->default(false);
            $table->foreignUuid('user_id')->index()->constrained()->cascadeOnDelete();;
            $table->foreignUuid('project_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('time_id')->index()->nullable()->constrained()->nullOnDelete();


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
        Schema::dropIfExists('tickets');
    }
};
