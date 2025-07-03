<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$renderedTable = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
    $filePath = $_FILES['excel_file']['tmp_name'];

    try {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getSheetByName('Income');

        if (!$sheet) {
            $renderedTable = '<p style="color:red;">找不到名為 Income 的工作表。</p>';
        } else {
            // 第6列當表頭
            $headerRow = $sheet->rangeToArray('A6:' . $sheet->getHighestColumn() . '6', null, true, false)[0];

            $renderedTable .= '<table border="1" cellpadding="8" cellspacing="0">';
            $renderedTable .= '<thead><tr>';
            foreach ($headerRow as $header) {
                $renderedTable .= '<th>' . htmlspecialchars($header) . '</th>';
            }
            $renderedTable .= '</tr></thead><tbody>';

            // 從第7列開始
            foreach ($sheet->getRowIterator(7) as $row) {
                $rowData = [];
                foreach ($row->getCellIterator() as $cell) {
                    $rowData[] = $cell->getValue();
                }

                // 跳過整列空值（null、空字串、空格）
                $nonEmpty = array_filter($rowData, fn($val) => trim((string)$val) !== '');
                if (count($nonEmpty) === 0) {
                    continue;
                }

                // 輸出資料列
                $renderedTable .= '<tr>';
                foreach ($rowData as $val) {
                    $renderedTable .= '<td>' . htmlspecialchars((string)$val) . '</td>';
                }
                $renderedTable .= '</tr>';
            }

            $renderedTable .= '</tbody></table>';
        }
    } catch (Exception $e) {
        $renderedTable = '<p style="color:red;">讀取檔案失敗：' . htmlspecialchars($e->getMessage()) . '</p>';
    }
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>匯入 Income 表格</title>
</head>
<body>
    <h2>上傳 Excel 並顯示 Income 分頁</h2>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="excel_file" accept=".xlsx" required>
        <button type="submit">上傳並讀取</button>
    </form>

    <hr>
    <?= $renderedTable ?>
</body>
</html>
