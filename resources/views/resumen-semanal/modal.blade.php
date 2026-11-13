<dialog
    id="resumen-semanal-modal"
    aria-labelledby="resumen-semanal-titulo"
    class="w-11/12 max-w-2xl rounded-xl bg-white p-6 text-slate-900 shadow-xl"
    data-resumen-url="{{ route('resumen-semanal.show') }}"
    data-visto-url="{{ route('resumen-semanal.visto') }}">
    <h2 id="resumen-semanal-titulo" class="text-lg font-bold">Resumen semanal</h2>
    <div class="mt-2" data-resumen-contenido aria-live="polite"></div>
    <form method="dialog" class="mt-5 flex justify-end">
        <button autofocus class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
            Cerrar
        </button>
    </form>
</dialog>

<style>
    #resumen-semanal-modal::backdrop { background: rgb(2 6 23 / 65%); }
</style>

<script>
    (() => {
        const modal = document.getElementById('resumen-semanal-modal');
        const contenido = modal.querySelector('[data-resumen-contenido]');
        let solicitud;

        async function abrir(automatico = false) {
            solicitud?.abort();
            solicitud = new AbortController();
            const { signal } = solicitud;

            if (!automatico) {
                contenido.textContent = 'Cargando el resumen…';
                if (!modal.open) modal.showModal();
            }

            try {
                const url = new URL(modal.dataset.resumenUrl);
                if (automatico) url.searchParams.set('automatico', '1');

                const respuesta = await fetch(url, { signal, redirect: 'error' });
                if (!respuesta.ok) throw new Error();
                if (respuesta.status === 204) {
                    if (!automatico) contenido.textContent = 'No hay una semana operativa configurada.';
                    return;
                }

                const html = await respuesta.text();
                if (signal.aborted || (!automatico && !modal.open)) return;

                contenido.innerHTML = html;
                if (!modal.open) modal.showModal();

                const inicioSemana = respuesta.headers.get('X-Inicio-Semana');
                if (inicioSemana) {
                    const registro = await fetch(modal.dataset.vistoUrl, {
                        method: 'POST',
                        redirect: 'error',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({ inicio_semana: inicioSemana }),
                    });
                    if (!registro.ok) throw new Error();
                }
            } catch {
                if (signal.aborted || !modal.open) return;
                if (contenido.querySelector('section')) {
                    const aviso = document.createElement('p');
                    aviso.className = 'mt-3 text-sm text-red-700';
                    aviso.textContent = 'No se pudo registrar la lectura. Puedes volver a abrir el resumen para reintentar.';
                    contenido.append(aviso);
                } else {
                    contenido.textContent = 'No se pudo cargar el resumen. Vuelve a intentarlo.';
                }
            }
        }

        window.addEventListener('abrir-resumen-semanal', () => abrir());
        abrir(true);
    })();
</script>
