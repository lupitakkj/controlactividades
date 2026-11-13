<?php

namespace App\Services;

use App\Models\Pieza;
use App\Models\Hoja;
use Illuminate\Support\Facades\Log;

class CutListOptimizer
{
    /**
     * Ejecutar la optimización.
     */
    public function optimizar(
        $piezas,
        $hojas,
        array $configuracion
    ) {
        // Valores predeterminados de los ajustes del optimizador.
        // La vista/controlador puede sobrescribirlos enviando estos campos.
        $configuracion = array_merge([
            'prioridad_optimizacion' => 'menor_cantidad_tableros',
            'dimension_minima_retal_cm' => 20.0,
            'saneado_bordes_cm' => 0.0,
        ], $configuracion);

        $configuracion['prioridad_optimizacion'] = in_array(
            $configuracion['prioridad_optimizacion'],
            ['menor_cantidad_tableros', 'menor_desperdicio', 'mejores_retales'],
            true
        ) ? $configuracion['prioridad_optimizacion'] : 'menor_cantidad_tableros';

        $configuracion['dimension_minima_retal_cm'] = max(
            0.0,
            (float) $configuracion['dimension_minima_retal_cm']
        );

        $configuracion['saneado_bordes_cm'] = max(
            0.0,
            (float) $configuracion['saneado_bordes_cm']
        );

        // El saneado acordado actualmente es 0 cm. No se altera la geometría
        // de los tableros; valores distintos de cero requieren conectar el
        // margen útil también con la vista y el validador de cortes.
        if ($configuracion['saneado_bordes_cm'] > 0) {
            throw new \InvalidArgumentException(
                'El saneado de bordes distinto de 0 cm aún no está implementado en la geometría del optimizador.'
            );
        }

        // ==========================================
        // PREPARAR PIEZAS
        // ==========================================

        $piezasPreparadas =
            $this->prepararPiezas($piezas);


        // ==========================================
        // GENERAR ORIENTACIONES
        // ==========================================

        foreach ($piezasPreparadas as &$pieza) {

            $pieza['orientaciones'] =
                $this->obtenerOrientaciones(
                    $pieza,
                    $configuracion
                );
        }

        unset($pieza);

        // ==========================================
        // AGRUPAR POR DIMENSIÓN
        // ==========================================

        $gruposDimension =
            $this->agruparPorDimension(
                $piezasPreparadas
            );

        Log::info(
            '=== GRUPOS POR DIMENSION ==='
        );

        Log::info(
            $gruposDimension
        );

        // ==========================================
        // EXPANDIR HOJAS SEGÚN CANTIDAD
        // ==========================================

        $hojasDisponibles =
            $this->prepararHojas($hojas);

        // ==========================================
        // GENERAR FRANJAS
        // ==========================================


        $franjas =
            $this->generarFranjas(
                $gruposDimension,
                $hojasDisponibles,
                $configuracion
            );

        Log::info('=== FRANJAS GENERADAS ===');

        Log::info($franjas);

        // ==========================================
        // PROBAR FRANJAS EN UNA HOJA
        // ==========================================

        $pruebaFranjas = [];

        foreach ($hojasDisponibles as $hoja) {

            $resultadoHoja =
                $this->colocarFranjasEnHoja(
                    $franjas,
                    $hoja,
                    $configuracion
                );

            $pruebaFranjas[] =
                $resultadoHoja;
        }

        foreach ($pruebaFranjas as &$resultadoHoja) {

            $hojaOriginal = null;

            foreach ($hojasDisponibles as $hoja) {

                if ((int) $hoja['id'] === (int) $resultadoHoja['hoja_id']) {

                    $hojaOriginal = $hoja;
                    break;
                }
            }

            $resultadoHoja['estadisticas_cortes'] =
                $this->calcularEstadisticasCortes(
                    $resultadoHoja
                );

            $resultadoHoja['arbol_cortes'] =
                $this->construirArbolCortes($resultadoHoja);
        }

        unset($resultadoHoja);

        Log::info(
            '=== FRANJAS COLOCADAS EN HOJAS ==='
        );

        Log::info(
            $pruebaFranjas
        );



        // ==========================================
        // COLOCAR PIEZAS
        // ==========================================

        $resultadoColocacion =
            $this->colocarPiezas(
                $piezasPreparadas,
                $hojasDisponibles,
                $configuracion
            );
        $bloquesRecursivos = [];

        foreach ($hojasDisponibles as $indice => $hoja) {

            $colocadasHoja = array_filter(
                $resultadoColocacion['colocadas'],
                function ($pieza) use ($hoja) {
                    return
                        $pieza['hoja_id'] == $hoja['id'] &&
                        $pieza['hoja_numero'] == $hoja['numero'];
                }
            );

            $colocadasHoja = array_values($colocadasHoja);

            if (count($colocadasHoja) === 0) {
                continue;
            }

            $bloquesRecursivos[] = [
                'hoja_id' => $hoja['id'],
                'hoja_numero' => $hoja['numero'],
                'arbol' => $this->detectarBloquesRecursivos(
                    $colocadasHoja,
                    0,
                    0,
                    $hoja['largo'],
                    $hoja['ancho'],
                    (float) $configuracion['kerf']
                ),
            ];
        }

        Log::info('=== BLOQUES RECURSIVOS ===');
        Log::info($bloquesRecursivos);

        // Convertir el árbol de cada hoja en una secuencia de cortes.
        // La secuencia es ejecutable solo cuando todos los bloques terminales
        // son piezas o retales; los bloques sin división se marcan para revisión.
        $listaCortes = $this->generarListaCortesOrdenada($bloquesRecursivos);

        $analisisCortes = $this->analizarCortesDesdeColocacion(
            $resultadoColocacion,
            $hojasDisponibles,
            $configuracion
        );

        Log::info('=== ANALISIS DE CORTES DESDE COLOCACION ===');
        Log::info($analisisCortes);
        $erroresColocacion =
            $this->validarColocacion(
                $resultadoColocacion['colocadas'],
                $hojasDisponibles,
                $configuracion['kerf']
            );

        // ==========================================
        // CALCULAR ESTADÍSTICAS
        // ==========================================

        $estadisticas =
            $this->calcularEstadisticas(
                $resultadoColocacion['colocadas'],
                $hojasDisponibles,
                $resultadoColocacion['sobrantes'] ?? [],
                (float) $configuracion['dimension_minima_retal_cm']
            );

        return [

            'configuracion' =>
            $configuracion,

            'ajustes_optimizacion' => [
                'prioridad' => $configuracion['prioridad_optimizacion'],
                'dimension_minima_retal_cm' => $configuracion['dimension_minima_retal_cm'],
                'saneado_bordes_cm' => $configuracion['saneado_bordes_cm'],
            ],

            'piezas' =>
            $piezasPreparadas,

            'hojas' =>
            $hojasDisponibles,

            'colocacion' =>
            $resultadoColocacion,

            'validacion' => [

                'correcta' =>
                count($erroresColocacion) === 0,

                'errores' =>
                $erroresColocacion,

            ],

            'estadisticas' =>
            $estadisticas,

            'grupos_dimension' =>
            $gruposDimension,

            'franjas' =>
            $franjas,


            'prueba_franjas' =>
            $pruebaFranjas,

            'analisis_cortes' => $analisisCortes,

            'bloques_recursivos' => $bloquesRecursivos,

            // Lista ordenada de cortes por hoja, derivada del árbol guillotina.
            'lista_cortes' => $listaCortes,
        ];
    }


    /**
     * Convertir cantidades en piezas individuales.
     */
    private function prepararPiezas($piezas)
    {
        $resultado = [];

        foreach ($piezas as $pieza) {

            for (
                $i = 1;
                $i <= $pieza->cantidad;
                $i++
            ) {

                $resultado[] = [
                    'id' => $pieza->id,

                    'numero' => $i,

                    'largo' => (float) $pieza->largo,

                    'ancho' => (float) $pieza->ancho,

                    'material_id' =>
                    $pieza->material_id,

                    'material' =>
                    $pieza->material
                        ? $pieza->material->nombre
                        : null,

                    'etiqueta' =>
                    $pieza->etiqueta,

                    'direccion_grano' =>
                    $pieza->direccion_grano,

                    'permitir_rotacion' =>
                    (bool) $pieza->permitir_rotacion,
                ];
            }
        }

        return $resultado;
    }

    private function prepararHojas($hojas)
    {
        $resultado = [];

        foreach ($hojas as $hoja) {

            for (
                $i = 1;
                $i <= $hoja->cantidad;
                $i++
            ) {

                $largoOriginal = (float) $hoja->largo;
                $anchoOriginal = (float) $hoja->ancho;

                /*
             * Normalizamos la orientación de la hoja:
             *
             * El lado más largo será LARGO.
             * El lado más corto será ANCHO.
             *
             * Ejemplo:
             *
             * 1220 x 3050
             *
             * se convierte en:
             *
             * 3050 x 1220
             *
             * Esto permite aprovechar correctamente
             * la dimensión larga de la hoja.
             */

                $largo = max(
                    $largoOriginal,
                    $anchoOriginal
                );

                $ancho = min(
                    $largoOriginal,
                    $anchoOriginal
                );

                $resultado[] = [

                    'id' =>
                    $hoja->id,

                    'numero' =>
                    $i,

                    'largo' =>
                    $largo,

                    'ancho' =>
                    $ancho,

                    'material_id' =>
                    $hoja->material_id,

                    'material' =>
                    $hoja->material
                        ? $hoja->material->nombre
                        : null,

                    'etiqueta' =>
                    $hoja->etiqueta,

                ];
            }
        }

        return $resultado;
    }

    private function obtenerOrientaciones(
        array $pieza,
        array $configuracion
    ) {
        $orientaciones = [];


        // ==========================================
        // ORIENTACIÓN ORIGINAL
        // ==========================================

        $orientaciones[] = [
            'largo' => $pieza['largo'],
            'ancho' => $pieza['ancho'],
            'rotada' => false,
        ];


        // ==========================================
        // ¿PODEMOS ROTAR?
        // ==========================================

        $rotacionPermitida =
            $configuracion['permitir_rotacion']
            &&
            $pieza['permitir_rotacion'];


        if (!$rotacionPermitida) {

            return $orientaciones;
        }


        // ==========================================
        // SI CONSIDERAMOS GRANO
        // ==========================================

        if ($configuracion['considerar_grano']) {

            $grano =
                $pieza['direccion_grano'];


            // --------------------------------------
            // GRANO HORIZONTAL
            // --------------------------------------

            if ($grano === 'horizontal') {

                /*
             * La orientación original conserva
             * el largo en horizontal.
             *
             * Al girarla 90°, el largo pasaría
             * a vertical.
             *
             * Por lo tanto NO agregamos la
             * orientación girada.
             */

                return $orientaciones;
            }


            // --------------------------------------
            // GRANO VERTICAL
            // --------------------------------------

            if ($grano === 'vertical') {

                /*
             * En este caso la orientación original
             * tiene el largo horizontal.
             *
             * Para que el largo quede vertical,
             * necesitamos girar la pieza.
             *
             * Por lo tanto la orientación original
             * NO es válida.
             */

                return [
                    [
                        'largo' =>
                        $pieza['ancho'],

                        'ancho' =>
                        $pieza['largo'],

                        'rotada' => true,
                    ]
                ];
            }


            // --------------------------------------
            // GRANO INDIFERENTE
            // --------------------------------------

            if ($grano === 'indiferente') {

                $orientaciones[] = [
                    'largo' =>
                    $pieza['ancho'],

                    'ancho' =>
                    $pieza['largo'],

                    'rotada' => true,
                ];
            }


            return $orientaciones;
        }


        // ==========================================
        // NO CONSIDERAR GRANO
        // ==========================================

        $orientaciones[] = [
            'largo' =>
            $pieza['ancho'],

            'ancho' =>
            $pieza['largo'],

            'rotada' => true,
        ];


        return $orientaciones;
    }

    private function puedeColocarse(
        array $pieza,
        array $orientacion,
        Hoja $hoja
    ): bool {
        $largoPieza = $orientacion['largo'];
        $anchoPieza = $orientacion['ancho'];

        $largoHoja = (float) $hoja->largo;
        $anchoHoja = (float) $hoja->ancho;


        // ==========================================
        // COMPROBAR DIMENSIONES
        // ==========================================

        if ($largoPieza > $largoHoja) {
            return false;
        }

        if ($anchoPieza > $anchoHoja) {
            return false;
        }


        return true;
    }

