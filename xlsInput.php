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
            $renderedTable = '<div class="alert alert-danger">找不到名為 <strong>Income</strong> 的工作表。</div>';
        } else {
            // 取得第6列作為表頭
            $headerRow = $sheet->rangeToArray('A6:' . $sheet->getHighestColumn() . '6', null, true, false)[0];

            $renderedTable = '<div class="table-responsive"><table class="table table-bordered table-striped table-hover">';
            $renderedTable .= '<thead class="table-dark"><tr>';
            foreach ($headerRow as $header) {
                $renderedTable .= '<th>' . htmlspecialchars($header) . '</th>';
            }
            $renderedTable .= '</tr></thead><tbody>';

            // 從第7列開始讀取資料
            foreach ($sheet->getRowIterator(7) as $row) {
                $rowData = [];
                foreach ($row->getCellIterator() as $cell) {
                    $rowData[] = $cell->getValue();
                }

                // 若整列都是空的，就跳過
                $nonEmpty = array_filter($rowData, fn($val) => trim((string)$val) !== '');
                if (count($nonEmpty) === 0) {
                    continue;
                }

                $renderedTable .= '<tr>';
                foreach ($rowData as $val) {
                    $renderedTable .= '<td>' . htmlspecialchars((string)$val) . '</td>';
                }
                $renderedTable .= '</tr>';
            }

            $renderedTable .= '</tbody></table></div>';
        }
    } catch (Exception $e) {
        $renderedTable = '<div class="alert alert-danger">讀取失敗：' . htmlspecialchars($e->getMessage()) . '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <title>Excel Income 匯入工具</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table-responsive {
            overflow-x: auto;
            max-height: 80vh;
            /* 若資料太多，會產生垂直捲軸 */
        }

        table {
            border-collapse: collapse;
            min-width: 1000px;
        }

        th,
        td {
            white-space: nowrap;
            vertical-align: middle;
            text-align: center;
        }

        thead th {
            position: sticky;
            top: 0;
            z-index: 2;
            background-color: #212529;
            color: white;
        }
    </style>


</head>

<body class="bg-light">
    <div class="container py-5">
        <h2 class="mb-4">匯入 Excel（Income 分頁）</h2>

        <form method="post" enctype="multipart/form-data" class="mb-4">
            <div class="mb-3">
                <label for="excel_file" class="form-label">上傳 .xlsx 檔案</label>
                <input type="file" name="excel_file" id="excel_file" accept=".xlsx" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">匯入資料</button>
        </form>

        <?= $renderedTable ?>
    </div>
</body>

</html>