<?php

declare(strict_types=1);

use App\Domains\Organization\Units\OrganizationUnitType;
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

        // Organizations
        Schema::create('organizations', static function (Blueprint $table) {
            $table->id();
            $table->string('prefixed_id')->nullable()->unique();
            // --------------
            $table->string('name')->unique()->nullable();
            $table->string('business_email')->unique()->nullable();
            $table->string('domain')->unique()->nullable();
            $table->json('alt_domains')->nullable();
            $table->foreignId('creator_sys_user_id')
                ->index()
                ->nullable()
                ->constrained(
                    table: 'system_users',
                    column: 'id',
                )
                ->onDelete('set null')
                ->onUpdate('cascade');

            // --------------
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('organization_users', static function (Blueprint $table) {
            $table->id();
            $table->string('prefixed_id')->nullable()->unique();
            // --------------
            $table->string('username')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            // --------------
            $table->foreignId('org_id')
                ->index()
                ->constrained(
                    table: 'organizations',
                    column: 'id',
                )
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('creator_org_user_id')
                ->index()
                ->nullable()
                ->constrained(
                    table: 'organization_users',
                    column: 'id',
                )
                ->onUpdate('cascade')
                ->onDelete('set null');
            // --------------
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('organization_user_settings', static function (Blueprint $table) {
            $table->foreignId('organization_user_id')
                ->primary()
                ->constrained(
                    table: 'organization_users',
                    column: 'id',
                )
                ->onDelete('cascade')
                ->onUpdate('cascade');
            // --------------
            $table->json(config('defaults.settings.tbl_column'));
            // --------------
            $table->timestamps();
        });

        // Organizations - Extra Data
        Schema::create('organization_units', static function (Blueprint $table) {
            $table->id();
            $table->string('prefixed_id')->nullable()->unique();
            // --------------
            $table->string('name');
            $table->string('code')->unique()->nullable();
            $table->text('description')->nullable();
            $table->enum('type', OrganizationUnitType::options());
            $table->integer('hierarchy')->default(10); // lowest is highest
            $table->boolean('is_strict_hierarchy')->default(false); // if true, then enforce its parent to the linear format
            $table->boolean('is_active')->default(true);
            // --------------
            $table->foreignId('parent_unit_id')
                ->index()
                ->nullable()
                ->constrained(
                    table: 'organization_units',
                    column: 'id',
                )
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreignId('head_org_user_id')
                ->index()
                ->nullable()
                ->constrained(
                    table: 'organization_users',
                    column: 'id',
                )
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreignId('org_id')
                ->index()
                ->constrained(
                    table: 'organizations',
                    column: 'id',
                )
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreignId('creator_org_user_id')
                ->index()
                ->nullable()
                ->constrained(
                    table: 'organization_users',
                    column: 'id',
                )
                ->onDelete('set null')
                ->onUpdate('cascade');

            // --------------
            $table->timestamps();
        });

        Schema::create('organization_unit_members', static function (Blueprint $table) {
            $table->id();
            // --------------
            $table->foreignId('org_unit_id')
                ->constrained(
                    table: 'organization_units',
                    column: 'id'
                )
                ->onDelete('cascade');

            $table->foreignId('org_user_id')
                ->constrained(
                    table: 'organization_users',
                    column: 'id',
                )
                ->onDelete('cascade');

            $table->string('role')->nullable(); // role within the unit

            // ---- Relations and Constraints
            $table->unique(['org_unit_id', 'org_user_id']);

            $table->foreignId('org_id')
                ->nullable()
                ->index()
                ->constrained(
                    table: 'organizations',
                    column: 'id',
                )
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->timestamps();
        });

        Schema::create('organization_tags', static function (Blueprint $table) {
            $table->id();
            $table->string('prefixed_id')->nullable()->unique();
            // --------------
            $table->string('name');
            $table->string('code')->unique()->nullable();
            // --------------
            $table->foreignId('org_id')
                ->index()
                ->constrained(
                    table: 'organizations',
                    column: 'id',
                )
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreignId('creator_org_user_id')
                ->nullable()
                ->index()
                ->constrained(
                    table: 'organization_users',
                    column: 'id',
                )
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->timestamps();
        });

        Schema::create('organization_taggables', static function (Blueprint $table) {
            $table->unsignedBigInteger('tag_id');
            $table->string('taggable_type');
            $table->unsignedBigInteger('taggable_id');

            // ---- Relations and Constraints
            $table->foreignId('org_id')
                ->nullable()
                ->index()
                ->constrained(
                    table: 'organizations',
                    column: 'id',
                )
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('tag_id')
                ->references('id')
                ->on('organization_tags')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            // ---- Indexes
            $table->index(['taggable_type', 'taggable_id']);
            $table->index(['tag_id', 'taggable_type', 'taggable_id']);

            // ---- Primary Key
            $table->primary(['tag_id', 'taggable_type', 'taggable_id']);
        });

        Schema::create('organization_networks', function (Blueprint $table) {
            $table->id();
            $table->string('prefixed_id')->nullable()->unique();
            // --------------
            $table->string('name')->fulltext();
            $table->ipAddress('network_address')->unique();
            $table->unsignedBigInteger('network_address_long')->unique();
            $table->unsignedTinyInteger('cidr');
            $table->string('broadcast');

            // ---- Relations and Constraints
            $table->foreignId('org_id')
                ->index()
                ->constrained(
                    table: 'organizations',
                    column: 'id',
                )
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('creator_org_user_id')
                ->index()
                ->nullable()
                ->constrained(
                    table: 'organization_users',
                    column: 'id',
                )
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->timestamps();
        });
    }
};
