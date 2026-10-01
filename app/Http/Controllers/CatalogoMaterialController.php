<?php

namespace App\Http\Controllers;

use App\Models\CatalogoMaterial;
use App\Models\FamiliaMaterial;
use App\Models\Partida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CatalogoMaterialController extends Controller
{
    /**
     * Mostrar catálogo de materiales.
     */
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Obtener todas las familias activas
        |--------------------------------------------------------------------------
        */

        $familias = FamiliaMaterial::where('activo', true)
            ->orderBy('nombre')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | 2. Obtener claves existentes en partidas
        |--------------------------------------------------------------------------
        |
        | Las agrupamos por clave para no mostrar repetida una misma clave
        | aunque aparezca en muchos pedidos.
        |
        */

        $clavesPartidas = Partida::query()
            ->select('clave')
            ->selectRaw('MAX(descripcion) as descripcion')
            ->whereNotNull('clave')
            ->where('clave', '<>', '')
            ->groupBy('clave')
            ->orderBy('clave')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | 3. Obtener claves que ya están clasificadas
        |--------------------------------------------------------------------------
        */

        $catalogo = CatalogoMaterial::with('familia')
            ->orderBy('clave')
            ->get()
            ->keyBy('clave');


        /*
        |--------------------------------------------------------------------------
        | 4. Separar claves sin clasificar
        |--------------------------------------------------------------------------
        */

        $clavesSinClasificar = $clavesPartidas
            ->filter(function ($partida) use ($catalogo) {

                return !$catalogo->has($partida->clave);
            })
            ->values();


        /*
        |--------------------------------------------------------------------------
        | 5. Vista
        |--------------------------------------------------------------------------
        */

        return view(
            'catalogo-materiales.index',
            compact(
                'familias',
                'catalogo',
                'clavesSinClasificar'
            )
        );
    }


    /**
 * Guardar clasificación de una clave.
 */
public function guardar(Request $request)
{
    /*
    |--------------------------------------------------------------------------
    | Validación manual
    |--------------------------------------------------------------------------
    */

    $validator = Validator::make(
        $request->all(),
        [
            'clave' => [
                'required',
                'string',
                'max:100',
            ],

            'familia_material_id' => [
                'required',
                'integer',
                'exists:familias_materiales,id',
            ],
        ]
    );


    /*
    |--------------------------------------------------------------------------
    | Si hay error de validación
    |--------------------------------------------------------------------------
    */

    if ($validator->fails()) {

        return response()->json([
            'success' => false,
            'message' => 'Los datos enviados no son válidos.',
            'errors' => $validator->errors(),
            'datos_recibidos' => [
                'clave' => $request->input('clave'),
                'familia_material_id' => $request->input('familia_material_id'),
            ],
        ], 422);
    }


    /*
    |--------------------------------------------------------------------------
    | Obtener datos validados
    |--------------------------------------------------------------------------
    */

    $validated = $validator->validated();


    /*
    |--------------------------------------------------------------------------
    | Obtener descripción desde partidas
    |--------------------------------------------------------------------------
    */

    $descripcion = Partida::where(
        'clave',
        $validated['clave']
    )->value('descripcion');


    /*
    |--------------------------------------------------------------------------
    | Crear o actualizar clasificación
    |--------------------------------------------------------------------------
    */

    $catalogo = CatalogoMaterial::updateOrCreate(
        [
            'clave' => $validated['clave'],
        ],
        [
            'descripcion' => $descripcion,
            'familia_material_id' => $validated['familia_material_id'],
            'activo' => true,
        ]
    );


    /*
    |--------------------------------------------------------------------------
    | Respuesta AJAX
    |--------------------------------------------------------------------------
    */

    return response()->json([
        'success' => true,
        'message' => 'La clave fue clasificada correctamente.',
        'catalogo' => [
            'id' => $catalogo->id,
            'clave' => $catalogo->clave,
            'familia_material_id' => $catalogo->familia_material_id,
        ],
    ]);
}

    /**
     * Actualizar una clasificación existente.
     */
    public function actualizar(Request $request, CatalogoMaterial $catalogoMaterial)
    {
        /*
        |--------------------------------------------------------------------------
        | Validación
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'familia_material_id' => [
                'required',
                'integer',
                'exists:familias_materiales,id',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Actualizar
        |--------------------------------------------------------------------------
        */

        $catalogoMaterial->update([
            'familia_material_id' =>
            $validated['familia_material_id'],
        ]);


        return redirect()
            ->route('catalogo-materiales.index')
            ->with(
                'success',
                'La familia del material fue actualizada.'
            );
    }
}
