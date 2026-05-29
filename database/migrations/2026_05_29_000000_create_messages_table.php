<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('receiver_id')->nullable();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->enum('type', ['direct', 'announcement']);
            $table->string('subject')->nullable();
            $table->text('body');
            $table->boolean('is_pinned')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');

            $table->index('sender_id');
            $table->index('receiver_id');
            $table->index('team_id');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
