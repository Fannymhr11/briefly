<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('brief_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('creative_task_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('action', 40)->index();
            $table->string('description');
            $table->timestamp('created_at')->useCurrent()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_histories');
    }
};