    /**
     * Coloca piezas mediante cortes guillotina y compara varias secuencias.
     *
     * La búsqueda prioriza:
     * 1. Colocar todas las piezas posibles.
     * 2. Utilizar el menor número de hojas.
     * 3. Repartir la superficie de forma equilibrada entre las hojas usadas.
     * 4. Preferir ajustes compactos dentro de cada espacio guillotina.
     *
     * Se conserva la estructura de salida consumida por la vista.
     */
    /**
     * Optimización por grupos de medidas iguales con cortes guillotina.
     * Primero intenta colocar juntas las piezas del mismo grupo; después
     * reutiliza los rectángulos sobrantes para los siguientes grupos.
     */
    /**
     * Optimiza la colocación pieza por pieza.
     * Prueba varios órdenes y selecciona el resultado que coloca más piezas,
     * utiliza menos hojas y deja espacios libres más compactos.
     */
    private function colocarPiezas(
        array $piezas,
        array &$hojas,
        array $configuracion
    ) {
        // Se comparan dos enfoques: colocación individual y bloques de piezas
        // idénticas. Ambos compiten con los mismos criterios y validaciones.
        $estrategiasIndividuales = [
            function ($a, $b) {
                return (($b['largo'] * $b['ancho']) <=> ($a['largo'] * $a['ancho']))
                    ?: (max($b['largo'], $b['ancho']) <=> max($a['largo'], $a['ancho']));
            },
            function ($a, $b) {
                return (max($b['largo'], $b['ancho']) <=> max($a['largo'], $a['ancho']))
                    ?: (($b['largo'] * $b['ancho']) <=> ($a['largo'] * $a['ancho']));
            },
            function ($a, $b) {
                return (min($b['largo'], $b['ancho']) <=> min($a['largo'], $a['ancho']))
                    ?: (($b['largo'] * $b['ancho']) <=> ($a['largo'] * $a['ancho']));
            },
            function ($a, $b) {
                return ($b['largo'] <=> $a['largo']) ?: ($b['ancho'] <=> $a['ancho']);
            },
            function ($a, $b) {
                return ($b['ancho'] <=> $a['ancho']) ?: ($b['largo'] <=> $a['largo']);
            },
            function ($a, $b) {
                return (($a['largo'] * $a['ancho']) <=> ($b['largo'] * $b['ancho']))
                    ?: (max($a['largo'], $a['ancho']) <=> max($b['largo'], $b['ancho']));
            },
            function ($a, $b) {
                return (max($a['largo'], $a['ancho']) <=> max($b['largo'], $b['ancho']))
                    ?: (($a['largo'] * $a['ancho']) <=> ($b['largo'] * $b['ancho']));
            },
            function ($a, $b) {
                return (min($a['largo'], $a['ancho']) <=> min($b['largo'], $b['ancho']))
                    ?: (($b['largo'] * $b['ancho']) <=> ($a['largo'] * $a['ancho']));
            },
            function ($a, $b) {
                return (($b['largo'] + $b['ancho']) <=> ($a['largo'] + $a['ancho']))
                    ?: (($b['largo'] * $b['ancho']) <=> ($a['largo'] * $a['ancho']));
            },
        ];

        $candidatos = [];

        foreach ($estrategiasIndividuales as $comparador) {
            $orden = array_values($piezas);
            usort($orden, $comparador);
            $candidatos[] = $this->empaquetarPiezasIndividuales(
                $orden,
                $hojas,
                $configuracion
            );
        }

        // El empaquetado por bloques solo agrupa piezas con el mismo material
        // y exactamente las mismas orientaciones permitidas. Así no se viola
        // la veta ni la restricción de giro de una pieza individual.
        $grupos = $this->crearGruposParaEmpaquetado($piezas);

        $estrategiasGrupos = [
            function ($a, $b) {
                return (($b['area'] * count($b['piezas'])) <=> ($a['area'] * count($a['piezas'])))
                    ?: (max($b['orientaciones'][0]['largo'], $b['orientaciones'][0]['ancho'])
                        <=> max($a['orientaciones'][0]['largo'], $a['orientaciones'][0]['ancho']));
            },
            function ($a, $b) {
                return (max($b['orientaciones'][0]['largo'], $b['orientaciones'][0]['ancho'])
                    <=> max($a['orientaciones'][0]['largo'], $a['orientaciones'][0]['ancho']))
                    ?: (count($b['piezas']) <=> count($a['piezas']));
            },
            function ($a, $b) {
                return (count($b['piezas']) <=> count($a['piezas']))
                    ?: (($b['area'] * count($b['piezas'])) <=> ($a['area'] * count($a['piezas'])));
            },
            function ($a, $b) {
                return (($a['area'] * count($a['piezas'])) <=> ($b['area'] * count($b['piezas'])))
                    ?: (count($a['piezas']) <=> count($b['piezas']));
            },
        ];

        foreach ($estrategiasGrupos as $comparador) {
            $ordenGrupos = array_values($grupos);
            usort($ordenGrupos, $comparador);
            $candidatos[] = $this->empaquetarGruposGuillotina(
                $ordenGrupos,
                $hojas,
                $configuracion
            );
        }

        // Búsqueda global por combinaciones de hojas:
        // vuelve a empaquetar TODAS las piezas usando subconjuntos de 1, 2
        // y 3 hojas. Esto permite que piezas de una hoja anterior se
        // redistribuyan en los espacios libres de otra hoja.
        $hojasBusqueda = array_values($hojas);
        usort($hojasBusqueda, function ($a, $b) {
            $areaA = (float) $a['largo'] * (float) $a['ancho'];
            $areaB = (float) $b['largo'] * (float) $b['ancho'];

            return ($areaB <=> $areaA)
                ?: ((int) $a['material_id'] <=> (int) $b['material_id']);
        });

        // Limitar la búsqueda para no disparar el tiempo de respuesta en
        // inventarios con muchas hojas físicas.
        $hojasBusqueda = array_slice($hojasBusqueda, 0, 16);
        $limiteCombinaciones = 300;
        $combinacionesGeneradas = 0;
        $cantidadHojas = count($hojasBusqueda);

        $agregarCombinaciones = function (
            int $tamano,
            int $inicio,
            array $seleccion
        ) use (
            &$agregarCombinaciones,
            &$candidatos,
            &$combinacionesGeneradas,
            $limiteCombinaciones,
            $hojasBusqueda,
            $piezas,
            $configuracion
        ) {
            if ($combinacionesGeneradas >= $limiteCombinaciones) {
                return;
            }

            if (count($seleccion) === $tamano) {
                $combinacionesGeneradas++;

                $ordenes = [
                    function ($a, $b) {
                        return (($b['largo'] * $b['ancho']) <=> ($a['largo'] * $a['ancho']))
                            ?: (max($b['largo'], $b['ancho']) <=> max($a['largo'], $a['ancho']));
                    },
                    function ($a, $b) {
                        return (max($b['largo'], $b['ancho']) <=> max($a['largo'], $a['ancho']))
                            ?: (($b['largo'] * $b['ancho']) <=> ($a['largo'] * $a['ancho']));
                    },
                    function ($a, $b) {
                        return (min($b['largo'], $b['ancho']) <=> min($a['largo'], $a['ancho']))
                            ?: (($b['largo'] * $b['ancho']) <=> ($a['largo'] * $a['ancho']));
                    },
                ];

                $hojasSeleccionadas = array_map(function ($indice) use ($hojasBusqueda) {
                    return $hojasBusqueda[$indice];
                }, $seleccion);

                foreach ($ordenes as $comparador) {
                    $orden = array_values($piezas);
                    usort($orden, $comparador);

                    $candidatos[] = $this->empaquetarPiezasIndividuales(
                        $orden,
                        $hojasSeleccionadas,
                        $configuracion
                    );
                }

                return;
            }

            $faltan = $tamano - count($seleccion);
            $ultimoInicio = count($hojasBusqueda) - $faltan;

            for ($i = $inicio; $i <= $ultimoInicio; $i++) {
                $nuevaSeleccion = $seleccion;
                $nuevaSeleccion[] = $i;
                $agregarCombinaciones($tamano, $i + 1, $nuevaSeleccion);

                if ($combinacionesGeneradas >= $limiteCombinaciones) {
                    return;
                }
            }
        };

        for ($tamano = 1; $tamano <= min(3, $cantidadHojas); $tamano++) {
            $agregarCombinaciones($tamano, 0, []);

            if ($combinacionesGeneradas >= $limiteCombinaciones) {
                break;
            }
        }

        $mejor = null;
        $mejorScore = null;
        $kerf = max(0.0, (float) ($configuracion['kerf'] ?? 0));

        foreach ($candidatos as $candidato) {
            // Un candidato geométricamente inválido nunca puede ganar.
            if ($this->validarColocacion($candidato['colocadas'], $hojas, $kerf) !== []) {
                continue;
            }

            $hojasUsadas = [];
            foreach ($candidato['colocadas'] as $pieza) {
                $hojasUsadas[$pieza['hoja_id'] . ':' . $pieza['hoja_numero']] = true;
            }

            $areaLibre = 0.0;
            $areaRetalMayor = 0.0;
            $concentracionRetales = 0.0;
            $ladoCortoMayor = 0.0;
            $cantidadRetales = 0;

            // No contar tableros completos sin usar como "retales": no son
            // sobrantes de corte y distorsionarían la comparación.
            foreach ($candidato['sobrantes'] as $sobrante) {
                $claveHoja = $sobrante['hoja_id'] . ':' . $sobrante['hoja_numero'];
                if (!isset($hojasUsadas[$claveHoja])) {
                    continue;
                }

                $area = (float) ($sobrante['area'] ?? 0);
                $largoRetal = (float) ($sobrante['largo'] ?? 0);
                $anchoRetal = (float) ($sobrante['ancho'] ?? 0);

                if (
                    $area <= 0 ||
                    $largoRetal + 0.000001 < (float) $configuracion['dimension_minima_retal_cm'] ||
                    $anchoRetal + 0.000001 < (float) $configuracion['dimension_minima_retal_cm']
                ) {
                    continue;
                }

                $ladoCorto = min($largoRetal, $anchoRetal);

                $areaLibre += $area;
                $areaRetalMayor = max($areaRetalMayor, $area);
                $ladoCortoMayor = max($ladoCortoMayor, $ladoCorto);
                $concentracionRetales += $area * $area;
                $cantidadRetales++;
            }

            // Prioridad acordada:
            // 1. Más piezas colocadas.
            // 2. Menos tableros abiertos.
            // 3. Mejor aprovechamiento/retales como desempate.
            $score = [
                count($candidato['colocadas']),
                -count($candidato['no_colocadas']),
                -count($hojasUsadas),
                round($areaLibre, 3),
                round($areaRetalMayor, 3),
                round($concentracionRetales, 3),
                round($ladoCortoMayor, 3),
                -$cantidadRetales,
            ];

            if ($mejor === null || $score > $mejorScore) {
                $mejor = $candidato;
                $mejorScore = $score;
            }
        }

        return $mejor ?? [
            'colocadas' => [],
            'no_colocadas' => array_map(function ($pieza) {
                return [
                    'pieza_id' => $pieza['id'],
                    'numero' => $pieza['numero'],
                    'etiqueta' => $pieza['etiqueta'],
                    'largo' => $pieza['largo'],
                    'ancho' => $pieza['ancho'],
                    'material_id' => $pieza['material_id'],
                ];
            }, $piezas),
            'sobrantes' => [],
        ];
    }

    /**
     * Construye grupos homogéneos para el empaquetado por bloques.
     * Solo combina piezas con material y opciones de orientación idénticos.
     */
    private function crearGruposParaEmpaquetado(array $piezas): array
    {
        $grupos = [];

        foreach ($piezas as $pieza) {
            $orientaciones = $pieza['orientaciones'] ?? [[
                'largo' => (float) $pieza['largo'],
                'ancho' => (float) $pieza['ancho'],
                'rotada' => false,
            ]];

            if ($orientaciones === []) {
                continue;
            }

            $firmaOrientaciones = [];
            foreach ($orientaciones as $orientacion) {
                $firmaOrientaciones[] = [
                    'largo' => round((float) $orientacion['largo'], 4),
                    'ancho' => round((float) $orientacion['ancho'], 4),
                    'rotada' => (bool) ($orientacion['rotada'] ?? false),
                ];
            }

            $clave = (string) $pieza['material_id'] . '|' . json_encode($firmaOrientaciones);

            if (!isset($grupos[$clave])) {
                $grupos[$clave] = [
                    'material_id' => $pieza['material_id'],
                    'orientaciones' => $orientaciones,
                    'area' => (float) $orientaciones[0]['largo']
                        * (float) $orientaciones[0]['ancho'],
                    'piezas' => [],
                ];
            }

            $grupos[$clave]['piezas'][] = [
                'id' => $pieza['id'],
                'numero' => $pieza['numero'],
                'etiqueta' => $pieza['etiqueta'],
                'largo' => $pieza['largo'],
                'ancho' => $pieza['ancho'],
                'material_id' => $pieza['material_id'],
            ];
        }

        return array_values($grupos);
    }
    private function empaquetarPiezasIndividuales(
        array $piezas,
        array $hojas,
        array $configuracion
    ): array {
        $kerf = max(0.0, (float) ($configuracion['kerf'] ?? 0));
        $espaciosPorHoja = [];
        $colocadas = [];
        $noColocadas = [];
        $hojasConPiezas = [];

        // Cuando todavía no hay una hoja abierta, priorizar la de mayor
        // superficie. Esto evita iniciar en una hoja pequeña solo porque
        // su primer encaje deja menos sobrante local.
        usort($hojas, function ($a, $b) {
            $areaA = (float) $a['largo'] * (float) $a['ancho'];
            $areaB = (float) $b['largo'] * (float) $b['ancho'];

            return ($areaB <=> $areaA)
                ?: ((float) $b['largo'] <=> (float) $a['largo']);
        });

        foreach ($hojas as $i => $hoja) {
            $espaciosPorHoja[$i] = [[
                'x' => 0.0,
                'y' => 0.0,
                'largo' => (float) $hoja['largo'],
                'ancho' => (float) $hoja['ancho'],
            ]];
        }

        foreach ($piezas as $pieza) {
            $mejor = null;
            $mejorScore = null;
            $orientaciones = $pieza['orientaciones'] ?? [[
                'largo' => (float) $pieza['largo'],
                'ancho' => (float) $pieza['ancho'],
                'rotada' => false,
            ]];

            foreach ($hojas as $iHoja => $hoja) {
                if (
                    !empty($configuracion['separar_materiales']) &&
                    (int) $pieza['material_id'] !== (int) $hoja['material_id']
                ) {
                    continue;
                }

                foreach ($orientaciones as $orientacion) {
                    $largoPieza = (float) $orientacion['largo'];
                    $anchoPieza = (float) $orientacion['ancho'];

                    foreach ($espaciosPorHoja[$iHoja] as $iEspacio => $espacio) {
                        $restoLargo = (float) $espacio['largo'] - $largoPieza;
                        $restoAncho = (float) $espacio['ancho'] - $anchoPieza;
                        if ($restoLargo < -0.000001 || $restoAncho < -0.000001) {
                            continue;
                        }

                        // Kerf requerido para separar esta pieza de los siguientes cortes.
                        $libreDerecha = max(0.0, $restoLargo - ($restoLargo > 0.000001 ? $kerf : 0));
                        $libreAbajo = max(0.0, $restoAncho - ($restoAncho > 0.000001 ? $kerf : 0));
                        $areaLibreLocal = ($libreDerecha * (float) $espacio['ancho'])
                            + ($largoPieza * $libreAbajo);
                        $ladoCorto = min(abs($restoLargo), abs($restoAncho));
                        $hojaNueva = empty($hojasConPiezas[$iHoja]) ? 1 : 0;

                        // Prioridad absoluta a reutilizar una hoja ya abierta.
                        $areaHoja = (float) $hoja['largo'] * (float) $hoja['ancho'];

                        $score = [
                            -$hojaNueva,
                            // Si ambas opciones requieren abrir hoja, elegir la mayor.
                            $hojaNueva ? round($areaHoja, 4) : 0,
                            -round($areaLibreLocal, 4),
                            -round($ladoCorto, 4),
                            -round(max($restoLargo, $restoAncho), 4),
                        ];

                        if ($mejorScore === null || $score > $mejorScore) {
                            $mejorScore = $score;
                            $mejor = [
                                'hoja' => $iHoja,
                                'espacio' => $iEspacio,
                                'rect' => $espacio,
                                'orientacion' => $orientacion,
                                'area_libre' => $areaLibreLocal,
                            ];
                        }
                    }
                }
            }

            if ($mejor === null) {
                $noColocadas[] = [
                    'pieza_id' => $pieza['id'],
                    'numero' => $pieza['numero'],
                    'etiqueta' => $pieza['etiqueta'],
                    'largo' => $pieza['largo'],
                    'ancho' => $pieza['ancho'],
                    'material_id' => $pieza['material_id'],
                ];
                continue;
            }

            $iHoja = $mejor['hoja'];
            $rect = $mejor['rect'];
            $orientacion = $mejor['orientacion'];
            $largoPieza = (float) $orientacion['largo'];
            $anchoPieza = (float) $orientacion['ancho'];

            $colocadas[] = [
                'hoja_id' => $hojas[$iHoja]['id'],
                'hoja_numero' => $hojas[$iHoja]['numero'],
                'pieza_id' => $pieza['id'],
                'numero' => $pieza['numero'],
                'etiqueta' => $pieza['etiqueta'],
                'material_id' => $pieza['material_id'],
                'largo' => $largoPieza,
                'ancho' => $anchoPieza,
                'x' => round((float) $rect['x'], 4),
                'y' => round((float) $rect['y'], 4),
                'rotada' => (bool) ($orientacion['rotada'] ?? false),
            ];
            $hojasConPiezas[$iHoja] = true;

            unset($espaciosPorHoja[$iHoja][$mejor['espacio']]);
            $nuevos = array_values($espaciosPorHoja[$iHoja]);

            // División guillotina: sobrante derecho de altura completa.
            $restoDerecha = (float) $rect['largo'] - $largoPieza - $kerf;
            if ($restoDerecha > 0.000001) {
                $nuevos[] = [
                    'x' => (float) $rect['x'] + $largoPieza + $kerf,
                    'y' => (float) $rect['y'],
                    'largo' => $restoDerecha,
                    'ancho' => (float) $rect['ancho'],
                ];
            }

            // Sobrante inferior limitado al ancho de la pieza: no se traslapa.
            $restoAbajo = (float) $rect['ancho'] - $anchoPieza - $kerf;
            if ($restoAbajo > 0.000001) {
                $nuevos[] = [
                    'x' => (float) $rect['x'],
                    'y' => (float) $rect['y'] + $anchoPieza + $kerf,
                    'largo' => $largoPieza,
                    'ancho' => $restoAbajo,
                ];
            }

            $espaciosPorHoja[$iHoja] = $nuevos;
        }

        $sobrantes = [];
        foreach ($espaciosPorHoja as $iHoja => $espacios) {
            foreach ($espacios as $espacio) {
                $largoLibre = round(max(0.0, (float) $espacio['largo']), 2);
                $anchoLibre = round(max(0.0, (float) $espacio['ancho']), 2);
                if ($largoLibre <= 0 || $anchoLibre <= 0) {
                    continue;
                }
                $sobrantes[] = [
                    'hoja_id' => $hojas[$iHoja]['id'],
                    'hoja_numero' => $hojas[$iHoja]['numero'],
                    'material_id' => $hojas[$iHoja]['material_id'],
                    'x' => round((float) $espacio['x'], 2),
                    'y' => round((float) $espacio['y'], 2),
                    'largo' => $largoLibre,
                    'ancho' => $anchoLibre,
                    'area' => round($largoLibre * $anchoLibre, 2),
                ];
            }
        }

        return [
            'colocadas' => $colocadas,
            'no_colocadas' => $noColocadas,
            'sobrantes' => $sobrantes,
        ];
    }

