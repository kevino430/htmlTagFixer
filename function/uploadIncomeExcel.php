<?php
$DIR_PATH  = '../';
include $DIR_PATH . 'common/common.php';
require $DIR_PATH . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
    $filePath = $_FILES['excel_file']['tmp_name'];

    try {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getSheetByName('Income');

        if (!$sheet) {
            echo '<div class="alert alert-danger">找不到名為 <strong>Income</strong> 的工作表。</div>';
            exit;
        }

        // 第6列為表頭
        $headerRow = $sheet->rangeToArray(
            'A6:' . $sheet->getHighestColumn() . '6',
            null,
            true,
            false
        )[0];

        // 以下是存入 資料庫的程式碼
        if (1 == 1) {
            $targetTable = 'shopee_income';
            // 1. 先列出所有欄位（順序照 Excel）
            $columns = [
                'order_no',
                'refund_no',
                'buyer_account',
                'order_created_at',
                'buyer_payment_method',
                'wallet_in_at',
                'product_price',
                'product_discount',
                'refund_amount',
                'shopee_subsidy',
                'shop_coupon',
                'seller_coin_cashback',
                'buyer_shipping_fee',
                'shopee_shipping_subsidy',
                'shopee_paid_shipping',
                'return_shipping_fee',
                'installments',
                'payment_fee_rate',
                'ams_fee',
                'transaction_fee',
                'other_service_fee',
                'payment_processing_fee',
                'wallet_in_amount',
                'payout_source',
                'promo_code',
                'compensation',
                'shop_shipping_subsidy'
            ];
            // 2. 自動產生 VALUES 佔位符
            $placeholders = ':' . implode(', :', $columns);      // :order_no, :refund_no, ...

            // 3. 自動產生 ON DUPLICATE KEY UPDATE
            $updates = [];
            foreach ($columns as $col) {
                // createtime 不更新；updt 另外手動
                if ($col !== 'createtime') {
                    $updates[] = "$col = :$col";
                }
            }
            $updates[] = 'updt = :updt';   // 強制更新時間戳

            // 4. 拼好 SQL
            $sql = 'INSERT INTO ' . $targetTable . ' (' . implode(', ', $columns) . ', createtime, updt)
                    VALUES (' . $placeholders . ', :createtime, :updt)
                    ON DUPLICATE KEY UPDATE ' . implode(",\n        ", $updates);

            // 5. 預先 prepare（放迴圈外效能更好）
            $stmt = $system->db->prepare($sql);

            foreach ($sheet->getRowIterator(7) as $row) {

                // 讀取一列
                $rowData = [];
                foreach ($row->getCellIterator() as $cell) {
                    $rowData[] = $cell->getValue();
                }
                // ① 拿掉最左邊序號欄
                $rowData = array_slice($rowData, 1);   // 由索引 1 開始

                if (!array_filter($rowData, fn($v) => trim((string)$v) !== '')) {
                    continue;      // 整列空白就跳過
                }

                // 7. 自動組參數
                $params = [];
                foreach ($columns as $idx => $col) {
                    $params[":$col"] = $rowData[$idx] ?? null;
                }
                $params[':createtime'] = time();
                $params[':updt']       = time();

                // 8. 執行
                $t = $stmt->execute($params);
            }
        }


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
            $nonEmpty = array_filter(
                $rowData,
                fn($val) => trim((string)$val) !== ''
            );
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
