<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // System Users
        Schema::create('system_users', static function (Blueprint $table) {
            $table->id();
            $table->string('prefixed_id')->nullable()->unique();
            // --------------
            $table->string('username')->unique()->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            // --------------
            $table->foreignId('creator_id')
                ->index()
                ->nullable()
                ->constrained(
                    table: 'system_users',
                    column: 'id',
                )
                ->onUpdate('cascade');

            // --------------
            $table->timestamps();
        });

        Schema::create('system_user_settings', static function (Blueprint $table) {
            $table->foreignId('system_user_id')
                ->primary()
                ->constrained(
                    table: 'system_users',
                    column: 'id',
                )
                ->onDelete('cascade')
                ->onUpdate('cascade');
            // --------------
            $table->json(config('defaults.settings.tbl_column'));
            // --------------
            $table->timestamps();
        });

    }
};
