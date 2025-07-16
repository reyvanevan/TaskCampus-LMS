<?php

use Illuminate\Support\Facades\Route;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

Route::get('/test-excel', function () {
    try {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'Test');
        $sheet->setCellValue('B1', 'Excel');
        $sheet->setCellValue('A2', 'Hello');
        $sheet->setCellValue('B2', 'World');
        
        $writer = new Xlsx($spreadsheet);
        
        $filename = 'test_excel.xlsx';
        
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
        
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});
