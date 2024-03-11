<?php

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldVariation;
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
        Schema::create('custom_field_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_form_answer_id')->index("form_answer_id")->constrained()->cascadeOnDelete();
            $table->foreignIdFor(CustomFieldVariation::class)->index("variation_id"); //toDo fix ->constrained()->cascadeOnDelete();
            $table->json("answer")->nullable();

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
        Schema::dropIfExists('custom_field_variation');
    }
};

