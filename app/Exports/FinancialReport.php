<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
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
        return new Collection($this->data['items']);
    }

    public function headings(): array
    {
        $headers = ['Período'];

        if ($this->data['filters']['report_type'] === 'balance') {
            return array_merge($headers, ['Receitas', 'Despesas', 'Saldo']);
        }

        return array_merge($headers, ['Total']);
    }

    public function map($row): array
    {
        $period = match($this->data['filters']['group_by']) {
            'daily' => Carbon::parse($row['period'])->format('d/m/Y'),
            'monthly' => Carbon::parse($row['period'])->format('m/Y'),
            'yearly' => $row['period'],
        };

        if ($this->data['filters']['report_type'] === 'balance') {
            return [
                $period,
                $row['revenues'],
                $row['expenses'],
                $row['balance']
            ];
        }

        return [
            $period,
            $row['total']
        ];
    }

    public function title(): string
    {
        return $this->data['metadata']['type'];
    }

    public function styles(Worksheet $sheet)
    {
        $lastColumn = $this->data['filters']['report_type'] === 'balance' ? 'D' : 'B';
        $lastRow = $sheet->getHighestRow();
        $currentRow = 1;

        // Adicionar dados da prefeitura
        if ($this->citySettings) {
            // Nome da prefeitura
            $sheet->mergeCells('A1:' . $lastColumn . '1');
            $sheet->setCellValue('A1', 'Secretaria de Saúde');
            
            // Endereço completo
            $sheet->mergeCells('A2:' . $lastColumn . '2');
            $sheet->setCellValue('A2', 'Rua Vereador Manoel Firmino - Teotônio Vilela/AL');
            
            // Contato
            $sheet->mergeCells('A3:' . $lastColumn . '3');
            $sheet->setCellValue('A3', 'Telefone: (82) 3543-1365 - Email: saude@gov.br');
            
            // Prefeito
            $sheet->mergeCells('A4:' . $lastColumn . '4');
            $sheet->setCellValue('A4', 'Prefeito: Peu Pereira');

            // Estilo para o cabeçalho da prefeitura
            $sheet->getStyle('A1:' . $lastColumn . '4')->applyFromArray([
                'font' => [
                    'bold' => false,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
            ]);

            // Linha em branco após os dados da prefeitura
            $currentRow = 6;
        }

        // Título do relatório
        $sheet->mergeCells('A' . $currentRow . ':' . $lastColumn . $currentRow);
        $sheet->setCellValue('A' . $currentRow, $this->data['metadata']['type']);
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(false);
        $currentRow++;

        // Adicionar valor total
        if ($this->data['filters']['report_type'] === 'revenues') {
            $totalValue = collect($this->data['items'])->sum('total');
            $sheet->mergeCells('A' . $currentRow . ':' . $lastColumn . $currentRow);
            $sheet->setCellValue('A' . $currentRow, 'R$ ' . number_format($totalValue, 2, ',', '.'));
            $sheet->getStyle('A' . $currentRow)->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                ],
            ]);
            $currentRow++;
        } elseif ($this->data['filters']['report_type'] === 'balance') {
            $totalRevenues = collect($this->data['items'])->sum('revenues');
            $totalExpenses = collect($this->data['items'])->sum('expenses');
            $totalBalance = $totalRevenues - $totalExpenses;
            
            $sheet->mergeCells('A' . $currentRow . ':' . $lastColumn . $currentRow);
            $sheet->setCellValue('A' . $currentRow, 'Total Receitas: R$ ' . number_format($totalRevenues, 2, ',', '.'));
            $currentRow++;
            
            $sheet->mergeCells('A' . $currentRow . ':' . $lastColumn . $currentRow);
            $sheet->setCellValue('A' . $currentRow, 'Total Despesas: R$ ' . number_format($totalExpenses, 2, ',', '.'));
            $currentRow++;
            
            $sheet->mergeCells('A' . $currentRow . ':' . $lastColumn . $currentRow);
            $sheet->setCellValue('A' . $currentRow, 'Saldo: R$ ' . number_format($totalBalance, 2, ',', '.'));
            $currentRow++;
        }

        // Período do relatório
        $sheet->mergeCells('A' . $currentRow . ':' . $lastColumn . $currentRow);
        $sheet->setCellValue('A' . $currentRow, 'De ' . Carbon::parse($this->data['filters']['start_date'])->format('d/m/Y') . ' até ' . Carbon::parse($this->data['filters']['end_date'])->format('d/m/Y'));
        $currentRow++;

        // Agrupamento
        $sheet->mergeCells('A' . $currentRow . ':' . $lastColumn . $currentRow);
        $sheet->setCellValue('A' . $currentRow, 'Agrupamento: Diário');
        $currentRow++;

        // Data de geração (ajustando o fuso horário)
        $sheet->mergeCells('A' . $currentRow . ':' . $lastColumn . $currentRow);
        $sheet->setCellValue('A' . $currentRow, 'Gerado em: ' . $this->data['metadata']['generated_at']->format('d/m/Y H:i:s'));
        $currentRow++;

        // Estilo para as informações do relatório
        $sheet->getStyle('A6:' . $lastColumn . $currentRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ]);

        // Adicionar filtros aplicados
        if (!empty($this->data['filters']['category_id']) || 
            !empty($this->data['filters']['block_id']) || 
            !empty($this->data['filters']['group_id']) || 
            !empty($this->data['filters']['action_id']) || 
            !empty($this->data['filters']['expense_classification_id'])) {
            
            $currentRow++;
            $sheet->mergeCells('A' . $currentRow . ':' . $lastColumn . $currentRow);
            $sheet->setCellValue('A' . $currentRow, 'Filtros Aplicados:');
            $currentRow++;

            if (!empty($this->data['filters']['category_id'])) {
                $sheet->mergeCells('A' . $currentRow . ':' . $lastColumn . $currentRow);
                $sheet->setCellValue('A' . $currentRow, 'Fonte: ' . Category::find($this->data['filters']['category_id'])->name);
                $currentRow++;
            }

            if (!empty($this->data['filters']['block_id'])) {
                $sheet->mergeCells('A' . $currentRow . ':' . $lastColumn . $currentRow);
                $sheet->setCellValue('A' . $currentRow, 'Bloco: ' . Category::find($this->data['filters']['block_id'])->name);
                $currentRow++;
            }

            if (!empty($this->data['filters']['group_id'])) {
                $sheet->mergeCells('A' . $currentRow . ':' . $lastColumn . $currentRow);
                $sheet->setCellValue('A' . $currentRow, 'Grupo: ' . Category::find($this->data['filters']['group_id'])->name);
                $currentRow++;
            }

            if (!empty($this->data['filters']['action_id'])) {
                $sheet->mergeCells('A' . $currentRow . ':' . $lastColumn . $currentRow);
                $sheet->setCellValue('A' . $currentRow, 'Ação: ' . Category::find($this->data['filters']['action_id'])->name);
                $currentRow++;
            }

            if (!empty($this->data['filters']['expense_classification_id'])) {
                $sheet->mergeCells('A' . $currentRow . ':' . $lastColumn . $currentRow);
                $sheet->setCellValue('A' . $currentRow, 'Classificação de Despesa: ' . ExpenseClassification::find($this->data['filters']['expense_classification_id'])->name);
                $currentRow++;
            }

            // Estilo para os filtros
            $sheet->getStyle('A' . ($currentRow - 1) . ':' . $lastColumn . $currentRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
            ]);
        }

        // Linha em branco antes dos dados
        $currentRow++;

        // Início dos dados
        $dataStartRow = $currentRow;

        // Estilo para o cabeçalho dos dados
        $sheet->getStyle('A' . $dataStartRow . ':' . $lastColumn . $dataStartRow)->applyFromArray([
            'font' => [
                'bold' => false,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F8F9FA']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ]);

        // Estilo para todas as células de dados
        $sheet->getStyle('A' . $dataStartRow . ':' . $lastColumn . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ]);

        // Ajustar largura das colunas
        foreach(range('A', $lastColumn) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Alinhar à direita e formatar valores monetários
        if ($this->data['filters']['report_type'] === 'balance') {
            $sheet->getStyle('B' . $dataStartRow . ':D' . $lastRow)->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                ],
                'numberFormat' => [
                    'formatCode' => '_-[$R$-pt-BR] * #,##0.00_-;-[$R$-pt-BR] * #,##0.00_-;_-[$R$-pt-BR] * "-"??_-;_-@_-'
                ]
            ]);
        } else {
            $sheet->getStyle('B' . $dataStartRow . ':B' . $lastRow)->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                ],
                'numberFormat' => [
                    'formatCode' => '_-[$R$-pt-BR] * #,##0.00_-;-[$R$-pt-BR] * #,##0.00_-;_-[$R$-pt-BR] * "-"??_-;_-@_-'
                ]
            ]);
        }

        return [
            $dataStartRow => ['font' => ['bold' => false]],
        ];
    }
} 