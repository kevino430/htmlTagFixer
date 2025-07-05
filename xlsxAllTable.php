<?php 
$DIR_PATH  = '';
include $DIR_PATH .'common/common.php';

$sql = "SELECT * 
        FROM `shopee_income` As `list`
        WHERE 1=1 
        and refund_no IS NULL
        ORDER BY list.`shopee_income_id` DESC";

$general_setting = $system->fetch_arrr($sql);
?>

<!doctype html>
<html lang="en">
    <head>
        <title>Shopee Income Table</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
        <style>
            table {
                font-size: 14px;
            }
            thead th {
                position: sticky;
                top: 0;
                background-color: #f8f9fa;
                z-index: 1;
            }
        </style>
    </head>

    <body>
        <header class="bg-dark text-white py-2">
            <div class="container">
                <h1 class="h4">Shopee Income 資料列表</h1>
            </div>
        </header>
        <main class="container py-4">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <?php if (!empty($general_setting[0])): ?>
                                <?php foreach (array_keys($general_setting[0]) as $col): ?>
                                    <th><?= htmlspecialchars($col) ?></th>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <th>查無資料</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($general_setting as $row): ?>
                            <tr>
                                <?php foreach ($row as $value): ?>
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
