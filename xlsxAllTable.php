<?php
$DIR_PATH  = '';
include $DIR_PATH . 'common/common.php';

// 1. 查詢資料內容
$sql = "SELECT 
            list.`order_no`,
            list.`refund_no`,
            list.`buyer_account`,
            list.`product_price`,
            list.`product_discount`,
            list.`refund_amount`,
            list.`shopee_subsidy`,
            list.`shop_coupon`,
            list.`seller_coin_cashback`,
            list.`buyer_shipping_fee`,
            list.`shopee_shipping_subsidy`,
            list.`shopee_paid_shipping`,
            list.`return_shipping_fee`,
            list.`installments`,
            list.`payment_fee_rate`,
            list.`ams_fee`,
            list.`transaction_fee`,
            list.`other_service_fee`,
            list.`payment_processing_fee`,
            list.`wallet_in_amount`,
            list.`payout_source`,
            list.`promo_code`,
            list.`compensation`,

            detail.`order_status`,
            detail.`failure_reason`,
            detail.`rma_status`,
            detail.`items_subtotal`,
            detail.`buyer_shipping_fee`,
            detail.`shopee_shipping_subsidy`,
            detail.`return_shipping_fee`,
            detail.`buyer_total_paid`,
            detail.`shopee_subsidy`,
            detail.`coin_discount`,
            detail.`bank_cc_discount`,
            detail.`shop_coupon`,
            detail.`seller_coin_cashback`,
            detail.`generic_coupon`,
            detail.`transaction_fee`,
            detail.`other_service_fee`,
            detail.`payment_processing_fee`,
            detail.`installments`,
            detail.`payment_fee_rate`,
            detail.`txn_fee_rule_name`,
            detail.`item_name`,
            detail.`item_option_name`,
            detail.`item_original_price`,
            detail.`item_promo_price`,
            detail.`item_sku`,
            detail.`item_option_sku`,
            detail.`qty`,
            detail.`rma_qty`,
            detail.`bundle_key`,
            detail.`bundle_discount_label`,	
            detail.`tracking_no`,	
            detail.`paid_at`,	
            detail.`shipped_at`,	
            detail.`completed_at`




        FROM `shopee_income` AS `list`
        LEFT JOIN `shopee_order_detail` AS `detail` 
        
        ON list.`order_no` = detail.`order_no`
        WHERE refund_no IS NULL

        ORDER BY list.`shopee_income_id` DESC 
        LIMIT 3
        ";

$general_setting = $system->fetch_arrr($sql);

// 2. 抓欄位備註
$comment_sql1 = "SELECT COLUMN_NAME, COLUMN_COMMENT 
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = 'shopi_test' 
                  AND TABLE_NAME = 'shopee_income'";

$column_comments1 = $system->fetch_arrr($comment_sql1);

$comment_sql2 = "SELECT COLUMN_NAME, COLUMN_COMMENT 
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = 'shopi_test' 
                  AND TABLE_NAME = 'shopee_order_detail'";

$column_comments2 = $system->fetch_arrr($comment_sql2);



// 合併兩個查詢結果
$column_comments = array_merge($column_comments1, $column_comments2);

$getHead = $general_setting[0] ?? [];

// 3. 轉為欄位 => 備註 對照陣列
$comment_map = [];
foreach ($column_comments as $col) {
    $comment_map[$col['COLUMN_NAME']] = $col['COLUMN_COMMENT'] ?: $col['COLUMN_NAME'];
}

// pre($comment_map);
// exit;
?>

<!doctype html>
<html lang="en">

<head>
    <title>Shopee Income Table</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
    <style>
        .table-responsive {
            overflow-x: auto;
            max-height: 90vh;
        }

        table {
            border-collapse: collapse;
            min-width: 2000px;
        }

        th,
        td {
            white-space: nowrap;
            vertical-align: middle;
            text-align: center;
        }

        thead tr th {
            position: sticky;
            top: 0;
            z-index: 2;
            background-color: #212529;
            color: white;
        }
    </style>
</head>

<body>
    <header class="bg-dark text-white py-2">
        <div class="container">
            <h1 class="h4">Shopee Income 資料列表</h1>
        </div>
    </header>
    <main class="px-4 py-4">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <?php if (!empty($getHead)): ?>
                            <?php foreach (array_keys($getHead) as $col): ?>
                                <th><?= htmlspecialchars($comment_map[$col] ?? $col) ?></th>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <th>查無資料</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($general_setting as $row): ?>
                        <tr>
                            <?php foreach ($row as $value): 
                                // 進行資料處理
                                // 若為空值 呈現 NULL
                                if (empty($value)) {
                                    $value = 'NULL';
                                }
                            ?>
                                <td><?= htmlspecialchars($value) ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
    <footer class="bg-light text-center py-3">
        <small>© <?= date('Y') ?> Shopee Income Viewer</small>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>

</html>