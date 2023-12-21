<?php

use App\Models\GeneralField;
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
        Schema::create('custom_forms', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->string("custom_form_identifier");
            $table->string("short_title")->nullable();

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

