<?php

use App\Enums\RussianAgesEnum;
use App\Models\User;
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
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->string('name');
            $table->unsignedSmallInteger('year')->nullable();
            $table->string('country')->nullable();
            $table->enum('age_restrictions', RussianAgesEnum::values())->nullable();
            $table->unsignedBigInteger('duration');
            $table->text('logline')->nullable();
            $table->text('description')->nullable();
            // TODO: Replace string length
            $table->string('poster', 1000)->nullable();
            $table->string('trailer')->nullable();
            $table->unsignedBigInteger('kinopoisk_id')->nullable();
            $table->unsignedFloat('kinopoisk_rating')->nullable();
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
        Schema::dropIfExists('movies');
    }
};