    private function empaquetarGruposGuillotina(
        array $grupos,
        array $hojas,
        array $configuracion
    ): array {
        $kerf = max(0.0, (float) ($configuracion['kerf'] ?? 0));
        $espaciosPorHoja = [];
        $colocadas = [];
        $noColocadas = [];
        $areaPorHoja = [];

        foreach ($hojas as $i => $hoja) {
            $espaciosPorHoja[$i] = [[
                'x' => 0.0,
                'y' => 0.0,
                'largo' => (float) $hoja['largo'],
                'ancho' => (float) $hoja['ancho'],
            ]];
            $areaPorHoja[$i] = 0.0;
        }

        $desperdicioLocal = 0.0;

        foreach ($grupos as $grupo) {
            $pendientes = $grupo['piezas'];

            while (count($pendientes) > 0) {
                $mejor = null;
                $mejorScore = null;

                foreach ($hojas as $iHoja => $hoja) {
                    if (
                        !empty($configuracion['separar_materiales']) &&
                        (int) $grupo['material_id'] !== (int) $hoja['material_id']
                    ) continue;

                    foreach ($grupo['orientaciones'] as $orientacion) {
                        $largoPieza = (float) $orientacion['largo'];
                        $anchoPieza = (float) $orientacion['ancho'];

                        foreach ($espaciosPorHoja[$iHoja] as $iEspacio => $espacio) {
                            $maxColumnas = (int) floor(
                                (((float) $espacio['largo']) + $kerf + 0.000001) /
                                max(0.000001, $largoPieza + $kerf)
                            );
                            $maxFilas = (int) floor(
                                (((float) $espacio['ancho']) + $kerf + 0.000001) /
                                max(0.000001, $anchoPieza + $kerf)
                            );

                            if ($maxColumnas < 1 || $maxFilas < 1) continue;

                            $cantidad = min(count($pendientes), $maxColumnas * $maxFilas);
                            if ($cantidad < 1) continue;

                            // Compara distribuciones con filas más llenas y columnas más llenas.
                            $columnasPatron1 = min($maxColumnas, $cantidad);
                            $filasObjetivoPatron2 = min($maxFilas, $cantidad);
                            $columnasPatron2 = (int) ceil($cantidad / $filasObjetivoPatron2);

                            // Ambos patrones se recorren por filas. Las filas reales
                            // siempre se calculan a partir de las columnas y la cantidad
                            // colocada; no se reserva una fila vacía que no contiene piezas.
                            $patrones = [
                                [
                                    'columnas' => $columnasPatron1,
                                    'filas' => (int) ceil($cantidad / $columnasPatron1),
                                ],
                                [
                                    'columnas' => $columnasPatron2,
                                    'filas' => (int) ceil($cantidad / $columnasPatron2),
                                ],
                            ];

                            foreach ($patrones as $patron) {
                                $cols = $patron['columnas'];
                                $rows = $patron['filas'];
                                if ($cols > $maxColumnas || $rows > $maxFilas || ($cols * $rows) < $cantidad) continue;

                                $bloqueLargo = ($cols * $largoPieza) + (max(0, $cols - 1) * $kerf);
                                $bloqueAncho = ($rows * $anchoPieza) + (max(0, $rows - 1) * $kerf);

                                if (
                                    $bloqueLargo > (float) $espacio['largo'] + 0.000001 ||
                                    $bloqueAncho > (float) $espacio['ancho'] + 0.000001
                                ) continue;

                                $areaSobrante = ((float) $espacio['largo'] * (float) $espacio['ancho'])
                                    - ($bloqueLargo * $bloqueAncho);
                                $areaHoja = (float) $hoja['largo'] * (float) $hoja['ancho'];
                                $cargaProyectada = $areaPorHoja[$iHoja]
                                    + ($cantidad * $grupo['area']);
                                $hojaNueva = $areaPorHoja[$iHoja] <= 0.000001 ? 1 : 0;

                                // Prioridad de colocación:
                                // 1. Reutilizar primero una hoja que ya tiene piezas.
                                // 2. Dentro de las hojas usadas, colocar la mayor cantidad posible.
                                // 3. Elegir el bloque que conserva sobrantes más aprovechables.
                                // Así evitamos abrir una hoja nueva para un grupo completo
                                // cuando sus piezas todavía caben en retales de hojas anteriores.
                                $score = [
                                    -$hojaNueva,
                                    $cantidad,
                                    // Al abrir una hoja, preferir la de mayor superficie.
                                    $hojaNueva ? round($areaHoja, 4) : 0,
                                    -round($areaSobrante, 4),
                                    -round(abs(($areaHoja - $cargaProyectada)), 4),
                                    -$rows,
                                ];

                                if ($mejorScore === null || $score > $mejorScore) {
                                    $mejorScore = $score;
                                    $mejor = [
                                        'hoja' => $iHoja,
                                        'espacio' => $iEspacio,
                                        'rectangulo' => $espacio,
                                        'orientacion' => $orientacion,
                                        'cantidad' => $cantidad,
                                        'columnas' => $cols,
                                        'filas' => $rows,
                                        'bloque_largo' => $bloqueLargo,
                                        'bloque_ancho' => $bloqueAncho,
                                        'area_sobrante' => $areaSobrante,
                                    ];
                                }
                            }
                        }
                    }
                }

                if ($mejor === null) {
                    foreach ($pendientes as $p) {
                        $noColocadas[] = [
                            'pieza_id' => $p['id'],
                            'numero' => $p['numero'],
                            'etiqueta' => $p['etiqueta'],
                            'largo' => $p['largo'],
                            'ancho' => $p['ancho'],
                            'material_id' => $p['material_id'],
                        ];
                    }
                    break;
                }

                $iHoja = $mejor['hoja'];
                $rect = $mejor['rectangulo'];
                $ori = $mejor['orientacion'];
                $largoPieza = (float) $ori['largo'];
                $anchoPieza = (float) $ori['ancho'];
                $cols = $mejor['columnas'];
                $bloqueLargo = $mejor['bloque_largo'];
                $bloqueAncho = $mejor['bloque_ancho'];
                $cantidad = $mejor['cantidad'];

                for ($n = 0; $n < $cantidad; $n++) {
                    $fila = (int) floor($n / $cols);
                    $columna = $n % $cols;
                    $pieza = array_shift($pendientes);

                    $colocadas[] = [
                        'hoja_id' => $hojas[$iHoja]['id'],
                        'hoja_numero' => $hojas[$iHoja]['numero'],
                        'pieza_id' => $pieza['id'],
                        'numero' => $pieza['numero'],
                        'etiqueta' => $pieza['etiqueta'],
                        'material_id' => $pieza['material_id'],
                        'largo' => $largoPieza,
                        'ancho' => $anchoPieza,
                        'x' => round((float) $rect['x'] + ($columna * ($largoPieza + $kerf)), 4),
                        'y' => round((float) $rect['y'] + ($fila * ($anchoPieza + $kerf)), 4),
                        'rotada' => $ori['rotada'],
                    ];
                    $areaPorHoja[$iHoja] += $grupo['area'];
                }

                $desperdicioLocal += max(0.0, $mejor['area_sobrante']);

                // Quitar el espacio utilizado y crear sobrantes rectangulares sin traslape.
                unset($espaciosPorHoja[$iHoja][$mejor['espacio']]);
                $nuevos = array_values($espaciosPorHoja[$iHoja]);

                $restoDerecha = (float) $rect['largo'] - $bloqueLargo - $kerf;
                if ($restoDerecha > 0.000001) {
                    $nuevos[] = [
                        'x' => (float) $rect['x'] + $bloqueLargo + $kerf,
                        'y' => (float) $rect['y'],
                        'largo' => $restoDerecha,
                        'ancho' => (float) $rect['ancho'],
                    ];
                }

                // Si la última fila quedó incompleta, conservar el hueco
                // horizontal como un espacio independiente para otros grupos.
                $piezasUltimaFila = $cantidad % $cols;
                if ($piezasUltimaFila > 0 && $mejor['filas'] > 1) {
                    $inicioHueco = (float) $rect['x']
                        + ($piezasUltimaFila * ($largoPieza + $kerf));
                    $anchoHueco = (float) $rect['x'] + $bloqueLargo - $inicioHueco;
                    if ($anchoHueco > 0.000001) {
                        $nuevos[] = [
                            'x' => $inicioHueco,
                            'y' => (float) $rect['y'] + (($mejor['filas'] - 1) * ($anchoPieza + $kerf)),
                            'largo' => $anchoHueco,
                            'ancho' => $anchoPieza,
                        ];
                    }
                }

                $restoAbajo = (float) $rect['ancho'] - $bloqueAncho - $kerf;
                if ($restoAbajo > 0.000001) {
                    $nuevos[] = [
                        'x' => (float) $rect['x'],
                        'y' => (float) $rect['y'] + $bloqueAncho + $kerf,
                        'largo' => $bloqueLargo,
                        'ancho' => $restoAbajo,
                    ];
                }

                $espaciosPorHoja[$iHoja] = $nuevos;
            }
        }

        // Exponer cada retazo rectangular con sus dimensiones y coordenadas.
        // Los rectángulos se mantienen separados por hoja y no se suman entre sí.
        $sobrantes = [];
        foreach ($espaciosPorHoja as $iHoja => $espacios) {
            foreach ($espacios as $espacio) {
                $largoLibre = round(max(0.0, (float) $espacio['largo']), 2);
                $anchoLibre = round(max(0.0, (float) $espacio['ancho']), 2);
                if ($largoLibre <= 0 || $anchoLibre <= 0) {
                    continue;
                }
                $sobrantes[] = [
                    'hoja_id' => $hojas[$iHoja]['id'],
                    'hoja_numero' => $hojas[$iHoja]['numero'],
                    'material_id' => $hojas[$iHoja]['material_id'],
                    'x' => round((float) $espacio['x'], 2),
                    'y' => round((float) $espacio['y'], 2),
                    'largo' => $largoLibre,
                    'ancho' => $anchoLibre,
                    'area' => round($largoLibre * $anchoLibre, 2),
                ];
            }
        }
        usort($sobrantes, function ($a, $b) {
            return ($b['area'] <=> $a['area'])
                ?: ($b['largo'] <=> $a['largo'])
                ?: ($b['ancho'] <=> $a['ancho']);
        });

        return [
            'colocadas' => $colocadas,
            'no_colocadas' => $noColocadas,
            'sobrantes' => $sobrantes,
            '_desperdicio_local' => $desperdicioLocal,
        ];
    }

    private function puedeColocarseEnEspacio(
        array $orientacion,
        array $espacio,
        float $kerf
    ): bool {

        $largo =
            $orientacion['largo'];

        $ancho =
            $orientacion['ancho'];


        $largoDisponible =
            $espacio['largo'];

        $anchoDisponible =
            $espacio['ancho'];


        // ==========================================
        // COMPROBAR LARGO
        // ==========================================

        if ($largo > $largoDisponible) {
            return false;
        }


        // ==========================================
        // COMPROBAR ANCHO
        // ==========================================

        if ($ancho > $anchoDisponible) {
            return false;
        }


        return true;
    }

    private function dividirEspacio(
        array $espacios,
        int $indiceEspacio,
        array $orientacion,
        float $kerf
    ): array {

        $espacio =
            $espacios[$indiceEspacio];


        unset(
            $espacios[$indiceEspacio]
        );


        $espacios =
            array_values($espacios);


        $largoPieza =
            $orientacion['largo'];

        $anchoPieza =
            $orientacion['ancho'];


        // ==========================================
        // ESPACIO A LA DERECHA
        // ==========================================

        $largoDerecha =
            $espacio['largo']
            -
            $largoPieza
            -
            $kerf;


        if ($largoDerecha > 0) {

            $espacios[] = [

                'x' =>
                $espacio['x']
                    +
                    $largoPieza
                    +
                    $kerf,

                'y' =>
                $espacio['y'],

                'largo' =>
                $largoDerecha,

                'ancho' =>
                $espacio['ancho'],

            ];
        }


        // ==========================================
        // ESPACIO ABAJO
        // ==========================================

        $anchoAbajo =
            $espacio['ancho']
            -
            $anchoPieza
            -
            $kerf;


        if ($anchoAbajo > 0) {

            $espacios[] = [

                'x' =>
                $espacio['x'],

                'y' =>
                $espacio['y']
                    +
                    $anchoPieza
                    +
                    $kerf,

                'largo' =>
                $largoPieza,

                'ancho' =>
                $anchoAbajo,

            ];
        }


        return $espacios;
    }

