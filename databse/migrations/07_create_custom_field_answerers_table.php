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
        Schema::create('custom_field_answerers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_form_answerer_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(CustomFieldVariation::class)->constrained()->cascadeOnDelete();
            $table->json("answerer")->nullable();

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

