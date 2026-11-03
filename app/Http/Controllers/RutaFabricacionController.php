<?php

namespace App\Http\Controllers;

use App\Models\Proceso;
use App\Models\RutaFabricacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RutaFabricacionController extends Controller
{
    public function index()
    {
        $this->authorizeManagement();
        $rutas = RutaFabricacion::with('etapas.proceso')->orderByDesc('updated_at')->get();
        return view('rutas-fabricacion.index', compact('rutas'));
    }

    public function create()
    {
        $this->authorizeManagement();
        $ruta = new RutaFabricacion(['activo' => true]);
        $procesos = Proceso::where('activo', 1)->orderBy('orden')->orderBy('nombre')->get();
        return view('rutas-fabricacion.form', compact('ruta', 'procesos'));
    }

    public function store(Request $request)
    {
        $this->authorizeManagement();
        $data = $this->validated($request);
        DB::transaction(function () use ($data, &$ruta) {
            $ruta = RutaFabricacion::create([
                'nombre' => $data['nombre'],
                'descripcion' => $data['descripcion'] ?? null,
                'activo' => isset($data['activo']),
            ]);
            $this->syncEtapas($ruta, $data['etapas']);
        });

        return redirect()->route('rutas-fabricacion.index')->with('success', 'Ruta creada correctamente.');
    }

    public function edit(RutaFabricacion $ruta)
    {
        $this->authorizeManagement();
        $ruta->load('etapas.proceso');
        $procesos = Proceso::where('activo', 1)->orderBy('orden')->orderBy('nombre')->get();
        return view('rutas-fabricacion.form', compact('ruta', 'procesos'));
    }

    public function update(Request $request, RutaFabricacion $ruta)
    {
        $this->authorizeManagement();
        $data = $this->validated($request, $ruta);
        DB::transaction(function () use ($data, $ruta) {
            $ruta->update([
                'nombre' => $data['nombre'],
                'descripcion' => $data['descripcion'] ?? null,
                'activo' => isset($data['activo']),
            ]);
            $this->syncEtapas($ruta, $data['etapas']);
        });

        return redirect()->route('rutas-fabricacion.index')->with('success', 'Ruta actualizada correctamente.');
    }

    public function duplicate(RutaFabricacion $ruta)
    {
        $this->authorizeManagement();
        $ruta->load('etapas');
        DB::transaction(function () use ($ruta, &$copia) {
            $copia = RutaFabricacion::create([
                'nombre' => $ruta->nombre . ' (copia)',
                'descripcion' => $ruta->descripcion,
                'activo' => false,
            ]);
            foreach ($ruta->etapas as $etapa) {
                $copia->etapas()->create([
                    'proceso_id' => $etapa->proceso_id,
                    'orden' => $etapa->orden,
                    'obligatorio' => $etapa->obligatorio,
                ]);
            }
        });
        return redirect()->route('rutas-fabricacion.edit', $copia)->with('success', 'Ruta duplicada. Revisa el nombre y actívala cuando esté lista.');
    }

    public function toggle(RutaFabricacion $ruta)
    {
        $this->authorizeManagement();
        $ruta->update(['activo' => !$ruta->activo]);
        return redirect()->route('rutas-fabricacion.index')->with('success', 'Estado de la ruta actualizado.');
    }

    private function authorizeManagement(): void
    {
        abort_unless(auth()->check() && auth()->user()->hasAnyRole(['Supervisor', 'Administrador']), 403);
    }

    private function validated(Request $request, ?RutaFabricacion $ruta = null): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:150', Rule::unique('rutas_fabricacion', 'nombre')->ignore($ruta?->id)],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'activo' => ['nullable', 'boolean'],
            'etapas' => ['required', 'array', 'min:1'],
            'etapas.*.proceso_id' => ['required', 'integer', 'distinct', 'exists:procesos,id'],
            'etapas.*.orden' => ['required', 'integer', 'min:1'],
            'etapas.*.obligatorio' => ['nullable', 'boolean'],
        ], [
            'etapas.min' => 'Agrega al menos un proceso a la ruta.',
            'etapas.*.proceso_id.distinct' => 'No puedes agregar el mismo proceso dos veces en una ruta.',
        ]);
    }

    private function syncEtapas(RutaFabricacion $ruta, array $etapas): void
    {
        $ruta->etapas()->delete();
        foreach (array_values($etapas) as $index => $etapa) {
            $ruta->etapas()->create([
                'proceso_id' => $etapa['proceso_id'],
                'orden' => $index + 1,
                'obligatorio' => isset($etapa['obligatorio']),
            ]);
        }
    }
}
