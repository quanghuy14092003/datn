<?php

use App\Models\Cart;
use App\Models\Color;
use App\Models\Product;
use App\Models\Size;
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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Cart::class)->constrained();
            $table->foreignIdFor(Product::class)->constrained();
            $table->integer('quantity'); 
            $table->decimal('price', 10, 2);
            $table->foreignIdFor(Size::class)->constrained();
            $table->foreignIdFor(Color::class)->constrained();
            $table->decimal('total', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
