<?php
$adminTitle = 'Quản lý Crawler';
require_once __DIR__ . '/admin_layout_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-b">
  <h2 class="h3 mb-0">Quản lý Crawler</h2>
</div>

<!-- Thống kê tổng quan -->
<div class="row g-2 mb-4">
  <?php
  $badges = [
    'cho' => ['warning', 'Chờ xử lý'],
    'dang_xu_ly' => ['info', 'Đang xử lý'],
    'thanh_cong' => ['success', 'Thành công'],
    'that_bai' => ['danger', 'Thất bại'],
  ];
  foreach ($badges as $key => [$color, $label]):
    ?>
    <div class="col-auto">
      <span class="badge text-bg-<?= $color ?> fs-6 px-3 py-2">
        <?= (int) ($statsRaw[$key] ?? 0) ?>   <?= $label ?>
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
<div class="card mb-4 shadow-sm border-0">
  <div class="card-header fw-bold bg-primary text-white border-0">Bước 1 – Crawl danh sách theo tỉnh</div>
  <div class="card-body bg-white">
    <form method="GET" action="run_crawl.php" target="logframe" id="formList">
      <input type="hidden" name="action" value="list">
      <div class="row g-3 align-items-end">
        <div class="col-md-4">
          <label class="form-label text-muted small fw-bold">Chọn tỉnh nhanh</label>
          <select id="tinhSelect" class="form-select" onchange="document.getElementById('tinhInput').value=this.value">
            <?php foreach ($tinhOptions as $v => $label): ?>
              <option value="<?= htmlspecialchars($v) ?>"><?= htmlspecialchars($label) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label text-muted small fw-bold">
            Slug tỉnh <small class="text-muted">(vd: can-tho-96)</small>
          </label>
          <input type="text" id="tinhInput" name="tinh" class="form-control" placeholder="can-tho-96"
            pattern="[a-z0-9-]+" required>
        </div>
        <div class="col-md-2">
          <label class="form-label text-muted small fw-bold">Số trang tối đa (không quá 100)</label>
          <input type="number" name="limit" class="form-control" value="5" min="1" max="100">
        </div>
        <div class="col-auto">
          <button type="submit" class="btn btn-primary">
            ▶ Crawl danh sách
          </button>
        </div>
      </div>
      <div class="form-text mt-2 small text-muted">
        Slug = phần sau <code>/tra-cuu-ma-so-thue-theo-tinh/</code> trên masothue.com
      </div>
    </form>
  </div>
</div>

<!-- Bước 2: Crawl chi tiết -->
<div class="card mb-4 shadow-sm border-0">
  <div class="card-header fw-bold bg-success text-white border-0">Bước 2 – Crawl chi tiết từ queue</div>
  <div class="card-body bg-white">
    <form method="GET" action="run_crawl.php" target="logframe">
      <input type="hidden" name="action" value="detail">
      <div class="row g-3 align-items-end">
        <div class="col-md-3">
          <label class="form-label text-muted small fw-bold">Số bản ghi / lần (không quá 100)</label>
          <input type="number" name="limit" class="form-control" value="20" min="1" max="100">
        </div>
        <div class="col-auto">
          <button type="submit" class="btn btn-success">
            ▶ Crawl chi tiết
          </button>
        </div>
      </div>
      <div class="form-text mt-2 small text-muted">
        Chỉ xử lý các URL trạng thái <em>chờ</em> hoặc <em>thất bại &lt;
          <?= defined('CRAWL_MAX_RETRY') ? CRAWL_MAX_RETRY : 3 ?> lần</em>.
      </div>
    </form>
  </div>
</div>

<!-- Iframe log output (Terminal Logs) -->
<div class="card shadow-sm border-0">
  <div class="card-header fw-bold bg-dark text-white border-0 d-flex justify-content-between align-items-center">
    <span>Crawl Logs Terminal</span>
    <button class="btn btn-sm btn-outline-light py-0 px-2"
      onclick="document.querySelector('iframe[name=logframe]').src='about:blank'">
      Xoá log
    </button>
  </div>
  <div class="card-body p-0">
    <iframe name="logframe" src="about:blank"
      style="width:100%;height:400px;border:0;background:#212529;color:#f8f9fa;font-family:monospace;"
      class="rounded-bottom"></iframe>
  </div>
</div>

<?php
require_once __DIR__ . '/admin_layout_footer.php';
?>