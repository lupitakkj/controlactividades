<?php

namespace App\Services;

use App\Models\AuditoriaEvento;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditoriaSistema
{
    public function contexto(Request $request): array
    {
        $usuario = $request->user();

        return [
            'solicitud_id' => $request->attributes->get('auditoria_id'),
            'usuario_id' => $usuario?->getAuthIdentifier(),
            'usuario_nombre' => $usuario?->name,
            'ruta' => $request->route()?->getName(),
            'metodo' => $request->method(),
            // No incluir query string, cuerpos, cookies ni cabeceras de autenticación.
            'url' => $request->url(),
        ];
    }

    public function registrar(array $datos): void
    {
        DB::table('auditoria_eventos')->insert($datos + ['created_at' => now()]);
    }

    public function cambio(string $evento, Model $modelo): void
    {
        $request = request();
        if (!$request->attributes->get('auditoria_activa')
            || !str_starts_with($modelo::class, 'App\\Models\\')
            || $modelo instanceof AuditoriaEvento) {
            return;
        }

        $contexto = $this->contexto($request);
        if (!$contexto['usuario_id']) {
            return;
        }

        $antes = $evento === 'created' ? [] : $modelo->getRawOriginal();
        $despues = $evento === 'deleted' ? [] : $modelo->getAttributes();
        if ($evento === 'updated') {
            $campos = array_keys($modelo->getChanges());
            $antes = array_intersect_key($antes, array_flip($campos));
            $despues = array_intersect_key($despues, array_flip($campos));
        }
        $antes = $this->filtrar($antes, $modelo);
        $despues = $this->filtrar($despues, $modelo);
        if ($evento === 'updated' && $antes === $despues) {
            return;
        }

        $datos = $contexto + [
            'accion' => ['created' => 'crear', 'updated' => 'modificar', 'deleted' => 'eliminar'][$evento],
            'modelo' => $modelo::class,
            'registro_id' => (string) $modelo->getKey(),
            'antes' => $antes ? json_encode($antes, JSON_THROW_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE) : null,
            'despues' => $despues ? json_encode($despues, JSON_THROW_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE) : null,
        ];

        // Un rollback descarta el evento; solo registrar modificaciones confirmadas.
        $modelo->getConnection()->afterCommit(fn () => $this->registrar($datos));
    }

    private function filtrar(array $datos, Model $modelo): array
    {
        return array_filter($datos, fn ($campo) =>
            !in_array($campo, array_merge($modelo->getHidden(), ['updated_at']), true)
            && !preg_match('/password|token|secret|credential|api_key|contrasena|contraseña/i', $campo),
            ARRAY_FILTER_USE_KEY);
    }
}
