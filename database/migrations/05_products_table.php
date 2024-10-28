<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécutez les migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name_product');
            $table->string('picture_product');
            $table->decimal('price', 9, 2);
            $table->string('description_product');
            $table->unsignedBigInteger('id_category');
            $table->unsignedBigInteger('id_company');
            $table->foreign('id_category')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');
            $table->foreign('id_company')
                ->references('id')
                ->on('companies')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Annulez les migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
