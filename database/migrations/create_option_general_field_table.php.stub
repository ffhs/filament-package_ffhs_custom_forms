<?php

use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomFieldVariation;
use Ffhs\FilamentPackageFfhsCustomForms\Models\CustomOption;
use Ffhs\FilamentPackageFfhsCustomForms\Models\GeneralField;
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
        Schema::create('option_general_field', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(GeneralField::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(CustomOption::class)->constrained()->cascadeOnDelete();

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

