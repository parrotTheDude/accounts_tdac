<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('participant_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('linked_user_id')->constrained('users')->onDelete('cascade');
            $table->enum('relation', ['parent', 'support_coordinator']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('participant_links');
    }
};
