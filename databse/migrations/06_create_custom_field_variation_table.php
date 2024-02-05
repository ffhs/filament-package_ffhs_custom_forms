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
        Schema::create('custom_field_variation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_field_id')->nullable()->constrained()->cascadeOnDelete();

            $table->boolean("required");
            $table->boolean("is_active");
            $table->json('options')->nullable();

            $table->nullableMorphs("variation");

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

