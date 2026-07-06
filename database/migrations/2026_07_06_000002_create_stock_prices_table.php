<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_upload_id')->constrained()->cascadeOnDelete();
            $table->string('stock_name');
            $table->decimal('price', 12, 6);
            $table->date('date');
            $table->timestamps();

            $table->index(['stock_upload_id', 'stock_name', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_prices');
    }
};
