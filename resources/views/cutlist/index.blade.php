<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>CutList Optimizer</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f6f8;
            color: #1f2937;
        }

        .topbar {
            height: 64px;
            background: #111827;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
        }

        .logo {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: .3px;
        }

        .logo span {
            color: #60a5fa;
        }

        .topbar-right {
            font-size: 13px;
            color: #d1d5db;
        }

        .container {
            max-width: 1450px;
            margin: 0 auto;
            padding: 28px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .page-header h1 {
            margin: 0;
            font-size: 28px;
        }

        .page-header p {
            margin: 6px 0 0;
            color: #6b7280;
            font-size: 14px;
        }

        .options {
            display: flex;
            gap: 10px;
        }

        button {
            border: none;
            border-radius: 8px;
            padding: 10px 16px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-secondary {
            background: white;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            margin-bottom: 22px;
            overflow: hidden;
        }

        .card-header {
            padding: 17px 20px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-size: 17px;
            font-weight: 700;
        }

        .card-subtitle {
            font-size: 12px;
            color: #6b7280;
            margin-top: 3px;
        }

        .btn-add {
            background: #eff6ff;
            color: #2563eb;
            border: 1px solid #bfdbfe;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
        }

        th {
            background: #f9fafb;
            color: #6b7280;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
            text-align: left;
            padding: 13px 16px;
            border-bottom: 1px solid #e5e7eb;
        }

        td {
            padding: 13px 16px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .number {
            font-variant-numeric: tabular-nums;
        }

        .material {
            display: inline-flex;
            align-items: center;
            padding: 5px 9px;
            border-radius: 6px;
            background: #f3f4f6;
            font-size: 12px;
            font-weight: 600;
        }

        .grain {
            font-size: 18px;
            font-weight: bold;
        }

        .actions {
            text-align: right;
        }

        .delete {
            color: #dc2626;
            background: transparent;
            padding: 5px 8px;
        }

        .optimization-options {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            padding: 20px;
        }

        .option {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 15px;
        }

        .option label {
            display: block;
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 8px;
        }

        .option input,
        .option select {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 7px;
            padding: 9px 10px;
            font-size: 14px;
            background: white;
        }

        .checks {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .check {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
        }

        .check input {
            width: auto;
        }

        .optimize-area {
            display: flex;
            justify-content: center;
            padding: 8px 0 30px;
        }

        .btn-optimize {
            min-width: 230px;
            padding: 14px 24px;
            font-size: 16px;
            background: #2563eb;
            color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(37, 99, 235, .18);
        }

        .btn-optimize:hover {
            background: #1d4ed8;
        }

        .empty-result {
            padding: 45px 20px;
            text-align: center;
            color: #9ca3af;
        }

        @media (max-width: 900px) {
            .optimization-options {
                grid-template-columns: repeat(2, 1fr);
            }

            .page-header {
                align-items: flex-start;
                gap: 15px;
                flex-direction: column;
            }
        }

        @media (max-width: 600px) {
            .container {
                padding: 15px;
            }

            .optimization-options {
                grid-template-columns: 1fr;
            }

            .topbar {
                padding: 0 15px;
            }
        }

        /* ==============================
                     MODAL PIEZAS
            ============================== */

        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.55);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal {
            width: 100%;
            max-width: 650px;
            background: white;
            border-radius: 14px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 20px 24px;
            border-bottom: 1px solid #e5e7eb;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 20px;
        }

        .modal-header p {
            margin: 5px 0 0;
            font-size: 13px;
            color: #6b7280;
        }

        .modal-close {
            background: transparent;
            color: #6b7280;
            font-size: 28px;
            line-height: 1;
            padding: 0 5px;
        }

        .modal-close:hover {
            color: #111827;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            padding: 22px 24px 10px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        .form-group label,
        .section-label {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 7px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px 11px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            background: white;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-section {
            padding: 10px 24px;
        }

        .grain-options {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .grain-option {
            border: 1px solid #e5e7eb;
            border-radius: 9px;
            padding: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 13px;
        }

        .grain-option:hover {
            border-color: #93c5fd;
        }

        .grain-option input {
            margin: 0;
        }

        .grain-symbol {
            font-size: 20px;
            font-weight: 600;
        }

        .check-option {
            display: flex;
            align-items: center;
            gap: 9px;
            font-size: 14px;
            cursor: pointer;
        }

        .check-option input {
            width: 16px;
            height: 16px;
        }

        .form-message {
            margin: 5px 24px 0;
            padding: 10px 12px;
            border-radius: 7px;
            font-size: 13px;
            display: none;
        }

        .form-message.error {
            display: block;
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .form-message.success {
            display: block;
            background: #f0fdf4;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 20px 24px;
            margin-top: 10px;
            border-top: 1px solid #e5e7eb;
        }

        @media (max-width: 600px) {

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full {
                grid-column: auto;
            }

            .grain-options {
                grid-template-columns: 1fr;
            }

        }

        /* ==========================================
            CELDAS EDITABLES
        ========================================== */

        .pieza-editable {
            cursor: text;
            position: relative;
        }

        .pieza-editable:hover {
            background: #f8fafc;
        }

        .pieza-editable input,
        .pieza-editable select {
            width: 100%;
            min-width: 80px;
            box-sizing: border-box;
            padding: 6px 8px;
            border: 1px solid #2563eb;
            border-radius: 5px;
            outline: none;
            font-size: inherit;
            background: white;
        }

        .pieza-editable input:focus,
        .pieza-editable select:focus {
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.12);
        }

        .guardando-pieza {
            opacity: 0.6;
        }

        .pieza-error {
            background: #fef2f2 !important;
        }

        .pieza-check {
            width: 17px;
            height: 17px;
            cursor: pointer;
        }

        .hoja-editable {
            cursor: text;
            position: relative;
        }

        .hoja-editable:hover {
            background: #f8fafc;
        }

        .hoja-editable input,
        .hoja-editable select {
            width: 100%;
            min-width: 80px;
            box-sizing: border-box;
            padding: 6px 8px;
            border: 1px solid #2563eb;
            border-radius: 5px;
            outline: none;
            font-size: inherit;
            background: white;
        }

        .hoja-editable input:focus,
        .hoja-editable select:focus {
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.12);
        }

        /* ==========================================
             RESULTADO CUTLIST
        ========================================== */

        .resultado-resumen {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            padding: 20px;
        }

        .resultado-indicador {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 18px;
            text-align: center;
            background: #f8fafc;
        }

        .resultado-valor {
            font-size: 28px;
            font-weight: 700;
            color: #2563eb;
        }

        .resultado-label {
            margin-top: 5px;
            font-size: 13px;
            color: #64748b;
        }


        /* ==========================================
            HOJA
            ========================================== */

        .resultado-hoja {
            margin: 0 20px 20px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
            background: white;
        }

        .resultado-hoja-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 18px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        .resultado-hoja-titulo {
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
        }

        .resultado-hoja-subtitulo {
            margin-top: 4px;
            font-size: 13px;
            color: #64748b;
        }

        .resultado-hoja-aprovechamiento {
            font-size: 22px;
            font-weight: 700;
            color: #2563eb;
        }


        /* ==========================================
        DATOS DE LA HOJA
        ========================================== */

        .resultado-hoja-datos {
            display: flex;
            gap: 30px;
            padding: 14px 18px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 13px;
            color: #64748b;
        }

        .resultado-hoja-datos strong {
            color: #1e293b;
        }


        /* ==========================================
        PIEZAS
        ========================================== */

        .resultado-piezas {
            padding: 16px 18px 18px;
        }

        .resultado-piezas-titulo {
            font-size: 13px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 8px;
        }

        .resultado-piezas-lista {
            border: 1px solid #e2e8f0;
            border-radius: 7px;
            overflow: hidden;
        }

        .resultado-pieza {
            display: grid;
            grid-template-columns: 1fr 180px 180px 30px;
            gap: 10px;
            padding: 10px 12px;
            font-size: 13px;
            border-bottom: 1px solid #f1f5f9;
        }

        .resultado-pieza:last-child {
            border-bottom: none;
        }


        /* ==========================================
        RESPONSIVE
        ========================================== */

        @media (max-width: 700px) {

            .resultado-resumen {
                grid-template-columns: 1fr;
            }

            .resultado-hoja-datos {
                flex-direction: column;
                gap: 6px;
            }

            .resultado-pieza {
                grid-template-columns: 1fr;
            }
        }

        /* ==========================================
   PLANO DE CORTE
   ========================================== */

        .plano-hoja {
            padding: 18px;
            border-bottom: 1px solid #e2e8f0;
        }

        .plano-hoja-titulo {
            font-size: 13px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 12px;
        }

        .plano-contenedor {
            width: 100%;
            overflow-x: auto;
        }

        /* Acotación general del tablero completo */
        .plano-acotado {
            position: relative;
            width: 100%;
            max-width: 1040px;
            margin: 0 auto;
            padding: 0 40px 34px 0;
            box-sizing: border-box;
        }

        .plano-acotado .plano {
            position: relative;
            width: 100%;
            max-width: none;
            margin: 0;
            overflow: visible;
        }

        .plano-cota-tablero {
            position: absolute;
            color: #ef2929;
            font-size: 16px;
            font-weight: 600;
            line-height: 1;
            pointer-events: none;
            z-index: 30;
        }

        /* Al medir una pieza o retal, ocultar temporalmente las cotas generales. */
        .plano-acotado.cotas-elemento-activo .plano-cota-tablero {
            opacity: 0;
            visibility: hidden;
        }

        .plano-cota-tablero {
            transition: opacity .12s ease, visibility .12s ease;
        }

        .plano-cota-tablero-horizontal {
            left: 0;
            right: 40px;
            bottom: 0;
            height: 22px;
        }

        .plano-cota-tablero-horizontal::before {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            top: 5px;
            border-top: 1.5px solid #ef2929;
        }

        .plano-cota-tablero-horizontal::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            top: 1px;
            height: 9px;
            background:
                linear-gradient(#ef2929, #ef2929) left center / 1.5px 9px no-repeat,
                linear-gradient(#ef2929, #ef2929) right center / 1.5px 9px no-repeat;
        }

        .plano-cota-tablero-horizontal span {
            position: absolute;
            left: 50%;
            top: 9px;
            transform: translateX(-50%);
            padding: 0 5px;
            background: #fff;
            white-space: nowrap;
        }

        .plano-cota-tablero-vertical {
            top: 0;
            bottom: 34px;
            right: 0;
            width: 28px;
        }

        .plano-cota-tablero-vertical::before {
            content: "";
            position: absolute;
            top: 0;
            bottom: 0;
            left: 5px;
            border-left: 1.5px solid #ef2929;
        }

        .plano-cota-tablero-vertical::after {
            content: "";
            position: absolute;
            top: 0;
            bottom: 0;
            left: 1px;
            width: 9px;
            background:
                linear-gradient(#ef2929, #ef2929) center top / 9px 1.5px no-repeat,
                linear-gradient(#ef2929, #ef2929) center bottom / 9px 1.5px no-repeat;
        }

        .plano-cota-tablero-vertical span {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translate(-50%, -50%) rotate(-90deg);
            padding: 0 5px;
            background: #fff;
            white-space: nowrap;
        }

        .plano {
            position: relative;
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;

            background: #f8fafc;
            border: 2px solid #334155;

            box-sizing: border-box;
            overflow: hidden;
        }

        .plano-retal {
            position: absolute;
            box-sizing: border-box;
            border: 1px dashed #16a34a;
            background: rgba(220, 252, 231, 0.72);
            color: #166534;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            overflow: hidden;
            padding: 2px;
            z-index: 2;
            pointer-events: auto;
            cursor: crosshair;
        }

        .plano-retal-nombre {
            font-size: 10px;
            font-weight: 700;
            white-space: nowrap;
        }

        .plano-retal-medida {
            font-size: 9px;
            white-space: nowrap;
        }

        .plano-pieza {
            position: absolute;
            z-index: 1;

            box-sizing: border-box;

            border: 1px solid #2563eb;
            background: #dbeafe;

            display: flex;
            flex-direction: column;

            align-items: center;
            justify-content: center;

            text-align: center;

            overflow: hidden;

            padding: 4px;
        }

        .plano-pieza {
            cursor: pointer;
            transition: background .15s ease, outline .15s ease, box-shadow .15s ease;
        }

        .plano-pieza:hover {
            background: #bfdbfe;
            outline: 2px solid #60a5fa;
            outline-offset: -2px;
        }

        .plano-pieza.seleccionada {
            z-index: 5;
            background: #fef3c7;
            border: 2px solid #d97706;
            outline: 2px solid #f59e0b;
            box-shadow: 0 0 0 2px rgba(245, 158, 11, .18);
        }

        .resultado-pieza {
            cursor: pointer;
            transition: background .15s ease;
        }

        .resultado-pieza:hover {
            background: #eff6ff;
        }

        .resultado-pieza.seleccionada {
            background: #fffbeb;
            box-shadow: inset 4px 0 0 #d97706;
        }

        .plano-cotas {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            z-index: 8;
            overflow: visible;
            pointer-events: none;
        }

        .plano-cotas-linea {
            stroke: #ef2929;
            stroke-width: 2.2;
            fill: none;
            vector-effect: non-scaling-stroke;
        }

        .plano-cotas-texto {
            fill: #ef2929;
            font-size: 25px;
            font-weight: 700;
            text-anchor: middle;
            paint-order: stroke;
            stroke: #ffffff;
            stroke-width: 5px;
            stroke-linejoin: round;
        }

        /* Las cotas de cada pieza solo aparecen mientras el puntero está encima. */
        .plano-cotas-hover {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            z-index: 50;
            overflow: visible;
            pointer-events: none;
            display: block;
        }

        .plano-cotas-hover-linea {
            stroke: #ef2929;
            stroke-width: 0.65;
            fill: none;
            vector-effect: non-scaling-stroke;
        }

        .plano-cotas-hover-texto {
            fill: #ef2929;
            font-size: 3.8px;
            font-weight: 700;
            text-anchor: middle;
            dominant-baseline: middle;
            paint-order: stroke;
            stroke: #ffffff;
            stroke-width: 0.9px;
            stroke-linejoin: round;
        }

        .plano-seleccion-detalle {
            margin-top: 12px;
            padding: 12px 14px;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            background: #eff6ff;
            color: #1e3a8a;
            font-size: 13px;
        }

        .plano-seleccion-detalle strong {
            color: #1e40af;
        }

        .plano-seleccion-detalle.vacio {
            color: #64748b;
            background: #f8fafc;
            border-color: #e2e8f0;
        }

        .plano-pieza-nombre {
            font-size: 12px;
            font-weight: 700;
            color: #1e3a8a;

            max-width: 100%;

            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .plano-pieza-medida {
            font-size: 10px;
            color: #334155;

            white-space: nowrap;
        }

        .plano-pieza-rotada {
            position: absolute;

            top: 3px;
            right: 5px;

            font-size: 13px;
            font-weight: 700;
        }

        .plano-medidas {
            text-align: center;

            margin-top: 8px;

            font-size: 12px;
            color: #64748b;
        }
    </style>
</head>

<body>

    <header class="topbar">
        <div class="logo">
            Cut<span>List</span>
        </div>

        <div class="topbar-right">
            Optimizador de cortes
        </div>
    </header>

    <main class="container">

        <div class="page-header">
            <div>
                <h1>Optimización de cortes</h1>
                <p>Configura tus piezas y hojas para generar el mejor aprovechamiento.</p>
            </div>

            <div class="options">
                <button class="btn-secondary">
                    Guardar proyecto
                </button>

                <button class="btn-secondary">
                    Cargar proyecto
                </button>
            </div>
        </div>


        <!-- PIEZAS -->

        <section class="card">

            <div class="card-header">

                <div>
                    <div class="card-title">
                        Piezas
                    </div>

                    <div class="card-subtitle">
                        Piezas que necesitas fabricar
                    </div>
                </div>

                <button class="btn-add" id="btnAgregarPieza">
                    + Agregar pieza
                </button>

            </div>

            <div class="table-wrapper">

                <table>

                    <thead>
                        <tr>
                            <th>Largo</th>
                            <th>Ancho</th>
                            <th>Cantidad</th>
                            <th>Material</th>
                            <th>Etiqueta</th>
                            <th>Grano</th>
                            <th>Rotación</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr>
                            <td class="number">80.00 cm</td>
                            <td class="number">40.00 cm</td>
                            <td>2</td>
                            <td>
                                <span class="material">
                                    MDF 15 mm
                                </span>
                            </td>
                            <td>Nicholas</td>
                            <td class="grain">↔</td>
                            <td>✓</td>
                            <td class="actions">
                                <button class="delete">Eliminar</button>
                            </td>
                        </tr>

                        <tr>
                            <td class="number">70.00 cm</td>
                            <td class="number">40.00 cm</td>
                            <td>1</td>
                            <td>
                                <span class="material">
                                    MDF 15 mm
                                </span>
                            </td>
                            <td>Austin</td>
                            <td class="grain">↔</td>
                            <td>✓</td>
                            <td class="actions">
                                <button class="delete">Eliminar</button>
                            </td>
                        </tr>

                        <tr>
                            <td class="number">66.80 cm</td>
                            <td class="number">35.00 cm</td>
                            <td>2</td>
                            <td>
                                <span class="material">
                                    MDF 15 mm
                                </span>
                            </td>
                            <td>Daniel</td>
                            <td class="grain">↔</td>
                            <td>✓</td>
                            <td class="actions">
                                <button class="delete">Eliminar</button>
                            </td>
                        </tr>

                        <tr>
                            <td class="number">70.00 cm</td>
                            <td class="number">30.00 cm</td>
                            <td>1</td>
                            <td>
                                <span class="material">
                                    MDF 18 mm
                                </span>
                            </td>
                            <td>Amanda</td>
                            <td class="grain">↔</td>
                            <td>✓</td>
                            <td class="actions">
                                <button class="delete">Eliminar</button>
                            </td>
                        </tr>

                        <tr>
                            <td class="number">100.00 cm</td>
                            <td class="number">30.00 cm</td>
                            <td>1</td>
                            <td>
                                <span class="material">
                                    MDF 18 mm
                                </span>
                            </td>
                            <td>Jim</td>
                            <td class="grain">↔</td>
                            <td>✓</td>
                            <td class="actions">
                                <button
                                    class="delete btn-eliminar-pieza"
                                    type="button"
                                    data-id="${pieza.id}">
                                    Eliminar
                                </button>
                            </td>
                        </tr>

                    </tbody>

                </table>

            </div>

        </section>


        <!-- HOJAS -->

        <section class="card">

            <div class="card-header">

                <div>
                    <div class="card-title">
                        Hojas disponibles
                    </div>

                    <div class="card-subtitle">
                        Material disponible para realizar los cortes
                    </div>
                </div>

                <button class="btn-add" id="btnAgregarHoja">
                    + Agregar hoja
                </button>

            </div>

            <div class="table-wrapper">

                <table>

                    <thead>
                        <tr>
                            <th>Largo</th>
                            <th>Ancho</th>
                            <th>Cantidad</th>
                            <th>Material</th>
                            <th>Etiqueta</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr>
                            <td class="number">244.00 cm</td>
                            <td class="number">122.00 cm</td>
                            <td>1</td>

                            <td>
                                <span class="material">
                                    MDF 15 mm
                                </span>
                            </td>

                            <td>Charlie</td>

                            <td class="actions">
                                <button class="delete">
                                    Eliminar
                                </button>
                            </td>
                        </tr>

                        <tr>
                            <td class="number">244.00 cm</td>
                            <td class="number">122.00 cm</td>
                            <td>1</td>

                            <td>
                                <span class="material">
                                    MDF 18 mm
                                </span>
                            </td>

                            <td>Trevor</td>

                            <td class="actions">
                                <button class="delete">
                                    Eliminar
                                </button>
                            </td>
                        </tr>

                    </tbody>

                </table>

            </div>

        </section>


        <!-- OPCIONES -->

        <section class="card">

            <div class="card-header">

                <div>
                    <div class="card-title">
                        Opciones de optimización
                    </div>

                    <div class="card-subtitle">
                        Configura las condiciones que debe respetar el optimizador
                    </div>
                </div>

            </div>

            <div class="optimization-options">

                <div class="option">

                    <label>
                        Espesor de corte / Kerf (cm)
                    </label>

                    <input
                        type="number"
                        id="opcionKerf"
                        value="0.30"
                        min="0"
                        step="0.01">

                </div>


                <div class="option">

                    <label>
                        Considerar material
                    </label>

                    <div class="checks">

                        <label class="check">
                            <input type="checkbox" id="opcionSepararMateriales" checked>
                            Separar materiales
                        </label>

                    </div>

                </div>


                <div class="option">

                    <label>
                        Dirección de grano
                    </label>

                    <div class="checks">

                        <label class="check">
                            <input type="checkbox" id="opcionConsiderarGrano" checked>
                            Considerar grano
                        </label>

                    </div>

                </div>


                <div class="option">

                    <label>
                        Rotación
                    </label>

                    <div class="checks">

                        <label class="check">
                            <input type="checkbox" id="opcionPermitirRotacion" checked>
                            Permitir rotación
                        </label>

                    </div>

                </div>

            </div>

        </section>


        <!-- OPTIMIZAR -->

        <div class="optimize-area">

            <button
                class="btn-optimize"
                id="btnOptimizar">
                🔵 Optimizar cortes
            </button>

        </div>


        <!-- RESULTADOS -->

        <section class="card">

            <div class="card-header">

                <div>
                    <div class="card-title">
                        Resultado
                    </div>

                    <div class="card-subtitle">
                        Aquí aparecerá el resultado de la optimización
                    </div>
                </div>

            </div>

            <div id="resultado-optimizacion">

                <div class="empty-result">

                    <div style="font-size: 35px; margin-bottom: 10px;">
                        📐
                    </div>

                    <div>
                        Aún no se ha ejecutado ninguna optimización.
                    </div>

                </div>

            </div>

        </section>

    </main>
    <script>
        document.addEventListener('DOMContentLoaded', async function() {

            const tbody = document.querySelector('.card:first-of-type tbody');

            try {
                const respuesta = await fetch('/api/piezas');

                if (!respuesta.ok) {
                    throw new Error('Error al consultar la API de piezas');
                }

                const resultado = await respuesta.json();

                if (!resultado.success) {
                    throw new Error('La API no devolvió los datos correctamente');
                }

                tbody.innerHTML = '';

                resultado.data.forEach(pieza => {

                    const fila = document.createElement('tr');

                    let grano = '*';

                    if (pieza.direccion_grano === 'horizontal') {
                        grano = '↔';
                    } else if (pieza.direccion_grano === 'vertical') {
                        grano = '↕';
                    } else if (pieza.direccion_grano === 'indiferente') {
                        grano = '*';
                    }

                    const rotacion = pieza.permitir_rotacion ?
                        '✓' :
                        '—';

                    fila.innerHTML = `
                <td class="number">${Number(pieza.largo).toFixed(2)} cm</td>

                <td class="number">${Number(pieza.ancho).toFixed(2)} cm</td>

                <td>${pieza.cantidad}</td>

                <td>
                    <span class="material">
                        ${pieza.material ? pieza.material.nombre : 'Sin material'}
                    </span>
                </td>

                <td>
                    ${pieza.etiqueta ?? ''}
                </td>

                <td class="grain">
                    ${grano}
                </td>

                <td>
                    ${rotacion}
                </td>

                <td class="actions">
                    <button class="delete">
                        Eliminar
                    </button>
                </td>
            `;

                    tbody.appendChild(fila);
                });

            } catch (error) {

                console.error('Error CutList:', error);

                tbody.innerHTML = `
            <tr>
                <td colspan="8" style="text-align:center; color:#dc2626; padding:25px;">
                    No se pudieron cargar las piezas.
                </td>
            </tr>
        `;
            }

        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const tarjetas = document.querySelectorAll('.card');

            // La segunda tarjeta corresponde a Hojas
            const tablaHojas = tarjetas[1];
            const tbodyHojas = tablaHojas.querySelector('tbody');


            // ==========================================
            // CARGAR HOJAS
            // ==========================================

            async function cargarHojas() {

                try {

                    const respuesta = await fetch('/api/hojas');

                    if (!respuesta.ok) {
                        throw new Error('No se pudieron cargar las hojas');
                    }

                    const resultado = await respuesta.json();

                    if (!resultado.success) {
                        throw new Error(
                            'La API de hojas devolvió un error'
                        );
                    }

                    tbodyHojas.innerHTML = '';

                    resultado.data.forEach(hoja => {

                        const fila = document.createElement('tr');

                        fila.dataset.id = hoja.id;

                        fila.innerHTML = `

                    <td
                        class="hoja-editable"
                        data-campo="largo"
                        data-valor="${hoja.largo}"
                    >
                        ${Number(hoja.largo).toFixed(2)} cm
                    </td>

                    <td
                        class="hoja-editable"
                        data-campo="ancho"
                        data-valor="${hoja.ancho}"
                    >
                        ${Number(hoja.ancho).toFixed(2)} cm
                    </td>

                    <td
                        class="hoja-editable"
                        data-campo="cantidad"
                        data-valor="${hoja.cantidad}"
                    >
                        ${hoja.cantidad}
                    </td>

                    <td
                        class="hoja-editable"
                        data-campo="material_id"
                        data-valor="${hoja.material_id}"
                    >
                        <span class="material">
                            ${
                                hoja.material
                                    ? hoja.material.nombre
                                    : 'Sin material'
                            }
                        </span>
                    </td>

                    <td
                        class="hoja-editable"
                        data-campo="etiqueta"
                        data-valor="${hoja.etiqueta ?? ''}"
                    >
                        ${hoja.etiqueta ?? ''}
                    </td>

                    <td class="actions">

                        <button
                            class="delete btn-eliminar-hoja"
                            type="button"
                            data-id="${hoja.id}"
                        >
                            Eliminar
                        </button>

                    </td>

                `;

                        tbodyHojas.appendChild(fila);

                    });

                } catch (error) {

                    console.error(
                        'Error al cargar hojas:',
                        error
                    );

                    tbodyHojas.innerHTML = `
                <tr>
                    <td
                        colspan="6"
                        style="text-align:center; color:#dc2626; padding:25px;"
                    >
                        No se pudieron cargar las hojas.
                    </td>
                </tr>
            `;

                }

            }
            window.cargarHojas = cargarHojas;

            // ==========================================
            // CLICK EN CELDA DE HOJA
            // ==========================================

            tbodyHojas.addEventListener('click', function(event) {

                const celda =
                    event.target.closest('.hoja-editable');

                if (!celda) {
                    return;
                }

                if (celda.querySelector('input, select')) {
                    return;
                }

                editarHoja(celda);

            });


            // ==========================================
            // EDITAR HOJA
            // ==========================================

            async function editarHoja(celda) {

                const campo = celda.dataset.campo;
                const valorOriginal = celda.dataset.valor;

                const fila = celda.closest('tr');
                const hojaId = fila.dataset.id;


                // ======================================
                // MATERIAL
                // ======================================

                if (campo === 'material_id') {

                    const select =
                        document.createElement('select');

                    select.innerHTML = `
                <option value="">
                    Cargando...
                </option>
            `;

                    celda.innerHTML = '';
                    celda.appendChild(select);

                    try {

                        const respuesta =
                            await fetch('/api/materiales');

                        const resultado =
                            await respuesta.json();

                        if (!resultado.success) {
                            throw new Error(
                                'No se pudieron cargar los materiales.'
                            );
                        }

                        select.innerHTML = '';

                        resultado.data.forEach(material => {

                            const option =
                                document.createElement('option');

                            option.value = material.id;

                            option.textContent =
                                material.nombre;

                            if (
                                String(material.id) ===
                                String(valorOriginal)
                            ) {
                                option.selected = true;
                            }

                            select.appendChild(option);

                        });

                        select.focus();

                        select.addEventListener(
                            'change',
                            async function() {

                                await guardarHoja(
                                    celda,
                                    hojaId,
                                    campo,
                                    select.value
                                );

                            }
                        );

                        select.addEventListener(
                            'blur',
                            function() {

                                if (
                                    String(select.value) ===
                                    String(valorOriginal)
                                ) {
                                    cargarHojas();
                                }

                            }
                        );

                    } catch (error) {

                        console.error(error);

                        cargarHojas();

                    }

                    return;
                }


                // ======================================
                // LARGO / ANCHO / CANTIDAD / ETIQUETA
                // ======================================

                const input =
                    document.createElement('input');

                input.type =
                    campo === 'etiqueta' ?
                    'text' :
                    'number';


                if (campo !== 'etiqueta') {

                    input.step =
                        campo === 'cantidad' ?
                        '1' :
                        '0.01';

                    input.min =
                        campo === 'cantidad' ?
                        '1' :
                        '0.01';

                }


                input.value = valorOriginal;

                celda.innerHTML = '';

                celda.appendChild(input);

                input.focus();

                input.select();


                let guardado = false;


                async function guardar() {

                    if (guardado) {
                        return;
                    }

                    const nuevoValor =
                        input.value.trim();


                    if (
                        nuevoValor ===
                        valorOriginal
                    ) {

                        cargarHojas();

                        return;
                    }


                    if (!nuevoValor) {

                        alert(
                            'El valor no puede estar vacío.'
                        );

                        input.focus();

                        return;
                    }


                    if (
                        campo !== 'etiqueta' &&
                        Number(nuevoValor) <= 0
                    ) {

                        alert(
                            'El valor debe ser mayor que cero.'
                        );

                        input.focus();

                        return;
                    }


                    guardado = true;

                    await guardarHoja(
                        celda,
                        hojaId,
                        campo,
                        nuevoValor
                    );

                }


                input.addEventListener(
                    'keydown',
                    function(event) {

                        if (event.key === 'Enter') {

                            event.preventDefault();

                            guardar();

                        }

                        if (event.key === 'Escape') {

                            cargarHojas();

                        }

                    }
                );


                input.addEventListener(
                    'blur',
                    function() {

                        guardar();

                    }
                );

            }


            // ==========================================
            // GUARDAR HOJA EN API
            // ==========================================

            async function guardarHoja(
                celda,
                hojaId,
                campo,
                nuevoValor
            ) {

                try {

                    // Obtener datos actuales
                    const respuestaActual =
                        await fetch(
                            `/api/hojas/${hojaId}`
                        );

                    if (!respuestaActual.ok) {
                        throw new Error(
                            'No se pudo obtener la hoja.'
                        );
                    }

                    const resultadoActual =
                        await respuestaActual.json();

                    const hoja =
                        resultadoActual.data;


                    const datos = {

                        largo: Number(hoja.largo),

                        ancho: Number(hoja.ancho),

                        cantidad: Number(hoja.cantidad),

                        material_id: Number(hoja.material_id),

                        etiqueta: hoja.etiqueta ?? ''

                    };


                    // Sustituir el campo modificado

                    if (campo === 'largo') {

                        datos.largo =
                            Number(nuevoValor);

                    }

                    if (campo === 'ancho') {

                        datos.ancho =
                            Number(nuevoValor);

                    }

                    if (campo === 'cantidad') {

                        datos.cantidad =
                            Number(nuevoValor);

                    }

                    if (campo === 'material_id') {

                        datos.material_id =
                            Number(nuevoValor);

                    }

                    if (campo === 'etiqueta') {

                        datos.etiqueta =
                            nuevoValor;

                    }


                    celda.classList.add(
                        'guardando-pieza'
                    );


                    const respuesta =
                        await fetch(
                            `/api/hojas/${hojaId}`, {
                                method: 'PUT',

                                headers: {
                                    'Content-Type': 'application/json',

                                    'Accept': 'application/json'
                                },

                                body: JSON.stringify(datos)
                            }
                        );


                    const resultado =
                        await respuesta.json();


                    if (!respuesta.ok) {

                        let mensaje =
                            resultado.message ||
                            'No se pudo actualizar la hoja.';

                        if (resultado.errors) {

                            mensaje =
                                Object.values(
                                    resultado.errors
                                )
                                .flat()
                                .join(' ');

                        }

                        throw new Error(mensaje);

                    }


                    if (!resultado.success) {

                        throw new Error(
                            resultado.message ||
                            'No se pudo actualizar la hoja.'
                        );

                    }


                    // Recargar datos reales

                    await cargarHojas();


                } catch (error) {

                    console.error(
                        'Error al actualizar hoja:',
                        error
                    );

                    alert(error.message);

                    await cargarHojas();

                } finally {

                    celda.classList.remove(
                        'guardando-pieza'
                    );

                }

            }


            // ==========================================
            // ELIMINAR HOJA
            // ==========================================

            document.addEventListener(
                'click',
                async function(event) {

                    const boton =
                        event.target.closest(
                            '.btn-eliminar-hoja'
                        );

                    if (!boton) {
                        return;
                    }


                    const hojaId =
                        boton.dataset.id;


                    const fila =
                        boton.closest('tr');


                    const etiquetaCelda =
                        fila.querySelector(
                            '[data-campo="etiqueta"]'
                        );


                    const etiqueta =
                        etiquetaCelda?.dataset.valor || '';


                    const confirmar =
                        confirm(
                            `¿Eliminar la hoja${
                        etiqueta
                            ? ` "${etiqueta}"`
                            : ''
                    }?`
                        );


                    if (!confirmar) {
                        return;
                    }


                    boton.disabled = true;

                    boton.textContent =
                        'Eliminando...';


                    try {

                        const respuesta =
                            await fetch(
                                `/api/hojas/${hojaId}`, {
                                    method: 'DELETE',

                                    headers: {
                                        'Accept': 'application/json'
                                    }
                                }
                            );


                        const resultado =
                            await respuesta.json();


                        if (
                            !respuesta.ok ||
                            !resultado.success
                        ) {

                            throw new Error(
                                resultado.message ||
                                'No se pudo eliminar la hoja.'
                            );

                        }


                        await cargarHojas();


                    } catch (error) {

                        console.error(
                            'Error al eliminar hoja:',
                            error
                        );

                        alert(error.message);

                        boton.disabled = false;

                        boton.textContent =
                            'Eliminar';

                    }

                }
            );


            // ==========================================
            // INICIAR
            // ==========================================

            cargarHojas();

        });
    </script>

    <!-- MODAL AGREGAR PIEZA -->

    <div id="modalPieza" class="modal-overlay">

        <div class="modal">

            <div class="modal-header">
                <div>
                    <h2>Agregar pieza</h2>
                    <p>Introduce las características de la pieza.</p>
                </div>

                <button type="button" class="modal-close" id="cerrarModalPieza">
                    ×
                </button>
            </div>

            <form id="formPieza">

                <div class="form-grid">

                    <div class="form-group">
                        <label for="piezaLargo">
                            Largo (cm)
                        </label>

                        <input
                            type="number"
                            id="piezaLargo"
                            min="0.01"
                            step="0.01"
                            required>
                    </div>


                    <div class="form-group">
                        <label for="piezaAncho">
                            Ancho (cm)
                        </label>

                        <input
                            type="number"
                            id="piezaAncho"
                            min="0.01"
                            step="0.01"
                            required>
                    </div>


                    <div class="form-group">
                        <label for="piezaCantidad">
                            Cantidad
                        </label>

                        <input
                            type="number"
                            id="piezaCantidad"
                            min="1"
                            step="1"
                            value="1"
                            required>
                    </div>


                    <div class="form-group">
                        <label for="piezaMaterial">
                            Material
                        </label>

                        <select id="piezaMaterial" required>
                            <option value="">
                                Cargando materiales...
                            </option>
                        </select>
                    </div>


                    <div class="form-group full">
                        <label for="piezaEtiqueta">
                            Etiqueta
                        </label>

                        <input
                            type="text"
                            id="piezaEtiqueta"
                            maxlength="150"
                            placeholder="Ej. Nicholas">
                    </div>

                </div>


                <div class="form-section">

                    <label class="section-label">
                        Dirección del grano
                    </label>

                    <div class="grain-options">

                        <label class="grain-option">
                            <input
                                type="radio"
                                name="direccion_grano"
                                value="horizontal"
                                checked>

                            <span class="grain-symbol">↔</span>

                            <span>
                                Horizontal
                            </span>
                        </label>


                        <label class="grain-option">
                            <input
                                type="radio"
                                name="direccion_grano"
                                value="vertical">

                            <span class="grain-symbol">↕</span>

                            <span>
                                Vertical
                            </span>
                        </label>


                        <label class="grain-option">
                            <input
                                type="radio"
                                name="direccion_grano"
                                value="indiferente">

                            <span class="grain-symbol">＊</span>

                            <span>
                                Indiferente
                            </span>
                        </label>

                    </div>

                </div>


                <div class="form-section">

                    <label class="check-option">

                        <input
                            type="checkbox"
                            id="piezaRotacion"
                            checked>

                        <span>
                            Permitir rotación de la pieza
                        </span>

                    </label>

                </div>


                <div id="mensajePieza" class="form-message"></div>


                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn-secondary"
                        id="cancelarPieza">
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="btn-primary"
                        id="guardarPieza">
                        Agregar pieza
                    </button>

                </div>

            </form>

        </div>

    </div>
    <!-- MODAL AGREGAR HOJA -->

    <div id="modalHoja" class="modal-overlay">

        <div class="modal">

            <div class="modal-header">

                <div>
                    <h2>Agregar hoja</h2>
                    <p>Introduce las características de la hoja disponible.</p>
                </div>

                <button
                    type="button"
                    class="modal-close"
                    id="cerrarModalHoja">
                    ×
                </button>

            </div>


            <form id="formHoja">

                <div class="form-grid">

                    <!-- LARGO -->

                    <div class="form-group">

                        <label for="hojaLargo">
                            Largo (cm)
                        </label>

                        <input
                            type="number"
                            id="hojaLargo"
                            min="0.01"
                            step="0.01"
                            required>

                    </div>


                    <!-- ANCHO -->

                    <div class="form-group">

                        <label for="hojaAncho">
                            Ancho (cm)
                        </label>

                        <input
                            type="number"
                            id="hojaAncho"
                            min="0.01"
                            step="0.01"
                            required>

                    </div>


                    <!-- CANTIDAD -->

                    <div class="form-group">

                        <label for="hojaCantidad">
                            Cantidad
                        </label>

                        <input
                            type="number"
                            id="hojaCantidad"
                            min="1"
                            step="1"
                            value="1"
                            required>

                    </div>


                    <!-- MATERIAL -->

                    <div class="form-group">

                        <label for="hojaMaterial">
                            Material
                        </label>

                        <select
                            id="hojaMaterial"
                            required>

                            <option value="">
                                Selecciona un material
                            </option>

                        </select>

                    </div>


                    <!-- ETIQUETA -->

                    <div class="form-group full">

                        <label for="hojaEtiqueta">
                            Etiqueta
                        </label>

                        <input
                            type="text"
                            id="hojaEtiqueta"
                            maxlength="150"
                            placeholder="Ej. Charlie">

                    </div>

                </div>


                <div
                    id="mensajeHoja"
                    class="form-message">
                </div>


                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn-secondary"
                        id="cancelarHoja">
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="btn-primary"
                        id="guardarHoja">
                        Agregar hoja
                    </button>

                </div>

            </form>

        </div>

    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const modal = document.getElementById('modalPieza');

            const btnAgregar = document.getElementById('btnAgregarPieza');

            const btnCerrar = document.getElementById('cerrarModalPieza');

            const btnCancelar = document.getElementById('cancelarPieza');

            const form = document.getElementById('formPieza');

            const selectMaterial = document.getElementById('piezaMaterial');

            const mensaje = document.getElementById('mensajePieza');


            // ==========================================
            // ABRIR MODAL
            // ==========================================

            btnAgregar.addEventListener('click', async function() {

                modal.classList.add('active');

                mensaje.className = 'form-message';
                mensaje.textContent = '';

                await cargarMateriales();

            });


            // ==========================================
            // CERRAR MODAL
            // ==========================================

            function cerrarModal() {

                modal.classList.remove('active');

                form.reset();

                document.getElementById('piezaCantidad').value = 1;

                document.getElementById('piezaRotacion').checked = true;

                document.querySelector(
                    'input[name="direccion_grano"][value="horizontal"]'
                ).checked = true;

                mensaje.className = 'form-message';
                mensaje.textContent = '';
            }


            btnCerrar.addEventListener('click', cerrarModal);

            btnCancelar.addEventListener('click', cerrarModal);


            modal.addEventListener('click', function(event) {

                if (event.target === modal) {
                    cerrarModal();
                }

            });


            // ==========================================
            // CARGAR MATERIALES
            // ==========================================

            async function cargarMateriales() {

                try {

                    selectMaterial.innerHTML = `
                <option value="">
                    Cargando materiales...
                </option>
            `;

                    const respuesta = await fetch('/api/materiales');

                    if (!respuesta.ok) {
                        throw new Error('No se pudieron cargar los materiales');
                    }

                    const resultado = await respuesta.json();

                    if (!resultado.success) {
                        throw new Error('La API de materiales devolvió un error');
                    }

                    selectMaterial.innerHTML = `
                <option value="">
                    Selecciona un material
                </option>
            `;

                    resultado.data.forEach(material => {

                        const option = document.createElement('option');

                        option.value = material.id;

                        option.textContent = material.nombre;

                        selectMaterial.appendChild(option);

                    });

                } catch (error) {

                    console.error(error);

                    selectMaterial.innerHTML = `
                <option value="">
                    Error al cargar materiales
                </option>
            `;

                    mostrarMensaje(
                        'No se pudieron cargar los materiales.',
                        'error'
                    );

                }

            }


            // ==========================================
            // GUARDAR PIEZA
            // ==========================================

            form.addEventListener('submit', async function(event) {

                event.preventDefault();


                const largo = document.getElementById('piezaLargo').value;

                const ancho = document.getElementById('piezaAncho').value;

                const cantidad = document.getElementById('piezaCantidad').value;

                const material_id = selectMaterial.value;

                const etiqueta = document.getElementById('piezaEtiqueta').value;

                const permitir_rotacion =
                    document.getElementById('piezaRotacion').checked;


                const direccion_grano =
                    document.querySelector(
                        'input[name="direccion_grano"]:checked'
                    ).value;


                if (!material_id) {

                    mostrarMensaje(
                        'Selecciona un material.',
                        'error'
                    );

                    return;
                }


                const boton = document.getElementById('guardarPieza');

                boton.disabled = true;

                boton.textContent = 'Guardando...';


                try {

                    const respuesta = await fetch('/api/piezas', {

                        method: 'POST',

                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },

                        body: JSON.stringify({

                            largo: Number(largo),

                            ancho: Number(ancho),

                            cantidad: Number(cantidad),

                            material_id: Number(material_id),

                            etiqueta: etiqueta,

                            permitir_rotacion: permitir_rotacion,

                            direccion_grano: direccion_grano

                        })

                    });


                    const resultado = await respuesta.json();


                    if (!respuesta.ok) {

                        let mensajeError =
                            resultado.message ||
                            'No se pudo guardar la pieza.';

                        if (resultado.errors) {

                            const errores =
                                Object.values(resultado.errors)
                                .flat()
                                .join(' ');

                            mensajeError = errores;

                        }

                        throw new Error(mensajeError);

                    }


                    if (!resultado.success) {

                        throw new Error(
                            resultado.message ||
                            'No se pudo guardar la pieza.'
                        );

                    }


                    mostrarMensaje(
                        'Pieza agregada correctamente.',
                        'success'
                    );


                    // Actualizar tabla

                    await cargarPiezas();


                    // Cerrar después de un momento

                    setTimeout(function() {

                        cerrarModal();

                    }, 700);


                } catch (error) {

                    console.error(
                        'Error al guardar pieza:',
                        error
                    );

                    mostrarMensaje(
                        error.message,
                        'error'
                    );

                } finally {

                    boton.disabled = false;

                    boton.textContent = 'Agregar pieza';

                }

            });


            // ==========================================
            // MENSAJES
            // ==========================================

            function mostrarMensaje(texto, tipo) {

                mensaje.textContent = texto;

                mensaje.className =
                    'form-message ' + tipo;

            }


            // ==========================================
            // RECARGAR PIEZAS
            // ==========================================

            async function cargarPiezas() {

                try {

                    const respuesta = await fetch('/api/piezas');

                    if (!respuesta.ok) {
                        throw new Error('No se pudieron cargar las piezas');
                    }

                    const resultado = await respuesta.json();

                    if (!resultado.success) {
                        throw new Error('La API de piezas devolvió un error');
                    }

                    const tarjetas = document.querySelectorAll('.card');

                    const tbody = tarjetas[0].querySelector('tbody');

                    tbody.innerHTML = '';

                    resultado.data.forEach(pieza => {

                        let grano = '*';

                        if (pieza.direccion_grano === 'horizontal') {
                            grano = '↔';
                        } else if (pieza.direccion_grano === 'vertical') {
                            grano = '↕';
                        }

                        const fila = document.createElement('tr');

                        fila.dataset.id = pieza.id;

                        fila.innerHTML = `

                <td
                    class="pieza-editable"
                    data-campo="largo"
                    data-valor="${pieza.largo}"
                >
                    ${Number(pieza.largo).toFixed(2)} cm
                </td>

                <td
                    class="pieza-editable"
                    data-campo="ancho"
                    data-valor="${pieza.ancho}"
                >
                    ${Number(pieza.ancho).toFixed(2)} cm
                </td>

                <td
                    class="pieza-editable"
                    data-campo="cantidad"
                    data-valor="${pieza.cantidad}"
                >
                    ${pieza.cantidad}
                </td>

                <td
                    class="pieza-editable"
                    data-campo="material_id"
                    data-valor="${pieza.material_id}"
                >
                    <span class="material">
                        ${
                            pieza.material
                                ? pieza.material.nombre
                                : 'Sin material'
                        }
                    </span>
                </td>

                <td
                    class="pieza-editable"
                    data-campo="etiqueta"
                    data-valor="${pieza.etiqueta ?? ''}"
                >
                    ${pieza.etiqueta ?? ''}
                </td>

                <td
                    class="pieza-editable"
                    data-campo="direccion_grano"
                    data-valor="${pieza.direccion_grano}"
                    style="text-align:center;"
                >
                    ${grano}
                </td>

                <td style="text-align:center;">
                    <input
                        type="checkbox"
                        class="pieza-check"
                        data-id="${pieza.id}"
                        data-valor="${pieza.permitir_rotacion ? 1 : 0}"
                        ${pieza.permitir_rotacion ? 'checked' : ''}
                    >
                </td>

                <td class="actions">

                    <button
        class="delete btn-eliminar-pieza"
        type="button"
        data-id="${pieza.id}"
        >
        Eliminar
        </button>

                </td>

            `;

                        tbody.appendChild(fila);

                    });

                } catch (error) {

                    console.error(
                        'Error al cargar piezas:',
                        error
                    );

                }

            }
            // ==========================================
            // EDICIÓN DIRECTA DE PIEZAS
            // ==========================================

            const tablaPiezas = document.querySelectorAll('.card')[0];

            const tbodyPiezas = tablaPiezas.querySelector('tbody');


            // ==========================================
            // CLICK EN UNA CELDA
            // ==========================================

            tbodyPiezas.addEventListener('click', function(event) {

                const celda = event.target.closest('.pieza-editable');

                if (!celda) {
                    return;
                }

                if (celda.querySelector('input, select')) {
                    return;
                }

                editarCelda(celda);

            });


            // ==========================================
            // EDITAR CELDA
            // ==========================================

            async function editarCelda(celda) {

                const campo = celda.dataset.campo;

                const valorOriginal = celda.dataset.valor;

                const fila = celda.closest('tr');

                const piezaId = fila.dataset.id;


                // ======================================
                // MATERIAL
                // ======================================

                if (campo === 'material_id') {

                    const select = document.createElement('select');

                    select.innerHTML = `
            <option value="">Cargando...</option>
        `;

                    celda.innerHTML = '';

                    celda.appendChild(select);

                    try {

                        const respuesta =
                            await fetch('/api/materiales');

                        const resultado =
                            await respuesta.json();

                        select.innerHTML = '';

                        resultado.data.forEach(material => {

                            const option =
                                document.createElement('option');

                            option.value = material.id;

                            option.textContent = material.nombre;

                            if (
                                String(material.id) ===
                                String(valorOriginal)
                            ) {
                                option.selected = true;
                            }

                            select.appendChild(option);

                        });

                        select.focus();

                        select.addEventListener(
                            'change',
                            async function() {

                                await guardarCelda(
                                    celda,
                                    piezaId,
                                    campo,
                                    select.value,
                                    valorOriginal
                                );

                            }
                        );

                        select.addEventListener(
                            'blur',
                            function() {

                                if (
                                    String(select.value) ===
                                    String(valorOriginal)
                                ) {

                                    cargarPiezas();

                                }

                            }
                        );

                    } catch (error) {

                        console.error(error);

                        cargarPiezas();

                    }

                    return;
                }


                // ======================================
                // DIRECCIÓN DE GRANO
                // ======================================

                if (campo === 'direccion_grano') {

                    const select = document.createElement('select');

                    select.innerHTML = `

            <option value="horizontal">
                ↔ Horizontal
            </option>

            <option value="vertical">
                ↕ Vertical
            </option>

            <option value="indiferente">
                * Indiferente
            </option>

        `;

                    select.value = valorOriginal;

                    celda.innerHTML = '';

                    celda.appendChild(select);

                    select.focus();

                    select.addEventListener(
                        'change',
                        async function() {

                            await guardarCelda(
                                celda,
                                piezaId,
                                campo,
                                select.value,
                                valorOriginal
                            );

                        }
                    );

                    select.addEventListener(
                        'blur',
                        function() {

                            if (
                                select.value ===
                                valorOriginal
                            ) {

                                cargarPiezas();

                            }

                        }
                    );

                    return;
                }


                // ======================================
                // LARGO / ANCHO / CANTIDAD / ETIQUETA
                // ======================================

                const input =
                    document.createElement('input');


                input.type =
                    campo === 'etiqueta' ?
                    'text' :
                    'number';


                if (campo !== 'etiqueta') {

                    input.step =
                        campo === 'cantidad' ?
                        '1' :
                        '0.01';

                    input.min =
                        campo === 'cantidad' ?
                        '1' :
                        '0.01';

                }


                input.value = valorOriginal;

                celda.innerHTML = '';

                celda.appendChild(input);

                input.focus();

                input.select();


                let guardado = false;


                async function guardar() {

                    if (guardado) {
                        return;
                    }

                    const nuevoValor =
                        input.value.trim();


                    if (
                        nuevoValor ===
                        valorOriginal
                    ) {

                        cargarPiezas();

                        return;

                    }


                    if (!nuevoValor) {

                        alert('El valor no puede estar vacío.');

                        input.focus();

                        return;

                    }


                    if (
                        campo !== 'etiqueta' &&
                        Number(nuevoValor) <= 0
                    ) {

                        alert('El valor debe ser mayor que cero.');

                        input.focus();

                        return;

                    }


                    guardado = true;


                    await guardarCelda(
                        celda,
                        piezaId,
                        campo,
                        nuevoValor,
                        valorOriginal
                    );

                }


                input.addEventListener(
                    'keydown',
                    function(event) {

                        if (event.key === 'Enter') {

                            event.preventDefault();

                            guardar();

                        }

                        if (event.key === 'Escape') {

                            cargarPiezas();

                        }

                    }
                );


                input.addEventListener(
                    'blur',
                    function() {

                        guardar();

                    }
                );

            }


            // ==========================================
            // GUARDAR EN API
            // ==========================================

            async function guardarCelda(
                celda,
                piezaId,
                campo,
                nuevoValor,
                valorOriginal
            ) {

                const fila =
                    celda.closest('tr');


                // Obtener todos los valores actuales
                // de la fila

                const celdas =
                    fila.querySelectorAll(
                        '.pieza-editable'
                    );


                const datos = {

                    largo: Number(
                        celdas[0].dataset.valor
                    ),

                    ancho: Number(
                        celdas[1].dataset.valor
                    ),

                    cantidad: Number(
                        celdas[2].dataset.valor
                    ),

                    material_id: Number(
                        celdas[3].dataset.valor
                    ),

                    etiqueta: celdas[4].dataset.valor,

                    direccion_grano: celdas[5].dataset.valor,

                    permitir_rotacion: fila.querySelector(
                        '.pieza-check'
                    ).checked

                };


                // Sustituir el valor que estamos editando

                if (campo === 'largo') {

                    datos.largo =
                        Number(nuevoValor);

                }

                if (campo === 'ancho') {

                    datos.ancho =
                        Number(nuevoValor);

                }

                if (campo === 'cantidad') {

                    datos.cantidad =
                        Number(nuevoValor);

                }

                if (campo === 'material_id') {

                    datos.material_id =
                        Number(nuevoValor);

                }

                if (campo === 'etiqueta') {

                    datos.etiqueta =
                        nuevoValor;

                }

                if (campo === 'direccion_grano') {

                    datos.direccion_grano =
                        nuevoValor;

                }


                celda.classList.add(
                    'guardando-pieza'
                );


                try {

                    const respuesta =
                        await fetch(
                            `/api/piezas/${piezaId}`, {
                                method: 'PUT',

                                headers: {
                                    'Content-Type': 'application/json',

                                    'Accept': 'application/json'
                                },

                                body: JSON.stringify(datos)
                            }
                        );


                    const resultado =
                        await respuesta.json();


                    if (!respuesta.ok) {

                        let mensaje =
                            resultado.message ||
                            'No se pudo actualizar la pieza.';


                        if (resultado.errors) {

                            mensaje =
                                Object.values(
                                    resultado.errors
                                )
                                .flat()
                                .join(' ');

                        }

                        throw new Error(mensaje);

                    }


                    if (!resultado.success) {

                        throw new Error(
                            resultado.message ||
                            'No se pudo actualizar la pieza.'
                        );

                    }


                    // Volvemos a consultar la API
                    // para mostrar el dato actualizado

                    await cargarPiezas();


                } catch (error) {

                    console.error(
                        'Error al actualizar pieza:',
                        error
                    );

                    celda.classList.add(
                        'pieza-error'
                    );

                    alert(error.message);

                    await cargarPiezas();


                } finally {

                    celda.classList.remove(
                        'guardando-pieza'
                    );

                }

            }

            cargarPiezas();


            // ==========================================
            // CAMBIAR ROTACIÓN DIRECTAMENTE
            // ==========================================

            document.addEventListener('click', async function(event) {

                const checkbox = event.target.closest('.pieza-check');

                if (!checkbox) {
                    return;
                }

                const piezaId = checkbox.dataset.id;

                const nuevoValor = checkbox.checked;

                checkbox.disabled = true;

                try {

                    // Obtener datos actuales de la pieza
                    const respuestaActual = await fetch(
                        `/api/piezas/${piezaId}`
                    );

                    if (!respuestaActual.ok) {
                        throw new Error(
                            'No se pudo obtener la pieza.'
                        );
                    }

                    const resultadoActual =
                        await respuestaActual.json();

                    const pieza =
                        resultadoActual.data;


                    // Mantener todos los datos actuales
                    const datos = {

                        largo: Number(pieza.largo),

                        ancho: Number(pieza.ancho),

                        cantidad: Number(pieza.cantidad),

                        material_id: Number(pieza.material_id),

                        etiqueta: pieza.etiqueta ?? '',

                        direccion_grano: pieza.direccion_grano,

                        permitir_rotacion: nuevoValor

                    };


                    // Actualizar
                    const respuesta = await fetch(
                        `/api/piezas/${piezaId}`, {
                            method: 'PUT',

                            headers: {
                                'Content-Type': 'application/json',

                                'Accept': 'application/json'
                            },

                            body: JSON.stringify(datos)
                        }
                    );


                    const resultado =
                        await respuesta.json();


                    if (!respuesta.ok) {

                        throw new Error(
                            resultado.message ||
                            'No se pudo actualizar la rotación.'
                        );

                    }


                    // Recargar tabla con los datos reales
                    await cargarPiezas();


                } catch (error) {

                    console.error(
                        'Error al cambiar rotación:',
                        error
                    );

                    alert(error.message);

                    // Recuperar estado anterior
                    checkbox.checked = !nuevoValor;


                } finally {

                    checkbox.disabled = false;

                }

            });

            // ==========================================
            // ELIMINAR PIEZA
            // ==========================================

            document.addEventListener('click', async function(event) {

                const boton = event.target.closest('.btn-eliminar-pieza');

                if (!boton) {
                    return;
                }

                const piezaId = boton.dataset.id;

                const fila = boton.closest('tr');

                const celdaEtiqueta =
                    fila.querySelectorAll('.pieza-editable')[4];

                const etiqueta =
                    celdaEtiqueta?.dataset.valor || '';

                const confirmar = confirm(
                    `¿Eliminar la pieza${etiqueta ? ` "${etiqueta}"` : ''}?`
                );

                if (!confirmar) {
                    return;
                }

                boton.disabled = true;
                boton.textContent = 'Eliminando...';

                try {

                    const respuesta = await fetch(
                        `/api/piezas/${piezaId}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json'
                            }
                        }
                    );

                    const resultado = await respuesta.json();

                    if (!respuesta.ok || !resultado.success) {

                        throw new Error(
                            resultado.message ||
                            'No se pudo eliminar la pieza.'
                        );

                    }

                    await cargarPiezas();

                } catch (error) {

                    console.error(
                        'Error al eliminar pieza:',
                        error
                    );

                    alert(error.message);

                    boton.disabled = false;
                    boton.textContent = 'Eliminar';

                }

            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const modal =
                document.getElementById('modalHoja');

            const btnAgregar =
                document.getElementById('btnAgregarHoja');

            const btnCerrar =
                document.getElementById('cerrarModalHoja');

            const btnCancelar =
                document.getElementById('cancelarHoja');

            const form =
                document.getElementById('formHoja');

            const selectMaterial =
                document.getElementById('hojaMaterial');

            const mensaje =
                document.getElementById('mensajeHoja');


            // ==========================================
            // ABRIR MODAL
            // ==========================================

            btnAgregar.addEventListener(
                'click',
                async function() {

                    modal.classList.add('active');

                    mensaje.className =
                        'form-message';

                    mensaje.textContent = '';

                    await cargarMaterialesHoja();

                }
            );


            // ==========================================
            // CERRAR MODAL
            // ==========================================

            function cerrarModalHoja() {

                modal.classList.remove('active');

                form.reset();

                document.getElementById(
                    'hojaCantidad'
                ).value = 1;

                mensaje.className =
                    'form-message';

                mensaje.textContent = '';

            }


            btnCerrar.addEventListener(
                'click',
                cerrarModalHoja
            );

            btnCancelar.addEventListener(
                'click',
                cerrarModalHoja
            );


            modal.addEventListener(
                'click',
                function(event) {

                    if (event.target === modal) {

                        cerrarModalHoja();

                    }

                }
            );


            // ==========================================
            // CARGAR MATERIALES
            // ==========================================

            async function cargarMaterialesHoja() {

                try {

                    selectMaterial.innerHTML = `
                <option value="">
                    Cargando materiales...
                </option>
            `;


                    const respuesta =
                        await fetch('/api/materiales');


                    if (!respuesta.ok) {

                        throw new Error(
                            'No se pudieron cargar los materiales.'
                        );

                    }


                    const resultado =
                        await respuesta.json();


                    if (!resultado.success) {

                        throw new Error(
                            'La API de materiales devolvió un error.'
                        );

                    }


                    selectMaterial.innerHTML = `
                <option value="">
                    Selecciona un material
                </option>
            `;


                    resultado.data.forEach(
                        material => {

                            const option =
                                document.createElement(
                                    'option'
                                );

                            option.value =
                                material.id;

                            option.textContent =
                                material.nombre;

                            selectMaterial.appendChild(
                                option
                            );

                        }
                    );


                } catch (error) {

                    console.error(
                        'Error al cargar materiales:',
                        error
                    );


                    selectMaterial.innerHTML = `
                <option value="">
                    Error al cargar materiales
                </option>
            `;


                    mostrarMensajeHoja(
                        error.message,
                        'error'
                    );

                }

            }


            // ==========================================
            // GUARDAR HOJA
            // ==========================================

            form.addEventListener(
                'submit',
                async function(event) {

                    event.preventDefault();


                    const largo =
                        document.getElementById(
                            'hojaLargo'
                        ).value;


                    const ancho =
                        document.getElementById(
                            'hojaAncho'
                        ).value;


                    const cantidad =
                        document.getElementById(
                            'hojaCantidad'
                        ).value;


                    const material_id =
                        selectMaterial.value;


                    const etiqueta =
                        document.getElementById(
                            'hojaEtiqueta'
                        ).value;


                    // Validar material

                    if (!material_id) {

                        mostrarMensajeHoja(
                            'Selecciona un material.',
                            'error'
                        );

                        return;

                    }


                    const boton =
                        document.getElementById(
                            'guardarHoja'
                        );


                    boton.disabled = true;

                    boton.textContent =
                        'Guardando...';


                    try {

                        const respuesta =
                            await fetch(
                                '/api/hojas', {
                                    method: 'POST',

                                    headers: {
                                        'Content-Type': 'application/json',

                                        'Accept': 'application/json'
                                    },

                                    body: JSON.stringify({

                                        largo: Number(largo),

                                        ancho: Number(ancho),

                                        cantidad: Number(cantidad),

                                        material_id: Number(material_id),

                                        etiqueta: etiqueta

                                    })

                                }
                            );


                        const resultado =
                            await respuesta.json();


                        if (!respuesta.ok) {

                            let mensajeError =
                                resultado.message ||
                                'No se pudo guardar la hoja.';


                            if (resultado.errors) {

                                mensajeError =
                                    Object.values(
                                        resultado.errors
                                    )
                                    .flat()
                                    .join(' ');

                            }


                            throw new Error(
                                mensajeError
                            );

                        }


                        if (!resultado.success) {

                            throw new Error(
                                resultado.message ||
                                'No se pudo guardar la hoja.'
                            );

                        }


                        mostrarMensajeHoja(
                            'Hoja agregada correctamente.',
                            'success'
                        );

                        // Actualizar tabla

                        await window.cargarHojas();


                        // Cerrar modal

                        setTimeout(
                            function() {

                                cerrarModalHoja();

                            },
                            700
                        );


                    } catch (error) {

                        console.error(
                            'Error al guardar hoja:',
                            error
                        );


                        mostrarMensajeHoja(
                            error.message,
                            'error'
                        );


                    } finally {

                        boton.disabled = false;

                        boton.textContent =
                            'Agregar hoja';

                    }

                }
            );


            // ==========================================
            // MENSAJES
            // ==========================================

            function mostrarMensajeHoja(
                texto,
                tipo
            ) {

                mensaje.textContent =
                    texto;

                mensaje.className =
                    'form-message ' + tipo;

            }


        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ==========================================
            // ELEMENTOS
            // ==========================================

            const kerf =
                document.getElementById('opcionKerf');

            const separarMateriales =
                document.getElementById(
                    'opcionSepararMateriales'
                );

            const considerarGrano =
                document.getElementById(
                    'opcionConsiderarGrano'
                );

            const permitirRotacion =
                document.getElementById(
                    'opcionPermitirRotacion'
                );


            // ==========================================
            // CONFIGURACIÓN PREDETERMINADA
            // ==========================================

            const configuracionPredeterminada = {

                kerf: 0.30,

                separar_materiales: true,

                considerar_grano: true,

                permitir_rotacion: true

            };


            // ==========================================
            // LEER CONFIGURACIÓN
            // ==========================================

            function cargarConfiguracion() {

                const guardada =
                    localStorage.getItem(
                        'cutlist_configuracion_cm_v1'
                    );


                if (!guardada) {

                    aplicarConfiguracion(
                        configuracionPredeterminada
                    );

                    return;

                }


                try {

                    const configuracion =
                        JSON.parse(guardada);


                    aplicarConfiguracion(
                        configuracion
                    );


                } catch (error) {

                    console.error(
                        'Error al leer configuración:',
                        error
                    );


                    aplicarConfiguracion(
                        configuracionPredeterminada
                    );

                }

            }


            // ==========================================
            // APLICAR CONFIGURACIÓN A LA PANTALLA
            // ==========================================

            function aplicarConfiguracion(
                configuracion
            ) {

                kerf.value =
                    configuracion.kerf ?? 0.30;


                separarMateriales.checked =
                    configuracion.separar_materiales ??
                    true;


                considerarGrano.checked =
                    configuracion.considerar_grano ??
                    true;


                permitirRotacion.checked =
                    configuracion.permitir_rotacion ??
                    true;

            }


            // ==========================================
            // OBTENER CONFIGURACIÓN ACTUAL
            // ==========================================

            function obtenerConfiguracion() {

                return {

                    kerf: Number(kerf.value),

                    separar_materiales: separarMateriales.checked,

                    considerar_grano: considerarGrano.checked,

                    permitir_rotacion: permitirRotacion.checked

                };

            }


            // ==========================================
            // GUARDAR CONFIGURACIÓN
            // ==========================================

            function guardarConfiguracion() {

                const configuracion =
                    obtenerConfiguracion();


                localStorage.setItem(
                    'cutlist_configuracion_cm_v1',
                    JSON.stringify(configuracion)
                );


                console.log(
                    'Configuración guardada:',
                    configuracion
                );

            }


            // ==========================================
            // CAMBIOS
            // ==========================================

            kerf.addEventListener(
                'change',
                function() {

                    if (Number(kerf.value) < 0) {

                        kerf.value = 0;

                    }

                    guardarConfiguracion();

                }
            );


            separarMateriales.addEventListener(
                'change',
                guardarConfiguracion
            );


            considerarGrano.addEventListener(
                'change',
                guardarConfiguracion
            );


            permitirRotacion.addEventListener(
                'change',
                guardarConfiguracion
            );


            // ==========================================
            // CARGAR AL ABRIR LA PÁGINA
            // ==========================================

            cargarConfiguracion();


            // ==========================================
            // HACER DISPONIBLE PARA EL OPTIMIZADOR
            // ==========================================

            window.obtenerConfiguracionCutList =
                obtenerConfiguracion;

        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const botonOptimizar =
                document.getElementById('btnOptimizar');


            // ==========================================
            // OPTIMIZAR
            // ==========================================

            botonOptimizar.addEventListener(
                'click',
                async function() {

                    botonOptimizar.disabled = true;

                    botonOptimizar.textContent =
                        'Optimizando...';


                    try {

                        // ==================================
                        // OBTENER CONFIGURACIÓN
                        // ==================================

                        const configuracion =
                            window.obtenerConfiguracionCutList();


                        console.log(
                            'Configuración:',
                            configuracion
                        );


                        // ==================================
                        // ENVIAR A LARAVEL
                        // ==================================

                        const respuesta =
                            await fetch(
                                '/api/cutlist/optimizar', {
                                    method: 'POST',

                                    headers: {
                                        'Content-Type': 'application/json',

                                        'Accept': 'application/json'
                                    },

                                    body: JSON.stringify(
                                        configuracion
                                    )
                                }
                            );


                        const resultado =
                            await respuesta.json();

                        console.log(
                            'Respuesta del optimizador:',
                            resultado
                        );

                        mostrarResultadoCutList(resultado);

                        // ==================================
                        // COMPROBAR RESPUESTA
                        // ==================================

                        if (!respuesta.ok) {

                            let mensaje =
                                resultado.message ||
                                'Error al ejecutar la optimización.';


                            if (resultado.errors) {

                                mensaje =
                                    Object.values(
                                        resultado.errors
                                    )
                                    .flat()
                                    .join(' ');

                            }


                            throw new Error(
                                mensaje
                            );

                        }


                        if (!resultado.success) {

                            throw new Error(
                                resultado.message ||
                                'La optimización no pudo ejecutarse.'
                            );

                        }


                        // ==================================
                        // MOSTRAR EN CONSOLA
                        // ==================================

                        console.log(
                            'Respuesta del optimizador:',
                            resultado
                        );


                        console.log(
                            'Configuración recibida:',
                            resultado.resultado.configuracion
                        );

                        console.log(
                            '=== ORIENTACIONES VÁLIDAS ==='
                        );

                        console.log(
                            '===== RESPUESTA COMPLETA ====='
                        );

                        console.log(resultado);

                        console.log(
                            '===== RESULTADO ====='
                        );

                        console.log(
                            resultado.resultado
                        );

                        console.log(
                            '===== COLOCACION ====='
                        );

                        console.log(
                            resultado.resultado?.colocacion
                        );

                        console.log(
                            '===== COLOCADAS ====='
                        );

                        console.log(
                            resultado.resultado?.colocacion?.colocadas
                        );

                        console.log(
                            'VALIDACIÓN:',
                            resultado.resultado.validacion
                        );

                        console.log(
                            '===== NO COLOCADAS ====='
                        );

                        console.log(
                            resultado.resultado?.colocacion?.no_colocadas
                        );

                        resultado.resultado.estadisticas.detalle_hojas
                            .forEach(hoja => {

                                console.log(
                                    `HOJA: ${hoja.etiqueta}`
                                );

                                console.log(
                                    'Material:',
                                    hoja.material
                                );

                                console.log(
                                    'Área hoja:',
                                    hoja.area_hoja
                                );

                                console.log(
                                    'Área piezas:',
                                    hoja.area_piezas
                                );

                                console.log(
                                    'Desperdicio:',
                                    hoja.area_desperdicio
                                );

                                console.log(
                                    'Aprovechamiento:',
                                    hoja.porcentaje_aprovechamiento + '%'
                                );

                                console.table(
                                    hoja.piezas
                                );
                            });

                        console.log(
                            'Hojas:',
                            resultado.resultado.hojas
                        );

                        console.log(
                            '===== ESTADÍSTICAS ====='
                        );

                        console.log(
                            resultado.resultado.estadisticas
                        );

                        console.log(
                            '===== ESTADÍSTICAS POR HOJA ====='
                        );

                        console.table(
                            resultado.resultado.estadisticas.detalle_hojas
                        );

                        // ==================================
                        // MENSAJE TEMPORAL
                        // ==================================

                        alert(
                            'Los datos llegaron correctamente al servidor.'
                        );


                    } catch (error) {

                        console.error(
                            'Error al optimizar:',
                            error
                        );


                        alert(
                            error.message
                        );


                    } finally {

                        botonOptimizar.disabled = false;

                        botonOptimizar.textContent =
                            '🔵 Optimizar cortes';

                    }

                }
            );

            function mostrarResultadoCutList(resultado) {

                const contenedor =
                    document.getElementById(
                        'resultado-optimizacion'
                    );

                if (!contenedor) {
                    console.error(
                        'No se encontró #resultado-optimizacion'
                    );
                    return;
                }


                // ==========================================
                // VALIDAR RESPUESTA
                // ==========================================

                if (
                    !resultado ||
                    !resultado.resultado ||
                    !resultado.resultado.estadisticas
                ) {

                    console.error(
                        'La respuesta no contiene estadísticas:',
                        resultado
                    );

                    return;
                }


                const estadisticas =
                    resultado.resultado.estadisticas;

                const detalleHojas =
                    estadisticas.detalle_hojas || [];


                // ==========================================
                // RESUMEN GENERAL
                // ==========================================

                let html = `

        <div class="resultado-resumen">

            <div class="resultado-indicador">

                <div class="resultado-valor">
                    ${estadisticas.hojas_utilizadas}
                </div>

                <div class="resultado-label">
                    Hojas utilizadas
                </div>

            </div>


            <div class="resultado-indicador">

                <div class="resultado-valor">
                    ${Number(
                        estadisticas.porcentaje_aprovechamiento
                    ).toFixed(2)}%
                </div>

                <div class="resultado-label">
                    Aprovechamiento
                </div>

            </div>


            <div class="resultado-indicador">

                <div class="resultado-valor">
                    ${Number(
                        estadisticas.porcentaje_desperdicio
                    ).toFixed(2)}%
                </div>

                <div class="resultado-label">
                    Desperdicio
                </div>

            </div>

        </div>

    `;


                // ==========================================
                // HOJAS INDIVIDUALES
                // ==========================================

                detalleHojas.forEach(
                    (hoja, indice) => {

                        html += `

                    <div class="resultado-hoja">

                    <div class="resultado-hoja-header">

                        <div>

                            <div class="resultado-hoja-titulo">

                                Hoja ${indice + 1}

                                ${
                                    hoja.etiqueta
                                        ? `— ${hoja.etiqueta}`
                                        : ''
                                }

                            </div>

                            <div class="resultado-hoja-subtitulo">

                                ${hoja.material || ''}

                                ·

                                ${hoja.largo} ×
                                ${Number(hoja.ancho).toFixed(2)} cm

                            </div>

                        </div>


                        <div class="resultado-hoja-aprovechamiento">

                            ${Number(
                                hoja.porcentaje_aprovechamiento
                            ).toFixed(2)}%

                        </div>

                    </div>


                    <div class="resultado-hoja-datos">

                        <div>

                            <strong>
                                ${hoja.cantidad_piezas}
                            </strong>

                            piezas

                        </div>


                        <div>

                            <strong>
                                ${Number(
                                    hoja.area_piezas
                                ).toLocaleString()}
                            </strong>

                            cm² utilizados

                        </div>


                        <div>

                            <strong>
                                ${Number(
                                    hoja.area_desperdicio
                                ).toLocaleString()}
                            </strong>

                            cm² desperdicio

                        </div>

                        <div>
                            <strong>${hoja.cantidad_retales || 0}</strong>
                            retales recuperables
                        </div>

                        <div>
                            <strong>${Number(hoja.area_retales || 0).toLocaleString('es-MX', { maximumFractionDigits: 2 })}</strong>
                            cm² en retales
                        </div>

                    </div>

                                        </div>


                    <!-- ==========================================
                         PLANO DE CORTE
                         ========================================== -->

                    <div class="plano-hoja">

                        <div class="plano-hoja-titulo">
                            Plano de corte
                        </div>

                        <div class="plano-contenedor">

                            <div class="plano-acotado">
                            <div
                                class="plano"
                                data-sheet-length="${Number(hoja.largo)}"
                                data-sheet-width="${Number(hoja.ancho)}"
                                style="
                                    aspect-ratio: ${hoja.largo} / ${hoja.ancho};
                                "
                            >

                                ${(hoja.retales || []).map((retal, indiceRetal) => {
                                    const left = (Number(retal.x) / Number(hoja.largo)) * 100;
                                    const top = (Number(retal.y) / Number(hoja.ancho)) * 100;
                                    const width = (Number(retal.largo) / Number(hoja.largo)) * 100;
                                    const height = (Number(retal.ancho) / Number(hoja.ancho)) * 100;

                                    return `
                                        <div
                                            class="plano-retal"
                                            data-piece-length="${Number(retal.largo)}"
                                            data-piece-width="${Number(retal.ancho)}"
                                            data-piece-x="${Number(retal.x)}"
                                            data-piece-y="${Number(retal.y)}"
                                            style="
                                                left: ${left}%;
                                                top: ${top}%;
                                                width: ${width}%;
                                                height: ${height}%;
                                            "
                                        >
                                            <div class="plano-retal-nombre">Retal ${indiceRetal + 1}</div>
                                            <div class="plano-retal-medida">
                                                ${Number(retal.largo).toFixed(2)} × ${Number(retal.ancho).toFixed(2)} cm
                                            </div>
                                        </div>
                                    `;
                                }).join('')}

                                ${(hoja.piezas || []).map(pieza => {

                                    const left =
                                        (
                                            Number(pieza.x) /
                                            Number(hoja.largo)
                                        ) * 100;

                                    const top =
                                        (
                                            Number(pieza.y) /
                                            Number(hoja.ancho)
                                        ) * 100;

                                    const width =
                                        (
                                            Number(pieza.largo) /
                                            Number(hoja.largo)
                                        ) * 100;

                                    const height =
                                        (
                                            Number(pieza.ancho) /
                                            Number(hoja.ancho)
                                        ) * 100;


                                    return `

                                        <div
                                            class="plano-pieza"
                                            data-piece-key="${hoja.hoja_id}:${hoja.hoja_numero}:${pieza.pieza_id}:${pieza.numero}"
                                            data-piece-id="${pieza.pieza_id}"
                                            data-piece-number="${pieza.numero}"
                                            data-piece-length="${Number(pieza.largo)}"
                                            data-piece-width="${Number(pieza.ancho)}"
                                            data-piece-x="${Number(pieza.x)}"
                                            data-piece-y="${Number(pieza.y)}"
                                            data-piece-rotated="${pieza.rotada ? 'Sí' : 'No'}"
                                            style="
                                                left: ${left}%;
                                                top: ${top}%;
                                                width: ${width}%;
                                                height: ${height}%;
                                            "
                                        >

                                            <div class="plano-pieza-nombre">
                                                ${
                                                    pieza.etiqueta ||
                                                    'Sin etiqueta'
                                                }
                                            </div>

                                            <div class="plano-pieza-medida">
                                                ${pieza.largo} ×
                                                ${Number(pieza.ancho).toFixed(2)} cm
                                            </div>

                                            ${
                                                pieza.rotada
                                                    ? `
                                                        <div class="plano-pieza-rotada">
                                                            ↻
                                                        </div>
                                                    `
                                                    : ''
                                            }

                                        </div>

                                    `;

                                }).join('')}

                            </div>

                            <div class="plano-cota-tablero plano-cota-tablero-horizontal">
                                <span>${Number(hoja.largo).toFixed(2)} cm</span>
                            </div>
                            <div class="plano-cota-tablero plano-cota-tablero-vertical">
                                <span>${Number(hoja.ancho).toFixed(2)} cm</span>
                            </div>
                            </div>

                        </div>

                        <div class="plano-medidas">

                            ${hoja.largo} ×
                            ${Number(hoja.ancho).toFixed(2)} cm

                        </div>

                        <div
                            class="plano-seleccion-detalle vacio"
                            data-sheet-key="${hoja.hoja_id}:${hoja.hoja_numero}"
                        >
                            Haz clic en una pieza del plano o de la lista para consultar sus medidas.
                        </div>

                    </div>


                    <!-- ==========================================
                         PIEZAS COLOCADAS
                         ========================================== -->

                    <div class="resultado-piezas">

                        <div class="resultado-piezas-titulo">
                            Piezas colocadas
                        </div>

                        <div class="resultado-piezas-lista">

                `;


                        // ======================================
                        // PIEZAS DE LA HOJA
                        // ======================================

                        (hoja.piezas || []).forEach(
                            pieza => {

                                html += `

                        <div
                            class="resultado-pieza"
                            data-piece-key="${hoja.hoja_id}:${hoja.hoja_numero}:${pieza.pieza_id}:${pieza.numero}"
                        >

                            <span>
                                ${
                                    pieza.etiqueta ||
                                    'Sin etiqueta'
                                }
                            </span>

                            <span>
                                ${pieza.largo} ×
                                ${Number(pieza.ancho).toFixed(2)} cm
                            </span>

                            <span>
                                X: ${pieza.x}
                                ·
                                Y: ${pieza.y}
                            </span>

                            <span>
                                ${
                                    pieza.rotada
                                        ? '↻'
                                        : ''
                                }
                            </span>

                        </div>

                    `;
                            }
                        );


                        html += `

                        </div>

                    </div>

                </div>

            `;
                    }
                );


                // ==========================================
                // MOSTRAR RESULTADO
                // ==========================================

                contenedor.innerHTML =
                    html;

                // Selección sincronizada entre el plano y la lista.
                // No requiere una consulta adicional al servidor.

                // Acotaciones rojas temporales: aparecen al pasar el mouse por una pieza.
                const NS_SVG = 'http://www.w3.org/2000/svg';

                function quitarCotasHover() {
                    contenedor.querySelectorAll('.plano-cotas-hover').forEach(
                        overlay => overlay.remove()
                    );
                }

                function agregarLineaCota(svg, x1, y1, x2, y2) {
                    const linea = document.createElementNS(NS_SVG, 'line');
                    linea.setAttribute('x1', x1);
                    linea.setAttribute('y1', y1);
                    linea.setAttribute('x2', x2);
                    linea.setAttribute('y2', y2);
                    linea.setAttribute('class', 'plano-cotas-hover-linea');
                    svg.appendChild(linea);
                }

                function agregarTextoCota(svg, x, y, texto, rotacion = 0) {
                    const etiqueta = document.createElementNS(NS_SVG, 'text');
                    etiqueta.setAttribute('x', x);
                    etiqueta.setAttribute('y', y);
                    etiqueta.setAttribute('class', 'plano-cotas-hover-texto');
                    if (rotacion) {
                        etiqueta.setAttribute(
                            'transform',
                            `rotate(${rotacion} ${x} ${y})`
                        );
                    }
                    etiqueta.textContent = texto;
                    svg.appendChild(etiqueta);
                }

                function mostrarCotasPieza(pieza) {
                    quitarCotasHover();

                    const plano = pieza.closest('.plano');
                    if (!plano) return;

                    const largoHoja = Number(plano.dataset.sheetLength);
                    const anchoHoja = Number(plano.dataset.sheetWidth);
                    const largoPieza = Number(pieza.dataset.pieceLength);
                    const anchoPieza = Number(pieza.dataset.pieceWidth);
                    const xPieza = Number(pieza.dataset.pieceX);
                    const yPieza = Number(pieza.dataset.pieceY);

                    if (
                        !largoHoja || !anchoHoja ||
                        !largoPieza || !anchoPieza ||
                        !Number.isFinite(xPieza) || !Number.isFinite(yPieza)
                    ) return;

                    const svg = document.createElementNS(NS_SVG, 'svg');
                    svg.setAttribute('viewBox', `0 0 ${largoHoja} ${anchoHoja}`);
                    svg.setAttribute('preserveAspectRatio', 'none');
                    svg.setAttribute('class', 'plano-cotas-hover');
                    svg.setAttribute('aria-hidden', 'true');

                    // Cota horizontal debajo de la pieza, con extensiones en ambos extremos.
                    const yCota = yPieza + anchoPieza + 1.5;
                    const extension = 1.5;
                    agregarLineaCota(
                        svg, xPieza, yPieza + anchoPieza,
                        xPieza, yCota + extension
                    );
                    agregarLineaCota(
                        svg, xPieza + largoPieza, yPieza + anchoPieza,
                        xPieza + largoPieza, yCota + extension
                    );
                    agregarLineaCota(
                        svg, xPieza, yCota,
                        xPieza + largoPieza, yCota
                    );
                    agregarLineaCota(svg, xPieza, yCota - 1.2, xPieza, yCota + 1.2);
                    agregarLineaCota(
                        svg,
                        xPieza + largoPieza, yCota - 1.2,
                        xPieza + largoPieza, yCota + 1.2
                    );

                    // Cota vertical a la derecha de la pieza.
                    const xCota = xPieza + largoPieza + 1.5;
                    agregarLineaCota(
                        svg, xPieza + largoPieza, yPieza,
                        xCota + extension, yPieza
                    );
                    agregarLineaCota(
                        svg, xPieza + largoPieza, yPieza + anchoPieza,
                        xCota + extension, yPieza + anchoPieza
                    );
                    agregarLineaCota(
                        svg, xCota, yPieza,
                        xCota, yPieza + anchoPieza
                    );
                    agregarLineaCota(svg, xCota - 1.2, yPieza, xCota + 1.2, yPieza);
                    agregarLineaCota(
                        svg,
                        xCota - 1.2, yPieza + anchoPieza,
                        xCota + 1.2, yPieza + anchoPieza
                    );

                    agregarTextoCota(
                        svg,
                        xPieza + largoPieza / 2,
                        yCota + 1.8,
                        `${largoPieza.toFixed(2)} cm`
                    );
                    agregarTextoCota(
                        svg,
                        xCota + 1.8,
                        yPieza + anchoPieza / 2,
                        `${anchoPieza.toFixed(2)} cm`,
                        -90
                    );

                    plano.appendChild(svg);
                }

                contenedor.onpointerover = function(event) {
                    const elemento = event.target.closest('.plano-pieza, .plano-retal');
                    if (!elemento || !contenedor.contains(elemento)) return;
                    if (event.relatedTarget && elemento.contains(event.relatedTarget)) return;

                    const acotado = elemento.closest('.plano-acotado');
                    if (acotado) acotado.classList.add('cotas-elemento-activo');

                    mostrarCotasPieza(elemento);
                };

                contenedor.onpointerout = function(event) {
                    const elemento = event.target.closest('.plano-pieza, .plano-retal');
                    if (!elemento || !contenedor.contains(elemento)) return;
                    if (event.relatedTarget && elemento.contains(event.relatedTarget)) return;

                    const acotado = elemento.closest('.plano-acotado');
                    if (acotado) acotado.classList.remove('cotas-elemento-activo');

                    quitarCotasHover();
                };

                contenedor.onclick = function(event) {
                    const elemento = event.target.closest(
                        '.plano-pieza, .resultado-pieza'
                    );

                    if (!elemento || !contenedor.contains(elemento)) {
                        return;
                    }

                    const clave = elemento.dataset.pieceKey;
                    const tarjetaResultado = elemento.closest('.resultado-hoja');
                    const detalle = tarjetaResultado?.querySelector(
                        '.plano-seleccion-detalle'
                    );

                    if (!clave || !detalle) {
                        return;
                    }

                    const yaSeleccionada = elemento.classList.contains('seleccionada');

                    // Eliminar cotas anteriores para que solo se mida una pieza a la vez.
                    contenedor.querySelectorAll('.plano-cotas, .plano-cotas-overlay').forEach(
                        overlay => overlay.remove()
                    );

                    contenedor.querySelectorAll(
                        '.plano-pieza.seleccionada, .resultado-pieza.seleccionada'
                    ).forEach(item => item.classList.remove('seleccionada'));

                    if (yaSeleccionada) {
                        detalle.classList.add('vacio');
                        detalle.textContent =
                            'Haz clic en una pieza del plano o de la lista para consultar sus medidas.';
                        return;
                    }

                    const piezasRelacionadas = tarjetaResultado.querySelectorAll(
                        `[data-piece-key="${clave}"]`
                    );

                    piezasRelacionadas.forEach(item => {
                        item.classList.add('seleccionada');
                    });

                    const piezaPlano = tarjetaResultado.querySelector(
                        `.plano-pieza[data-piece-key="${clave}"]`
                    );

                    const piezaLista = tarjetaResultado.querySelector(
                        `.resultado-pieza[data-piece-key="${clave}"]`
                    );

                    const origen = piezaPlano || piezaLista;
                    const nombre = origen?.querySelector(
                        '.plano-pieza-nombre'
                    )?.textContent.trim()
                        || piezaLista?.querySelector('span')?.textContent.trim()
                        || 'Sin etiqueta';

                    const datos = {
                        id: origen?.dataset.pieceId || '',
                        numero: origen?.dataset.pieceNumber || '',
                        largo: Number(origen?.dataset.pieceLength || 0),
                        ancho: Number(origen?.dataset.pieceWidth || 0),
                        x: Number(origen?.dataset.pieceX || 0),
                        y: Number(origen?.dataset.pieceY || 0),
                        rotada: origen?.dataset.pieceRotated || 'No'
                    };

                    detalle.classList.remove('vacio');
                    detalle.textContent = '';

                    const titulo = document.createElement('div');
                    const tituloStrong = document.createElement('strong');
                    tituloStrong.textContent = `Pieza ${datos.numero} · ${nombre}`;
                    titulo.appendChild(tituloStrong);

                    const medidas = document.createElement('div');
                    medidas.textContent =
                        `Medidas: ${datos.largo.toFixed(2)} × ${datos.ancho.toFixed(2)} cm`;

                    const posicion = document.createElement('div');
                    posicion.textContent =
                        `Posición: X ${datos.x.toFixed(2)} cm · Y ${datos.y.toFixed(2)} cm`;

                    const orientacion = document.createElement('div');
                    orientacion.textContent =
                        `ID de pieza: ${datos.id} · Rotada: ${datos.rotada}`;

                    detalle.append(titulo, medidas, posicion, orientacion);


                };
            }
        });
    </script>
</body>

</html>