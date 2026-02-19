<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Fedeltà - Colors S.r.l.</title>
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
            border-bottom: 3px solid #7c3aed;
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
            color: #7c3aed;
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
            color: #7c3aed;
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
            border-left: 4px solid #7c3aed;
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
            border-bottom: 2px solid #7c3aed;
        }

        /* Tier Grid */
        .tier-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .tier-card {
            border: 1px solid #e5e7eb;
            padding: 12px;
            background-color: #f9fafb;
            border-radius: 4px;
            text-align: center;
        }

        .tier-name {
            font-size: 12px;
            font-weight: bold;
            color: #374151;
            margin-bottom: 6px;
            text-transform: capitalize;
        }

        .tier-count {
            font-size: 18px;
            font-weight: bold;
            color: #7c3aed;
            margin-bottom: 3px;
        }

        .tier-percent {
            font-size: 10px;
            color: #999;
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

        /* Tier Badges */
        .tier-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-transform: capitalize;
        }

        .tier-base {
            background-color: #f3f4f6;
            color: #374151;
        }

        .tier-silver {
            background-color: #e8e8e8;
            color: #404040;
        }

        .tier-gold {
            background-color: #fef3c7;
            color: #92400e;
        }

        .tier-platinum {
            background-color: #dbeafe;
            color: #0c4a6e;
        }

        /* Top Customers Section */
        .top-customers-section {
            margin-bottom: 20px;
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

        .position-badge {
            display: inline-block;
            width: 24px;
            height: 24px;
            background-color: #7c3aed;
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 24px;
            font-weight: bold;
            font-size: 11px;
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
                <p class="subtitle">Programma Fedeltà</p>
            </div>
            <div class="header-right">
                <div class="report-title">Report Fedeltà</div>
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
                <div class="label">Carte Attive</div>
                <div class="value">{{ $totalCards }}</div>
            </div>
            <div class="kpi-card">
                <div class="label">Punti in Circolo</div>
                <div class="value">{{ number_format($totalPoints, 0, ',', '.') }}</div>
            </div>
            <div class="kpi-card">
                <div class="label">Spesa Totale</div>
                <div class="value">€ <span style="font-size: 16px;">{{ number_format($totalSpent, 2, ',', '.') }}</span></div>
            </div>
            <div class="kpi-card">
                <div class="label">Media Spesa/Carta</div>
                <div class="value">€ <span style="font-size: 16px;">{{ $totalCards > 0 ? number_format($totalSpent / $totalCards, 2, ',', '.') : '0,00' }}</span></div>
            </div>
        </div>

        <!-- Two Column Section: Distribuzione Tier | Transazioni -->
        <div class="two-column">
            <!-- Distribuzione per Tier -->
            <div>
                <div class="section-title">Distribuzione per Tier</div>
                <div class="tier-grid">
                    @php
                        $tiers = ['base' => 0, 'silver' => 0, 'gold' => 0, 'platinum' => 0];
                        foreach($cardsByTier as $item) {
                            $tierKey = strtolower($item->tier ?? 'base');
                            $tiers[$tierKey] = $item->count;
                        }
                        $grandTotal = array_sum($tiers);
                    @endphp

                    @foreach($tiers as $tierName => $count)
                        <div class="tier-card">
                            <div class="tier-name">{{ ucfirst($tierName) }}</div>
                            <div class="tier-count">{{ $count }}</div>
                            <div class="tier-percent">{{ $grandTotal > 0 ? number_format(($count / $grandTotal) * 100, 1, ',', '.') : '0,0' }}%</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Transazioni -->
            <div>
                <div class="section-title">Transazioni</div>
                <table>
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th class="text-center">Numero</th>
                            <th class="text-right">Punti Totali</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactionsSummary as $item)
                            <tr>
                                <td>{{ ucfirst($item->type ?? 'N/A') }}</td>
                                <td class="text-center">{{ $item->count }}</td>
                                <td class="text-right">{{ number_format($item->total_points, 0, ',', '.') }}</td>
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

        <!-- Top Customers Section -->
        <div class="top-customers-section">
            <div class="section-title">Top 10 Clienti</div>
            @forelse($topCustomers as $customer)
                @if($loop->first)
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 30px;">#</th>
                                <th>Nome Cliente</th>
                                <th class="text-center">N. Carta</th>
                                <th class="text-center">Tier</th>
                                <th class="text-right">Punti</th>
                                <th class="text-right">Spesa Totale (€)</th>
                            </tr>
                        </thead>
                        <tbody>
                @endif
                            <tr>
                                <td class="text-center">
                                    <span class="position-badge">{{ $loop->iteration }}</span>
                                </td>
                                <td><strong>{{ $customer->user->name ?? 'N/A' }}</strong></td>
                                <td class="text-center">{{ $customer->card_number ?? 'N/A' }}</td>
                                <td class="text-center">
                                    <span class="tier-badge tier-{{ strtolower($customer->tier ?? 'base') }}">
                                        {{ ucfirst($customer->tier ?? 'Base') }}
                                    </span>
                                </td>
                                <td class="text-right"><strong>{{ number_format($customer->points, 0, ',', '.') }}</strong></td>
                                <td class="text-right">{{ number_format($customer->total_spent, 2, ',', '.') }}</td>
                            </tr>
                @if($loop->last)
                        </tbody>
                    </table>
                @endif
            @empty
                <div class="empty-state">
                    <p>Nessun cliente disponibile nel periodo selezionato.</p>
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
