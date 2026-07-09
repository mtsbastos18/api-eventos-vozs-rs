<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('event_post_details', function (Blueprint $table) {
            // Adiciona a coluna 'youtube_video_url' após a coluna 'flickr_images' (opcional)
            $table->string('youtube_video_url')->nullable()->after('flickr_images');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_post_details', function (Blueprint $table) {
            // Remove a coluna caso seja feito um rollback
            $table->dropColumn('youtube_video_url');
        });
    }
};
