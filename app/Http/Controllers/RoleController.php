<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')->paginate(15);
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        $groupedPermissions = $this->groupPermissions($permissions);
        
        $moduleNames = $this->getModuleNames();
        $actionNames = $this->getActionNames();
        
        return view('roles.create', compact('groupedPermissions', 'moduleNames', 'actionNames'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'permissions' => 'array',
        ]);

        $role = Role::create(['name' => $request->name]);
        
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Rol creado exitosamente.');
    }

    public function edit(Role $role)
    {
        if ($role->name === 'superadmin') {
            return redirect()->route('roles.index')
                ->with('error', 'El rol de Super Administrador es inmutable y no puede ser editado.');
        }

        $permissions = Permission::all();
        $groupedPermissions = $this->groupPermissions($permissions);
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        
        $moduleNames = $this->getModuleNames();
        $actionNames = $this->getActionNames();

        return view('roles.edit', compact('role', 'groupedPermissions', 'rolePermissions', 'moduleNames', 'actionNames'));
    }

    public function update(Request $request, Role $role)
    {
        if ($role->name === 'superadmin') {
            return redirect()->route('roles.index')
                ->with('error', 'El rol de Super Administrador es inmutable y no puede ser modificado.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array',
        ]);

        $role->update(['name' => $request->name]);
        
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        } else {
            $role->syncPermissions([]); // Clear all if none selected
        }

        return redirect()->route('roles.index')
            ->with('success', 'Rol actualizado exitosamente.');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'superadmin') {
            return redirect()->route('roles.index')
                ->with('error', 'El rol de Super Administrador es inmutable y no puede ser eliminado.');
        }

        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')
                ->with('error', 'No puedes eliminar un rol que tiene usuarios asignados. Quita los usuarios primero.');
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Rol eliminado exitosamente.');
    }

    /**
     * Helper to group permissions by logical Sidebar section instead of just the prefix
     */
    private function groupPermissions($permissions)
    {
        $sections = $this->getSidebarSections();
        $grouped = [];
        
        // Initialize the grouped array with the exact order of the sidebar
        foreach ($sections as $key => $section) {
            $grouped[$key] = [
                'name' => $section['name'],
                'icon' => $section['icon'],
                'permissions' => []
            ];
        }

        foreach ($permissions as $permission) {
            $parts = explode('.', $permission->name);
            $modulePrefix = $parts[0];
            $action = $parts[1] ?? 'general';
            
            // Find which sidebar section this module belongs to
            $targetSectionKey = 'settings'; // Default to settings if unknown
            $moduleDisplayName = ucfirst($modulePrefix);

            foreach ($sections as $key => $section) {
                if (in_array($modulePrefix, $section['prefixes'])) {
                    $targetSectionKey = $key;
                    // Try to get a specific translation for the sub-module if it exists
                    $moduleDisplayName = $this->getModuleNames()[$modulePrefix] ?? ucfirst($modulePrefix);
                    break;
                }
            }
            
            $grouped[$targetSectionKey]['permissions'][] = [
                'id' => $permission->id,
                'name' => $permission->name,
                'action' => $action,
                'module_display_name' => $moduleDisplayName
            ];
        }

        // Remove empty sections
        foreach ($grouped as $key => $data) {
            if (empty($data['permissions'])) {
                unset($grouped[$key]);
            }
        }

        return $grouped;
    }

    private function getSidebarSections()
    {
        return [
            'dashboard' => [
                'name' => 'Tablero',
                'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>',
                'prefixes' => ['dashboard']
            ],
            'properties' => [
                'name' => 'Propiedades',
                'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>',
                'prefixes' => ['properties', 'property_documents']
            ],
            'owners' => [
                'name' => 'Propietarios',
                'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
                'prefixes' => ['owners']
            ],
            'tenants' => [
                'name' => 'Inquilinos',
                'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>',
                'prefixes' => ['tenants']
            ],
            'leases' => [
                'name' => 'Contratos',
                'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>',
                'prefixes' => ['leases', 'extra_charges', 'fixed_charges', 'lease_documents']
            ],
            'collections' => [
                'name' => 'Cobros',
                'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>',
                'prefixes' => ['collections']
            ],
            'cash_register' => [
                'name' => 'Caja',
                'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>',
                'prefixes' => ['cash_register']
            ],
            'expenses' => [
                'name' => 'Gastos',
                'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>',
                'prefixes' => ['expenses', 'expense_documents']
            ],
            'settlements' => [
                'name' => 'Rendiciones',
                'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>',
                'prefixes' => ['settlements']
            ],
            'reports' => [
                'name' => 'Reportes',
                'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>',
                'prefixes' => ['reports']
            ],
            'users' => [
                'name' => 'Usuarios',
                'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
                'prefixes' => ['users']
            ],
            'rrhh' => [
                'name' => 'Recursos Humanos',
                'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
                'prefixes' => ['rrhh', 'rrhh_employees', 'rrhh_attendances', 'rrhh_office', 'rrhh_objectives']
            ],
            'roles' => [
                'name' => 'Roles y Permisos',
                'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>',
                'prefixes' => ['roles']
            ],
            'settings' => [
                'name' => 'Configuración',
                'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>',
                'prefixes' => [
                    'settings', 'accounts', 'categories', 'taxes',
                    'cfg_locations', 'cfg_property_types', 'cfg_index_rates',
                    'cfg_treasury', 'cfg_chart_accounts', 'cfg_collection_accounts', 'cfg_contact',
                    'cfg_absence_reasons'
                ]
            ],
        ];
    }

    private function getModuleNames()
    {
        return [
            'dashboard' => 'Tablero',
            'properties' => 'Propiedades',
            'owners' => 'Propietarios',
            'tenants' => 'Inquilinos',
            'leases' => 'Contratos',
            'collections' => 'Cobros',
            'cash_register' => 'Caja',
            'expenses' => 'Gastos',
            'settlements' => 'Rendiciones',
            'reports' => 'Reportes',
            'settings' => 'Configuración',
            'users' => 'Usuarios',
            'roles' => 'Roles y Permisos',
            'rrhh' => 'Recursos Humanos',
            'rrhh_employees' => 'Legajos',
            'rrhh_attendances' => 'Asistencias',
            'rrhh_office' => 'Tablero de Oficina',
            'rrhh_objectives' => 'Objetivos',
            'documents'                => 'Documentos',
            'extra_charges'            => 'Cargos Extra',
            'fixed_charges'            => 'Cargos Fijos',
            // Sub-secciones de Configuración
            'cfg_locations'            => 'Ubicación',
            'cfg_property_types'       => 'Inmuebles',
            'cfg_index_rates'          => 'Actualización',
            'cfg_treasury'             => 'Tesorería',
            'cfg_chart_accounts'       => 'Plan de Cuentas',
            'cfg_collection_accounts'  => 'Cuentas Cobro',
            'cfg_contact'              => 'Contacto',
            'cfg_absence_reasons'      => 'Motivos de Ausencia',
        ];
    }

    private function getActionNames()
    {
        return [
            'create' => 'Crear',
            'read' => 'Ver / Listar',
            'update' => 'Editar',
            'delete' => 'Eliminar',
            'renew' => 'Renovar',
            'renegotiate' => 'Renegociar',
            'terminate' => 'Rescindir',
            'pay' => 'Pagar / Procesar',
            'send_receipt' => 'Enviar Recibo',
            'transfer' => 'Transferir',
            'send_to_owner' => 'Enviar a Propietario',
            'adjust' => 'Ajustes Manuales',
            'send_email' => 'Enviar Email',
            'manage' => 'Gestionar (Subir/Eliminar)',
        ];
    }
}