    private function validarColocacion(
        array $colocadas,
        array $hojas,
        float $kerf
    ): array {

        $errores = [];


        // ==========================================
        // RECORRER PIEZAS COLOCADAS
        // ==========================================

        foreach ($colocadas as $indice => $pieza) {

            // ==========================================
            // BUSCAR LA HOJA
            // ==========================================

            $hoja = null;

            foreach ($hojas as $h) {

                if (
                    $h['id'] == $pieza['hoja_id']
                    &&
                    $h['numero'] == $pieza['hoja_numero']
                ) {

                    $hoja = $h;

                    break;
                }
            }


            // ==========================================
            // HOJA NO ENCONTRADA
            // ==========================================

            if (!$hoja) {

                $errores[] = [

                    'tipo' =>
                    'hoja_no_encontrada',

                    'pieza_id' =>
                    $pieza['pieza_id'],

                    'etiqueta' =>
                    $pieza['etiqueta'],

                ];

                continue;
            }


            // ==========================================
            // POSICIÓN DE LA PIEZA
            // ==========================================

            $x = (float) $pieza['x'];

            $y = (float) $pieza['y'];

            $largo =
                (float) $pieza['largo'];

            $ancho =
                (float) $pieza['ancho'];


            // ==========================================
            // CALCULAR EXTREMOS
            // ==========================================

            $derecha =
                $x + $largo;

            $abajo =
                $y + $ancho;


            // ==========================================
            // COMPROBAR LÍMITES DE LA HOJA
            // ==========================================

            if ($x < 0) {

                $errores[] = [

                    'tipo' =>
                    'fuera_de_hoja',

                    'razon' =>
                    'x_negativo',

                    'pieza_id' =>
                    $pieza['pieza_id'],

                    'etiqueta' =>
                    $pieza['etiqueta'],

                ];
            }


            if ($y < 0) {

                $errores[] = [

                    'tipo' =>
                    'fuera_de_hoja',

                    'razon' =>
                    'y_negativo',

                    'pieza_id' =>
                    $pieza['pieza_id'],

                    'etiqueta' =>
                    $pieza['etiqueta'],

                ];
            }


            if (
                $derecha > $hoja['largo']
            ) {

                $errores[] = [

                    'tipo' =>
                    'fuera_de_hoja',

                    'razon' =>
                    'excede_largo',

                    'pieza_id' =>
                    $pieza['pieza_id'],

                    'etiqueta' =>
                    $pieza['etiqueta'],

                ];
            }


            if (
                $abajo > $hoja['ancho']
            ) {

                $errores[] = [

                    'tipo' =>
                    'fuera_de_hoja',

                    'razon' =>
                    'excede_ancho',

                    'pieza_id' =>
                    $pieza['pieza_id'],

                    'etiqueta' =>
                    $pieza['etiqueta'],

                ];
            }


            // ==========================================
            // COMPARAR CONTRA LAS DEMÁS PIEZAS
            // ==========================================

            foreach (
                $colocadas
                as $otroIndice => $otraPieza
            ) {

                // No compararse consigo misma
                if (
                    $indice == $otroIndice
                ) {
                    continue;
                }


                // Deben estar en la misma hoja
                if (
                    $pieza['hoja_id']
                    !=
                    $otraPieza['hoja_id']
                ) {
                    continue;
                }


                if (
                    $pieza['hoja_numero']
                    !=
                    $otraPieza['hoja_numero']
                ) {
                    continue;
                }


                // ======================================
                // EXTREMOS DE LA OTRA PIEZA
                // ======================================

                $otraX =
                    (float) $otraPieza['x'];

                $otraY =
                    (float) $otraPieza['y'];

                $otraLargo =
                    (float) $otraPieza['largo'];

                $otraAncho =
                    (float) $otraPieza['ancho'];


                $otraDerecha =
                    $otraX + $otraLargo;

                $otraAbajo =
                    $otraY + $otraAncho;


                // ======================================
                // COMPROBAR SUPERPOSICIÓN
                // ======================================

                $seSuperponen = !(
                    $derecha <= $otraX
                    ||
                    $x >= $otraDerecha
                    ||
                    $abajo <= $otraY
                    ||
                    $y >= $otraAbajo
                );


                if ($seSuperponen) {

                    $errores[] = [

                        'tipo' =>
                        'superposicion',

                        'pieza_id' =>
                        $pieza['pieza_id'],

                        'etiqueta' =>
                        $pieza['etiqueta'],

                        'otra_pieza_id' =>
                        $otraPieza['pieza_id'],

                        'otra_etiqueta' =>
                        $otraPieza['etiqueta'],

                    ];
                }
            }
        }


        // ==========================================
        // ELIMINAR ERRORES DUPLICADOS
        // ==========================================

        $erroresUnicos = [];

        foreach ($errores as $error) {

            $clave =
                json_encode($error);

            $erroresUnicos[$clave] =
                $error;
        }


        return array_values(
            $erroresUnicos
        );
    }

    private function calcularEstadisticas(
        array $colocadas,
        array $hojas,
        array $sobrantes = [],
        float $dimensionMinimaRetal = 20.0
    ): array {

        // ==========================================
        // ESTADÍSTICAS POR HOJA
        // ==========================================

        $detalleHojas = [];

        $areaHojasTotal = 0;

        $areaPiezasTotal = 0;


        // ==========================================
        // RECORRER HOJAS
        // ==========================================

        foreach ($hojas as $hoja) {

            $piezasDeEstaHoja = [];
            $retalesDeEstaHoja = [];

            $areaPiezas = 0;
            $areaRetales = 0;
            $areaRetalesRecuperables = 0.0;
            $cantidadRetalesRecuperables = 0;


            // ======================================
            // BUSCAR PIEZAS DE ESTA HOJA
            // ======================================

            foreach ($colocadas as $pieza) {

                if (
                    $pieza['hoja_id'] != $hoja['id']
                    ||
                    $pieza['hoja_numero'] != $hoja['numero']
                ) {
                    continue;
                }


                $areaPieza =
                    $pieza['largo'] *
                    $pieza['ancho'];


                $areaPiezas +=
                    $areaPieza;


                $piezasDeEstaHoja[] = [

                    'pieza_id' =>
                    $pieza['pieza_id'],

                    'numero' =>
                    $pieza['numero'],

                    'etiqueta' =>
                    $pieza['etiqueta'],

                    'largo' =>
                    $pieza['largo'],

                    'ancho' =>
                    $pieza['ancho'],

                    'x' =>
                    $pieza['x'],

                    'y' =>
                    $pieza['y'],

                    'rotada' =>
                    $pieza['rotada'],

                    'area' =>
                    $areaPieza,

                ];
            }


            // ======================================
            // RETALES DE ESTA HOJA
            // ======================================

            foreach ($sobrantes as $sobrante) {
                if (
                    (int) ($sobrante['hoja_id'] ?? 0) !== (int) $hoja['id'] ||
                    (int) ($sobrante['hoja_numero'] ?? 0) !== (int) $hoja['numero']
                ) {
                    continue;
                }

                $largoRetal = max(0.0, (float) ($sobrante['largo'] ?? 0));
                $anchoRetal = max(0.0, (float) ($sobrante['ancho'] ?? 0));
                $areaRetal = round($largoRetal * $anchoRetal, 2);

                if ($largoRetal <= 0 || $anchoRetal <= 0 || $areaRetal <= 0) {
                    continue;
                }

                $recuperable = (
                    $largoRetal + 0.000001 >= $dimensionMinimaRetal &&
                    $anchoRetal + 0.000001 >= $dimensionMinimaRetal
                );

                $retalesDeEstaHoja[] = [
                    'x' => round((float) ($sobrante['x'] ?? 0), 2),
                    'y' => round((float) ($sobrante['y'] ?? 0), 2),
                    'largo' => round($largoRetal, 2),
                    'ancho' => round($anchoRetal, 2),
                    'area' => $areaRetal,
                    'recuperable' => $recuperable,
                    'motivo_no_recuperable' => $recuperable
                        ? null
                        : 'Una de sus dimensiones es menor que ' . round($dimensionMinimaRetal, 2) . ' cm.',
                ];
                $areaRetales += $areaRetal;

                if ($recuperable) {
                    $areaRetalesRecuperables += $areaRetal;
                    $cantidadRetalesRecuperables++;
                }
            }

            // ======================================
            // SI LA HOJA NO SE UTILIZÓ
            // ======================================

            if (count($piezasDeEstaHoja) === 0) {
                continue;
            }


            // ======================================
            // ÁREA DE LA HOJA
            // ======================================

            $areaHoja =
                $hoja['largo'] *
                $hoja['ancho'];


            // ======================================
            // DESPERDICIO
            // ======================================

            // El desperdicio es el área que no quedó dentro de piezas
            // ni dentro de rectángulos libres recuperables.
            $areaDesperdicio = max(
                0.0,
                $areaHoja - $areaPiezas - $areaRetales
            );


            // ======================================
            // APROVECHAMIENTO
            // ======================================

            $porcentajeAprovechamiento = 0;

            if ($areaHoja > 0) {

                $porcentajeAprovechamiento =
                    (
                        $areaPiezas /
                        $areaHoja
                    ) * 100;
            }


            // ======================================
            // DESPERDICIO %
            // ======================================

            $porcentajeDesperdicio =
                100 -
                $porcentajeAprovechamiento;


            // ======================================
            // GUARDAR HOJA
            // ======================================

            $detalleHojas[] = [

                'hoja_id' =>
                $hoja['id'],

                'hoja_numero' =>
                $hoja['numero'],

                'etiqueta' =>
                $hoja['etiqueta'],

                'material_id' =>
                $hoja['material_id'],

                'material' =>
                $hoja['material'],

                'largo' =>
                $hoja['largo'],

                'ancho' =>
                $hoja['ancho'],

                'area_hoja' =>
                $areaHoja,

                'area_piezas' =>
                $areaPiezas,

                'area_retales' =>
                round($areaRetales, 2),

                'cantidad_retales' =>
                count($retalesDeEstaHoja),

                'cantidad_retales_recuperables' =>
                $cantidadRetalesRecuperables,

                'area_retales_recuperables' =>
                round($areaRetalesRecuperables, 2),

                'dimension_minima_retal_cm' =>
                round($dimensionMinimaRetal, 2),

                'retales' =>
                $retalesDeEstaHoja,

                'area_desperdicio' =>
                $areaDesperdicio,

                'porcentaje_aprovechamiento' =>
                round(
                    $porcentajeAprovechamiento,
                    2
                ),

                'porcentaje_desperdicio' =>
                round(
                    $porcentajeDesperdicio,
                    2
                ),

                'cantidad_piezas' =>
                count($piezasDeEstaHoja),

                'piezas' =>
                $piezasDeEstaHoja,

            ];


            $areaHojasTotal +=
                $areaHoja;

            $areaPiezasTotal +=
                $areaPiezas;
        }


        // ==========================================
        // TOTALES
        // ==========================================

        $areaRetalesTotal = 0.0;
        $cantidadRetalesTotal = 0;
        $areaRetalesRecuperablesTotal = 0.0;
        $cantidadRetalesRecuperablesTotal = 0;

        foreach ($detalleHojas as $detalle) {
            $areaRetalesTotal += (float) ($detalle['area_retales'] ?? 0);
            $cantidadRetalesTotal += (int) ($detalle['cantidad_retales'] ?? 0);
            $areaRetalesRecuperablesTotal += (float) ($detalle['area_retales_recuperables'] ?? 0);
            $cantidadRetalesRecuperablesTotal += (int) ($detalle['cantidad_retales_recuperables'] ?? 0);
        }

        $areaDesperdicioTotal = max(
            0.0,
            $areaHojasTotal - $areaPiezasTotal - $areaRetalesTotal
        );


        $porcentajeAprovechamientoTotal = 0;

        if ($areaHojasTotal > 0) {

            $porcentajeAprovechamientoTotal =
                (
                    $areaPiezasTotal /
                    $areaHojasTotal
                ) * 100;
        }


        $porcentajeDesperdicioTotal =
            100 -
            $porcentajeAprovechamientoTotal;


        return [

            // --------------------------------------
            // RESUMEN GENERAL
            // --------------------------------------

            'hojas_utilizadas' =>
            count($detalleHojas),

            'area_hojas' =>
            $areaHojasTotal,

            'area_piezas' =>
            $areaPiezasTotal,

            'area_retales' =>
            round($areaRetalesTotal, 2),

            'cantidad_retales_recuperables' =>
            $cantidadRetalesRecuperablesTotal,

            'area_retales_recuperables' =>
            round($areaRetalesRecuperablesTotal, 2),

            'dimension_minima_retal_cm' =>
            round($dimensionMinimaRetal, 2),

            'cantidad_retales' =>
            $cantidadRetalesTotal,

            'area_desperdicio' =>
            $areaDesperdicioTotal,

            'porcentaje_aprovechamiento' =>
            round(
                $porcentajeAprovechamientoTotal,
                2
            ),

            'porcentaje_desperdicio' =>
            round(
                $porcentajeDesperdicioTotal,
                2
            ),

            // --------------------------------------
            // DETALLE INDIVIDUAL
            // --------------------------------------

            'detalle_hojas' =>
            $detalleHojas,

        ];
    }

    private function agruparPorDimension(array $piezas): array
    {
        $grupos = [];

        foreach ($piezas as $pieza) {

            /*
         * Las orientaciones son alternativas de colocación,
         * NO piezas adicionales.
         *
         * Por ahora utilizamos la primera orientación disponible.
         * Más adelante el optimizador decidirá cuál orientación
         * es la mejor para cada pieza.
         */

            if (empty($pieza['orientaciones'])) {
                continue;
            }

            $orientacion = $pieza['orientaciones'][0];

            $largo = (float) $orientacion['largo'];
            $ancho = (float) $orientacion['ancho'];

            /*
         * La franja se identifica por su ANCHO.
         *
         * Ejemplo:
         *
         * 700 x 400
         * 800 x 400
         *
         * pueden pertenecer a una misma franja de 400.
         */

            $clave = $pieza['material_id'] . '|' . $ancho;

            if (!isset($grupos[$clave])) {

                $grupos[$clave] = [
                    'material_id' => $pieza['material_id'],
                    'ancho' => $ancho,
                    'piezas' => [],
                ];
            }

            $grupos[$clave]['piezas'][] = [
                'pieza_id' => $pieza['id'],
                'numero' => $pieza['numero'] ?? 1,
                'etiqueta' => $pieza['etiqueta'] ?? '',
                'largo' => $largo,
                'ancho' => $ancho,
                'rotada' => $orientacion['rotada'] ?? false,
            ];
        }

        return array_values($grupos);
    }


