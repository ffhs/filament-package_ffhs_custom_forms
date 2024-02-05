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
        Schema::create('custom_options', function (Blueprint $table) {
            $table->id();
            $table->string('name_de');
            $table->string('name_en');
            $table->string('custom_key')->nullable();

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

