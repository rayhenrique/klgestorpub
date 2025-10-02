<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ config('app.name') }} - {{ $metadata['type'] }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
            padding: 10px 0;
            border-bottom: 2px solid #333;
        }
        .header p {
            color: #666;
            margin: 5px 0;
        }
        .city-info {
            text-align: center;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .city-info h2 {
            font-size: 16px;
            margin: 0 0 5px 0;
            padding: 0;
            color: #333;
        }
        .city-info h3 {
            font-size: 14px;
            margin: 0 0 10px 0;
            padding: 0;
            color: #444;
        }
        .city-info p {
            margin: 3px 0;
            font-size: 11px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
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
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
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

    <div class="header">
        <div class="city-info">
            <h2>{{ $citySettings->city_hall_name ?? 'Prefeitura Municipal' }}</h2>
            <h3>{{ $citySettings->city_name ?? '' }}</h3>
            <p>
                {{ $citySettings->address ?? '' }}
                @if($citySettings->zip_code)
                    - CEP: {{ substr($citySettings->zip_code, 0, 5) . '-' . substr($citySettings->zip_code, 5) }}
                @endif
                @if($citySettings->state)
                    - {{ $citySettings->state }}
                @endif
            </p>
            @if($citySettings->phone || $citySettings->email)
                <p>
                    @if($citySettings->phone)
                        Telefone: {{ $citySettings->phone }}
                    @endif
                    @if($citySettings->phone && $citySettings->email)
                        -
                    @endif
                    @if($citySettings->email)
                        E-mail: {{ $citySettings->email }}
                    @endif
                </p>
            @endif
            @if($citySettings->mayor_name)
                <p>Prefeito: {{ $citySettings->mayor_name }}</p>
            @endif
        </div>

        <h1>{{ $metadata['title'] }}</h1>
        <p>Período: {{ $metadata['period'] }}</p>
        @if(isset($metadata['category']))
            <p>{{ $metadata['category_type'] }}: {{ $metadata['category'] }}</p>
        @endif
        @if(isset($metadata['classification']))
            <p>Classificação: {{ $metadata['classification'] }}</p>
        @endif
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
                    <th>Fonte</th>
                    <th>Bloco</th>
                    <th>Grupo</th>
                    <th>Ação</th>
                    <th class="text-end">Receitas</th>
                    <th class="text-end">Despesas</th>
                    <th class="text-end">Saldo</th>
                @else
                    <th>Fonte</th>
                    <th>Bloco</th>
                    <th>Grupo</th>
                    <th>Ação</th>
                    <th class="text-end">Total</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
                <tr>
                    <td>
                        @if($filters['group_by'] === 'daily')
                            {{ \Carbon\Carbon::parse($item['period'])->format('d/m/Y') }}
                        @elseif($filters['group_by'] === 'monthly')
                            {{ \Carbon\Carbon::parse($item['period'])->format('m/Y') }}
                        @else
                            {{ \Carbon\Carbon::parse($item['period'])->format('d/m/Y') }}
                        @endif
                    </td>
                    @if($filters['report_type'] === 'balance')
                        <td>{{ $item['fonte'] ?? '-' }}</td>
                        <td>{{ $item['bloco'] ?? '-' }}</td>
                        <td>{{ $item['grupo'] ?? '-' }}</td>
                        <td>{{ $item['acao'] ?? '-' }}</td>
                        <td class="text-end text-success">R$ {{ number_format($item['revenues'], 2, ',', '.') }}</td>
                        <td class="text-end text-danger">R$ {{ number_format($item['expenses'], 2, ',', '.') }}</td>
                        <td class="text-end {{ $item['balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                            R$ {{ number_format(abs($item['balance']), 2, ',', '.') }}
                        </td>
                    @else
                        <td>{{ $item['fonte'] ?? '-' }}</td>
                        <td>{{ $item['bloco'] ?? '-' }}</td>
                        <td>{{ $item['grupo'] ?? '-' }}</td>
                        <td>{{ $item['acao'] ?? '-' }}</td>
                        <td class="text-end {{ $filters['report_type'] === 'revenues' ? 'text-success' : 'text-danger' }}">
                            R$ {{ number_format($item['total'], 2, ',', '.') }}
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $filters['report_type'] === 'balance' ? 8 : 6 }}" style="text-align: center">
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
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td class="text-end text-success"><strong>R$ {{ number_format($items->sum('revenues'), 2, ',', '.') }}</strong></td>
                    <td class="text-end text-danger"><strong>R$ {{ number_format($items->sum('expenses'), 2, ',', '.') }}</strong></td>
                    <td class="text-end {{ $items->sum('balance') >= 0 ? 'text-success' : 'text-danger' }}">
                        <strong>R$ {{ number_format(abs($items->sum('balance')), 2, ',', '.') }}</strong>
                    </td>
                @else
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
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