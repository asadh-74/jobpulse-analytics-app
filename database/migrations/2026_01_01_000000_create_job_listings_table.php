<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_listings', function (Blueprint $table) {
            $table->id();

            // Remotive's own numeric id — used to upsert instead of duplicating
            // a listing every time the fetch command runs.
            $table->unsignedBigInteger('remotive_id')->unique();

            $table->string('title');
            $table->string('company_name');
            $table->string('company_logo_url')->nullable();
            $table->string('category')->index();
            $table->string('job_type')->nullable()->index();
            $table->string('candidate_location')->nullable();
            $table->string('salary_text')->nullable();
            $table->json('tags')->nullable();
            $table->text('description')->nullable();
            $table->string('apply_url');
            $table->timestamp('published_at')->index();

            // When our own fetch command first saw this row — lets the
            // dashboard show "new since yesterday" without relying on
            // Remotive's publish timestamp alone.
            $table->timestamp('first_seen_at');
            $table->boolean('is_active')->default(true)->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_listings');
    }
};
