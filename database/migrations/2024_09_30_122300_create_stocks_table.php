<?php

use App\Enums\Congelateur;
use App\Models\Produit;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produit_id')->constrained()->onUpdate('cascade')
                ->onDelete('cascade');
            $table->enum('congelateur', ['Grand', 'Petit', 'Menimur']);
            $table->integer('poids')->nullable();
            $table->enum('etage', [1, 2, 3, 4, 5, 6, 7])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
