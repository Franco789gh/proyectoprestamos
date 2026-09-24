<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte General de Préstamos</title>
    <style>
        @page {
            margin: 15mm 10mm 15mm 10mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9px;
            color: #1e293b;
            line-height: 1.3;
        }
        .header {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .title {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            color: #0f172a;
            margin: 0;
        }
        .subtitle {
            font-size: 9px;
            text-align: center;
            color: #64748b;
            margin-top: 2px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        th {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 5px 4px;
            font-size: 8.5px;
            text-align: left;
            text-transform: uppercase;
            color: #334155;
        }
        td {
            border: 1px solid #e2e8f0;
            padding: 4px;
            font-size: 8.5px;
        }
        tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .badge {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 7.5px;
        }
        .footer {
            margin-top: 15px;
            font-size: 8px;
            color: #64748b;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">Reporte General de Préstamos y Devoluciones</h1>
        <div class="subtitle">Sistema Institucional de Gestión y Control Patrimonial • Generado el {{ date('d/m/Y H:i:s') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 12%;">Código</th>
                <th style="width: 18%;">Solicitante / DNI</th>
                <th style="width: 15%;">Oficina</th>
                <th style="width: 18%;">Equipo</th>
                <th style="width: 11%;">Fecha Salida</th>
                <th style="width: 11%;">Fecha Retorno</th>
                <th style="width: 15%;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($loans as $loan)
                <tr>
                    <td style="font-family: Courier; font-weight: bold;">{{ $loan->loan_code }}</td>
                    <td><strong>{{ $loan->worker?->full_name }}</strong><br><span style="color:#64748b;">DNI: {{ $loan->worker?->dni }}</span></td>
                    <td>{{ $loan->worker?->office?->name }}</td>
                    <td><strong>{{ $loan->equipment?->name }}</strong><br><span style="color:#64748b;">{{ $loan->equipment?->code }}</span></td>
                    <td>{{ $loan->loan_date->format('d/m/Y H:i') }}</td>
                    <td>{{ $loan->actual_return_date ? $loan->actual_return_date->format('d/m/Y H:i') : $loan->expected_return_date->format('d/m/Y H:i') . ' (Est.)' }}</td>
                    <td class="badge">
                        {{ strtoupper(str_replace('_', ' ', $loan->status)) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 15px;">No se encontraron registros.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Total de operaciones en este reporte: <strong>{{ count($loans) }}</strong>
    </div>
</body>
</html>
