<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use App\Models\CitySetting;
use App\Models\Category;
use App\Models\ExpenseClassification;

class FinancialReport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles
{
    protected $data;
    protected $citySettings;
    protected $currentRow;

    public function __construct($data)
    {
        $this->data = $data;
        $this->citySettings = CitySetting::first();
        $this->currentRow = 1;
    }

    public function collection()
    {
        try {
            \Log::info('Iniciando collection do Excel', [
                'items_count' => count($this->data['items']),
                'filters' => $this->data['filters']
            ]);

            // Adiciona linhas em branco para o cabeçalho
            $header = collect([
                [], // Título
                [], // Tipo
                [], // Período
                [], // Linha em branco
                []  // Cabeçalho da tabela
            ]);
            
            // Verifica se items é um array aninhado ou um array simples
            if (isset($this->data['items']['items'])) {
                $items = collect($this->data['items']['items']);
            } else {
                $items = collect($this->data['items']);
            }

            // Converte os objetos Revenue em arrays
            $items = $items->map(function ($item) {
                if (is_object($item) && method_exists($item, 'toArray')) {
                    return $item->toArray();
                }
                return (array) $item;
            });

            \Log::info('Collection preparada', [
                'header_count' => $header->count(),
                'items_count' => $items->count(),
                'sample_item' => $items->first()
            ]);

            // Retorna a coleção completa
            return new Collection(array_merge(
                $header->toArray(),
                $items->toArray()
            ));
        } catch (\Exception $e) {
            \Log::error('Erro no método collection: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    public function headings(): array
    {
        $title = $this->data['metadata']['title'] ?? 'Relatório Financeiro';
        $period = $this->data['metadata']['period'] ?? '';
        
        return [
            [$this->data['metadata']['city_name'] ?? 'Teste Nome da Cidade'],
            [$this->data['metadata']['type'] ?? 'Receitas'],
            [$period],
            [],
            ['Período', 'Total']
        ];
    }

    public function map($row): array
    {
        try {
            // Se for uma das 5 primeiras linhas (cabeçalho), retorna array vazio
            if ($this->currentRow <= 5) {
                $this->currentRow++;
                return [];
            }

            // Se não houver dados na linha (pode acontecer se $row estiver vazio)
            if (empty($row)) {
                return [];
            }

            \Log::info('Mapeando linha', [
                'row' => $row,
                'current_row' => $this->currentRow,
                'row_type' => gettype($row)
            ]);

            // Garante que temos acesso aos dados corretos
            $period = $row['period'] ?? null;
            $total = $row['total'] ?? 0;

            if (!$period) {
                \Log::warning('Linha sem período válido', ['row' => $row]);
                return [];
            }

            $formattedPeriod = match($this->data['filters']['group_by']) {
                'daily' => Carbon::parse($period)->format('d/m/Y'),
                'monthly' => Carbon::parse($period)->format('m/Y'),
                'yearly' => $period,
                default => $period
            };

            if ($this->data['filters']['report_type'] === 'balance') {
                return [
                    $formattedPeriod,
                    floatval($row['revenues'] ?? 0),
                    floatval($row['expenses'] ?? 0),
                    floatval($row['balance'] ?? 0)
                ];
            }

            $this->currentRow++;
            return [
                $formattedPeriod,
                floatval($total)
            ];
        } catch (\Exception $e) {
            \Log::error('Erro no método map: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            \Log::error('Dados da linha: ' . json_encode($row));
            throw $e;
        }
    }

    public function styles(Worksheet $sheet)
    {
        try {
            \Log::info('Aplicando estilos ao Excel');
            
            // Estilos para o título
            $sheet->mergeCells('A1:B1');
            $sheet->mergeCells('A2:B2');
            $sheet->mergeCells('A3:B3');
            
            // Alinhamento e formatação do cabeçalho
            $sheet->getStyle('A1:B3')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 12
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ]);
            
            // Estilo para o cabeçalho da tabela
            $sheet->getStyle('A5:B5')->applyFromArray([
                'font' => ['bold' => true],
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN
                    ]
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E9ECEF']
                ]
            ]);
            
            // Formatação da coluna de valores
            $lastRow = $sheet->getHighestRow();
            $sheet->getStyle('B6:B'.$lastRow)->getNumberFormat()->setFormatCode('#,##0.00');
            
            // Ajusta largura das colunas
            $sheet->getColumnDimension('A')->setWidth(15);
            $sheet->getColumnDimension('B')->setWidth(20);
            
            \Log::info('Estilos aplicados com sucesso', ['last_row' => $lastRow]);
        } catch (\Exception $e) {
            \Log::error('Erro ao aplicar estilos: ' . $e->getMessage());
            throw $e;
        }
    }

    public function title(): string
    {
        return 'Relatório';
    }
}