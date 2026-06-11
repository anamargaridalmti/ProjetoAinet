<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1a1a1a; padding: 30px; }

        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px; border-bottom: 2px solid #111; padding-bottom: 20px; }
        .brand { font-size: 28px; font-weight: 900; letter-spacing: -1px; color: #111; }
        .brand span { color: #e11d48; }
        .brand-sub { font-size: 10px; color: #666; margin-top: 2px; }
        .receipt-meta { text-align: right; }
        .receipt-meta h2 { font-size: 18px; font-weight: 700; }
        .receipt-meta p { font-size: 11px; color: #555; margin-top: 2px; }

        .section { margin-bottom: 24px; }
        .section-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #666; margin-bottom: 8px; border-bottom: 1px solid #e5e5e5; padding-bottom: 4px; }

        .client-grid { display: flex; gap: 40px; }
        .client-col p { font-size: 11px; line-height: 1.6; color: #333; }
        .client-col strong { color: #111; }

        table { width: 100%; border-collapse: collapse; }
        thead tr { background-color: #111; color: white; }
        thead th { padding: 8px 10px; text-align: left; font-size: 11px; font-weight: 700; }
        tbody tr { border-bottom: 1px solid #f0f0f0; }
        tbody tr:nth-child(even) { background-color: #f9f9f9; }
        tbody td { padding: 7px 10px; font-size: 11px; color: #333; }
        tfoot tr td { padding: 8px 10px; font-weight: 700; background: #f5f5f5; }
        .text-right { text-align: right; }
        .total-row td { font-size: 14px; background: #111 !important; color: white !important; }

        .footer { margin-top: 40px; text-align: center; font-size: 9px; color: #999; border-top: 1px solid #e5e5e5; padding-top: 12px; }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <div>
            <div class="brand">Fun<span>Shirt</span></div>
            <div class="brand-sub">A sua loja de t-shirts personalizadas</div>
        </div>
        <div class="receipt-meta">
            <h2>RECIBO / FATURA</h2>
            <p>Nº Encomenda: #{{ $order->id }}</p>
            <p>Data: {{ $order->date->format('d/m/Y') }}</p>
            <p>Estado: {{ strtoupper($order->status) }}</p>
        </div>
    </div>

    {{-- Client Info --}}
    <div class="section">
        <div class="section-title">Dados do Cliente</div>
        <div class="client-grid">
            <div class="client-col">
                <p><strong>Nome:</strong> {{ $order->customer->user->name ?? 'N/A' }}</p>
                <p><strong>E-mail:</strong> {{ $order->customer->user->email ?? 'N/A' }}</p>
            </div>
            <div class="client-col">
                <p><strong>NIF:</strong> {{ $order->nif }}</p>
                <p><strong>Endereço:</strong> {{ $order->address }}</p>
            </div>
            <div class="client-col">
                <p><strong>Pagamento:</strong> {{ $order->payment_type }}</p>
            </div>
        </div>
    </div>

    {{-- Items --}}
    <div class="section">
        <div class="section-title">Itens da Encomenda</div>
        <table>
            <thead>
                <tr>
                    <th>Imagem</th>
                    <th>Cor</th>
                    <th>Tamanho</th>
                    <th class="text-right">Qtd.</th>
                    <th class="text-right">Preço Unit.</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>#{{ $item->tshirt_image_id }}</td>
                        <td>{{ $item->color_code }}</td>
                        <td>{{ $item->size }}</td>
                        <td class="text-right">{{ $item->qty }}</td>
                        <td class="text-right">{{ number_format($item->unit_price, 2, ',', '.') }}€</td>
                        <td class="text-right">{{ number_format($item->sub_total, 2, ',', '.') }}€</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="5" class="text-right">TOTAL A PAGAR</td>
                    <td class="text-right">{{ number_format($order->total_price, 2, ',', '.') }}€</td>
                </tr>
            </tfoot>
        </table>
    </div>

    @if($order->notes)
    <div class="section">
        <div class="section-title">Notas da Encomenda</div>
        <p style="font-size: 11px; color: #555;">{{ $order->notes }}</p>
    </div>
    @endif

    <div class="footer">
        FunShirt &bull; Obrigado pela sua compra! &bull; Este documento serve como comprovativo de pagamento.
    </div>

</body>
</html>
