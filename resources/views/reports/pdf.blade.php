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
        .filters-info {
            margin: 15px 0;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .filters-info h3 {
            font-size: 14px;
            margin: 0 0 10px 0;
            color: #0d6efd;
        }
        .filters-info ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .filters-info li {
            margin: 3px 0;
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
        <p>Agrupamento: {{ $metadata['group_by'] }}</p>
        <p>Gerado em: {{ $metadata['generated_at']->format('d/m/Y H:i:s') }}</p>
    </div>

    @if(!empty($filters['category_id']) || !empty($filters['block_id']) || !empty($filters['group_id']) || !empty($filters['action_id']) || !empty($filters['expense_classification_id']))
        <div class="filters-info">
            <h3>Filtros Aplicados</h3>
            <ul>
                @if(!empty($filters['category_id']))
                    <li><strong>Fonte:</strong> {{ \App\Models\Category::find($filters['category_id'])->name }}</li>
                @endif
                @if(!empty($filters['block_id']))
                    <li><strong>Bloco:</strong> {{ \App\Models\Category::find($filters['block_id'])->name }}</li>
                @endif
                @if(!empty($filters['group_id']))
                    <li><strong>Grupo:</strong> {{ \App\Models\Category::find($filters['group_id'])->name }}</li>
                @endif
                @if(!empty($filters['action_id']))
                    <li><strong>Ação:</strong> {{ \App\Models\Category::find($filters['action_id'])->name }}</li>
                @endif
                @if(!empty($filters['expense_classification_id']))
                    <li><strong>Classificação de Despesa:</strong> {{ \App\Models\ExpenseClassification::find($filters['expense_classification_id'])->name }}</li>
                @endif
            </ul>
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Período</th>
                @if($filters['report_type'] === 'balance')
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
                        @if($filters['group_by'] === 'daily')
                            {{ \Carbon\Carbon::createFromFormat('Y-m-d', $item['period'])->format('d/m/Y') }}
                        @elseif($filters['group_by'] === 'monthly')
                            {{ \Carbon\Carbon::createFromFormat('Y-m', $item['period'])->format('m/Y') }}
                        @else
                            {{ $item['period'] }}
                        @endif
                    </td>
                    @if($filters['report_type'] === 'balance')
                        <td class="text-end text-success">R$ {{ number_format($item['revenues'], 2, ',', '.') }}</td>
                        <td class="text-end text-danger">R$ {{ number_format($item['expenses'], 2, ',', '.') }}</td>
                        <td class="text-end {{ $item['balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                            R$ {{ number_format(abs($item['balance']), 2, ',', '.') }}
                        </td>
                    @else
                        <td class="text-end {{ $filters['report_type'] === 'revenues' ? 'text-success' : 'text-danger' }}">
                            R$ {{ number_format($item['total'], 2, ',', '.') }}
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $filters['report_type'] === 'balance' ? 4 : 2 }}" style="text-align: center">
                        Nenhum registro encontrado
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($items->count() > 0)
        <tfoot>
            <tr>
                <td><strong>Total</strong></td>
                @if($filters['report_type'] === 'balance')
                    <td class="text-end text-success"><strong>R$ {{ number_format($items->sum('revenues'), 2, ',', '.') }}</strong></td>
                    <td class="text-end text-danger"><strong>R$ {{ number_format($items->sum('expenses'), 2, ',', '.') }}</strong></td>
                    <td class="text-end {{ $items->sum('balance') >= 0 ? 'text-success' : 'text-danger' }}">
                        <strong>R$ {{ number_format(abs($items->sum('balance')), 2, ',', '.') }}</strong>
                    </td>
                @else
                    <td class="text-end {{ $filters['report_type'] === 'revenues' ? 'text-success' : 'text-danger' }}">
                        <strong>R$ {{ number_format($items->sum('total'), 2, ',', '.') }}</strong>
                    </td>
                @endif
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer">
        {{ $citySettings ? $citySettings->city_hall_name : 'Sistema de Gestão Financeira' }} - Relatório gerado em {{ $metadata['generated_at']->format('d/m/Y H:i:s') }}
    </div>
</body>
</html> 