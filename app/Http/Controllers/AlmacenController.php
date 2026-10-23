<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AlmacenController extends Controller
{
    private function autorizar(): void
    {
        abort_unless(auth()->user()->hasAnyRole(['Administrador', 'Compras', 'Almacén']), 403);
    }

    public function index(Request $request)
    {
        $this->autorizar();
        $busqueda = trim((string) $request->query('buscar', ''));
        $almacenes = Almacen::query()
            ->when($busqueda !== '', fn ($q) => $q->where(function ($sub) use ($busqueda) {
                $sub->where('nombre', 'like', "%{$busqueda}%")
                    ->orWhere('clave', 'like', "%{$busqueda}%")
                    ->orWhere('ubicacion', 'like', "%{$busqueda}%");
            }))
            ->orderBy('nombre')->paginate(15)->withQueryString();

        return view('compras.almacenes.index', compact('almacenes', 'busqueda'));
    }

    public function store(Request $request)
    {
        $this->autorizar();
        $data = $request->validate([
            'clave' => ['required', 'string', 'max:40', 'unique:almacenes,clave'],
            'nombre' => ['required', 'string', 'max:150'],
            'ubicacion' => ['nullable', 'string', 'max:255'],
            'responsable' => ['nullable', 'string', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:40'],
            'observaciones' => ['nullable', 'string', 'max:5000'],
        ]);
        $data['activo'] = true;
        $data['creado_por'] = auth()->id();
        $data['actualizado_por'] = auth()->id();
        Almacen::create($data);
        return redirect()->route('compras.almacenes.index')->with('success', 'Almacén registrado correctamente.');
    }

    public function update(Request $request, Almacen $almacen)
    {
        $this->autorizar();
        $data = $request->validate([
            'clave' => ['required', 'string', 'max:40', Rule::unique('almacenes', 'clave')->ignore($almacen->id)],
            'nombre' => ['required', 'string', 'max:150'],
            'ubicacion' => ['nullable', 'string', 'max:255'],
            'responsable' => ['nullable', 'string', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:40'],
            'observaciones' => ['nullable', 'string', 'max:5000'],
        ]);
        $data['actualizado_por'] = auth()->id();
        $almacen->update($data);
        return redirect()->route('compras.almacenes.index')->with('success', 'Almacén actualizado correctamente.');
    }

    public function cambiarEstado(Almacen $almacen)
    {
        $this->autorizar();
        $almacen->update(['activo' => !$almacen->activo, 'actualizado_por' => auth()->id()]);
        return back()->with('success', $almacen->activo ? 'Almacén activado.' : 'Almacén desactivado.');
    }
}
