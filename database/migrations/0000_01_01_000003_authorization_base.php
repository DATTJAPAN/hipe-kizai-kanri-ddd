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
        Schema::create('roles', static function (Blueprint $table) {
            $table->id();
            $table->string('prefixed_id')->nullable()->unique();
            // --------------
            $table->string('name');
            $table->string('display_name')->nullable();
            $table->string('guard_name');
            $table->unsignedBigInteger('hierarchy')->default(10); // lvl 0 - is the highest < is lowest
            $table->boolean('is_app_default')->default(false); // cannot be edited only the display; cannot be deleted either
            // ---- Relations and Constraints
            $table->unsignedBigInteger('org_id')->nullable();
            $table->string('org_type')->nullable();
            $table->index(['org_id', 'org_type'], 'roles_org_id_org_type_index');
            $table->foreign('org_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('cascade');

            // --------------
            $table->timestamps();
        });

        Schema::create('permissions', static function (Blueprint $table) {
            $table->id();
            $table->string('prefixed_id')->nullable()->unique();
            // --------------
            $table->string('name')->unique();
            $table->string('display_name')->nullable();
            $table->boolean('is_app_default')->default(false); // cannot be edited only the display; cannot be deleted either
            $table->string('target_class');

            $table->timestamps();
        });

        // ---- Intermediate Tables

        Schema::create('model_has_direct_permissions', static function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');

            // ---- Relations and Constraints
            $table->index(['model_id', 'model_type'], 'model_has_direct_permissions_model_id_model_type_index');

            $table->foreign('permission_id')
                ->references('id')
                ->on('permissions')
                ->onDelete('cascade');

            $table->primary(['permission_id', 'model_id', 'model_type'], 'model_has_direct_permissions_permission_model_type_primary');
        });

        /**
         *  Model has roles will get its permissions via roles
         */
        Schema::create('model_has_roles', static function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');

            // ---- Relations and Constraints
            $table->index(['model_id', 'model_type'], 'model_has_roles_model_id_model_type_index');
            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');

            $table->primary(['role_id', 'model_id', 'model_type'], 'model_has_roles_role_model_type_primary');
        });

        Schema::create('role_has_permissions', static function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('permission_id');

            $table->foreign('role_id')
                ->references('id')  // role id
                ->on('roles')
                ->onDelete('cascade');

            $table->foreign('permission_id')
                ->references('id') // permission id
                ->on('permissions')
                ->onDelete('cascade');

            $table->primary(['role_id', 'permission_id'], 'role_has_permissions_permission_id_role_id_primary');
        });
    }
};
