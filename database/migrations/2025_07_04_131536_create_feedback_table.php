<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->string('attendee_name');
            $table->string('attendee_email');
            $table->integer('overall_rating')->comment('1-5 rating');
            $table->integer('organization_rating')->nullable();
            $table->integer('venue_rating')->nullable();
            $table->integer('content_rating')->nullable();
            $table->text('comments')->nullable();
            $table->boolean('would_recommend')->default(true);
            $table->boolean('would_attend_again')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
