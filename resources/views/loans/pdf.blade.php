<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Constancia de Préstamo - {{ $loan->loan_code }}</title>
    <style>
        @page {
            margin: 20mm 15mm 20mm 15mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.4;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }
        .title {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            color: #0f172a;
            margin: 0;
            text-align: center;
        }
        .subtitle {
            font-size: 10px;
            color: #64748b;
            text-align: center;
            margin-top: 3px;
        }
        .doc-number {
            font-size: 13px;
            font-weight: bold;
            color: #2563eb;
            text-align: right;
            font-family: 'Courier New', Courier, monospace;
        }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            background-color: #f1f5f9;
            padding: 4px 8px;
            border-left: 3px solid #2563eb;
            margin-top: 12px;
            margin-bottom: 6px;
            text-transform: uppercase;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        table.data-table td {
            padding: 4px 6px;
            vertical-align: top;
        }
        table.data-table .label {
            font-weight: bold;
            color: #475569;
            width: 25%;
        }
        table.data-table .value {
            color: #0f172a;
            width: 75%;
        }
        table.grid-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            margin-bottom: 10px;
        }
        table.grid-table th {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 5px;
            font-size: 10px;
            text-align: left;
            text-transform: uppercase;
            color: #475569;
        }
        table.grid-table td {
            border: 1px solid #e2e8f0;
            padding: 5px;
            font-size: 10px;
        }
        .terms {
            font-size: 9px;
            color: #475569;
            text-align: justify;
            margin-top: 15px;
            padding: 8px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
        }
        .signatures {
            width: 100%;
            margin-top: 40px;
        }
        .signatures td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 20px;
        }
        .sign-line {
            border-top: 1px solid #0f172a;
            margin-top: 45px;
            padding-top: 5px;
            font-size: 10px;
            font-weight: bold;
        }
        .sign-sub {
            font-size: 9px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <table class="header-table">
        <tr>
            <td style="width: 20%; vertical-align: middle;">
                <div style="font-weight: bold; font-size: 12px; color: #0f172a;">SISTEMA WEB</div>
                <div style="font-size: 9px; color: #64748b;">CONTROL PATRIMONIAL</div>
            </td>
            <td style="width: 55%; text-align: center;">
                <h1 class="title">Constancia de Préstamo de Bienes</h1>
                <div class="subtitle">Comprobante Oficial de Entrega y Recepción de Equipos</div>
            </td>
            <td style="width: 25%; text-align: right; vertical-align: middle;">
                <div class="doc-number">{{ $loan->loan_code }}</div>
                <div style="font-size: 9px; color: #64748b;">Fecha: {{ $loan->created_at->format('d/m/Y H:i') }}</div>
            </td>
        </tr>
    </table>

    <!-- 1. Datos del Solicitante -->
    <div class="section-title">1. Datos del Solicitante (Trabajador)</div>
    <table class="data-table">
        <tr>
            <td class="label">Nombres y Apellidos:</td>
            <td class="value"><strong>{{ $loan->worker?->full_name }}</strong></td>
        </tr>
        <tr>
            <td class="label">DNI / Documento:</td>
            <td class="value">{{ $loan->worker?->dni }}</td>
        </tr>
        <tr>
            <td class="label">Oficina / Dependencia:</td>
            <td class="value"><strong>{{ $loan->worker?->office?->name }}</strong></td>
        </tr>
        <tr>
            <td class="label">Cargo / Función:</td>
            <td class="value">{{ $loan->worker?->position ?? 'No especificado' }}</td>
        </tr>
    </table>

    <!-- 2. Datos del Equipo -->
    <div class="section-title">2. Identificación del Equipo o Herramienta</div>
    <table class="data-table">
        <tr>
            <td class="label">Código Patrimonial:</td>
            <td class="value"><strong>{{ $loan->equipment?->code }}</strong></td>
        </tr>
        <tr>
            <td class="label">Denominación:</td>
            <td class="value"><strong>{{ $loan->equipment?->name }}</strong></td>
        </tr>
        <tr>
            <td class="label">Marca / Modelo:</td>
            <td class="value">{{ $loan->equipment?->brand }} {{ $loan->equipment?->model }}</td>
        </tr>
        <tr>
            <td class="label">Número de Serie:</td>
            <td class="value">{{ $loan->equipment?->serial_number ?? 'S/N' }}</td>
        </tr>
        <tr>
            <td class="label">Estado al Entregar:</td>
            <td class="value" style="text-transform: capitalize;">{{ $loan->equipment?->physical_condition }}</td>
        </tr>
    </table>

    <!-- 3. Accesorios -->
    <div class="section-title">3. Accesorios y Componentes Entregados</div>
    @if($loan->loanAccessories->isEmpty())
        <div style="font-size: 10px; color: #64748b; font-style: italic; padding: 4px;">Sin accesorios registrados.</div>
    @else
        <table class="grid-table">
            <thead>
                <tr>
                    <th style="width: 8%; text-align: center;">N°</th>
                    <th style="width: 50%;">Descripción del Accesorio</th>
                    <th style="width: 22%; text-align: center;">Estado en Salida</th>
                    <th style="width: 20%;">Retorno Físico</th>
                </tr>
            </thead>
            <tbody>
                @foreach($loan->loanAccessories as $index => $acc)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td><strong>{{ $acc->accessory?->name }}</strong></td>
                        <td style="text-align: center;">[ X ] Entregado</td>
                        <td>{{ $acc->was_returned ? '[ X ] Devuelto' : '[   ] Pendiente' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- 4. Período y Motivo -->
    <div class="section-title">4. Período y Motivo del Préstamo</div>
    <table class="data-table">
        <tr>
            <td class="label">Fecha y Hora de Salida:</td>
            <td class="value">{{ $loan->loan_date->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td class="label">Devolución Estimada:</td>
            <td class="value"><strong>{{ $loan->expected_return_date->format('d/m/Y H:i') }}</strong></td>
        </tr>
        <tr>
            <td class="label">Motivo / Destino de Uso:</td>
            <td class="value">{{ $loan->purpose }}</td>
        </tr>
        @if($loan->loan_notes)
            <tr>
                <td class="label">Observaciones:</td>
                <td class="value">{{ $loan->loan_notes }}</td>
            </tr>
        @endif
    </table>

    <!-- 5. Cláusula de Custodia -->
    <div class="terms">
        <strong>COMPROMISO DE CUSTODIA Y RESPONSABILIDAD:</strong><br>
        El solicitante declara recibir los bienes y accesorios descritos en el presente documento en óptimas condiciones de operatividad y se compromete a destinarlos exclusivamente para los fines institucionales autorizados. Asimismo, asume la obligación de custodiar, cuidar y devolver el equipo íntegro en la fecha y hora pactadas, respondiendo administrativamente por cualquier daño, avería o pérdida imputable a negligencia.
    </div>

    <!-- 6. Firmas -->
    <table class="signatures">
        <tr>
            <td>
                <div class="sign-line">
                    {{ $loan->worker?->full_name }}<br>
                    <span class="sign-sub">SOLICITANTE — DNI: {{ $loan->worker?->dni }}</span>
                </div>
            </td>
            <td>
                <div class="sign-line">
                    {{ $loan->loanUser?->name ?? 'ENCARGADO DE ALMACÉN' }}<br>
                    <span class="sign-sub">ENCARGADO DE ENTREGA Y RECEPCIÓN</span>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
