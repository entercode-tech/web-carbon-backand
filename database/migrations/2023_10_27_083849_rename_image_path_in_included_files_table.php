<?php

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
        Schema::table('included_files', function (Blueprint $table) {
            // rename image_path to file_path
            $table->renameColumn('image_path', 'file_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('included_files', function (Blueprint $table) {
            // rename file_path to image_path
            $table->renameColumn('file_path', 'image_path');
        });
    }
};
