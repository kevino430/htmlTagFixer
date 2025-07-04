<?php
$dir = '../';
require $dir.'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
    $filePath = $_FILES['excel_file']['tmp_name'];

    try {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getSheetByName('orders');

        if (!$sheet) {
            echo '<div class="alert alert-danger">找不到名為 <strong>orders</strong> 的工作表。</div>';
            exit;
        }

        // 第6列為表頭
        $headerRow = $sheet->rangeToArray('A1:' . $sheet->getHighestColumn() . '1', null, true, false)[0];

        echo '<div class="table-responsive"><table class="table table-bordered table-striped table-hover">';
        echo '<thead><tr>';
        foreach ($headerRow as $header) {
            echo '<th>' . htmlspecialchars($header) . '</th>';
        }
        echo '</tr></thead><tbody>';

        // 從第7列開始
        foreach ($sheet->getRowIterator(7) as $row) {
            $rowData = [];
            foreach ($row->getCellIterator() as $cell) {
                $rowData[] = $cell->getValue();
            }

            // 跳過整列空白
            $nonEmpty = array_filter($rowData, fn($val) => trim((string)$val) !== '');
            if (count($nonEmpty) === 0) continue;

            echo '<tr>';
            foreach ($rowData as $val) {
                echo '<td>' . htmlspecialchars((string)$val) . '</td>';
            }
            echo '</tr>';
        }

        echo '</tbody></table></div>';
    } catch (Exception $e) {
        echo '<div class="alert alert-danger">讀取失敗：' . htmlspecialchars($e->getMessage()) . '</div>';
    }
} else {
    echo '<div class="alert alert-warning">請上傳 .xlsx 檔案。</div>';
}
