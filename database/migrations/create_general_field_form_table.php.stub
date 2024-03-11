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
        Schema::create('general_field_form', function (Blueprint $table) {
            $table->id();

            $table->foreignId("general_field_id")->constrained();
            $table->string("custom_form_identifier");

            $table->boolean("is_required")->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migration
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('general_field_form');
    }
};

