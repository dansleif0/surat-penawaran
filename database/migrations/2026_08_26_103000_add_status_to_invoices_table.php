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
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('status')->default('single')->nullable()->after('no_invoice');
        });

        Schema::create('invoice_merged_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merged_invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->foreignId('ref_invoice_id')->nullable()->constrained('invoices')->onDelete('set null');
            $table->date('date');
            $table->date('due_date')->nullable();
            $table->string('invoice_no');
            $table->string('currency')->default('-');
            $table->bigInteger('debit')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_merged_items');
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