    private function generarFranjas(
        array $gruposDimension,
        array $hojas,
        array $configuracion
    ): array {

        $kerf = (float) ($configuracion['kerf'] ?? 0);

        $franjas = [];

        foreach ($gruposDimension as $grupo) {

            $materialId = $grupo['material_id'];
            $anchoFranja = (float) $grupo['ancho'];

            // ==========================================
            // BUSCAR LARGO MÁXIMO DE HOJA
            // ==========================================

            $largoMaximo = 0;

            foreach ($hojas as $hoja) {

                if (
                    (int) $hoja['material_id']
                    !== (int) $materialId
                ) {
                    continue;
                }

                $largoHoja =
                    (float) $hoja['largo'];

                if ($largoHoja > $largoMaximo) {
                    $largoMaximo = $largoHoja;
                }
            }

            // ==========================================
            // NO HAY HOJA PARA EL MATERIAL
            // ==========================================

            if ($largoMaximo <= 0) {
                continue;
            }

            // ==========================================
            // CREAR FRANJA
            // ==========================================

            $franjaActual = [
                'material_id' => $materialId,
                'ancho' => $anchoFranja,
                'largo_utilizado' => 0,
                'piezas' => [],
            ];

            // ==========================================
            // AGREGAR PIEZAS
            // ==========================================

            foreach ($grupo['piezas'] as $pieza) {

                $largoPieza =
                    (float) $pieza['largo'];

                /*
             * El kerf se aplica solamente
             * entre piezas.
             */
                $kerfNecesario =
                    count($franjaActual['piezas']) > 0
                    ? $kerf
                    : 0;

                $nuevoLargo =
                    $franjaActual['largo_utilizado']
                    + $kerfNecesario
                    + $largoPieza;

                // ======================================
                // SI YA NO CABE
                // ======================================

                if (
                    count($franjaActual['piezas']) > 0
                    && $nuevoLargo > $largoMaximo
                ) {

                    $franjas[] =
                        $franjaActual;

                    $franjaActual = [
                        'material_id' => $materialId,
                        'ancho' => $anchoFranja,
                        'largo_utilizado' => 0,
                        'piezas' => [],
                    ];

                    $nuevoLargo =
                        $largoPieza;
                }

                // ======================================
                // AGREGAR PIEZA
                // ======================================

                $franjaActual['piezas'][] =
                    $pieza;

                $franjaActual['largo_utilizado'] =
                    $nuevoLargo;
            }

            // ==========================================
            // GUARDAR ÚLTIMA FRANJA
            // ==========================================

            if (
                count($franjaActual['piezas']) > 0
            ) {

                $franjas[] =
                    $franjaActual;
            }
        }

        return $franjas;
    }


    private function colocarFranjasEnHoja(
        array $franjas,
        array $hoja,
        array $configuracion
    ): array {

        $kerf = (float) ($configuracion['kerf'] ?? 0);

        $largoHoja = (float) $hoja['largo'];
        $anchoHoja = (float) $hoja['ancho'];

        // =====================================================
        // ESPACIOS LIBRES DE LA HOJA
        // =====================================================

        $espacios = [
            [
                'x' => 0,
                'y' => 0,
                'largo' => $largoHoja,
                'ancho' => $anchoHoja,
            ]
        ];

        $franjasColocadas = [];
        $cortes = [];

        // =====================================================
        // RECORRER FRANJAS
        // =====================================================

        foreach ($franjas as $indice => $franja) {

            // =================================================
            // VERIFICAR MATERIAL
            // =================================================

            if (
                (int) $franja['material_id']
                !==
                (int) $hoja['material_id']
            ) {
                continue;
            }

            $altoFranja =
                (float) $franja['ancho'];

            $largoFranja =
                (float) $franja['largo_utilizado'];

            // =================================================
            // BUSCAR UN ESPACIO DONDE QUEPA
            // =================================================

            $indiceEspacioEncontrado = null;

            foreach ($espacios as $indiceEspacio => $espacio) {

                // La franja necesita como mínimo
                // el largo utilizado por sus piezas
                // y el ancho de la franja.

                $largoNecesario =
                    $largoFranja;

                $anchoNecesario =
                    $altoFranja;

                if (
                    $largoNecesario <= $espacio['largo']
                    &&
                    $anchoNecesario <= $espacio['ancho']
                ) {

                    $indiceEspacioEncontrado =
                        $indiceEspacio;

                    break;
                }
            }

            // =================================================
            // SI NO CABE, PASAR A LA SIGUIENTE FRANJA
            // =================================================

            if (
                $indiceEspacioEncontrado === null
            ) {
                continue;
            }

            // =================================================
            // OBTENER ESPACIO
            // =================================================

            $espacio =
                $espacios[$indiceEspacioEncontrado];

            // =================================================
            // POSICIÓN DE LA FRANJA
            // =================================================

            $xFranja =
                $espacio['x'];

            $yFranja =
                $espacio['y'];

            // =================================================
            // COLOCAR PIEZAS DENTRO DE LA FRANJA
            // =================================================

            $xActual =
                $xFranja;

            $piezasColocadas = [];

            foreach (
                $franja['piezas']
                as $indicePieza => $pieza
            ) {

                $largoPieza =
                    (float) $pieza['largo'];

                $anchoPieza =
                    (float) $pieza['ancho'];

                // ---------------------------------------------
                // KERF ENTRE PIEZAS
                // ---------------------------------------------

                if ($indicePieza > 0) {
                    $xActual += $kerf;
                }

                // ---------------------------------------------
                // COMPROBAR QUE LA PIEZA QUEPA
                // ---------------------------------------------

                if (
                    $xActual
                    + $largoPieza
                    >
                    $xFranja
                    + $espacio['largo']
                ) {
                    break;
                }

                // ---------------------------------------------
                // GUARDAR PIEZA
                // ---------------------------------------------

                $piezasColocadas[] = [

                    'pieza_id' =>
                    $pieza['pieza_id'],

                    'numero' =>
                    $pieza['numero'],

                    'etiqueta' =>
                    $pieza['etiqueta'],

                    'largo' =>
                    $largoPieza,

                    'ancho' =>
                    $anchoPieza,

                    'x' =>
                    $xActual,

                    'y' =>
                    $yFranja,

                    'rotada' =>
                    $pieza['rotada'],

                ];

                $xActual += $largoPieza;
            }

            // =================================================
            // SI NO SE COLOCÓ NINGUNA PIEZA
            // =================================================

            if (
                count($piezasColocadas) === 0
            ) {
                continue;
            }

            // =================================================
            // LARGO REAL UTILIZADO
            // =================================================

            $largoUtilizado =
                $xActual - $xFranja;

            $sobranteLargo =
                max(
                    0,
                    $espacio['largo']
                        - $largoUtilizado
                );

            // =================================================
            // REGISTRAR FRANJA
            // =================================================

            $franjaColocada = [

                'indice' =>
                $indice,

                'material_id' =>
                $franja['material_id'],

                'x' =>
                $xFranja,

                'y' =>
                $yFranja,

                'largo' =>
                $largoUtilizado,

                'ancho' =>
                $altoFranja,

                'largo_utilizado' =>
                $largoUtilizado,

                'sobrante_largo' =>
                $sobranteLargo,

                'retal' => [

                    'largo' =>
                    $sobranteLargo,

                    'ancho' =>
                    $altoFranja,

                    'x' =>
                    $xFranja
                        + $largoUtilizado,

                    'y' =>
                    $yFranja,

                    'reutilizable' =>
                    $sobranteLargo > 0,

                ],

                'piezas' =>
                $piezasColocadas,
            ];

            $franjasColocadas[] =
                $franjaColocada;

            // =================================================
            // ELIMINAR EL ESPACIO UTILIZADO
            // =================================================

            unset(
                $espacios[$indiceEspacioEncontrado]
            );

            $espacios =
                array_values($espacios);

            // =================================================
            // ESPACIO A LA DERECHA
            // =================================================

            $largoDerecha =
                $espacio['largo']
                -
                $largoUtilizado
                -
                $kerf;

            if ($largoDerecha > 0) {

                $espacios[] = [

                    'x' =>
                    $xFranja
                        + $largoUtilizado
                        + $kerf,

                    'y' =>
                    $yFranja,

                    'largo' =>
                    $largoDerecha,

                    'ancho' =>
                    $espacio['ancho'],
                ];
            }

            // =================================================
            // ESPACIO ABAJO
            // =================================================

            $anchoAbajo =
                $espacio['ancho']
                -
                $altoFranja
                -
                $kerf;

            if ($anchoAbajo > 0) {

                $espacios[] = [

                    'x' =>
                    $xFranja,

                    'y' =>
                    $yFranja
                        + $altoFranja
                        + $kerf,

                    'largo' =>
                    $largoUtilizado,

                    'ancho' =>
                    $anchoAbajo,
                ];
            }

            // =================================================
            // CORTE TRANSVERSAL
            // =================================================

            $cortes[] = [

                'tipo' =>
                'transversal',

                'x' =>
                $xFranja,

                'y' =>
                $yFranja,

                'longitud' =>
                $largoUtilizado,

                'kerf' =>
                $kerf,

                'franja_ancho' =>
                $altoFranja,

                'piezas' =>
                $piezasColocadas,

                'orden' =>
                count($cortes) + 1,
            ];

            // =================================================
            // CORTES LONGITUDINALES
            // =================================================

            foreach (
                $piezasColocadas
                as $indiceCorte => $piezaColocada
            ) {

                // No hacer corte después de la última pieza
                if (
                    $indiceCorte
                    >=
                    count($piezasColocadas) - 1
                ) {
                    continue;
                }

                $xCorte =
                    $piezaColocada['x']
                    +
                    $piezaColocada['largo']
                    +
                    ($kerf / 2);

                $cortes[] = [

                    'tipo' =>
                    'longitudinal',

                    'x' =>
                    $xCorte,

                    'y' =>
                    $yFranja,

                    'longitud' =>
                    $altoFranja,

                    'kerf' =>
                    $kerf,

                    'pieza_id' =>
                    $piezaColocada['pieza_id'],

                    'pieza_etiqueta' =>
                    $piezaColocada['etiqueta'],

                    'orden' =>
                    count($cortes) + 1,
                ];
            }
        }

        // =====================================================
        // CALCULAR SOBRANTE GLOBAL
        // =====================================================

        $areaOcupadaFranjas = 0;

        foreach ($franjasColocadas as $franjaColocada) {

            $areaOcupadaFranjas +=
                $franjaColocada['largo']
                *
                $franjaColocada['ancho'];
        }

        $areaHoja =
            $largoHoja
            *
            $anchoHoja;

        $areaSobrante =
            max(
                0,
                $areaHoja
                    -
                    $areaOcupadaFranjas
            );

        // =====================================================
        // RETAL PRINCIPAL
        // =====================================================

        $sobranteAlto = 0;

        if (count($espacios) > 0) {

            foreach ($espacios as $espacio) {

                $areaEspacio =
                    $espacio['largo']
                    *
                    $espacio['ancho'];

                if (
                    $areaEspacio
                    >
                    $sobranteAlto
                ) {
                    $sobranteAlto =
                        $areaEspacio;
                }
            }
        }

        // =====================================================
        // RESULTADO
        // =====================================================

        return [

            'hoja_id' =>
            $hoja['id'],

            'hoja_numero' =>
            $hoja['numero'] ?? 1,

            'largo_hoja' =>
            $hoja['largo'],

            'ancho_hoja' =>
            $hoja['ancho'],

            'franjas' =>
            $franjasColocadas,

            'piezas' =>
            count($franjasColocadas) > 0
                ? array_merge(
                    ...array_map(
                        fn($franja) =>
                        $franja['piezas'],
                        $franjasColocadas
                    )
                )
                : [],

            'cortes' =>
            $cortes,

            'alto_utilizado' =>
            $anchoHoja,

            'sobrante_alto' =>
            $sobranteAlto,

            'retal_hoja' => [

                'largo' =>
                $largoHoja,

                'ancho' =>
                $anchoHoja,

                'x' =>
                0,

                'y' =>
                0,

                'reutilizable' =>
                count($espacios) > 0,
            ],
        ];
    }

