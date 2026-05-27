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
        // 1. employees
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('document_number');
            $table->string('phone');
            $table->string('email');
            $table->date('hire_date');
            $table->string('job_title');
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('cbu_alias')->nullable();
            $table->timestamps();
        });

        // 2. employee_documents
        Schema::create('employee_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->string('document_type'); // e.g. 'dni', 'contract', 'other'
            $table->string('file_path');
            $table->string('original_name');
            $table->timestamps();
        });

        // 3. absence_reasons
        Schema::create('absence_reasons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. attendances
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->date('date');
            $table->enum('status', ['present', 'absent']);
            $table->foreignId('absence_reason_id')->nullable()->constrained('absence_reasons')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Unique constraint to avoid double check-in per day per employee
            $table->unique(['employee_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('absence_reasons');
        Schema::dropIfExists('employee_documents');
        Schema::dropIfExists('employees');
    }
};
