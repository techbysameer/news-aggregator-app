<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('source_id')->constrained('sources'); // Foreign key to sources
            $table->text('title'); 
            $table->text('description')->nullable(); 
            $table->string('author')->nullable(); 
            $table->jsonb('content'); 
            $table->string('url'); 
            $table->timestamp('published_at'); 
            $table->string('category')->nullable();
            $table->timestamps(); 
            // Add unique constraint for title and source_id
            $table->unique(['source_id', 'title']);
        });

        // Now added the tsvector type in a raw statement bcz it isnot supported in Laravel Bluprint
        DB::statement('ALTER TABLE articles ADD COLUMN search_vector tsvector');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