    private function analizarCortesDesdeColocacion(
        array $colocacion,
        array $hojas,
        array $configuracion
    ): array {

        $kerf = (float) ($configuracion['kerf'] ?? 0);

        $resultado = [];

        foreach ($hojas as $hoja) {

            $hojaId = $hoja['id'];
            $hojaNumero = $hoja['numero'];

            /*
         * =====================================================
         * PIEZAS DE ESTA HOJA
         * =====================================================
         */

            $piezasHoja = array_values(
                array_filter(
                    $colocacion['colocadas'],
                    function ($pieza) use ($hojaId, $hojaNumero) {

                        return
                            (int) $pieza['hoja_id'] === (int) $hojaId
                            &&
                            (int) $pieza['hoja_numero'] === (int) $hojaNumero;
                    }
                )
            );

            if (empty($piezasHoja)) {
                continue;
            }

            $largoHoja = (float) $hoja['largo'];
            $anchoHoja = (float) $hoja['ancho'];

            /*
         * =====================================================
         * CREAR RECTÁNGULOS DE LAS PIEZAS
         * =====================================================
         */

            $rectangulos = [];

            foreach ($piezasHoja as $pieza) {

                $x1 = (float) $pieza['x'];
                $y1 = (float) $pieza['y'];

                $x2 = $x1 + (float) $pieza['largo'];
                $y2 = $y1 + (float) $pieza['ancho'];

                $rectangulos[] = [
                    'pieza_id' => $pieza['pieza_id'],
                    'numero' => $pieza['numero'],

                    'x1' => $x1,
                    'y1' => $y1,

                    'x2' => $x2,
                    'y2' => $y2,

                    'largo' => (float) $pieza['largo'],
                    'ancho' => (float) $pieza['ancho'],
                ];
            }

            /*
         * =====================================================
         * CANDIDATOS DE CORTES VERTICALES
         * =====================================================
         */

            $candidatosVerticales = [];

            foreach ($rectangulos as $rectangulo) {

                $posiciones = [
                    $rectangulo['x1'],
                    $rectangulo['x2'],
                ];

                foreach ($posiciones as $x) {

                    if ($x <= 0 || $x >= $largoHoja) {
                        continue;
                    }

                    $candidatosVerticales[] = $x;
                }
            }

            /*
         * Eliminar duplicados
         */

            $candidatosVerticales = array_values(
                array_unique(
                    array_map(
                        function ($valor) {
                            return round($valor, 3);
                        },
                        $candidatosVerticales
                    )
                )
            );

            sort($candidatosVerticales);

            /*
         * =====================================================
         * AGRUPAR POSICIONES SEPARADAS POR EL KERF
         *
         * Ejemplo:
         *
         * 1200
         * 1203
         *
         * representa una sola zona de corte.
         * =====================================================
         */

            $candidatosVerticalesAgrupados = [];

            foreach ($candidatosVerticales as $x) {

                if (empty($candidatosVerticalesAgrupados)) {

                    $candidatosVerticalesAgrupados[] = $x;

                    continue;
                }

                $ultimoIndice =
                    count($candidatosVerticalesAgrupados) - 1;

                $ultimo =
                    $candidatosVerticalesAgrupados[$ultimoIndice];

                if (
                    $kerf > 0
                    &&
                    abs($x - $ultimo - $kerf) < 0.01
                ) {

                    $candidatosVerticalesAgrupados[$ultimoIndice] =
                        round(
                            ($ultimo + $x) / 2,
                            3
                        );
                } else {

                    $candidatosVerticalesAgrupados[] = $x;
                }
            }

            $candidatosVerticales =
                $candidatosVerticalesAgrupados;

            /*
         * =====================================================
         * VALIDAR CORTES VERTICALES
         * =====================================================
         */

            $cortesVerticales = [];

            foreach ($candidatosVerticales as $x) {

                $intervalos = [];

                foreach ($rectangulos as $rectangulo) {

                    /*
                 * La pieza bloquea el corte si X está
                 * estrictamente dentro de la pieza.
                 */

                    if (
                        $x > $rectangulo['x1']
                        &&
                        $x < $rectangulo['x2']
                    ) {

                        $intervalos[] = [
                            'inicio' => $rectangulo['y1'],
                            'fin' => $rectangulo['y2'],
                        ];
                    }
                }

                /*
             * Si ninguna pieza atraviesa X,
             * existe una separación completa.
             */

                if (empty($intervalos)) {

                    $cortesVerticales[] = [
                        'tipo' => 'vertical',
                        'posicion' => $x,
                        'inicio' => 0,
                        'fin' => $anchoHoja,
                        'longitud' => $anchoHoja,
                        'kerf' => $kerf,
                    ];

                    continue;
                }

                /*
             * Ordenar intervalos.
             */

                usort(
                    $intervalos,
                    function ($a, $b) {
                        return $a['inicio'] <=> $b['inicio'];
                    }
                );

                /*
             * Unificar intervalos que se tocan o se cruzan.
             */

                $intervalosUnidos = [];

                foreach ($intervalos as $intervalo) {

                    if (empty($intervalosUnidos)) {

                        $intervalosUnidos[] = $intervalo;

                        continue;
                    }

                    $ultimoIndice =
                        count($intervalosUnidos) - 1;

                    if (
                        $intervalo['inicio']
                        <=
                        $intervalosUnidos[$ultimoIndice]['fin']
                    ) {

                        $intervalosUnidos[$ultimoIndice]['fin'] =
                            max(
                                $intervalosUnidos[$ultimoIndice]['fin'],
                                $intervalo['fin']
                            );
                    } else {

                        $intervalosUnidos[] = $intervalo;
                    }
                }

                /*
             * Buscar espacios libres donde podría pasar
             * la sierra.
             */

                $inicioLibre = 0;

                foreach ($intervalosUnidos as $intervalo) {

                    if ($intervalo['inicio'] > $inicioLibre) {

                        $cortesVerticales[] = [
                            'tipo' => 'vertical',
                            'posicion' => $x,
                            'inicio' => $inicioLibre,
                            'fin' => $intervalo['inicio'],
                            'longitud' =>
                            $intervalo['inicio'] - $inicioLibre,
                            'kerf' => $kerf,
                        ];
                    }

                    $inicioLibre =
                        max(
                            $inicioLibre,
                            $intervalo['fin']
                        );
                }

                /*
             * Espacio libre después del último intervalo.
             */

                if ($inicioLibre < $anchoHoja) {

                    $cortesVerticales[] = [
                        'tipo' => 'vertical',
                        'posicion' => $x,
                        'inicio' => $inicioLibre,
                        'fin' => $anchoHoja,
                        'longitud' =>
                        $anchoHoja - $inicioLibre,
                        'kerf' => $kerf,
                    ];
                }
            }

            /*
         * =====================================================
         * CANDIDATOS DE CORTES HORIZONTALES
         * =====================================================
         */

            $candidatosHorizontales = [];

            foreach ($rectangulos as $rectangulo) {

                $posiciones = [
                    $rectangulo['y1'],
                    $rectangulo['y2'],
                ];

                foreach ($posiciones as $y) {

                    if ($y <= 0 || $y >= $anchoHoja) {
                        continue;
                    }

                    $candidatosHorizontales[] = $y;
                }
            }

            /*
         * Eliminar duplicados
         */

            $candidatosHorizontales = array_values(
                array_unique(
                    array_map(
                        function ($valor) {
                            return round($valor, 3);
                        },
                        $candidatosHorizontales
                    )
                )
            );

            sort($candidatosHorizontales);

            /*
         * =====================================================
         * AGRUPAR POSICIONES SEPARADAS POR EL KERF
         * =====================================================
         */

            $candidatosHorizontalesAgrupados = [];

            foreach ($candidatosHorizontales as $y) {

                if (empty($candidatosHorizontalesAgrupados)) {

                    $candidatosHorizontalesAgrupados[] = $y;

                    continue;
                }

                $ultimoIndice =
                    count($candidatosHorizontalesAgrupados) - 1;

                $ultimo =
                    $candidatosHorizontalesAgrupados[$ultimoIndice];

                if (
                    $kerf > 0
                    &&
                    abs($y - $ultimo - $kerf) < 0.01
                ) {

                    $candidatosHorizontalesAgrupados[$ultimoIndice] =
                        round(
                            ($ultimo + $y) / 2,
                            3
                        );
                } else {

                    $candidatosHorizontalesAgrupados[] = $y;
                }
            }

            $candidatosHorizontales =
                $candidatosHorizontalesAgrupados;

            /*
         * =====================================================
         * VALIDAR CORTES HORIZONTALES
         * =====================================================
         */

            $cortesHorizontales = [];

            foreach ($candidatosHorizontales as $y) {

                $intervalos = [];

                foreach ($rectangulos as $rectangulo) {

                    /*
                 * La pieza bloquea el corte si Y está
                 * estrictamente dentro de la pieza.
                 */

                    if (
                        $y > $rectangulo['y1']
                        &&
                        $y < $rectangulo['y2']
                    ) {

                        $intervalos[] = [
                            'inicio' => $rectangulo['x1'],
                            'fin' => $rectangulo['x2'],
                        ];
                    }
                }

                /*
             * Si ninguna pieza atraviesa Y,
             * existe una separación completa.
             */

                if (empty($intervalos)) {

                    $cortesHorizontales[] = [
                        'tipo' => 'horizontal',
                        'posicion' => $y,
                        'inicio' => 0,
                        'fin' => $largoHoja,
                        'longitud' => $largoHoja,
                        'kerf' => $kerf,
                    ];

                    continue;
                }

                /*
             * Ordenar intervalos.
             */

                usort(
                    $intervalos,
                    function ($a, $b) {
                        return $a['inicio'] <=> $b['inicio'];
                    }
                );

                /*
             * Unificar intervalos.
             */

                $intervalosUnidos = [];

                foreach ($intervalos as $intervalo) {

                    if (empty($intervalosUnidos)) {

                        $intervalosUnidos[] = $intervalo;

                        continue;
                    }

                    $ultimoIndice =
                        count($intervalosUnidos) - 1;

                    if (
                        $intervalo['inicio']
                        <=
                        $intervalosUnidos[$ultimoIndice]['fin']
                    ) {

                        $intervalosUnidos[$ultimoIndice]['fin'] =
                            max(
                                $intervalosUnidos[$ultimoIndice]['fin'],
                                $intervalo['fin']
                            );
                    } else {

                        $intervalosUnidos[] = $intervalo;
                    }
                }

                /*
             * Buscar espacios libres.
             */

                $inicioLibre = 0;

                foreach ($intervalosUnidos as $intervalo) {

                    if ($intervalo['inicio'] > $inicioLibre) {

                        $cortesHorizontales[] = [
                            'tipo' => 'horizontal',
                            'posicion' => $y,
                            'inicio' => $inicioLibre,
                            'fin' => $intervalo['inicio'],
                            'longitud' =>
                            $intervalo['inicio'] - $inicioLibre,
                            'kerf' => $kerf,
                        ];
                    }

                    $inicioLibre =
                        max(
                            $inicioLibre,
                            $intervalo['fin']
                        );
                }

                /*
             * Espacio libre después del último intervalo.
             */

                if ($inicioLibre < $largoHoja) {

                    $cortesHorizontales[] = [
                        'tipo' => 'horizontal',
                        'posicion' => $y,
                        'inicio' => $inicioLibre,
                        'fin' => $largoHoja,
                        'longitud' =>
                        $largoHoja - $inicioLibre,
                        'kerf' => $kerf,
                    ];
                }
            }

            /*
         * =====================================================
         * ORDENAR RESULTADOS
         * =====================================================
         */

            usort(
                $cortesVerticales,
                function ($a, $b) {
                    return $a['posicion'] <=> $b['posicion'];
                }
            );

            usort(
                $cortesHorizontales,
                function ($a, $b) {
                    return $a['posicion'] <=> $b['posicion'];
                }
            );

            /*
         * =====================================================
         * RESULTADO DE LA HOJA
         * =====================================================
         */

            $resultado[] = [
                'hoja_id' => $hojaId,
                'hoja_numero' => $hojaNumero,

                'largo' => $largoHoja,
                'ancho' => $anchoHoja,

                'piezas' => $piezasHoja,

                'cortes_verticales' => $cortesVerticales,
                'cortes_horizontales' => $cortesHorizontales,

                'cantidad_cortes_verticales' =>
                count($cortesVerticales),

                'cantidad_cortes_horizontales' =>
                count($cortesHorizontales),

                'cantidad_cortes' =>
                count($cortesVerticales)
                    +
                    count($cortesHorizontales),
            ];
        }

        return $resultado;
    }

