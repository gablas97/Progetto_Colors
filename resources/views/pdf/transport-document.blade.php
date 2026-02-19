<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>DDT {{ $ddt['document_number'] }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.6;
            font-size: 11px;
        }

        page {
            page-break-after: always;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 20px;
            border-bottom: 3px solid #2563eb;
            margin-bottom: 20px;
        }

        .company-info {
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
            line-height: 1.5;
        }

        .document-title {
            text-align: right;
            flex: 1;
        }

        .document-title h1 {
            font-size: 32px;
            color: #2563eb;
            margin-bottom: 10px;
            letter-spacing: 2px;
        }

        .document-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
            padding: 0 20px;
        }

        .meta-group {
            border-left: 2px solid #2563eb;
            padding-left: 10px;
        }

        .meta-label {
            font-weight: bold;
            color: #2563eb;
            font-size: 10px;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .meta-value {
            font-size: 12px;
            color: #333;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            margin-top: 5px;
        }

        .status-shipped {
            background-color: #0891b2;
            color: white;
        }

        .status-delivered {
            background-color: #10b981;
            color: white;
        }

        .status-pending {
            background-color: #f59e0b;
            color: white;
        }

        .section {
            margin: 20px;
            padding: 15px;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
        }

        .section-title {
            font-weight: bold;
            color: #2563eb;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #2563eb;
        }

        .two-column {
            display: flex;
            gap: 30px;
            margin-bottom: 20px;
        }

        .column {
            flex: 1;
        }

        .info-line {
            margin-bottom: 6px;
            display: flex;
        }

        .info-label {
            font-weight: bold;
            min-width: 120px;
            color: #2563eb;
        }

        .info-value {
            flex: 1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        table thead {
            background-color: #2563eb;
            color: white;
        }

        table th {
            padding: 10px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            border-bottom: 2px solid #2563eb;
        }

        table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        table tbody tr:nth-child(even) {
            background-color: #f3f4f6;
        }

        .numeric {
            text-align: right;
            font-family: monospace;
        }

        .info-box {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .info-block {
            background-color: #eff6ff;
            border-left: 3px solid #2563eb;
            padding: 10px;
            font-size: 10px;
        }

        .info-block-title {
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
            text-transform: uppercase;
            font-size: 9px;
        }

        .footer-section {
            margin: 20px;
            padding: 15px;
            border-top: 1px solid #e5e7eb;
            font-size: 10px;
            color: #666;
        }

        .notes {
            background-color: #fef3c7;
            border-left: 3px solid #f59e0b;
            padding: 10px;
            margin-bottom: 10px;
        }

        .signature-section {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }

        .signature-box {
            text-align: center;
            font-size: 10px;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
        }

        .totals-summary {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px;
        }

        .summary-box {
            border: 1px solid #e5e7eb;
            background-color: #f9fafb;
            padding: 15px;
        }

        .summary-box h3 {
            font-size: 10px;
            text-transform: uppercase;
            color: #2563eb;
            margin-bottom: 10px;
            border-bottom: 1px solid #2563eb;
            padding-bottom: 5px;
        }

        .summary-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 10px;
        }

        .summary-label {
            font-weight: 600;
        }

        .summary-value {
            font-family: monospace;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            font-size: 9px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-info">
            <div class="company-name">Colors S.r.l.</div>
            <div class="company-details">
                <div>Via Roma, 123 - 20100 Milano (MI)</div>
                <div>Tel: +39 02 1234567</div>
                <div>Email: info@colors.it</div>
                <div>P.IVA: IT12345678901</div>
            </div>
        </div>
        <div class="document-title">
            <h1>DDT</h1>
            <div class="status-badge status-{{ strtolower($ddt['status']) }}">
                {{ ucfirst($ddt['status']) }}
            </div>
        </div>
    </div>

    <div class="document-meta">
        <div class="meta-group">
            <div class="meta-label">Numero Documento</div>
            <div class="meta-value">{{ $ddt['document_number'] }}</div>
        </div>
        <div class="meta-group">
            <div class="meta-label">Data Trasporto</div>
            <div class="meta-value">{{ $ddt['transport_date'] }}</div>
        </div>
        <div class="meta-group">
            <div class="meta-label">Causale Trasporto</div>
            <div class="meta-value">{{ $ddt['reason_for_transport'] ?? 'Vendita' }}</div>
        </div>
        <div class="meta-group">
            <div class="meta-label">Metodo Spedizione</div>
            <div class="meta-value">{{ $ddt['shipping_method'] ?? 'Ritiro' }}</div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Informazioni di Trasporto</div>
        <div class="info-box">
            <div class="info-block">
                <div class="info-block-title">Mittente</div>
                <div class="info-line">
                    <span class="info-value"><strong>Colors S.r.l.</strong></span>
                </div>
                <div class="info-line">
                    <span class="info-value">Via Roma, 123<br>20100 Milano (MI)</span>
                </div>
            </div>
            <div class="info-block">
                <div class="info-block-title">Destinatario</div>
                <div class="info-line">
                    <span class="info-value"><strong>{{ $ddt['destination_name'] }}</strong></span>
                </div>
                <div class="info-line">
                    <span class="info-value">{{ $ddt['destination_address'] }}</span>
                </div>
            </div>
            <div class="info-block">
                <div class="info-block-title">Trasportatore</div>
                <div class="info-line">
                    <span class="info-value"><strong>{{ $ddt['carrier_name'] ?? 'Da definire' }}</strong></span>
                </div>
                <div class="info-line">
                    <span class="info-value">{{ $ddt['carrier_info'] ?? '' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="totals-summary">
        <div class="summary-box">
            <h3>Riepilogo Spedizione</h3>
            <div class="summary-line">
                <span class="summary-label">Numero Colli:</span>
                <span class="summary-value">{{ $ddt['packages'] ?? '-' }}</span>
            </div>
            <div class="summary-line">
                <span class="summary-label">Peso Totale:</span>
                <span class="summary-value">{{ $ddt['total_weight'] ?? '-' }} kg</span>
            </div>
            <div class="summary-line">
                <span class="summary-label">Metodo Spedizione:</span>
                <span class="summary-value">{{ $ddt['shipping_method'] ?? 'Non specificato' }}</span>
            </div>
        </div>
        <div class="summary-box">
            <h3>Dettagli Documento</h3>
            <div class="summary-line">
                <span class="summary-label">Data Emissione:</span>
                <span class="summary-value">{{ $ddt['transport_date'] }}</span>
            </div>
            <div class="summary-line">
                <span class="summary-label">Causale Trasporto:</span>
                <span class="summary-value">{{ $ddt['reason_for_transport'] ?? 'Vendita' }}</span>
            </div>
            <div class="summary-line">
                <span class="summary-label">Stato:</span>
                <span class="summary-value">{{ ucfirst($ddt['status']) }}</span>
            </div>
        </div>
    </div>

    <div style="margin: 20px;">
        <table>
            <thead>
                <tr>
                    <th style="width: 50%">Descrizione Merce</th>
                    <th class="numeric" style="width: 15%">Quantità</th>
                    <th class="numeric" style="width: 15%">Peso Unit. (kg)</th>
                    <th class="numeric" style="width: 20%">Peso Totale (kg)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ddt['items'] as $item)
                    <tr>
                        <td>{{ $item['description'] }}</td>
                        <td class="numeric">{{ number_format($item['quantity'], 2, ',', '.') }}</td>
                        <td class="numeric">{{ number_format($item['unit_weight'] ?? 0, 3, ',', '.') }}</td>
                        <td class="numeric">{{ number_format($item['weight'] ?? 0, 3, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer-section">
        @if($ddt['notes'])
            <div class="notes">
                <strong>Note e Osservazioni:</strong><br>
                {{ $ddt['notes'] }}
            </div>
        @endif
        <div style="margin-top: 15px; font-size: 9px; color: #666;">
            <strong>Informazioni Aggiuntive:</strong><br>
            Numero Articoli: {{ count($ddt['items']) }} | Peso Complessivo: {{ $ddt['total_weight'] ?? '-' }} kg | Colli: {{ $ddt['packages'] ?? '-' }}
        </div>
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <strong>Mittente</strong><br>
            <span style="font-size: 9px;">Colors S.r.l.</span><br>
            <div class="signature-line"></div>
        </div>
        <div class="signature-box">
            <strong>Trasportatore</strong><br>
            <span style="font-size: 9px;">{{ $ddt['carrier_name'] ?? '' }}</span><br>
            <div class="signature-line"></div>
        </div>
        <div class="signature-box">
            <strong>Destinatario</strong><br>
            <span style="font-size: 9px;">{{ $ddt['destination_name'] }}</span><br>
            <div class="signature-line"></div>
        </div>
    </div>

    <div class="footer">
        <p>Colors S.r.l. | P.IVA IT12345678901 | www.colors.it</p>
    </div>
</body>
</html>
