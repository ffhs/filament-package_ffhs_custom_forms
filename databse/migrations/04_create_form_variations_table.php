<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_variations', function (Blueprint $table) {
            $table->id();

            $table->foreignId("custom_form_id")->constrained();
            $table->string("short_title")->nullable();
            $table->boolean("is_hidden")->default(false);
            $table->boolean("is_disabled")->default(false);


            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migration
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('custom_fields');
    }
};

