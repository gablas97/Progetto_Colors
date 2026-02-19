<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Report Generale - Colors S.r.l.</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.6;
            background-color: #fff;
        }

        .page {
            width: 100%;
            page-break-after: always;
        }

        .header {
            border-bottom: 3px solid #2563eb;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .company-info {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .company-logo-section {
            flex: 1;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }

        .company-details {
            font-size: 10px;
            color: #666;
            line-height: 1.4;
        }

        .report-title {
            text-align: right;
            flex: 1;
        }

        .report-title h1 {
            font-size: 18px;
            color: #2563eb;
            margin-bottom: 5px;
        }

        .report-period {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }

        .kpi-section {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            margin-bottom: 25px;
        }

        .kpi-card {
            background-color: #f8fafc;
            border-left: 4px solid #2563eb;
            padding: 12px;
            border-radius: 4px;
        }

        .kpi-label {
            font-size: 10px;
            color: #666;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .kpi-value {
            font-size: 16px;
            font-weight: bold;
            color: #2563eb;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #2563eb;
            margin-top: 20px;
            margin-bottom: 10px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        thead {
            background-color: #f8fafc;
            border-bottom: 2px solid #2563eb;
        }

        th {
            padding: 8px 10px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            color: #2563eb;
            text-transform: uppercase;
        }

        td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11px;
        }

        tbody tr:nth-child(odd) {
            background-color: #f8fafc;
        }

        tbody tr:hover {
            background-color: #eff6ff;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            font-size: 9px;
            color: #999;
            text-align: center;
        }

        .row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .col {
            flex: 1;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 600;
        }

        .status-completed {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-cancelled {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .status-processing {
            background-color: #dbeafe;
            color: #1e40af;
        }
    </style>
</head>
<body>
    <div class="page">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <div class="company-logo-section">
                    <div class="company-name">Colors S.r.l.</div>
                    <div class="company-details">
                        <p>Vendita Online - E-Commerce</p>
                        <p>Italia</p>
                    </div>
                </div>
                <div class="report-title">
                    <h1>Report Generale</h1>
                    <div class="report-period">
                        Periodo: {{ \Carbon\Carbon::createFromFormat('Y-m-d', $dateFrom)->format('d/m/Y') }}
                        - {{ \Carbon\Carbon::createFromFormat('Y-m-d', $dateTo)->format('d/m/Y') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI Summary -->
        <div class="kpi-section">
            <div class="kpi-card">
                <div class="kpi-label">Ricavo Totale</div>
                <div class="kpi-value">€ {{ number_format($totalRevenue, 2, ',', '.') }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Ordini Totali</div>
                <div class="kpi-value">{{ number_format($totalOrders, 0, ',', '.') }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Scontrino Medio</div>
                <div class="kpi-value">€ {{ number_format($avgOrderValue, 2, ',', '.') }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Imposte Totali</div>
                <div class="kpi-value">€ {{ number_format($totalTax, 2, ',', '.') }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Sconti Totali</div>
                <div class="kpi-value">€ {{ number_format($totalDiscount, 2, ',', '.') }}</div>
            </div>
        </div>

        <!-- Orders by Status -->
        <div class="section-title">Ordini per Stato</div>
        <table>
            <thead>
                <tr>
                    <th>Stato</th>
                    <th class="text-right">Numero Ordini</th>
                    <th class="text-right">Valore Totale</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ordersByStatus as $item)
                    <tr>
                        <td>
                            <span class="status-badge status-{{ strtolower($item->status) }}">
                                {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                            </span>
                        </td>
                        <td class="text-right">{{ number_format($item->count, 0, ',', '.') }}</td>
                        <td class="text-right">€ {{ number_format($item->total, 2, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">Nessun dato disponibile</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Orders by Payment Method -->
        <div class="section-title">Ordini per Metodo di Pagamento</div>
        <table>
            <thead>
                <tr>
                    <th>Metodo di Pagamento</th>
                    <th class="text-right">Numero Ordini</th>
                    <th class="text-right">Valore Totale</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ordersByPayment as $item)
                    <tr>
                        <td>{{ $item->payment_method }}</td>
                        <td class="text-right">{{ number_format($item->count, 0, ',', '.') }}</td>
                        <td class="text-right">€ {{ number_format($item->total, 2, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">Nessun dato disponibile</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Orders by Source -->
        <div class="section-title">Ordini per Sorgente</div>
        <table>
            <thead>
                <tr>
                    <th>Sorgente</th>
                    <th class="text-right">Numero Ordini</th>
                    <th class="text-right">Valore Totale</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ordersBySource as $item)
                    <tr>
                        <td>{{ $item->source }}</td>
                        <td class="text-right">{{ number_format($item->count, 0, ',', '.') }}</td>
                        <td class="text-right">€ {{ number_format($item->total, 2, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">Nessun dato disponibile</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Invoices Summary -->
        <div class="section-title">Riepilogo Fatture</div>
        <table>
            <thead>
                <tr>
                    <th>Stato</th>
                    <th class="text-right">Numero Fatture</th>
                    <th class="text-right">Valore Totale</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoicesSummary as $item)
                    <tr>
                        <td>{{ ucfirst(str_replace('_', ' ', $item->status)) }}</td>
                        <td class="text-right">{{ number_format($item->count, 0, ',', '.') }}</td>
                        <td class="text-right">€ {{ number_format($item->total, 2, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">Nessun dato disponibile</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Footer -->
        <div class="footer">
            <p>Documento generato il {{ now()->format('d/m/Y H:i') }}</p>
            <p>Colors S.r.l. - Tutti i diritti riservati</p>
        </div>
    </div>
</body>
</html>
