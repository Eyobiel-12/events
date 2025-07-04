<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booths', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('vendor_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->string('number')->unique();
            $table->text('description')->nullable();
            $table->enum('size', ['small', 'medium', 'large'])->default('medium');
            $table->decimal('price', 10, 2)->default(0);
            $table->enum('status', ['available', 'reserved', 'occupied'])->default('available');
            $table->json('amenities')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booths');
    }
};
