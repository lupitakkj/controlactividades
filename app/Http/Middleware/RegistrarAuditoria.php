<?php

namespace App\Http\Middleware;

use App\Services\AuditoriaSistema;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class RegistrarAuditoria
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->attributes->set('auditoria_id', (string) Str::uuid());
        $request->attributes->set('auditoria_activa', true);
        $auditoria = app(AuditoriaSistema::class);
        $contexto = $auditoria->contexto($request);
        $estado = 500;

        try {
            $response = $next($request);
            $estado = $response->getStatusCode();

            return $response;
        } catch (Throwable $error) {
            $estado = $error instanceof ValidationException ? 422
                : (method_exists($error, 'getStatusCode') ? $error->getStatusCode() : 500);
            throw $error;
        } finally {
            // Capturar también el login (usuario posterior) y logout (usuario anterior).
            $contextoFinal = $auditoria->contexto($request);
            if ($contextoFinal['usuario_id']) {
                $contexto = $contextoFinal;
            }
            $contexto['ruta'] = $request->route()?->getName();
            if ($contexto['usuario_id']) {
                $auditoria->registrar($contexto + [
                    'accion' => $request->isMethodSafe() ? 'consultar' : 'solicitud',
                    'estado_http' => $estado,
                ]);
            }
            $request->attributes->set('auditoria_activa', false);
        }
    }
}
