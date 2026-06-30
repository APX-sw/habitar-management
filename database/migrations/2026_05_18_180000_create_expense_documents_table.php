<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('expense_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_id')->constrained()->onDelete('cascade');
            $table->string('filename');
            $table->string('path');
            $table->unsignedBigInteger('size')->nullable();
            $table->string('mime_type')->nullable();
            $table->timestamps();
        });

        // Migrate existing attachment_path fields safely
        if (Schema::hasColumn('expenses', 'attachment_path')) {
            $expenses = DB::table('expenses')->whereNotNull('attachment_path')->get();
            foreach ($expenses as $expense) {
                if (!empty($expense->attachment_path)) {
                    $filename = basename($expense->attachment_path);
                    DB::table('expense_documents')->insert([
                        'expense_id' => $expense->id,
                        'filename' => $filename,
                        'path' => $expense->attachment_path,
                        'size' => 0,
                        'mime_type' => 'application/octet-stream',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_documents');
    }
};
