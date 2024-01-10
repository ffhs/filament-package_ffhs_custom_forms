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
        Schema::create('custom_fields', function (Blueprint $table) {
            $table->id();

            // general field stuff
            $table->boolean('is_general_field_active')->nullable();
            $table->boolean('is_general_field')->default(false);
            $table->boolean("has_variations")->nullable();


            $table->foreignId('custom_form_id')->nullable()->constrained()->cascadeOnDelete();
            $table->integer('custom_form_place')->nullable();


            //Inherit GeneralField
            $table->foreignId("general_field_id")->nullable()
                ->constrained()->references('id')->on('custom_fields')->cascadeOnDelete();


            //That doesn't can be null if general_field_id are null
            $table->string('identify_key')->nullable()->unique();

            //FieldOptions
            $table->string('tool_tip_de')->nullable();
            $table->string('tool_tip_en')->nullable();
            $table->string('name_de')->nullable();
            $table->string('name_en')->nullable();
            $table->string("type")->nullable();
            $table->integer('form_position')->nullable();


            $table->timestamps();
            //$table->softDeletes();

            $table->unique(["general_field_id","custom_form_id"]);
            $table->unique(["custom_form_id","custom_form_place"]);


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

