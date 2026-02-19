<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Fattura {{ $invoice['invoice_number'] }}</title>
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

        .status-paid {
            background-color: #10b981;
            color: white;
        }

        .status-pending {
            background-color: #f59e0b;
            color: white;
        }

        .status-draft {
            background-color: #6b7280;
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
            min-width: 100px;
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

        .totals-section {
            margin: 20px;
            display: flex;
            justify-content: space-between;
            gap: 30px;
        }

        .totals-left {
            flex: 1;
        }

        .totals-box {
            border: 1px solid #e5e7eb;
            background-color: #f9fafb;
            padding: 15px;
            flex: 0 0 250px;
        }

        .totals-box h3 {
            font-size: 10px;
            text-transform: uppercase;
            color: #2563eb;
            margin-bottom: 10px;
            border-bottom: 1px solid #2563eb;
            padding-bottom: 5px;
        }

        .total-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 11px;
        }

        .total-line.large {
            font-size: 14px;
            font-weight: bold;
            color: #2563eb;
            border-top: 2px solid #2563eb;
            padding-top: 8px;
            margin-top: 8px;
        }

        .total-label {
            font-weight: 600;
        }

        .total-value {
            font-family: monospace;
        }

        .footer-section {
            margin: 20px;
            padding: 15px;
            border-top: 1px solid #e5e7eb;
            font-size: 10px;
            color: #666;
        }

        .footer-columns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 15px;
        }

        .footer-box h4 {
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 8px;
            font-size: 10px;
        }

        .payment-method {
            background-color: #eff6ff;
            border-left: 3px solid #2563eb;
            padding: 10px;
            margin-bottom: 10px;
        }

        .notes {
            background-color: #fef3c7;
            border-left: 3px solid #f59e0b;
            padding: 10px;
            margin-bottom: 10px;
        }

        .page-break {
            page-break-after: always;
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
            <h1>FATTURA</h1>
            <div class="status-badge status-{{ strtolower($invoice['status']) }}">
                {{ ucfirst($invoice['status']) }}
            </div>
        </div>
    </div>

    <div class="document-meta">
        <div class="meta-group">
            <div class="meta-label">Numero Fattura</div>
            <div class="meta-value">{{ $invoice['invoice_number'] }}</div>
        </div>
        <div class="meta-group">
            <div class="meta-label">Data Emissione</div>
            <div class="meta-value">{{ $invoice['issue_date'] }}</div>
        </div>
        <div class="meta-group">
            <div class="meta-label">Data Scadenza</div>
            <div class="meta-value">{{ $invoice['due_date'] }}</div>
        </div>
        <div class="meta-group">
            <div class="meta-label">Valuta</div>
            <div class="meta-value">EUR</div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Dati Cliente</div>
        <div class="two-column">
            <div class="column">
                <div class="info-line">
                    <span class="info-label">Cliente:</span>
                    <span class="info-value">{{ $invoice['client_name'] }}</span>
                </div>
                <div class="info-line">
                    <span class="info-label">Indirizzo:</span>
                    <span class="info-value">{{ $invoice['client_address'] }}</span>
                </div>
                <div class="info-line">
                    <span class="info-label">P.IVA:</span>
                    <span class="info-value">{{ $invoice['client_vat'] }}</span>
                </div>
            </div>
            <div class="column">
                <div class="info-line">
                    <span class="info-label">Email:</span>
                    <span class="info-value">{{ $invoice['client_email'] }}</span>
                </div>
                <div class="info-line">
                    <span class="info-label">Codice SDI:</span>
                    <span class="info-value">{{ $invoice['client_sdi_code'] ?? 'N/A' }}</span>
                </div>
                <div class="info-line">
                    <span class="info-label">PEC:</span>
                    <span class="info-value">{{ $invoice['client_pec'] ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div style="margin: 20px;">
        <table>
            <thead>
                <tr>
                    <th style="width: 40%">Descrizione</th>
                    <th class="numeric" style="width: 12%">Quantità</th>
                    <th class="numeric" style="width: 12%">Prezzo Unit.</th>
                    <th class="numeric" style="width: 8%">IVA %</th>
                    <th class="numeric" style="width: 14%">Sconto</th>
                    <th class="numeric" style="width: 14%">Totale</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice['items'] as $item)
                    <tr>
                        <td>{{ $item['description'] }}</td>
                        <td class="numeric">{{ number_format($item['quantity'], 2, ',', '.') }}</td>
                        <td class="numeric">€ {{ number_format($item['unit_price'], 2, ',', '.') }}</td>
                        <td class="numeric">{{ $item['vat_rate'] }}%</td>
                        <td class="numeric">
                            @if($item['discount'] > 0)
                                € {{ number_format($item['discount'], 2, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="numeric">€ {{ number_format($item['total'], 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="totals-section">
        <div class="totals-left">
            @if($invoice['notes'])
                <div class="notes">
                    <strong>Note:</strong><br>
                    {{ $invoice['notes'] }}
                </div>
            @endif
        </div>
        <div class="totals-box">
            <h3>Riepilogo</h3>
            <div class="total-line">
                <span class="total-label">Subtotale:</span>
                <span class="total-value">€ {{ number_format($invoice['subtotal'], 2, ',', '.') }}</span>
            </div>
            @if($invoice['discount_amount'] > 0)
                <div class="total-line">
                    <span class="total-label">Sconto:</span>
                    <span class="total-value">-€ {{ number_format($invoice['discount_amount'], 2, ',', '.') }}</span>
                </div>
            @endif
            <div class="total-line">
                <span class="total-label">Imposte (IVA):</span>
                <span class="total-value">€ {{ number_format($invoice['tax_amount'], 2, ',', '.') }}</span>
            </div>
            <div class="total-line large">
                <span class="total-label">Totale:</span>
                <span class="total-value">€ {{ number_format($invoice['total'], 2, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <div class="footer-section">
        <div class="footer-columns">
            <div class="footer-box">
                <h4>Metodo di Pagamento</h4>
                <div class="payment-method">
                    {{ $invoice['payment_method'] ?? 'Bonifico bancario' }}
                </div>
            </div>
            <div class="footer-box">
                <h4>Informazioni Banca</h4>
                <div>
                    IBAN: IT60X0542811101000000123456<br>
                    BIC: BCITITMM
                </div>
            </div>
        </div>
        <div style="text-align: center; font-size: 9px; color: #999; margin-top: 10px;">
            Documento generato automaticamente. Per questioni relative a questa fattura, contattare Colors S.r.l.
        </div>
    </div>

    <div class="footer">
        <p>Colors S.r.l. | P.IVA IT12345678901 | www.colors.it</p>
    </div>
</body>
</html>
