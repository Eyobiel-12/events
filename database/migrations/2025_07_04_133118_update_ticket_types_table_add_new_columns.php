<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_types', function (Blueprint $table) {
            // Alleen toevoegen als ze nog niet bestaan
            if (!Schema::hasColumn('ticket_types', 'sale_start_date')) {
                $table->timestamp('sale_start_date')->nullable();
            }
            if (!Schema::hasColumn('ticket_types', 'sale_end_date')) {
                $table->timestamp('sale_end_date')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('ticket_types', function (Blueprint $table) {
            if (Schema::hasColumn('ticket_types', 'sale_start_date')) {
                $table->dropColumn('sale_start_date');
            }
            if (Schema::hasColumn('ticket_types', 'sale_end_date')) {
                $table->dropColumn('sale_end_date');
            }
        });
    }
};
