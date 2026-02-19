<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Resi - Colors S.r.l.</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }

        .page {
            padding: 40px;
            page-break-after: always;
        }

        /* Header */
        .header {
            border-bottom: 3px solid #dc2626;
            padding-bottom: 20px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .header-left h1 {
            font-size: 28px;
            color: #1f2937;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .header-left p {
            font-size: 14px;
            color: #dc2626;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .header-left .subtitle {
            font-size: 12px;
            color: #666;
        }

        .header-right {
            text-align: right;
        }

        .header-right .report-title {
            font-size: 22px;
            font-weight: bold;
            color: #dc2626;
            margin-bottom: 10px;
        }

        .header-right .period {
            font-size: 11px;
            color: #555;
            line-height: 1.6;
        }

        .header-right .period strong {
            display: block;
            margin-bottom: 3px;
        }

        /* KPI Section */
        .kpi-section {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }

        .kpi-card {
            border: 1px solid #e5e7eb;
            border-left: 4px solid #dc2626;
            padding: 15px;
            background-color: #fafafa;
            border-radius: 4px;
        }

        .kpi-card .label {
            font-size: 11px;
            color: #666;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .kpi-card .value {
            font-size: 20px;
            font-weight: bold;
            color: #1f2937;
        }

        .kpi-card .unit {
            font-size: 10px;
            color: #999;
            margin-left: 4px;
        }

        /* Two Column Section */
        .two-column {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid #dc2626;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        table thead {
            background-color: #f3f4f6;
        }

        table thead tr {
            border-bottom: 2px solid #d1d5db;
        }

        table thead th {
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        table tbody td {
            padding: 9px 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }

        table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        table tbody tr:hover {
            background-color: #f3f4f6;
        }

        /* Alignment */
        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-muted {
            color: #999;
        }

        /* Recent Returns Section */
        .recent-returns-section {
            margin-bottom: 20px;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-processing {
            background-color: #bfdbfe;
            color: #1e40af;
        }

        .status-completed {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-rejected {
            background-color: #fee2e2;
            color: #991b1b;
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 30px 10px;
            color: #999;
            font-size: 12px;
        }

        .empty-state p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="page">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <h1>Colors S.r.l.</h1>
                <p>E-Commerce</p>
                <p class="subtitle">Report Resi e Rese</p>
            </div>
            <div class="header-right">
                <div class="report-title">Report Resi</div>
                <div class="period">
                    <strong>Generato:</strong>
                    {{ now()->format('d/m/Y H:i') }}
                    <strong style="margin-top: 5px; display: block;">Periodo:</strong>
                    {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}
                </div>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="kpi-section">
            <div class="kpi-card">
                <div class="label">Resi Totali</div>
                <div class="value">{{ $totalReturns }}</div>
            </div>
            <div class="kpi-card">
                <div class="label">Valore Totale</div>
                <div class="value">€ <span style="font-size: 16px;">{{ number_format($totalReturnValue, 2, ',', '.') }}</span></div>
            </div>
            <div class="kpi-card">
                <div class="label">Tasso di Reso</div>
                <div class="value">{{ $returnRate }}</div>
            </div>
            <div class="kpi-card">
                <div class="label">Valore Medio</div>
                <div class="value">€ <span style="font-size: 16px;">{{ $totalReturns > 0 ? number_format($totalReturnValue / $totalReturns, 2, ',', '.') : '0,00' }}</span></div>
            </div>
        </div>

        <!-- Two Column Section: Resi per Stato | Movimenti Magazzino -->
        <div class="two-column">
            <!-- Resi per Stato -->
            <div>
                <div class="section-title">Resi per Stato</div>
                <table>
                    <thead>
                        <tr>
                            <th>Stato</th>
                            <th class="text-center">Numero</th>
                            <th class="text-right">Valore (€)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($returnsByStatus as $item)
                            <tr>
                                <td>{{ $item->return_status ?? 'N/A' }}</td>
                                <td class="text-center">{{ $item->count }}</td>
                                <td class="text-right">{{ number_format($item->total, 2, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">Nessun dato disponibile</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Movimenti Magazzino -->
            <div>
                <div class="section-title">Movimenti Magazzino</div>
                <table>
                    <thead>
                        <tr>
                            <th>Motivo</th>
                            <th class="text-center">Num. Articoli</th>
                            <th class="text-right">Quantità</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($warehouseReturns as $item)
                            <tr>
                                <td>{{ $item->reason ?? 'N/A' }}</td>
                                <td class="text-center">{{ $item->count }}</td>
                                <td class="text-right">{{ $item->total_qty }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">Nessun dato disponibile</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Returns Section -->
        <div class="recent-returns-section">
            <div class="section-title">Ultimi Resi</div>
            @forelse($recentReturns as $return)
                @if($loop->first)
                    <table>
                        <thead>
                            <tr>
                                <th>N. Ordine</th>
                                <th>Cliente</th>
                                <th class="text-center">Stato</th>
                                <th>Motivo</th>
                                <th class="text-right">Totale (€)</th>
                                <th class="text-center">Data</th>
                            </tr>
                        </thead>
                        <tbody>
                @endif
                            <tr>
                                <td><strong>{{ $return->order_number }}</strong></td>
                                <td>{{ $return->user->name ?? 'N/A' }}</td>
                                <td class="text-center">
                                    <span class="status-badge status-{{ strtolower(str_replace(' ', '_', $return->return_status ?? 'pending')) }}">
                                        {{ $return->return_status ?? 'Sconosciuto' }}
                                    </span>
                                </td>
                                <td>{{ $return->return_reason ?? '-' }}</td>
                                <td class="text-right">{{ number_format($return->total, 2, ',', '.') }}</td>
                                <td class="text-center">{{ $return->return_requested_at?->format('d/m/Y') ?? '-' }}</td>
                            </tr>
                @if($loop->last)
                        </tbody>
                    </table>
                @endif
            @empty
                <div class="empty-state">
                    <p>Nessun reso disponibile nel periodo selezionato.</p>
                </div>
            @endforelse
        </div>

        <!-- Footer -->
        <div style="margin-top: 40px; padding-top: 15px; border-top: 1px solid #e5e7eb; font-size: 10px; color: #999; text-align: center;">
            <p>Questo documento è stato generato automaticamente dal sistema di gestione Colors S.r.l. - {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
