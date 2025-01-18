<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $metadata['type'] }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
            padding: 0;
        }
        .header p {
            color: #666;
            margin: 5px 0;
        }
        .city-info {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .city-info h2 {
            font-size: 16px;
            margin: 0 0 5px 0;
            padding: 0;
        }
        .city-info p {
            margin: 2px 0;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .text-end {
            text-align: right;
        }
        .text-success {
            color: #198754;
        }
        .text-danger {
            color: #dc3545;
        }
        .text-primary {
            color: #0d6efd;
        }
        tfoot tr {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #666;
            padding: 10px 0;
            border-top: 1px solid #ddd;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    @php
        $citySettings = App\Models\CitySetting::first();
    @endphp

    @if($citySettings)
    <div class="city-info">
        <h2>{{ $citySettings->city_hall_name }}</h2>
        <p>{{ $citySettings->address }} - {{ $citySettings->city_name }}/{{ $citySettings->state }}</p>
        @if($citySettings->zip_code)
            <p>CEP: {{ $citySettings->zip_code }}</p>
        @endif
        @if($citySettings->phone || $citySettings->email)
            <p>
                @if($citySettings->phone)
                    Telefone: {{ $citySettings->phone }}
                @endif
                @if($citySettings->phone && $citySettings->email)
                    -
                @endif
                @if($citySettings->email)
                    Email: {{ $citySettings->email }}
                @endif
            </p>
        @endif
        @if($citySettings->mayor_name)
            <p>Prefeito: {{ $citySettings->mayor_name }}</p>
        @endif
    </div>
    @endif

    <div class="header">
        <h1>{{ $metadata['type'] }}</h1>
        <p>{{ $metadata['period'] }}</p>
        <p>Gerado em: {{ $metadata['generated_at']->format('d/m/Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Período</th>
                @if($type === 'balance')
                    <th class="text-end">Receitas</th>
                    <th class="text-end">Despesas</th>
                    <th class="text-end">Saldo</th>
                @else
                    <th class="text-end">Total</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
                <tr>
                    <td>
                        @if($group_by === 'daily')
                            {{ \Carbon\Carbon::createFromFormat('Y-m-d', $item['period'])->format('d/m/Y') }}
                        @elseif($group_by === 'monthly')
                            {{ \Carbon\Carbon::createFromFormat('Y-m', $item['period'])->format('m/Y') }}
                        @else
                            {{ $item['period'] }}
                        @endif
                    </td>
                    @if($type === 'balance')
                        <td class="text-end text-success">R$ {{ number_format($item['revenues'], 2, ',', '.') }}</td>
                        <td class="text-end text-danger">R$ {{ number_format($item['expenses'], 2, ',', '.') }}</td>
                        <td class="text-end {{ $item['balance'] >= 0 ? 'text-primary' : 'text-danger' }}">
                            R$ {{ number_format(abs($item['balance']), 2, ',', '.') }}
                        </td>
                    @else
                        <td class="text-end {{ $type === 'revenues' ? 'text-success' : 'text-danger' }}">
                            R$ {{ number_format($item['total'], 2, ',', '.') }}
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $type === 'balance' ? 4 : 2 }}" style="text-align: center">
                        Nenhum registro encontrado
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($items->count() > 0)
            <tfoot>
                <tr>
                    <td>Total</td>
                    @if($type === 'balance')
                        <td class="text-end text-success">
                            R$ {{ number_format($items->sum('revenues'), 2, ',', '.') }}
                        </td>
                        <td class="text-end text-danger">
                            R$ {{ number_format($items->sum('expenses'), 2, ',', '.') }}
                        </td>
                        @php
                            $totalBalance = $items->sum('revenues') - $items->sum('expenses');
                        @endphp
                        <td class="text-end {{ $totalBalance >= 0 ? 'text-primary' : 'text-danger' }}">
                            R$ {{ number_format(abs($totalBalance), 2, ',', '.') }}
                        </td>
                    @else
                        <td class="text-end {{ $type === 'revenues' ? 'text-success' : 'text-danger' }}">
                            R$ {{ number_format($items->sum('total'), 2, ',', '.') }}
                        </td>
                    @endif
                </tr>
            </tfoot>
        @endif
    </table>

    <div class="footer">
        {{ config('app.name') }} - Relatório gerado em {{ $metadata['generated_at']->format('d/m/Y H:i:s') }}
    </div>
</body>
</html> 