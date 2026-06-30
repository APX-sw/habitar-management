<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Mapeo exhaustivo de módulos y permisos específicos
        // Permisos mapeados según la lógica de negocio real de la inmobiliaria.
        // Solo se incluyen las acciones que realmente se pueden realizar en cada sección.
        $modulesAndPermissions = [
            'dashboard'      => ['read'],
            'users'          => ['create', 'read', 'update', 'delete'],
            'roles'          => ['create', 'read', 'update', 'delete'],
            'properties'     => ['create', 'read', 'update', 'delete'],
            'owners'         => ['create', 'read', 'update', 'delete'],
            'tenants'        => ['create', 'read', 'update', 'delete'],

            // Contratos: se pueden crear, ver, editar (antes de activar) y cerrar su ciclo de vida.
            // NO se eliminan: los contratos se rescinden, renuevan o renegocian.
            'leases'         => ['create', 'read', 'update', 'renew', 'renegotiate', 'terminate'],

            // Cargos extra: se crean y eliminan. NO se editan (inmutables una vez cargados).
            'extra_charges'  => ['create', 'read', 'delete'],

            // Cargos fijos: sí se pueden editar (ej: actualizar monto de expensas).
            'fixed_charges'  => ['create', 'read', 'update', 'delete'],

            // Cobros: se registran y se envía el recibo. Son registros contables inmutables.
            // NO tiene update ni delete: no se editan ni eliminan.
            'collections'    => ['create', 'read', 'send_receipt'],

            // Caja: se puede ver, registrar movimientos, transferir y ajustar manualmente.
            // Los movimientos de caja son inmutables: NO tiene update ni delete.
            'cash_register'  => ['read', 'create', 'transfer', 'adjust'],

            // Gastos: CRUD completo, pueden editarse si aún no fueron rendidos.
            'expenses'       => ['create', 'read', 'update', 'delete'],

            // Rendiciones: se crean y se envían al propietario. Son inmutables una vez generadas.
            // NO tiene update ni delete.
            'settlements'    => ['create', 'read', 'send_to_owner'],

            // Reportes: solo se visualizan y envían. No se crean ni eliminan manualmente.
            'reports'        => ['read', 'send_email'],

            // -------------------------------------------------------------------------
            // CONFIGURACIÓN DEL SISTEMA (7 sub-secciones reales)
            // -------------------------------------------------------------------------

            // Ubicación: provincias y localidades para las propiedades.
            'cfg_locations'         => ['create', 'read', 'update', 'delete'],

            // Recursos Humanos (Sub-módulos)
            'rrhh_employees'        => ['create', 'read', 'update', 'delete'],
            'rrhh_attendances'      => ['create', 'read', 'update', 'delete'],
            'rrhh_office'           => ['read'],
            'rrhh_objectives'       => ['create', 'read', 'update', 'delete'],

            // Inmuebles: tipos de inmuebles (Casa, Dpto, Local, etc.).
            'cfg_property_types'    => ['create', 'read', 'update', 'delete'],

            // Actualización: índices de indexación mensual (IPC, ICL, etc.).
            'cfg_index_rates'       => ['create', 'read', 'update', 'delete'],

            // Tesorería: métodos de pago y cuentas bancarias internas.
            'cfg_treasury'          => ['create', 'read', 'update', 'delete'],

            // Plan de Cuentas: categorías de ingresos y gastos para reportes.
            'cfg_chart_accounts'    => ['create', 'read', 'update', 'delete'],

            // Cuentas Cobro: cuentas bancarias de la inmobiliaria para transferencias.
            'cfg_collection_accounts' => ['create', 'read', 'update', 'delete'],

            // Contacto: información de contacto (WhatsApp, email) para envío de mails.
            // Solo se consulta y se actualiza, no se crea ni se elimina.
            'cfg_contact'           => ['read', 'update'],

            // Motivos de ausencia para RRHH
            'cfg_absence_reasons'   => ['create', 'read', 'update', 'delete'],
        ];

        // Crear permisos
        foreach ($modulesAndPermissions as $module => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => $module . '.' . $action, 'guard_name' => 'web']);
            }
        }

        // Crear rol superadmin
        $role = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
        
        // El superadmin tiene todos los permisos
        $role->givePermissionTo(Permission::all());

        // Crear usuario superadmin
        $user = User::firstOrCreate(
            ['email' => 'superadmin@habitar.com.ar'],
            [
                'name' => 'Super Administrador',
                'password' => Hash::make('password'),
            ]
        );

        // Asignar rol al usuario
        $user->assignRole($role);
    }
}
