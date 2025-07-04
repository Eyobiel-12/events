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
        Schema::table('tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('tickets', 'attendee_name')) {
                $table->string('attendee_name')->nullable()->after('ticket_type_id');
            }
            if (!Schema::hasColumn('tickets', 'attendee_email')) {
                $table->string('attendee_email')->nullable()->after('attendee_name');
            }
            if (!Schema::hasColumn('tickets', 'attendee_phone')) {
                $table->string('attendee_phone')->nullable()->after('attendee_email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'attendee_name')) {
                $table->dropColumn('attendee_name');
            }
            if (Schema::hasColumn('tickets', 'attendee_email')) {
                $table->dropColumn('attendee_email');
            }
            if (Schema::hasColumn('tickets', 'attendee_phone')) {
                $table->dropColumn('attendee_phone');
            }
        });
    }
};
