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

class FinancialReport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles
{
    protected $data;
    protected $citySettings;

    public function __construct($data)
    {
        $this->data = $data;
        $this->citySettings = CitySetting::first();
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
                'R$ ' . number_format($row['revenues'], 2, ',', '.'),
                'R$ ' . number_format($row['expenses'], 2, ',', '.'),
                'R$ ' . number_format($row['balance'], 2, ',', '.')
            ];
        }

        return [
            $period,
            'R$ ' . number_format($row['total'], 2, ',', '.')
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
            $sheet->setCellValue('A1', $this->citySettings->city_hall_name);
            
            // Endereço completo
            $sheet->mergeCells('A2:' . $lastColumn . '2');
            $sheet->setCellValue('A2', $this->citySettings->address . ' - ' . $this->citySettings->city_name . '/' . $this->citySettings->state);
            
            // CEP
            if ($this->citySettings->zip_code) {
                $sheet->mergeCells('A3:' . $lastColumn . '3');
                $sheet->setCellValue('A3', 'CEP: ' . $this->citySettings->zip_code);
                $currentRow++;
            }
            
            // Contato (telefone e email)
            if ($this->citySettings->phone || $this->citySettings->email) {
                $contact = [];
                if ($this->citySettings->phone) $contact[] = 'Telefone: ' . $this->citySettings->phone;
                if ($this->citySettings->email) $contact[] = 'Email: ' . $this->citySettings->email;
                
                $sheet->mergeCells('A4:' . $lastColumn . '4');
                $sheet->setCellValue('A4', implode(' - ', $contact));
                $currentRow++;
            }
            
            // Nome do prefeito
            if ($this->citySettings->mayor_name) {
                $sheet->mergeCells('A5:' . $lastColumn . '5');
                $sheet->setCellValue('A5', 'Prefeito: ' . $this->citySettings->mayor_name);
                $currentRow++;
            }

            // Estilo para o cabeçalho da prefeitura
            $sheet->getStyle('A1:' . $lastColumn . '5')->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ]
            ]);

            // Adicionar linha em branco após os dados da prefeitura
            $currentRow = 7;
        }

        // Título do relatório
        $sheet->mergeCells('A' . $currentRow . ':' . $lastColumn . $currentRow);
        $sheet->setCellValue('A' . $currentRow, $this->data['metadata']['type']);
        $currentRow++;

        // Período do relatório
        $sheet->mergeCells('A' . $currentRow . ':' . $lastColumn . $currentRow);
        $sheet->setCellValue('A' . $currentRow, $this->data['metadata']['period']);
        $currentRow++;

        // Data de geração
        $sheet->mergeCells('A' . $currentRow . ':' . $lastColumn . $currentRow);
        $sheet->setCellValue('A' . $currentRow, 'Gerado em: ' . $this->data['metadata']['generated_at']->format('d/m/Y H:i:s'));
        $currentRow++;

        // Linha em branco
        $currentRow++;

        // Início dos dados
        $dataStartRow = $currentRow;

        // Estilo para o cabeçalho dos dados
        $sheet->getStyle('A' . $dataStartRow . ':' . $lastColumn . $dataStartRow)->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E9ECEF']
            ]
        ]);

        // Estilo para todas as células de dados
        $sheet->getStyle('A' . $dataStartRow . ':' . $lastColumn . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ]
        ]);

        // Ajustar largura das colunas
        foreach(range('A', $lastColumn) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return [
            $dataStartRow => ['font' => ['bold' => true]],
        ];
    }
} 