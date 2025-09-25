<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LoanApplicationsExport implements FromArray, WithStyles, WithColumnWidths, WithEvents
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function styles(Worksheet $sheet)
    {
        // 標題列樣式
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '3498DB']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,  // 申請編號
            'B' => 15,  // 姓名
            'C' => 15,  // 手機號碼
            'D' => 20,  // 職業
            'E' => 12,  // 居住縣市
            'F' => 25,  // 詳細地址
            'G' => 20,  // 方便聯繫時間
            'H' => 15,  // Line ID
            'I' => 12,  // 申請金額
            'J' => 10,  // 狀態
            'K' => 10,  // 當前步驟
            'L' => 15,  // 緊急聯絡人1姓名
            'M' => 15,  // 緊急聯絡人1電話
            'N' => 12,  // 緊急聯絡人1關係
            'O' => 15,  // 緊急聯絡人2姓名
            'P' => 15,  // 緊急聯絡人2電話
            'Q' => 12,  // 緊急聯絡人2關係
            'R' => 20,  // 申請時間
            'S' => 20,  // 步驟1完成時間
            'T' => 20,  // 步驟2完成時間
            'U' => 20,  // 步驟3完成時間
            'V' => 20,  // 步驟4完成時間
            'W' => 20,  // 步驟5完成時間
            'X' => 30,  // 備註
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // 設定所有資料的樣式
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // 資料列樣式
                $sheet->getStyle('A2:' . $highestColumn . $highestRow)
                    ->applyFromArray([
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_LEFT,
                            'vertical' => Alignment::VERTICAL_CENTER,
                            'wrapText' => true,
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'CCCCCC'],
                            ],
                        ],
                    ]);

                // 金額欄位特殊格式
                $sheet->getStyle('I2:I' . $highestRow)
                    ->getNumberFormat()
                    ->setFormatCode('#,##0');

                // 設定行高
                for ($i = 1; $i <= $highestRow; $i++) {
                    $sheet->getRowDimension($i)->setRowHeight(25);
                }

                // 凍結標題列
                $sheet->freezePane('A2');

                // 自動篩選
                $sheet->setAutoFilter('A1:' . $highestColumn . $highestRow);

                // 設定列印選項
                $sheet->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4)
                    ->setFitToWidth(1)
                    ->setFitToHeight(0);

                // 設定頁面邊距
                $sheet->getPageMargins()
                    ->setTop(0.75)
                    ->setRight(0.7)
                    ->setLeft(0.7)
                    ->setBottom(0.75);

                // 設定頁首
                $sheet->getHeaderFooter()
                    ->setOddHeader('&L&B貸款申請資料&R&D');
            },
        ];
    }
}
