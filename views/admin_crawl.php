<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin – Crawl dữ liệu | ThongTinDN</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-4" style="max-width:900px">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">⚙ Admin – Crawl dữ liệu</h4>
    <div>
      <a href="danh-muc.php" class="btn btn-sm btn-outline-primary">Quản lý ngành nghề</a>
      <a href="../index.php" class="btn btn-sm btn-outline-secondary ms-1">← Trang chủ</a>
    </div>
  </div>

  <!-- Thống kê tổng quan -->
  <div class="row g-2 mb-4">
    <?php
    $badges = [
      'cho'         => ['warning',  'Chờ xử lý'],
      'dang_xu_ly'  => ['info',     'Đang xử lý'],
      'thanh_cong'  => ['success',  'Thành công'],
      'that_bai'    => ['danger',   'Thất bại'],
    ];
    foreach ($badges as $key => [$color, $label]):
    ?>
    <div class="col-auto">
      <span class="badge text-bg-<?= $color ?> fs-6 px-3 py-2">
        <?= (int)($statsRaw[$key] ?? 0) ?> <?= $label ?>
      </span>
    </div>
    <?php endforeach; ?>
    <div class="col-auto">
      <span class="badge text-bg-dark fs-6 px-3 py-2">
        <?= number_format($totalDn) ?> DN đã lưu
      </span>
    </div>
  </div>

  <!-- Bước 1: Crawl danh sách -->
  <div class="card mb-4 shadow-sm">
    <div class="card-header fw-bold">Bước 1 – Crawl danh sách theo tỉnh</div>
    <div class="card-body">
      <form method="GET" action="run_crawl.php" target="logframe" id="formList">
        <input type="hidden" name="action" value="list">
        <div class="row g-3 align-items-end">
          <div class="col-md-4">
            <label class="form-label">Chọn tỉnh nhanh</label>
            <select id="tinhSelect" class="form-select"
                    onchange="document.getElementById('tinhInput').value=this.value">
              <?php foreach ($tinhOptions as $v => $label): ?>
              <option value="<?= htmlspecialchars($v) ?>"><?= htmlspecialchars($label) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">
              Slug tỉnh <small class="text-muted">(vd: can-tho-92)</small>
            </label>
            <input type="text" id="tinhInput" name="tinh" class="form-control"
                   placeholder="can-tho-92" pattern="[a-z0-9-]+" required>
          </div>
          <div class="col-md-2">
            <label class="form-label">Số trang tối đa</label>
            <input type="number" name="limit" class="form-control" value="5" min="1" max="100">
          </div>
          <div class="col-auto">
            <button type="submit" class="btn btn-primary">
              ▶ Crawl danh sách
            </button>
          </div>
        </div>
        <div class="form-text mt-1">
          Slug = phần sau <code>/tra-cuu-ma-so-thue-theo-tinh/</code> trên masothue.com
        </div>
      </form>
    </div>
  </div>

  <!-- Bước 2: Crawl chi tiết -->
  <div class="card mb-4 shadow-sm">
    <div class="card-header fw-bold">Bước 2 – Crawl chi tiết từ queue</div>
    <div class="card-body">
      <form method="GET" action="run_crawl.php" target="logframe">
        <input type="hidden" name="action" value="detail">
        <div class="row g-3 align-items-end">
          <div class="col-md-3">
            <label class="form-label">Số bản ghi / lần</label>
            <input type="number" name="limit" class="form-control" value="20" min="1" max="100">
          </div>
          <div class="col-auto">
            <button type="submit" class="btn btn-success">
              ▶ Crawl chi tiết
            </button>
          </div>
        </div>
        <div class="form-text mt-1">
          Chỉ xử lý các URL trạng thái <em>chờ</em> hoặc <em>thất bại &lt; <?= defined('CRAWL_MAX_RETRY') ? CRAWL_MAX_RETRY : 3 ?> lần</em>.
        </div>
      </form>
    </div>
  </div>

  <!-- Iframe log output -->
  <div class="card shadow-sm">
    <div class="card-header fw-bold d-flex justify-content-between">
      Log output
      <button class="btn btn-sm btn-outline-secondary"
              onclick="document.querySelector('iframe[name=logframe]').src='about:blank'">
        Xoá log
      </button>
    </div>
    <div class="card-body p-0">
      <iframe name="logframe" src="about:blank"
              style="width:100%;height:420px;border:0;background:#1e1e2e;"
              class="rounded-bottom"></iframe>
    </div>
  </div>

</div>
</body>
</html>
