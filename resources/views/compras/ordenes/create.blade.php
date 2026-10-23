<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="rounded-xl bg-slate-800 border border-slate-700 p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-white"><i class="fa-solid fa-file-invoice-dollar mr-2"></i>Nueva orden de compra</h1>
                    <p class="mt-1 text-sm text-slate-300">Carga el PDF y captura o corrige los renglones antes de guardar.</p>
                </div>
                <a href="{{ route('compras.ordenes.index') }}" class="inline-flex justify-center rounded-lg border border-slate-500 px-4 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-700">Volver</a>
            </div>
            @if(session('success'))<div class="rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-green-800">{{ session('success') }}</div>@endif
            @if($errors->any())<div class="rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-red-800"><strong>Revisa los siguientes datos:</strong>
                <ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>@endif
            <form method="POST"
                action="{{ route('compras.ordenes.store') }}"
                id="formOrden"
                class="space-y-6">
                @csrf
                <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                        <h2 class="font-semibold text-slate-800">Datos generales</h2>
                    </div>
                    <div class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-2 lg:grid-cols-4">
                        <label class="text-sm font-medium text-slate-700">Folio de orden *<input name="folio" value="{{ old('folio') }}" required maxlength="80" class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"></label>
                        <label class="text-sm font-medium text-slate-700 sm:col-span-2">Proveedor *<select id="proveedor_id" name="proveedor_id" required class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Selecciona proveedor</option>@foreach($proveedores as $p)<option value="{{ $p->id }}" @selected(old('proveedor_id')==$p->id)>{{ $p->razon_social }}{{ $p->rfc ? ' — '.$p->rfc : '' }}</option>@endforeach
                            </select><span class="mt-1 block text-xs text-slate-500">Si no existe, regístralo primero en Proveedores.</span></label>
                        <label class="text-sm font-medium text-slate-700">Fecha<input type="date" name="fecha_orden" value="{{ old('fecha_orden') }}" class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"></label>
                        <label class="text-sm font-medium text-slate-700">Moneda<select name="moneda" class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="MXN" @selected(old('moneda','MXN')==='MXN' )>MXN</option>
                                <option value="USD" @selected(old('moneda')==='USD' )>USD</option>
                            </select></label>
                        <label class="text-sm font-medium text-slate-700 sm:col-span-2">PDF de la orden (máx. 10 MB)<input id="pdfOrden" type="file" name="pdf" accept="application/pdf" class="mt-1 block w-full rounded-lg border border-slate-300 text-sm text-slate-700 file:mr-3 file:rounded-l-lg file:border-0 file:bg-slate-100 file:px-3 file:py-2">
                            <button type="button" id="leerPdf" class="mt-2 inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700"><i class="fa-solid fa-file-arrow-up mr-2"></i>Leer datos del PDF</button>
                            <span class="mt-1 block text-xs text-slate-500">Selecciona el PDF y pulsa “Leer datos del PDF”. Revisa los campos antes de guardar.</span></label>
                        <label class="text-sm font-medium text-slate-700">IVA total<input type="number" min="0" step="0.01" name="iva" value="{{ old('iva',0) }}" class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"></label>
                        <label class="text-sm font-medium text-slate-700 sm:col-span-2 lg:col-span-4">Observaciones<textarea name="observaciones" rows="2" class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('observaciones') }}</textarea></label>
                    </div>
                </section>
                <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex flex-col gap-3 border-b border-slate-200 bg-slate-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <h2 class="font-semibold text-slate-800">Productos solicitados</h2><button type="button" id="agregarRenglon" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"> <i class="fa-solid fa-plus mr-1"></i>Agregar producto</button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm" id="tablaDetalle">
                            <thead class="bg-slate-100 text-slate-700">
                                <tr>
                                    <th class="px-3 py-3 text-left">Clave</th>
                                    <th class="px-3 py-3 text-left">Descripción *</th>
                                    <th class="px-3 py-3 text-left">Observaciones del producto</th>
                                    <th class="px-3 py-3 text-left">Unidad</th>
                                    <th class="px-3 py-3 text-left">Cantidad *</th>
                                    <th class="px-3 py-3 text-left">Precio unitario *</th>
                                    <th class="px-3 py-3 text-right">Importe</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="renglones" class="divide-y divide-slate-200"></tbody>
                            <tfoot class="bg-slate-50">
                                <tr>
                                    <th colspan="6" class="px-3 py-3 text-right text-slate-700">Subtotal calculado</th>
                                    <th id="subtotalVista" class="px-3 py-3 text-right font-semibold text-slate-900">$0.00</th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th colspan="6" class="px-3 py-3 text-right text-slate-700">Total con IVA</th>
                                    <th id="totalVista" class="px-3 py-3 text-right font-bold text-slate-900">$0.00</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </section>
                <div class="flex flex-wrap gap-3"><button type="submit" class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700"><i class="fa-solid fa-floppy-disk mr-2"></i>Guardar orden</button><a href="{{ route('compras.ordenes.index') }}" class="rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancelar</a></div>
            </form>
        </div>
    </div>
    <script>
        (function() {
            const body = document.getElementById('renglones');
            const pdfInput = document.getElementById('pdfOrden');
            const btnLeer = document.getElementById('leerPdf');
            let idx = 0;
            const currency = () => document.querySelector('[name=moneda]').value || 'MXN';
            const money = n => new Intl.NumberFormat('es-MX', {
                style: 'currency',
                currency: currency()
            }).format(n || 0);

            function addRow(data = {}) {
                const tr = document.createElement('tr');
                tr.innerHTML = `<td class="p-2"><input class="w-32 rounded-lg border-slate-300 text-sm" name="detalles[${idx}][clave_material]" maxlength="100"></td>
        <td class="p-2"><input class="w-64 rounded-lg border-slate-300 text-sm" name="detalles[${idx}][descripcion]" required maxlength="500"></td>
        <td class="p-2"><textarea class="w-72 rounded-lg border-slate-300 text-sm" name="detalles[${idx}][observaciones]" rows="2" maxlength="1000" placeholder="Especificaciones o instrucciones del producto"></textarea></td>
        <td class="p-2"><input class="w-24 rounded-lg border-slate-300 text-sm" name="detalles[${idx}][unidad]" maxlength="40"></td>
        <td class="p-2"><input class="qty w-32 rounded-lg border-slate-300 text-sm" type="number" min="0.001" step="0.001" name="detalles[${idx}][cantidad_solicitada]" required></td>
        <td class="p-2"><input class="price w-36 rounded-lg border-slate-300 text-sm" type="number" min="0" step="0.000001" name="detalles[${idx}][precio_unitario]" required></td>
        <td class="importe whitespace-nowrap p-2 text-right font-medium text-slate-800">$0.00</td>
        <td class="p-2 text-center"><button type="button" class="quitar rounded-md px-2 py-1 font-bold text-red-600 hover:bg-red-50" aria-label="Quitar renglón">×</button></td>`;
                body.appendChild(tr);
                ['clave_material', 'descripcion', 'observaciones', 'unidad', 'cantidad_solicitada', 'precio_unitario'].forEach(k => {
                    if (data[k] !== undefined && data[k] !== null) tr.querySelector(`[name="detalles[${idx}][${k}]"]`).value = data[k];
                });
                idx++;
                tr.querySelector('.quitar').onclick = () => {
                    if (body.children.length > 1) {
                        tr.remove();
                        calc();
                    }
                };
                tr.querySelectorAll('input').forEach(el => el.addEventListener('input', calc));
                calc();
            }

            function calc() {
                let subtotal = 0;
                body.querySelectorAll('tr').forEach(tr => {
                    const q = parseFloat(tr.querySelector('.qty').value) || 0;
                    const p = parseFloat(tr.querySelector('.price').value) || 0;
                    const importe = q * p;
                    subtotal += importe;
                    tr.querySelector('.importe').textContent = money(importe);
                });
                document.getElementById('subtotalVista').textContent = money(subtotal);
                document.getElementById('totalVista').textContent = money(subtotal + (parseFloat(document.querySelector('[name=iva]').value) || 0));
            }

            function setValue(selector, value) {
                const el = document.querySelector(selector);
                if (el && value !== null && value !== undefined && value !== '') el.value = value;
            }

            // Lee el PDF dentro del navegador. No envía el archivo al servidor ni llama a /compras/ordenes/leer-pdf.
            let pdfJsPromise = null;
            async function cargarPdfJs() {
                if (!pdfJsPromise) {
                    pdfJsPromise = import('https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.4.168/pdf.min.mjs')
                        .then(pdfjsLib => {
                            pdfjsLib.GlobalWorkerOptions.workerSrc =
                                'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.4.168/pdf.worker.min.mjs';
                            return pdfjsLib;
                        });
                }
                return pdfJsPromise;
            }

            function normalizarTexto(valor) {
                return String(valor || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                    .replace(/\s+/g, ' ').trim();
            }

            function importeDesdeTexto(valor) {
                if (valor === null || valor === undefined) return null;
                const limpio = String(valor).replace(/[$\s]/g, '').replace(/,/g, '');
                const numero = Number(limpio);
                return Number.isFinite(numero) ? numero : null;
            }

            async function extraerTextoPdf(file) {
                const pdfjsLib = await cargarPdfJs();
                const bytes = await file.arrayBuffer();
                const pdf = await pdfjsLib.getDocument({
                    data: bytes
                }).promise;
                const paginas = [];

                for (let numeroPagina = 1; numeroPagina <= pdf.numPages; numeroPagina++) {
                    const pagina = await pdf.getPage(numeroPagina);
                    const contenido = await pagina.getTextContent();
                    const items = contenido.items
                        .filter(item => typeof item.str === 'string' && item.str.trim() !== '')
                        .map(item => ({
                            texto: item.str.trim(),
                            x: item.transform[4],
                            y: item.transform[5]
                        }))
                        .sort((a, b) => Math.abs(b.y - a.y) > 2 ? b.y - a.y : a.x - b.x);

                    const lineas = [];
                    for (const item of items) {
                        let linea = lineas.find(l => Math.abs(l.y - item.y) <= 2);
                        if (!linea) {
                            linea = {
                                y: item.y,
                                items: []
                            };
                            lineas.push(linea);
                        }
                        linea.items.push(item);
                    }

                    lineas.sort((a, b) => b.y - a.y);
                    paginas.push(lineas.map(l => l.items.sort((a, b) => a.x - b.x).map(i => i.texto).join(' ')).join('\n'));
                }
                return paginas.join('\n');
            }

            function detectarFecha(texto) {
                const m = texto.match(/Fecha\s*:\s*([0-3]?\d)[\/.-]([01]?\d)[\/.-](\d{4})/i);
                if (!m) return '';
                const dia = m[1].padStart(2, '0');
                const mes = m[2].padStart(2, '0');
                return `${m[3]}-${mes}-${dia}`;
            }

            function detectarProveedor(texto) {
                const rfcCoincidencias = [...texto.matchAll(/RFC\s*:\s*([A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3})/gi)];
                const rfc = rfcCoincidencias.length ? rfcCoincidencias[rfcCoincidencias.length - 1][1].toUpperCase() : '';
                let nombre = '';
                if (rfc) {
                    const escaped = rfc.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                    const m = texto.match(new RegExp('([A-ZÁÉÍÓÚÑ][A-ZÁÉÍÓÚÑ .,&-]{4,})\\s*\\(\\s*' + escaped + '\\s*\\)', 'i'));
                    if (m) nombre = m[1].trim();
                }
                if (!nombre) {
                    const m = texto.match(/RIJAYA\s+COMERCIAL\s+(.+?)\s+Fecha\s*:/is);
                    if (m) nombre = m[1].replace(/\s+/g, ' ').replace(/\s*\([^)]*\)\s*$/, '').trim();
                }
                return {
                    rfc,
                    nombre
                };
            }

            function detectarImportes(texto, detalles) {
                const numero = '(?:\\d{1,3}(?:,\\d{3})+|\\d+)\\.\\d{2}';
                const limpiar = valor => Number(String(valor).replace(/,/g, ''));
                let subtotal = detalles.length ?
                    Math.round(detalles.reduce((s, d) => s + d.cantidad_solicitada * d.precio_unitario, 0) * 100) / 100 :
                    null;
                let iva = null,
                    total = null;
                let m = texto.match(new RegExp('\\bSubtotal\\b\\s*:?\\s*\\$?\\s*(' + numero + ')', 'i'));
                if (m) subtotal = limpiar(m[1]);
                m = texto.match(new RegExp('\\bI\\s*\\.?\\s*V\\s*\\.?\\s*A\\s*\\.?\\s*:?\\s*\\$?\\s*(' + numero + ')', 'i'));
                if (m) iva = limpiar(m[1]);
                m = texto.match(new RegExp('\\bTotal\\b\\s*:?\\s*\\$?\\s*(' + numero + ')', 'i'));
                if (m) total = limpiar(m[1]);

                const montos = [...texto.matchAll(/(?<![\d.])(?:\d{1,3}(?:,\d{3})+|\d+)\.\d{2}(?!\d)/g)].map(m => limpiar(m[0]));
                if (subtotal !== null && total === null && montos.length) {
                    const candidato = montos[montos.length - 1];
                    if (candidato >= subtotal) total = candidato;
                }
                if (iva === null && subtotal !== null && total !== null && total >= subtotal) iva = Math.round((total - subtotal) * 100) / 100;
                return {
                    subtotal,
                    iva,
                    total
                };
            }

            function detectarDetalles(texto) {
                const detalles = [];
                const lineas = texto.split(/\r?\n/).map(l => l.replace(/\s+/g, ' ').trim()).filter(Boolean);
                const importeRegex = '(?:\\d{1,3}(?:,\\d{3})+|\\d+)\\.\\d{2}';
                const patron = new RegExp('^(\\d+(?:\\.\\d+)?)\\s*([A-Z][A-Z0-9._-]{3,})\\s+(.+?)\\s+(\\d+\\.\\d{2})\\s+(\\d+\\.\\d{3,8})\\s+(' + importeRegex + ')\\s*$', 'i');

                for (let i = 0; i < lineas.length; i++) {
                    const m = lineas[i].match(patron);
                    if (!m) continue;
                    const observaciones = [];
                    for (let j = i + 1; j < lineas.length; j++) {
                        const siguiente = lineas[j];
                        if (patron.test(siguiente)) break;
                        if (/^(Subtotal|Descuento|Desc\. Fin\.|I\.?E\.?P\.?S\.?|I\.?V\.?A\.?|Total|Solicita|Elabor[oó]|Autoriza)\b/i.test(siguiente)) break;
                        if (/^(Cantidad|Producto|Descripci[oó]n|%?\s*Desc\.?|Costo unitario|Importe)$/i.test(siguiente)) continue;
                        observaciones.push(siguiente);
                    }
                    detalles.push({
                        clave_material: m[2].trim(),
                        descripcion: m[3].trim().slice(0, 500),
                        unidad: '',
                        cantidad_solicitada: Number(m[1]),
                        precio_unitario: Number(m[5].replace(/,/g, '')),
                        observaciones: observaciones.join(' ').slice(0, 1000)
                    });
                }

                // Respaldo cuando el extractor distribuye las columnas en bloques separados.
                if (!detalles.length) {
                    const plano = texto.replace(/\s+/g, ' ');
                    const patronPlano = new RegExp('(\\d+(?:\\.\\d+)?)\\s*([A-Z][A-Z0-9._-]{3,})\\s+(.+?)\\s+(\\d+\\.\\d{2})\\s+(\\d+\\.\\d{3,8})\\s+(' + importeRegex + ')(?=\\s+\\d+(?:\\.\\d+)?\\s+[A-Z][A-Z0-9._-]{3,}\\s+|\\s+Subtotal\\b)', 'gi');
                    for (const m of plano.matchAll(patronPlano)) {
                        detalles.push({
                            clave_material: m[2].trim(),
                            descripcion: m[3].trim().slice(0, 500),
                            unidad: '',
                            cantidad_solicitada: Number(m[1]),
                            precio_unitario: Number(m[5].replace(/,/g, '')),
                            observaciones: ''
                        });
                    }
                }
                return detalles;
            }

            btnLeer.addEventListener('click', async () => {
                const file = pdfInput.files[0];
                if (!file) {
                    alert('Primero selecciona el PDF de la orden de compra.');
                    return;
                }
                if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
                    alert('Selecciona un archivo PDF válido.');
                    return;
                }

                const originalText = btnLeer.innerHTML;
                btnLeer.disabled = true;
                btnLeer.textContent = 'Leyendo PDF en el navegador...';

                try {
                    const texto = await extraerTextoPdf(file);
                    if (!texto.trim()) {
                        throw new Error('El PDF no contiene texto seleccionable. Si es un escaneo, necesitará OCR; puedes capturar los datos manualmente.');
                    }

                    const folioMatch = texto.match(/ORDEN\s*No\.?\s*:?\s*([0-9]{4,})/i);
                    const folio = folioMatch ? folioMatch[1].trim() : '';
                    const fecha = detectarFecha(texto);
                    const proveedor = detectarProveedor(texto);
                    const detalles = detectarDetalles(texto);
                    const importes = detectarImportes(texto, detalles);

                    setValue('[name="folio"]', folio);
                    setValue('[name="fecha_orden"]', fecha);
                    setValue('[name="moneda"]', /\bUSD\b|US\s*\$|DOLARES?|\bDLS\b/i.test(texto) ? 'USD' : 'MXN');
                    if (importes.iva !== null) setValue('[name="iva"]', importes.iva);

                    if (proveedor.rfc || proveedor.nombre) {
                        const opciones = [...document.getElementById('proveedor_id').options];
                        const normalizar = v => normalizarTexto(v).toLowerCase();
                        const opcion = opciones.find(o => o.value && proveedor.rfc && normalizar(o.text).includes(normalizar(proveedor.rfc))) ||
                            opciones.find(o => o.value && proveedor.nombre && normalizar(o.text).includes(normalizar(proveedor.nombre)));
                        if (opcion) {
                            document.getElementById('proveedor_id').value = opcion.value;
                        } else {
                            alert('Proveedor detectado: ' + (proveedor.nombre || 'nombre no detectado') +
                                (proveedor.rfc ? '\nRFC: ' + proveedor.rfc : '') +
                                '\nNo se encontró coincidencia en el catálogo. Selecciónalo manualmente.');
                        }
                    }

                    if (detalles.length) {
                        body.innerHTML = '';
                        idx = 0;
                        detalles.forEach(detalle => addRow(detalle));
                    }
                    calc();

                    const mensajes = ['PDF leído localmente en el navegador; no se envió a la ruta que devolvía 403.'];
                    if (!folio) mensajes.push('No se detectó el folio; revísalo manualmente.');
                    if (!fecha) mensajes.push('No se detectó la fecha; revísala manualmente.');
                    if (!proveedor.nombre && !proveedor.rfc) mensajes.push('No se detectó el proveedor con seguridad; selecciónalo manualmente.');
                    if (!detalles.length) mensajes.push('No se reconocieron productos con suficiente certeza. Captúralos manualmente.');
                    else mensajes.push('Se detectaron ' + detalles.length + ' renglón(es). Revisa clave, descripción, cantidad y precio unitario.');
                    if (importes.subtotal !== null) mensajes.push('Subtotal detectado/calculado: ' + money(importes.subtotal));
                    if (importes.iva !== null) mensajes.push('IVA detectado/calculado: ' + money(importes.iva));
                    else mensajes.push('No se pudo identificar el IVA con seguridad; captúralo manualmente.');
                    if (importes.total !== null) mensajes.push('Total impreso detectado: ' + money(importes.total) + '. Compáralo con el total calculado del formulario.');
                    alert(mensajes.join('\n\n') + '\n\nRevisa todos los campos antes de guardar.');
                } catch (error) {
                    console.error('Error al leer PDF localmente:', error);
                    alert(error.message || 'No fue posible leer el PDF en el navegador.');
                } finally {
                    btnLeer.disabled = false;
                    btnLeer.innerHTML = originalText;
                }
            });

            // Evita reenviar el PDF al endpoint de guardado: solo se envía a leerPdf cuando el usuario pulsa el botón.
            document.getElementById('formOrden').addEventListener('submit', () => {
                pdfInput.removeAttribute('name');
            });

            document.getElementById('agregarRenglon').onclick = () => addRow();
            document.querySelector('[name=iva]').addEventListener('input', calc);
            document.querySelector('[name=moneda]').addEventListener('change', calc);
            addRow();
        })();
    </script>
</x-app-layout>