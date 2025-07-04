<!DOCTYPE html>
<html lang="zh-TW">
<head>
  <meta charset="UTF-8" />
  <title>Income Excel 上傳</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    .table-responsive {
      overflow-x: auto;
      max-height: 80vh;
    }
    table {
      border-collapse: collapse;
      min-width: 1000px;
    }
    th, td {
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
    <h2 class="mb-4">上傳 Excel（Income 分頁）</h2>
    <form id="uploadFormOne" enctype="multipart/form-data">
      <div class="mb-3">
        <input type="file" name="excel_file" class="form-control" accept=".xlsx" required />
      </div>
      <button type="submit" class="btn btn-primary">上傳並載入</button>
    </form>

    <hr />
    <div id="tableAreaOne" class="mt-4"></div>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script>
    $('#uploadFormOne').on('submit', function (e) {
      e.preventDefault();
      const formData = new FormData(this);

      $('#tableAreaOne').html('<div class="text-muted">資料處理中...</div>');

      $.ajax({
        url: 'function/uploadIncomeExcel.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (html) {
          $('#tableAreaOne').html(html);
        },
        error: function () {
          $('#tableAreaOne').html('<div class="alert alert-danger">上傳失敗，請重試。</div>');
        },
      });
    });
  </script>
</body>
</html>
