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
        

        // 在這裡執行資料插入的 SQL 語句
        $sql = "INSERT INTO shopee_income (
                        order_no,
                        refund_no,
                        buyer_account,
                        order_created_at,
                        buyer_payment_method,
                        wallet_in_at,
                        product_price,
                        product_discount,
                        refund_amount,
                        shopee_subsidy,
                        shop_coupon,
                        seller_coin_cashback,
                        buyer_shipping_fee,
                        shopee_shipping_subsidy,
                        shopee_paid_shipping,
                        return_shipping_fee,
                        installments,
                        payment_fee_rate,
                        ams_fee,
                        transaction_fee,
                        other_service_fee,
                        payment_processing_fee,
                        wallet_in_amount,
                        payout_source,
                        promo_code,
                        compensation,
                        shop_shipping_subsidy,
                        createtime,
                        updt
                    ) VALUES (
                        :order_no,
                        :refund_no,
                        :buyer_account,
                        :order_created_at,
                        :buyer_payment_method,
                        :wallet_in_at,
                        :product_price,
                        :product_discount,
                        :refund_amount,
                        :shopee_subsidy,
                        :shop_coupon,
                        :seller_coin_cashback,
                        :buyer_shipping_fee,
                        :shopee_shipping_subsidy,
                        :shopee_paid_shipping,
                        :return_shipping_fee,
                        :installments,
                        :payment_fee_rate,
                        :ams_fee,
                        :transaction_fee,
                        :other_service_fee,
                        :payment_processing_fee,
                        :wallet_in_amount,
                        :payout_source,
                        :promo_code,
                        :compensation,
                        :shop_shipping_subsidy,
                        :createtime,
                        :updt
                    ) 
                    ON DUPLICATE KEY UPDATE
                        refund_no               =  :refund_no,
                        buyer_account           =  :buyer_account,
                        order_created_at        =  :order_created_at,
                        buyer_payment_method    =  :buyer_payment_method,
                        wallet_in_at            =  :wallet_in_at,
                        product_price           =  :product_price,
                        product_discount        =  :product_discount,
                        refund_amount           =  :refund_amount,
                        shopee_subsidy          =  :shopee_subsidy,
                        shop_coupon             =  :shop_coupon,
                        seller_coin_cashback    =  :seller_coin_cashback,
                        buyer_shipping_fee      =  :buyer_shipping_fee,
                        shopee_shipping_subsidy =  :shopee_shipping_subsidy,
                        shopee_paid_shipping    =  :shopee_paid_shipping,
                        return_shipping_fee     =  :return_shipping_fee,
                        installments            =  :installments,
                        payment_fee_rate        =  :payment_fee_rate,
                        ams_fee                 =  :ams_fee,
                        transaction_fee         =  :transaction_fee,
                        other_service_fee       =  :other_service_fee,
                        payment_processing_fee  =  :payment_processing_fee,
                        wallet_in_amount        =  :wallet_in_amount,
                        payout_source           =  :payout_source,
                        promo_code              =  :promo_code,
                        compensation            =  :compensation,
                        shop_shipping_subsidy   =  :shop_shipping_subsidy,
                        updt                    =  :updt
                    ";

        $stmt = $system->db->prepare($sql);   // ← 這裡改成 prepare


        // 插入匯入資料的程式
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


            $params = [
                'order_no'                => $rowData[1],
                'refund_no'               => $rowData[2],
                'buyer_account'           => $rowData[3],
                'order_created_at'        => $rowData[4],
                'buyer_payment_method'    => $rowData[5],
                'wallet_in_at'            => $rowData[6],
                'product_price'           => $rowData[7],
                'product_discount'        => $rowData[8],
                'refund_amount'           => $rowData[9],
                'shopee_subsidy'          => $rowData[10],
                'shop_coupon'             => $rowData[11],
                'seller_coin_cashback'    => $rowData[12],
                'buyer_shipping_fee'      => $rowData[13],
                'shopee_shipping_subsidy' => $rowData[14],
                'shopee_paid_shipping'    => $rowData[15],
                'return_shipping_fee'     => $rowData[16],
                'installments'            => $rowData[17],
                'payment_fee_rate'        => $rowData[18],
                'ams_fee'                 => $rowData[19],
                'transaction_fee'         => $rowData[20],
                'other_service_fee'       => $rowData[21],
                'payment_processing_fee'  => $rowData[22],
                'wallet_in_amount'        => $rowData[23],
                'payout_source'           => $rowData[24],
                'promo_code'              => $rowData[25],
                'compensation'            => $rowData[26],
                'shop_shipping_subsidy'   => $rowData[27],
                'createtime'              => time(),
                'updt'                    => time()
            ];
            $r =    $stmt->execute($params);           // ← 用 execute 帶陣列
            // pre("SQL 執行結果");
            // pre($r);
        }

        // $system->db->commit();                 // 提交

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
