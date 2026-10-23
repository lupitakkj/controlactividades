<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProveedorController extends Controller
{
    private function autorizar(): void
    {
        abort_unless(auth()->user()->hasAnyRole(['Administrador', 'Compras']), 403);
    }

    public function index(Request $request)
    {
        $this->autorizar();
        $busqueda = trim((string) $request->query('buscar', ''));
        $proveedores = Proveedor::query()
            ->when($busqueda !== '', fn ($q) => $q->where(function ($sub) use ($busqueda) {
                $sub->where('razon_social', 'like', "%{$busqueda}%")
                    ->orWhere('rfc', 'like', "%{$busqueda}%")
                    ->orWhere('clave', 'like', "%{$busqueda}%");
            }))
            ->orderBy('razon_social')->paginate(15)->withQueryString();

        return view('compras.proveedores.index', compact('proveedores', 'busqueda'));
    }

    public function store(Request $request)
    {
        $this->autorizar();
        $data = $request->validate([
            'clave' => ['nullable', 'string', 'max:40', 'unique:proveedores,clave'],
            'razon_social' => ['required', 'string', 'max:180'],
            'rfc' => ['nullable', 'string', 'max:13', 'regex:/^[A-Za-z&Ññ]{3,4}[0-9]{6}[A-Za-z0-9]{3}$/', 'unique:proveedores,rfc'],
            'contacto' => ['nullable', 'string', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:40'],
            'correo' => ['nullable', 'email', 'max:180'],
            'direccion' => ['nullable', 'string', 'max:5000'],
            'observaciones' => ['nullable', 'string', 'max:5000'],
        ], $this->mensajesValidacion());

        $data['rfc'] = isset($data['rfc']) && $data['rfc'] !== '' ? strtoupper(trim($data['rfc'])) : null;
        $data['activo'] = true;
        $data['creado_por'] = auth()->id();
        $data['actualizado_por'] = auth()->id();
        Proveedor::create($data);

        return redirect()->route('compras.proveedores.index')->with('success', 'Proveedor registrado correctamente.');
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $this->autorizar();
        $data = $request->validate([
            'clave' => ['nullable', 'string', 'max:40', Rule::unique('proveedores', 'clave')->ignore($proveedor->id)],
            'razon_social' => ['required', 'string', 'max:180'],
            'rfc' => ['nullable', 'string', 'max:13', 'regex:/^[A-Za-z&Ññ]{3,4}[0-9]{6}[A-Za-z0-9]{3}$/', Rule::unique('proveedores', 'rfc')->ignore($proveedor->id)],
            'contacto' => ['nullable', 'string', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:40'],
            'correo' => ['nullable', 'email', 'max:180'],
            'direccion' => ['nullable', 'string', 'max:5000'],
            'observaciones' => ['nullable', 'string', 'max:5000'],
        ], $this->mensajesValidacion());

        $data['rfc'] = isset($data['rfc']) && $data['rfc'] !== '' ? strtoupper(trim($data['rfc'])) : null;
        $data['actualizado_por'] = auth()->id();
        $proveedor->update($data);

        return redirect()->route('compras.proveedores.index')->with('success', 'Proveedor actualizado correctamente.');
    }

    private function mensajesValidacion(): array
    {
        return [
            'clave.unique' => 'La clave del proveedor ya está registrada. Usa otra clave.',
            'rfc.unique' => 'El RFC del proveedor ya está registrado. Busca el proveedor existente para editarlo.',
            'razon_social.required' => 'La razón social del proveedor es obligatoria.',
            'razon_social.max' => 'La razón social no debe superar los 180 caracteres.',
            'clave.max' => 'La clave no debe superar los 40 caracteres.',
            'rfc.max' => 'El RFC no debe superar los 13 caracteres.',
            'rfc.regex' => 'El formato del RFC no es válido. Verifica que esté escrito correctamente.',
            'contacto.max' => 'El contacto no debe superar los 150 caracteres.',
            'telefono.max' => 'El teléfono no debe superar los 40 caracteres.',
            'correo.email' => 'Escribe una dirección de correo electrónico válida.',
            'correo.max' => 'El correo no debe superar los 180 caracteres.',
            'direccion.max' => 'La dirección no debe superar los 5000 caracteres.',
            'observaciones.max' => 'Las observaciones no deben superar los 5000 caracteres.',
        ];
    }

    public function cambiarEstado(Proveedor $proveedor)
    {
        $this->autorizar();
        $proveedor->update(['activo' => !$proveedor->activo, 'actualizado_por' => auth()->id()]);
        return back()->with('success', $proveedor->activo ? 'Proveedor activado.' : 'Proveedor desactivado.');
    }
}
