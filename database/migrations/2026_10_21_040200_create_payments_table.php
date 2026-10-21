<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->index()->constrained()->restrictOnDelete();
            $table->string('reference')->unique();
            $table->string('method', 100);
            $table->decimal('amount', 16, 2);
            $table->enum('status', ['failed', 'pending', 'paid']);
            $table->text('redirect_url')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
