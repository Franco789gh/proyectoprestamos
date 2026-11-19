<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etiqueta Patrimonial — {{ $equipment->code }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                margin: 0;
                padding: 0;
            }
            .label-card {
                box-shadow: none !important;
                border: 2px solid #000 !important;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen p-4 sm:p-8 flex flex-col items-center justify-center font-sans">

    <!-- Acciones en pantalla (no se imprimen) -->
    <div class="no-print max-w-md w-full mb-6 flex items-center justify-between">
        <a href="{{ route('equipment.show', $equipment) }}" class="inline-flex items-center text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white px-3.5 py-2 rounded-xl shadow-xs border border-slate-200">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Volver a la Ficha</span>
        </a>

        <button onclick="window.print()" class="inline-flex items-center text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-xl shadow-md shadow-blue-500/20 transition-all cursor-pointer">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            <span>Imprimir Etiqueta</span>
        </button>
    </div>

    <!-- Etiqueta Patrimonial Estilizada -->
    <div class="label-card bg-white rounded-2xl border-2 border-slate-900 p-6 max-w-md w-full shadow-xl space-y-4 text-slate-900">
        <!-- Membrete -->
        <div class="border-b-2 border-slate-900 pb-2.5 flex items-center justify-between">
            <div>
                <h1 class="text-xs font-black tracking-widest text-slate-900 uppercase">SISTEMA DE CONTROL PATRIMONIAL</h1>
                <p class="text-[10px] font-semibold text-slate-600 tracking-wider">GESTIÓN Y PRÉSTAMO INSTITUCIONAL</p>
            </div>
            <span class="px-2 py-0.5 rounded-md bg-slate-900 text-white font-mono text-[10px] font-bold">
                {{ strtoupper($equipment->category) }}
            </span>
        </div>

        <!-- Contenido Central: QR + Datos -->
        <div class="flex items-center space-x-4">
            <div class="p-1 border border-slate-300 rounded-xl bg-white shrink-0">
                <canvas id="qrCanvas" class="w-28 h-28"></canvas>
            </div>

            <div class="space-y-1.5 flex-1 min-w-0">
                <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider block">Código Patrimonial</span>
                <div class="font-mono text-2xl font-black text-slate-950 tracking-tight leading-none">
                    {{ $equipment->code }}
                </div>
                <div class="font-bold text-xs text-slate-800 line-clamp-2 mt-1">
                    {{ $equipment->name }}
                </div>
                <div class="text-[11px] text-slate-600">
                    <span class="font-semibold">{{ $equipment->brand }}</span> {{ $equipment->model }}
                </div>
                @if($equipment->serial_number)
                    <div class="text-[10px] font-mono text-slate-500">
                        S/N: {{ $equipment->serial_number }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Accesorios incluidos en este bien -->
        @if($equipment->accessories->isNotEmpty())
            <div class="bg-slate-50 rounded-xl p-2.5 border border-slate-200 text-[10px] space-y-1">
                <span class="font-bold text-slate-700 uppercase tracking-wider block text-[9px]">Accesorios Asociados:</span>
                <p class="text-slate-600 font-medium">
                    {{ $equipment->accessories->pluck('name')->join(', ') }}
                </p>
            </div>
        @endif

        <!-- Pie de etiqueta -->
        <div class="border-t border-slate-300 pt-2 flex items-center justify-between text-[9px] text-slate-500 font-mono">
            <span>ID: #{{ str_pad($equipment->id, 5, '0', STR_PAD_LEFT) }}</span>
            <span>NO RETIRAR ESTE CÓDIGO</span>
            <span>ESTADO: {{ strtoupper($equipment->physical_condition) }}</span>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const qrData = "{{ url('/equipment/' . $equipment->id) }}";
            if (window.generateQRCode) {
                window.generateQRCode('qrCanvas', qrData, { width: 112, margin: 0 });
            }
        });
    </script>
</body>
</html>
