<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feedback', function (Blueprint $table) {
            // Hernoem kolommen indien nodig
            if (Schema::hasColumn('feedback', 'overall_rating') && !Schema::hasColumn('feedback', 'rating')) {
                $table->renameColumn('overall_rating', 'rating');
            }
            if (Schema::hasColumn('feedback', 'comments') && !Schema::hasColumn('feedback', 'comment')) {
                $table->renameColumn('comments', 'comment');
            }

            // Voeg ontbrekende kolommen toe
            if (!Schema::hasColumn('feedback', 'categories')) {
                $table->json('categories')->nullable()->comment('JSON array of feedback categories')->after('comment');
            }
            if (!Schema::hasColumn('feedback', 'status')) {
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('categories');
            }
            if (!Schema::hasColumn('feedback', 'admin_notes')) {
                $table->text('admin_notes')->nullable()->after('status');
            }
            if (!Schema::hasColumn('feedback', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable()->after('admin_notes');
            }
            if (!Schema::hasColumn('feedback', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('submitted_at');
            }
            if (!Schema::hasColumn('feedback', 'reviewed_by')) {
                $table->foreignId('reviewed_by')->nullable()->after('reviewed_at')->constrained('users')->onDelete('set null');
            }

            // Verwijder oude kolommen indien aanwezig
            if (Schema::hasColumn('feedback', 'organization_rating')) {
                $table->dropColumn('organization_rating');
            }
            if (Schema::hasColumn('feedback', 'venue_rating')) {
                $table->dropColumn('venue_rating');
            }
            if (Schema::hasColumn('feedback', 'content_rating')) {
                $table->dropColumn('content_rating');
            }
            if (Schema::hasColumn('feedback', 'would_recommend')) {
                $table->dropColumn('would_recommend');
            }
            if (Schema::hasColumn('feedback', 'would_attend_again')) {
                $table->dropColumn('would_attend_again');
            }
        });

        // Voeg indexes toe (eenvoudige aanpak voor SQLite)
        try {
            Schema::table('feedback', function (Blueprint $table) {
                $table->index(['event_id', 'status']);
            });
        } catch (\Exception $e) {
            // Index bestaat al of kan niet worden aangemaakt
        }

        try {
            Schema::table('feedback', function (Blueprint $table) {
                $table->index(['rating']);
            });
        } catch (\Exception $e) {
            // Index bestaat al of kan niet worden aangemaakt
        }

        try {
            Schema::table('feedback', function (Blueprint $table) {
                $table->index(['submitted_at']);
            });
        } catch (\Exception $e) {
            // Index bestaat al of kan niet worden aangemaakt
        }
    }

    public function down(): void
    {
        Schema::table('feedback', function (Blueprint $table) {
            // Down migratie: alleen verwijderen wat is toegevoegd
            if (Schema::hasColumn('feedback', 'categories')) {
                $table->dropColumn('categories');
            }
            if (Schema::hasColumn('feedback', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('feedback', 'admin_notes')) {
                $table->dropColumn('admin_notes');
            }
            if (Schema::hasColumn('feedback', 'submitted_at')) {
                $table->dropColumn('submitted_at');
            }
            if (Schema::hasColumn('feedback', 'reviewed_at')) {
                $table->dropColumn('reviewed_at');
            }
            if (Schema::hasColumn('feedback', 'reviewed_by')) {
                $table->dropForeign(['reviewed_by']);
                $table->dropColumn('reviewed_by');
            }
        });

        // Verwijder indexes
        try {
            Schema::table('feedback', function (Blueprint $table) {
                $table->dropIndex(['event_id', 'status']);
            });
        } catch (\Exception $e) {
            // Index bestaat niet
        }

        try {
            Schema::table('feedback', function (Blueprint $table) {
                $table->dropIndex(['rating']);
            });
        } catch (\Exception $e) {
            // Index bestaat niet
        }

        try {
            Schema::table('feedback', function (Blueprint $table) {
                $table->dropIndex(['submitted_at']);
            });
        } catch (\Exception $e) {
            // Index bestaat niet
        }
    }
};