    private function construirArbolCortes(array $resultadoHoja): array
    {
        $franjas = $resultadoHoja['franjas'] ?? [];
        $cortes = $resultadoHoja['cortes'] ?? [];

        $largoHoja = 0;
        $anchoHoja = 0;

        /*
        | DIMENSIONES DE LA HOJA
        |--------------------------------------------------------------------------
        |--------------------------------------------------------------------------
        */

        if (!empty($franjas)) {
            $largoHoja = (float) $franjas[0]['largo'];
        } elseif (!empty($resultadoHoja['retal_hoja'])) {
            $largoHoja = (float) $resultadoHoja['retal_hoja']['largo'];
        }

        $anchoHoja =
            (float) ($resultadoHoja['alto_utilizado'] ?? 0) +
            (float) ($resultadoHoja['sobrante_alto'] ?? 0);


        /*
        |--------------------------------------------------------------------------
        | NODO RAÍZ: HOJA
        |--------------------------------------------------------------------------
        */

        $hoja = [
            'tipo' => 'hoja',
            'hoja_id' => $resultadoHoja['hoja_id'],
            'hoja_numero' => $resultadoHoja['hoja_numero'],
            'x' => 0,
            'y' => 0,
            'largo' => $largoHoja,
            'ancho' => $anchoHoja,
            'hijos' => [],
        ];


        /*
        | NODO ACTUAL
        |--------------------------------------------------------------------------
    |--------------------------------------------------------------------------
    |
    | Este puntero permite construir:
    |
    | HOJA
    |   └── CORTE
    |       └── RESTO
    |           └── CORTE
    |               └── RESTO
    |
    */

        $nodoActual = &$hoja;


        /*
    |--------------------------------------------------------------------------
    | 1. CONSTRUIR CORTES TRANSVERSALES
    |--------------------------------------------------------------------------
    */

        foreach ($franjas as $indice => $franja) {

            $corteFranja = null;

            foreach ($cortes as $corte) {

                if (
                    ($corte['tipo'] ?? '') === 'transversal' &&
                    isset($corte['franja_ancho']) &&
                    (float) $corte['franja_ancho'] === (float) $franja['ancho'] &&
                    (float) $corte['y'] === (float) $franja['y']
                ) {
                    $corteFranja = $corte;
                    break;
                }
            }

            if ($corteFranja === null) {
                continue;
            }


            /*
        |--------------------------------------------------------------------------
        | NODO CORTE TRANSVERSAL
        |--------------------------------------------------------------------------
        */

            $nodoCorte = [
                'tipo' => 'corte',
                'orden' => $corteFranja['orden'],
                'orientacion' => $corteFranja['tipo'],
                'x' => $corteFranja['x'],
                'y' => $corteFranja['y'],
                'longitud' => $corteFranja['longitud'],
                'kerf' => $corteFranja['kerf'],
                'resultado_1' => null,
                'resultado_2' => null,
            ];


            /*
        |--------------------------------------------------------------------------
        | NODO FRANJA
        |--------------------------------------------------------------------------
        */

            $nodoFranja = [
                'tipo' => 'franja',
                'indice' => $indice,
                'material_id' => $franja['material_id'],
                'x' => $franja['x'],
                'y' => $franja['y'],
                'largo' => $franja['largo'],
                'ancho' => $franja['ancho'],
                'largo_utilizado' => $franja['largo_utilizado'],
                'sobrante_largo' => $franja['sobrante_largo'],
                'retal' => $franja['retal'] ?? null,
                'hijos' => [],
            ];


            /*
        |--------------------------------------------------------------------------
        | PIEZAS DE LA FRANJA
        |--------------------------------------------------------------------------
        */

            foreach ($franja['piezas'] as $pieza) {

                $nodoPieza = [
                    'tipo' => 'pieza',
                    'pieza_id' => $pieza['pieza_id'],
                    'numero' => $pieza['numero'],
                    'etiqueta' => $pieza['etiqueta'],
                    'x' => $pieza['x'],
                    'y' => $pieza['y'],
                    'largo' => $pieza['largo'],
                    'ancho' => $pieza['ancho'],
                    'rotada' => $pieza['rotada'],
                ];

                $nodoFranja['hijos'][] = $nodoPieza;
            }


            /*
        |--------------------------------------------------------------------------
        | RETAL DE LA FRANJA
        |--------------------------------------------------------------------------
        */

            if (!empty($franja['retal'])) {

                $nodoFranja['hijos'][] = [
                    'tipo' => 'retal',
                    'origen' => 'franja',
                    'largo' => $franja['retal']['largo'],
                    'ancho' => $franja['retal']['ancho'],
                    'x' => $franja['retal']['x'],
                    'y' => $franja['retal']['y'],
                    'reutilizable' => $franja['retal']['reutilizable'],
                ];
            }


            /*
        |--------------------------------------------------------------------------
        | RESULTADO 1 DEL CORTE TRANSVERSAL
        |--------------------------------------------------------------------------
        */

            $nodoCorte['resultado_1'] = $nodoFranja;


            /*
        |--------------------------------------------------------------------------
        | RESULTADO 2 = RESTO DE LA HOJA
        |--------------------------------------------------------------------------
        */

            $altoRestante =
                $anchoHoja -
                (
                    (float) $franja['y'] +
                    (float) $franja['ancho']
                );


            if ($altoRestante > 0) {

                $nodoCorte['resultado_2'] = [
                    'tipo' => 'resto',
                    'x' => 0,
                    'y' => (
                        (float) $franja['y'] +
                        (float) $franja['ancho']
                    ),
                    'largo' => $largoHoja,
                    'ancho' => $altoRestante,
                    'hijos' => [],
                ];
            }


            /*
        |--------------------------------------------------------------------------
        | AGREGAR CORTE A LA RAMA ACTUAL
        |--------------------------------------------------------------------------
        */

            $nodoActual['hijos'][] = $nodoCorte;


            /*
        |--------------------------------------------------------------------------
        | BAJAR AL RESTO
        |--------------------------------------------------------------------------
        */

            if ($nodoCorte['resultado_2'] !== null) {

                $indiceUltimo = count($nodoActual['hijos']) - 1;

                $nodoActual['hijos'][$indiceUltimo]['resultado_2']['hijos'] = [];

                $nodoActual =
                    &$nodoActual['hijos'][$indiceUltimo]['resultado_2'];
            }
        }


        /*
|--------------------------------------------------------------------------
| 2. INTEGRAR CORTES LONGITUDINALES
|--------------------------------------------------------------------------
|
| Recorremos RECURSIVAMENTE todo el árbol porque los cortes
| transversales están anidados dentro de resultado_2.
|
| Ejemplo:
|
| HOJA
|   └── CORTE 1
|       └── RESTO
|           └── CORTE 2
|               └── RESTO
|                   └── CORTE 4
|
| Cada franja puede contener además cortes longitudinales:
|
| FRANJA
|   └── CORTE 3
|       ├── PIEZA
|       └── RESTO
|           └── PIEZA
|
*/

        $integrarCortesLongitudinales = function (&$nodos) use (
            &$integrarCortesLongitudinales,
            $cortes
        ) {

            foreach ($nodos as &$nodo) {

                /*
        |--------------------------------------------------------------------------
        | Solo nos interesan nodos de tipo CORTE
        |--------------------------------------------------------------------------
        */

                if (($nodo['tipo'] ?? '') !== 'corte') {
                    continue;
                }


                /*
        |--------------------------------------------------------------------------
        | Buscar la franja resultante del corte transversal
        |--------------------------------------------------------------------------
        */

                if (
                    !isset($nodo['resultado_1']) ||
                    ($nodo['resultado_1']['tipo'] ?? '') !== 'franja'
                ) {
                    /*
            | Aunque no sea una franja, todavía debemos seguir
            | recorriendo resultado_2.
            */
                } else {

                    $franja = &$nodo['resultado_1'];


                    /*
            |--------------------------------------------------------------------------
            | BUSCAR CORTES LONGITUDINALES DE ESTA FRANJA
            |--------------------------------------------------------------------------
            */

                    $cortesLongitudinales = [];

                    foreach ($cortes as $corte) {

                        if (($corte['tipo'] ?? '') !== 'longitudinal') {
                            continue;
                        }

                        /*
                | El Y del corte longitudinal debe coincidir
                | con el Y de la franja.
                */

                        if (
                            abs(
                                (float) ($corte['y'] ?? 0) -
                                    (float) ($franja['y'] ?? 0)
                            ) < 0.01
                        ) {
                            $cortesLongitudinales[] = $corte;
                        }
                    }


                    /*
            |--------------------------------------------------------------------------
            | ORDENAR CORTES LONGITUDINALES
            |--------------------------------------------------------------------------
            */

                    usort(
                        $cortesLongitudinales,
                        function ($a, $b) {
                            return ($a['orden'] ?? 0) <=>
                                ($b['orden'] ?? 0);
                        }
                    );


                    /*
            |--------------------------------------------------------------------------
            | SI EXISTEN CORTES LONGITUDINALES
            |--------------------------------------------------------------------------
            */

                    if (!empty($cortesLongitudinales)) {

                        /*
                |--------------------------------------------------------------------------
                | Guardamos las piezas originales de la franja
                |--------------------------------------------------------------------------
                */

                        $piezasFranja = [];

                        foreach ($franja['hijos'] as $hijo) {

                            if (($hijo['tipo'] ?? '') === 'pieza') {
                                $piezasFranja[] = $hijo;
                            }
                        }


                        /*
                |--------------------------------------------------------------------------
                | Empezamos desde la franja
                |--------------------------------------------------------------------------
                */

                        $nodoActualFranja = &$franja;


                        /*
                |--------------------------------------------------------------------------
                | CONSTRUIR CADENA DE CORTES LONGITUDINALES
                |--------------------------------------------------------------------------
                */

                        foreach (
                            $cortesLongitudinales
                            as $indiceCorte => $corteLongitudinal
                        ) {

                            $xCorte =
                                (float) ($corteLongitudinal['x'] ?? 0);


                            /*
                    |--------------------------------------------------------------------------
                    | ENCONTRAR LA PIEZA QUE ESTÁ ANTES DEL CORTE
                    |--------------------------------------------------------------------------
                    */

                            $piezaCorte = null;

                            foreach ($piezasFranja as $pieza) {

                                $xPieza =
                                    (float) ($pieza['x'] ?? 0);

                                $finPieza =
                                    $xPieza +
                                    (float) ($pieza['largo'] ?? 0);


                                if (
                                    $xPieza <= $xCorte &&
                                    $finPieza <= ($xCorte + 0.01)
                                ) {

                                    if (
                                        $piezaCorte === null ||
                                        $xPieza >
                                        (float) ($piezaCorte['x'] ?? 0)
                                    ) {
                                        $piezaCorte = $pieza;
                                    }
                                }
                            }


                            /*
                    |--------------------------------------------------------------------------
                    | CREAR NODO DEL CORTE
                    |--------------------------------------------------------------------------
                    */

                            $nodoCorteLongitudinal = [
                                'tipo' => 'corte',
                                'orden' => $corteLongitudinal['orden'],
                                'orientacion' => $corteLongitudinal['tipo'],
                                'x' => $corteLongitudinal['x'],
                                'y' => $corteLongitudinal['y'],
                                'longitud' => $corteLongitudinal['longitud'],
                                'kerf' => $corteLongitudinal['kerf'],
                                'pieza_id' =>
                                $corteLongitudinal['pieza_id'] ?? null,
                                'pieza_etiqueta' =>
                                $corteLongitudinal['pieza_etiqueta'] ?? null,
                                'resultado_1' => null,
                                'resultado_2' => null,
                            ];


                            /*
                    |--------------------------------------------------------------------------
                    | RESULTADO 1 = PIEZA
                    |--------------------------------------------------------------------------
                    */

                            if ($piezaCorte !== null) {

                                $nodoCorteLongitudinal['resultado_1'] =
                                    $piezaCorte;
                            }


                            /*
                    |--------------------------------------------------------------------------
                    | ENCONTRAR PIEZAS DESPUÉS DEL CORTE
                    |--------------------------------------------------------------------------
                    */

                            $piezasRestantes = [];

                            foreach ($piezasFranja as $pieza) {

                                $xPieza =
                                    (float) ($pieza['x'] ?? 0);


                                if ($xPieza > $xCorte) {

                                    $piezasRestantes[] = $pieza;
                                }
                            }


                            /*
                    |--------------------------------------------------------------------------
                    | ENCONTRAR EL FINAL ÚTIL DE LA FRANJA
                    |--------------------------------------------------------------------------
                    */

                            $finUtilFranja =
                                (float) ($franja['largo_utilizado'] ?? 0);


                            /*
                    |--------------------------------------------------------------------------
                    | BUSCAR EL INICIO DEL SIGUIENTE RESTO
                    |--------------------------------------------------------------------------
                    */

                            $xInicioResto = $xCorte + ($corteLongitudinal['kerf'] / 2);


                            /*
                    |--------------------------------------------------------------------------
                    | CALCULAR LARGO DEL RESTO
                    |--------------------------------------------------------------------------
                    */

                            $esUltimoCorteLongitudinal =
                                $indiceCorte === count($cortesLongitudinales) - 1;

                            if (
                                $esUltimoCorteLongitudinal &&
                                !empty($franja['retal'])
                            ) {
                                $finResto = (float) $franja['largo'];
                            } else {
                                $finResto = $finUtilFranja;
                            }

                            $restoLargo =
                                $finResto -
                                $xInicioResto;


                            Log::info('=== DEBUG CORTE LONGITUDINAL ===', [
                                'indiceCorte' => $indiceCorte,
                                'cantidadCortesLongitudinales' => count($cortesLongitudinales),
                                'esUltimoCorteLongitudinal' => $esUltimoCorteLongitudinal,
                                'xCorte' => $xCorte,
                                'xInicioResto' => $xInicioResto,
                                'finUtilFranja' => $finUtilFranja,
                                'finResto' => $finResto,
                                'restoLargo' => $restoLargo,
                                'largoFranja' => $franja['largo'],
                                'largoUtilizadoFranja' => $franja['largo_utilizado'],
                                'retal' => $franja['retal'] ?? null,
                            ]);
                            /*
                    |--------------------------------------------------------------------------
                    | CREAR RESTO
                    |--------------------------------------------------------------------------
                    */

                            if ($restoLargo > 0) {

                                $nodoResto = [
                                    'tipo' => 'resto',
                                    'x' => $xInicioResto,
                                    'y' => $franja['y'],
                                    'largo' => $restoLargo,
                                    'ancho' => $franja['ancho'],
                                    'hijos' => [],
                                ];


                                /*
                        |--------------------------------------------------------------------------
                        | AGREGAR PIEZAS RESTANTES
                        |--------------------------------------------------------------------------
                        */

                                foreach ($piezasRestantes as $piezaRestante) {

                                    $nodoResto['hijos'][] =
                                        $piezaRestante;
                                }


                                /*
                        |--------------------------------------------------------------------------
                        | SI NO HAY MÁS PIEZAS, AGREGAR RETAL
                        |--------------------------------------------------------------------------
                        */

                                if (
                                    $esUltimoCorteLongitudinal &&
                                    !empty($franja['retal'])
                                ) {

                                    $nodoResto['hijos'][] = [
                                        'tipo' => 'retal',
                                        'origen' => 'franja',
                                        'largo' =>
                                        $franja['retal']['largo'],
                                        'ancho' =>
                                        $franja['retal']['ancho'],
                                        'x' =>
                                        $franja['retal']['x'],
                                        'y' =>
                                        $franja['retal']['y'],
                                        'reutilizable' =>
                                        $franja['retal']['reutilizable'],
                                    ];
                                }


                                $nodoCorteLongitudinal['resultado_2'] =
                                    $nodoResto;
                            }


                            /*
                    |--------------------------------------------------------------------------
                    | AGREGAR EL CORTE AL NODO ACTUAL
                    |--------------------------------------------------------------------------
                    */

                            $nodoActualFranja['hijos'] = [
                                $nodoCorteLongitudinal
                            ];


                            /*
                    |--------------------------------------------------------------------------
                    | BAJAR AL RESTO
                    |--------------------------------------------------------------------------
                    */

                            if (
                                $nodoCorteLongitudinal['resultado_2'] !== null
                            ) {

                                $nodoActualFranja =
                                    &$nodoActualFranja['hijos'][0]['resultado_2'];
                            }
                        }

                        unset($nodoActualFranja);
                    }

                    unset($franja);
                }


                /*
        |--------------------------------------------------------------------------
        | SEGUIR RECORRIENDO resultado_1
        |--------------------------------------------------------------------------
        */

                if (isset($nodo['resultado_1'])) {

                    $resultado1 = &$nodo['resultado_1'];

                    if (isset($resultado1['hijos'])) {

                        $integrarCortesLongitudinales(
                            $resultado1['hijos']
                        );
                    }

                    unset($resultado1);
                }


                /*
        |--------------------------------------------------------------------------
        | SEGUIR RECORRIENDO resultado_2
        |--------------------------------------------------------------------------
        */

                if (isset($nodo['resultado_2'])) {

                    $resultado2 = &$nodo['resultado_2'];

                    if (isset($resultado2['hijos'])) {

                        $integrarCortesLongitudinales(
                            $resultado2['hijos']
                        );
                    }

                    unset($resultado2);
                }
            }

            unset($nodo);
        };


        /*
|--------------------------------------------------------------------------
| EJECUTAR RECORRIDO DEL ÁRBOL
|--------------------------------------------------------------------------
*/

        $integrarCortesLongitudinales(
            $hoja['hijos']
        );

        /*
    |--------------------------------------------------------------------------
    | 3. RETAL FINAL DE LA HOJA
    |--------------------------------------------------------------------------
    */

        if (!empty($resultadoHoja['retal_hoja'])) {

            /*
        | Si existe una rama de resto, agregar ahí el retal.
        */

            $retalHoja = [
                'tipo' => 'retal_hoja',
                'origen' => 'hoja',
                'largo' => $resultadoHoja['retal_hoja']['largo'],
                'ancho' => $resultadoHoja['retal_hoja']['ancho'],
                'x' => $resultadoHoja['retal_hoja']['x'],
                'y' => $resultadoHoja['retal_hoja']['y'],
                'reutilizable' => $resultadoHoja['retal_hoja']['reutilizable'],
            ];

            $nodoActual['hijos'][] = $retalHoja;
        }


        unset($nodoActual);

        return [$hoja];
    }

