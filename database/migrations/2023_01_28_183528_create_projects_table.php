<?php

use Carbon\Carbon;
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
        Schema::create('projects', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('title');
            $table->longText('description');
            $table->longText('our_review')->nullable();
            $table->string('state_name');
            $table->string('city_name')->nullable();
            $table->string('summary')->nullable();
            $table->bigInteger('price')->nullable();


            $table->boolean('validated')->default(false);
            $table->timestamp('show_time')->default(Carbon::now()->addDays(7));


            $table->foreignId('state_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('city_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('sub_category_id')->index()->constrained()->cascadeOnDelete();
            // $table->foreignId('category_id')->index()->constrained()->restrictOnDelete();
            // $table->foreignId('sub_category_id')->index()->constrained('sub_categories')->restrictOnDelete();
            $table->foreignUuid('user_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignUuid('company_id')->index()->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->index()->constrained()->cascadeOnDelete();
            // $table->foreignId('permission_id')->index()->constrained()->restrictOnDelete();
            $table->foreignId("tag_id")->index()->nullable()->constrained()->cascadeOnDelete();




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
        Schema::dropIfExists('projects');
    }
};
