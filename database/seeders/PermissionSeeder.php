<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Crear permisos
        $verImportacion = Permission::firstOrCreate([
            'name' => 'ver importacion pedidos',
            'guard_name' => 'web',
        ]);

        $importarPedidos = Permission::firstOrCreate([
            'name' => 'importar pedidos',
            'guard_name' => 'web',
        ]);

        $verHistorial = Permission::firstOrCreate([
            'name' => 'ver historial importaciones',
            'guard_name' => 'web',
        ]);

        // Administrador
        $administrador = Role::where('name', 'Administrador')->first();

        if ($administrador) {
            $administrador->givePermissionTo([
                $verImportacion,
                $importarPedidos,
                $verHistorial,
            ]);
        }

        // Ventas
        $ventas = Role::where('name', 'Ventas')->first();

        if ($ventas) {
            $ventas->givePermissionTo([
                $verImportacion,
                $importarPedidos,
                $verHistorial,
            ]);
        }

        // Departamentos que pueden VER el módulo
        $rolesConsulta = [
            'Compras',
            'Producción',
            'Calidad',
            'Ingeniería',
            'Mantenimiento',
        ];

        foreach ($rolesConsulta as $nombreRol) {
            $rol = Role::where('name', $nombreRol)->first();

            if ($rol) {
                $rol->givePermissionTo($verImportacion);
            }
        }
    }
}