    private function calcularEstadisticasCortes(
        array $resultadoHoja
    ): array {
        $numeroCortes = 0;
        $longitudTotal = 0;
        $kerfTotal = 0;

        /*
    |--------------------------------------------------------------------------
    | CORTES
    |--------------------------------------------------------------------------
    */

        foreach ($resultadoHoja['cortes'] as $corte) {

            $numeroCortes++;

            $longitudTotal +=
                (float) ($corte['longitud'] ?? 0);

            $kerfTotal +=
                (float) ($corte['kerf'] ?? 0);
        }

        /*
    |--------------------------------------------------------------------------
    | AREA DE LA HOJA
    |--------------------------------------------------------------------------
    */

        $largoHoja =
            (float) ($resultadoHoja['largo_hoja'] ?? 0);

        $anchoHoja =
            (float) ($resultadoHoja['ancho_hoja'] ?? 0);

        $areaHoja =
            $largoHoja * $anchoHoja;

        /*
    |--------------------------------------------------------------------------
    | AREA DE PIEZAS
    |--------------------------------------------------------------------------
    */

        $areaPiezas = 0;

        foreach ($resultadoHoja['piezas'] as $pieza) {

            $largoPieza =
                (float) ($pieza['largo'] ?? 0);

            $anchoPieza =
                (float) ($pieza['ancho'] ?? 0);

            $areaPiezas +=
                $largoPieza * $anchoPieza;
        }

        /*
    |--------------------------------------------------------------------------
    | RETALES REUTILIZABLES
    |--------------------------------------------------------------------------
    */

        $retales = [];
        $areaRetales = 0;

        foreach ($resultadoHoja['franjas'] as $franja) {

            if (
                isset($franja['retal']) &&
                !empty($franja['retal']['reutilizable'])
            ) {

                $retal = $franja['retal'];

                $largoRetal =
                    (float) ($retal['largo'] ?? 0);

                $anchoRetal =
                    (float) ($retal['ancho'] ?? 0);

                $areaRetal =
                    $largoRetal * $anchoRetal;

                $areaRetales += $areaRetal;

                $retales[] = [
                    'origen' => 'franja',
                    'largo' => $largoRetal,
                    'ancho' => $anchoRetal,
                    'area' => $areaRetal,
                    'x' => $retal['x'] ?? 0,
                    'y' => $retal['y'] ?? 0,
                    'reutilizable' => true,
                ];
            }
        }

        /*
    |--------------------------------------------------------------------------
    | RETAL FINAL DE LA HOJA
    |--------------------------------------------------------------------------
    */

        if (
            isset($resultadoHoja['retal_hoja']) &&
            !empty($resultadoHoja['retal_hoja']['reutilizable'])
        ) {

            $retal = $resultadoHoja['retal_hoja'];

            $largoRetal =
                (float) ($retal['largo'] ?? 0);

            $anchoRetal =
                (float) ($retal['ancho'] ?? 0);

            $areaRetal =
                $largoRetal * $anchoRetal;

            $areaRetales += $areaRetal;

            $retales[] = [
                'origen' => 'hoja',
                'largo' => $largoRetal,
                'ancho' => $anchoRetal,
                'area' => $areaRetal,
                'x' => $retal['x'] ?? 0,
                'y' => $retal['y'] ?? 0,
                'reutilizable' => true,
            ];
        }

        /*
    |--------------------------------------------------------------------------
    | AREA DE DESPERDICIO REAL
    |--------------------------------------------------------------------------
    */

        $areaDesperdicio =
            max(
                0,
                $areaHoja
                    - $areaPiezas
                    - $areaRetales
            );

        /*
    |--------------------------------------------------------------------------
    | PORCENTAJES
    |--------------------------------------------------------------------------
    */

        $porcentajeAprovechamiento = 0;

        $porcentajeRetales = 0;

        $porcentajeDesperdicio = 0;

        if ($areaHoja > 0) {

            $porcentajeAprovechamiento =
                ($areaPiezas / $areaHoja) * 100;

            $porcentajeRetales =
                ($areaRetales / $areaHoja) * 100;

            $porcentajeDesperdicio =
                ($areaDesperdicio / $areaHoja) * 100;
        }

        /*
    |--------------------------------------------------------------------------
    | RESULTADO
    |--------------------------------------------------------------------------
    */

        return [

            'numero_cortes' =>
            $numeroCortes,

            'longitud_total' =>
            $longitudTotal,

            'kerf_total' =>
            $kerfTotal,

            'largo_hoja' =>
            $largoHoja,

            'ancho_hoja' =>
            $anchoHoja,

            'area_hoja' =>
            $areaHoja,

            'area_piezas' =>
            $areaPiezas,

            'area_retales' =>
            $areaRetales,

            'area_desperdicio' =>
            $areaDesperdicio,

            'porcentaje_aprovechamiento' =>
            round(
                $porcentajeAprovechamiento,
                2
            ),

            'porcentaje_retales' =>
            round(
                $porcentajeRetales,
                2
            ),

            'porcentaje_desperdicio' =>
            round(
                $porcentajeDesperdicio,
                2
            ),

            'retales' =>
            $retales,
        ];
    }

    /**
     * Convierte los árboles recursivos de las hojas en una lista de cortes
     * ordenada en preorden: primero se realiza el corte del bloque padre y
     * después se procesan sus sub-bloques de izquierda a derecha / arriba abajo.
     *
     * Los nodos tipo "bloque" indican que el árbol no encontró una división
     * guillotina completa para esa zona; se reportan como pendientes de revisión.
     */
    private function generarListaCortesOrdenada(array $bloquesRecursivos): array
    {
        $resultado = [];

        foreach ($bloquesRecursivos as $bloqueHoja) {
            $cortes = [];
            $bloquesPendientes = [];
            $contador = 0;

            $recorrer = function (
                array $nodo,
                string $ruta = 'Tablero'
            ) use (&$recorrer, &$cortes, &$bloquesPendientes, &$contador) {
                $tipo = $nodo['tipo'] ?? '';

                if ($tipo === 'corte_vertical' || $tipo === 'corte_horizontal') {
                    $contador++;

                    $direccion = $nodo['corte']['direccion'] ?? (
                        $tipo === 'corte_vertical' ? 'vertical' : 'horizontal'
                    );

                    $posicion = (float) ($nodo['corte']['posicion'] ?? 0);
                    $origenX = (float) ($nodo['x'] ?? 0);
                    $origenY = (float) ($nodo['y'] ?? 0);
                    $largoBloque = (float) ($nodo['largo'] ?? 0);
                    $anchoBloque = (float) ($nodo['ancho'] ?? 0);
                    $longitud = (float) ($nodo['corte']['longitud'] ?? (
                        $direccion === 'vertical' ? $anchoBloque : $largoBloque
                    ));

                    $cortes[] = [
                        'orden' => $contador,
                        'ruta' => $ruta,
                        'tipo' => $direccion,
                        'descripcion' => $direccion === 'vertical'
                            ? 'Corte vertical (a lo ancho del tablero)'
                            : 'Corte horizontal (a lo largo del tablero)',
                        // Coordenada absoluta desde la esquina inferior/izquierda
                        // del tablero, y distancia desde el borde del bloque actual.
                        'posicion' => round($posicion, 2),
                        'distancia_borde_bloque' => round(
                            $direccion === 'vertical'
                                ? $posicion - $origenX
                                : $posicion - $origenY,
                            2
                        ),
                        'desde_x' => round(
                            $direccion === 'vertical' ? $posicion : $origenX,
                            2
                        ),
                        'desde_y' => round(
                            $direccion === 'vertical' ? $origenY : $posicion,
                            2
                        ),
                        'hasta_x' => round(
                            $direccion === 'vertical' ? $posicion : $origenX + $largoBloque,
                            2
                        ),
                        'hasta_y' => round(
                            $direccion === 'vertical' ? $origenY + $anchoBloque : $posicion,
                            2
                        ),
                        'longitud' => round($longitud, 2),
                        'kerf' => round((float) ($nodo['corte']['kerf'] ?? 0), 2),
                        'bloque' => [
                            'x' => round($origenX, 2),
                            'y' => round($origenY, 2),
                            'largo' => round($largoBloque, 2),
                            'ancho' => round($anchoBloque, 2),
                        ],
                    ];

                    // El orden físico es: corte padre, sub-bloque izquierdo/superior,
                    // y después sub-bloque derecho/inferior.
                    foreach (['izquierda', 'arriba', 'derecha', 'abajo'] as $rama) {
                        if (isset($nodo[$rama]) && is_array($nodo[$rama])) {
                            $nombreRama = [
                                'izquierda' => 'lado izquierdo',
                                'derecha' => 'lado derecho',
                                'arriba' => 'parte superior',
                                'abajo' => 'parte inferior',
                            ][$rama];

                            $recorrer($nodo[$rama], $ruta . ' > ' . $nombreRama);
                        }
                    }

                    return;
                }

                if ($tipo === 'bloque') {
                    $piezas = $nodo['piezas'] ?? [];
                    $bloquesPendientes[] = [
                        'ruta' => $ruta,
                        'x' => round((float) ($nodo['x'] ?? 0), 2),
                        'y' => round((float) ($nodo['y'] ?? 0), 2),
                        'largo' => round((float) ($nodo['largo'] ?? 0), 2),
                        'ancho' => round((float) ($nodo['ancho'] ?? 0), 2),
                        'cantidad_piezas' => count($piezas),
                        'motivo' => 'El algoritmo no encontró una división guillotina completa para este bloque.',
                    ];
                }
            };

            if (isset($bloqueHoja['arbol']) && is_array($bloqueHoja['arbol'])) {
                $recorrer($bloqueHoja['arbol']);
            }

            $resultado[] = [
                'hoja_id' => $bloqueHoja['hoja_id'] ?? null,
                'hoja_numero' => $bloqueHoja['hoja_numero'] ?? null,
                'cantidad_cortes' => count($cortes),
                'cortes' => $cortes,
                'lista_completa' => count($bloquesPendientes) === 0,
                'bloques_pendientes_revision' => $bloquesPendientes,
            ];
        }

        return $resultado;
    }

    private function detectarBloquesRecursivos(
        array $colocadas,
        float $x,
        float $y,
        float $largo,
        float $ancho,
        float $kerf
    ): array {

        // Piezas que están completamente dentro de este bloque
        $piezasBloque = [];

        foreach ($colocadas as $pieza) {

            $piezaDerecha = $pieza['x'] + $pieza['largo'];
            $piezaAbajo   = $pieza['y'] + $pieza['ancho'];

            if (
                $pieza['x'] >= $x &&
                $pieza['y'] >= $y &&
                $piezaDerecha <= $x + $largo + 0.01 &&
                $piezaAbajo <= $y + $ancho + 0.01
            ) {
                $piezasBloque[] = $pieza;
            }
        }

        // Si no hay piezas, es un retal
        if (count($piezasBloque) === 0) {
            return [
                'tipo' => 'retal',
                'x' => $x,
                'y' => $y,
                'largo' => $largo,
                'ancho' => $ancho,
            ];
        }

        // Si solamente hay una pieza, terminamos aquí
        if (count($piezasBloque) === 1) {

            $pieza = $piezasBloque[0];

            return [
                'tipo' => 'pieza',
                'pieza_id' => $pieza['pieza_id'],
                'numero' => $pieza['numero'],
                'etiqueta' => $pieza['etiqueta'],
                'x' => $pieza['x'],
                'y' => $pieza['y'],
                'largo' => $pieza['largo'],
                'ancho' => $pieza['ancho'],
                'rotada' => $pieza['rotada'],
            ];
        }

        /*
     * ==========================================
     * BUSCAR CORTE VERTICAL
     * ==========================================
     */

        $posicionesX = [];

        foreach ($piezasBloque as $pieza) {

            $derecha = $pieza['x'] + $pieza['largo'];

            if (
                $derecha > $x &&
                $derecha < $x + $largo
            ) {
                $posicionesX[] = $derecha;
            }

            if (
                $pieza['x'] > $x &&
                $pieza['x'] < $x + $largo
            ) {
                $posicionesX[] = $pieza['x'];
            }
        }

        usort($posicionesX, function ($a, $b) use ($x, $largo) {
            $centro = $x + ($largo / 2);

            return abs($b - $centro) <=> abs($a - $centro);
        });

        foreach ($posicionesX as $posicionX) {

            $izquierda = [];
            $derecha = [];

            foreach ($piezasBloque as $pieza) {

                $piezaDerecha = $pieza['x'] + $pieza['largo'];

                if ($piezaDerecha <= $posicionX + 0.01) {
                    $izquierda[] = $pieza;
                }

                if ($pieza['x'] >= $posicionX + $kerf - 0.01) {
                    $derecha[] = $pieza;
                }
            }

            if (
                count($izquierda) > 0 &&
                count($derecha) > 0
            ) {

                $anchoIzquierda = $posicionX - $x;
                $anchoDerecha =
                    ($x + $largo) -
                    ($posicionX + $kerf);

                if (
                    $anchoIzquierda > 0 &&
                    $anchoDerecha > 0
                ) {

                    return [
                        'tipo' => 'corte_vertical',

                        'x' => $posicionX,
                        'y' => $y,

                        'largo' => $largo,
                        'ancho' => $ancho,

                        'corte' => [
                            'direccion' => 'vertical',
                            'posicion' => $posicionX,
                            'longitud' => $ancho,
                            'kerf' => $kerf,
                        ],

                        'izquierda' =>
                        $this->detectarBloquesRecursivos(
                            $colocadas,
                            $x,
                            $y,
                            $anchoIzquierda,
                            $ancho,
                            $kerf
                        ),

                        'derecha' =>
                        $this->detectarBloquesRecursivos(
                            $colocadas,
                            $posicionX + $kerf,
                            $y,
                            $anchoDerecha,
                            $ancho,
                            $kerf
                        ),
                    ];
                }
            }
        }

        /*
     * ==========================================
     * BUSCAR CORTE HORIZONTAL
     * ==========================================
     */

        $posicionesY = [];

        foreach ($piezasBloque as $pieza) {

            $abajo = $pieza['y'] + $pieza['ancho'];

            if (
                $abajo > $y &&
                $abajo < $y + $ancho
            ) {
                $posicionesY[] = $abajo;
            }

            if (
                $pieza['y'] > $y &&
                $pieza['y'] < $y + $ancho
            ) {
                $posicionesY[] = $pieza['y'];
            }
        }

        sort($posicionesY);

        foreach ($posicionesY as $posicionY) {

            $arriba = [];
            $abajo = [];

            foreach ($piezasBloque as $pieza) {

                $piezaAbajo = $pieza['y'] + $pieza['ancho'];

                if ($piezaAbajo <= $posicionY + 0.01) {
                    $arriba[] = $pieza;
                }

                if ($pieza['y'] >= $posicionY + $kerf - 0.01) {
                    $abajo[] = $pieza;
                }
            }

            if (
                count($arriba) > 0 &&
                count($abajo) > 0
            ) {

                $altoArriba = $posicionY - $y;

                $altoAbajo =
                    ($y + $ancho) -
                    ($posicionY + $kerf);

                if (
                    $altoArriba > 0 &&
                    $altoAbajo > 0
                ) {

                    return [
                        'tipo' => 'corte_horizontal',

                        'x' => $x,
                        'y' => $posicionY,

                        'largo' => $largo,
                        'ancho' => $ancho,

                        'corte' => [
                            'direccion' => 'horizontal',
                            'posicion' => $posicionY,
                            'longitud' => $largo,
                            'kerf' => $kerf,
                        ],

                        'arriba' =>
                        $this->detectarBloquesRecursivos(
                            $colocadas,
                            $x,
                            $y,
                            $largo,
                            $altoArriba,
                            $kerf
                        ),

                        'abajo' =>
                        $this->detectarBloquesRecursivos(
                            $colocadas,
                            $x,
                            $posicionY + $kerf,
                            $largo,
                            $altoAbajo,
                            $kerf
                        ),
                    ];
                }
            }
        }

        /*
     * ==========================================
     * SI NO PODEMOS DIVIDIR
     * ==========================================
     */

        return [
            'tipo' => 'bloque',
            'x' => $x,
            'y' => $y,
            'largo' => $largo,
            'ancho' => $ancho,
            'piezas' => $piezasBloque,
        ];
    }
}
