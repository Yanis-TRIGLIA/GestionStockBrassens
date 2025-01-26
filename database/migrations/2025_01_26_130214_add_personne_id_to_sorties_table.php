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
        Schema::table('sorties', function (Blueprint $table) {
            // Vérifier si la colonne n'existe pas déjà
            if (!Schema::hasColumn('sorties', 'personne_id')) {
                // Ajoute la colonne personne_id et la lie à la table zones
                $table->foreignId('personne_id')->nullable()->constrained('zones')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sorties', function (Blueprint $table) {
            if (Schema::hasColumn('sorties', 'personne_id')) {
                $table->dropForeign(['personne_id']);
                $table->dropColumn('personne_id');
            }
        });
    }
};
