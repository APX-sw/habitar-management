<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Consolida el esquema de la tabla employees luego de varios parches.
     *
     * Problema 1: increase_frequency (columna vieja) puede existir en BDs antiguas.
     * Problema 2: update_type fue creado como varchar(255) en vez de enum.
     * Problema 3: update_frequency_months puede no existir en BDs muy antiguas.
     *
     * Esta migración es idempotente: segura de correr en cualquier estado de la BD.
     */
    public function up(): void
    {
        // 1. Eliminar columna vieja si existe (de la migración original pre-corrección)
        if (Schema::hasColumn('employees', 'increase_frequency')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropColumn('increase_frequency');
            });
        }

        // 2. Agregar update_frequency_months si no existe
        if (!Schema::hasColumn('employees', 'update_frequency_months')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->integer('update_frequency_months')->nullable()->after('base_salary');
            });
        }

        // 3. Agregar update_type si no existe (por si el parche anterior no corrió)
        if (!Schema::hasColumn('employees', 'update_type')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->enum('update_type', ['fixed', 'indexed'])->nullable()->default('fixed')->after('base_salary');
            });
        } else {
            // Existe pero puede ser varchar(255): corregir a enum
            // Primero sanitizamos valores inválidos para que ALTER no falle
            DB::statement("UPDATE employees SET update_type = 'fixed' WHERE update_type NOT IN ('fixed', 'indexed') OR update_type IS NULL");
            // Convertir a ENUM correcto con default
            DB::statement("ALTER TABLE employees MODIFY COLUMN update_type ENUM('fixed', 'indexed') NOT NULL DEFAULT 'fixed'");
        }
    }

    /**
     * Revertir (best-effort sin borrar datos).
     */
    public function down(): void
    {
        // Revertir update_type a varchar nullable
        DB::statement("ALTER TABLE employees MODIFY COLUMN update_type VARCHAR(255) NULL DEFAULT 'fixed'");

        // Eliminar update_frequency_months si fue agregado aquí
        // (solo lo eliminamos si la columna existe para no romper BDs que ya lo tenían)
        if (Schema::hasColumn('employees', 'update_frequency_months')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropColumn('update_frequency_months');
            });
        }
    }
};
