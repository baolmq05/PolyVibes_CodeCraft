<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle) ?> – ThongTinDN</title>
  <meta name="description"
    content="<?= e($pageTitle) ?>. Tra cứu thông tin doanh nghiệp, mã số thuế, người đại diện tại Việt Nam.">

  <!-- Google Fonts (Inter) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Bootstrap Icons (for icon support) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- Tom Select CSS (Custom styled for Tailwind compatibility) -->
  <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Tailwind Theme Configuration (Nexus Alpha palette) -->
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            surface: '#f8f9ff',
            'surface-dim': '#cbdbf5',
            'surface-bright': '#f8f9ff',
            'surface-container-lowest': '#ffffff',
            'surface-container-low': '#eff4ff',
            'surface-container': '#e5eeff',
            'surface-container-high': '#dce9ff',
            'surface-container-highest': '#d3e4fe',
            'on-surface': '#0b1c30',
            'on-surface-variant': '#464555',
            'inverse-surface': '#213145',
            'inverse-on-surface': '#eaf1ff',
            outline: '#777587',
            'outline-variant': '#c7c4d8',
            'surface-tint': '#4d44e3',
            primary: '#3525cd',
            'on-primary': '#ffffff',
            'primary-container': '#4f46e5',
            'on-primary-container': '#dad7ff',
            'inverse-primary': '#c3c0ff',
            secondary: '#0058be',
            'on-secondary': '#ffffff',
            'secondary-container': '#2170e4',
            'on-secondary-container': '#fefcff',
            background: '#f8f9ff',
            'on-background': '#0b1c30',
            'surface-variant': '#d3e4fe',
          },
          fontFamily: {
            sans: ['Inter', 'sans-serif'],
          }
        }
      }
    }
  </script>

  <style>
    /* Reset & Tinh chỉnh chiều cao Tom Select */
    .ts-wrapper .ts-control,
    .ts-wrapper .ts-control input {
      font-size: 0.875rem !important;
    }

    .ts-wrapper .ts-control {
      padding: 0 8px !important;
      height: 31px !important;
      min-height: 31px !important;
      max-height: 31px !important;
      display: flex !important;
      align-items: center !important;
      box-sizing: border-box !important;
      overflow: hidden !important;
      border-radius: 0.5rem !important;
      border-color: #e2e8f0 !important;
      background-color: #ffffff !important;
    }

    .ts-wrapper.disabled .ts-control {
      background-color: #f1f5f9 !important;
      opacity: 0.65 !important;
      cursor: not-allowed !important;
    }

    .ts-wrapper {
      padding: 0 !important;
      border: 0 !important;
      height: 31px !important;
    }

    .ts-dropdown {
      font-size: 0.875rem !important;
      border-radius: 0.5rem !important;
      box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05) !important;
      background-color: #ffffff !important;
    }

    .ts-wrapper .ts-control>input {
      display: inline-block !important;
      height: 100% !important;
      margin: 0 !important;
      padding: 0 !important;
      background: transparent !important;
      border: 0 !important;
    }
  </style>
</head>

<body class="bg-background text-on-surface font-sans antialiased min-h-screen flex flex-col">

  <!-- Header Section (Glassmorphic) -->
  <header class="sticky top-0 z-50 backdrop-blur-md bg-white/75 border-b border-slate-100 transition-all">
    <div class="max-w-[1280px] mx-auto px-4 md:px-6 h-16 flex items-center justify-between">
      <!-- Left: Logo + Tên website -->
      <a class="flex items-center gap-2 font-bold text-lg text-primary tracking-tight" href="index.php">
        <span class="text-xl">🏢</span>
        <span>Thông Tin Doanh Nghiệp</span>
      </a>

      <!-- Right: Nút Admin -->
      <a href="admin/crawl.php"
        class="text-xs px-3.5 py-1.5 rounded-lg border border-primary/20 text-primary font-medium hover:bg-primary hover:text-white transition duration-200">
        Admin
      </a>
    </div>
  </header>

  <!-- Banner/Hero Section (Không có thanh tìm kiếm) -->
  <section class="py-12 md:py-16 bg-gradient-to-b from-[#e5eeff]/40 to-transparent">
    <div class="max-w-[1280px] mx-auto px-4 md:px-6 text-center">
      <span
        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-primary/10 text-primary mb-4">
        <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
        Nền tảng tra cứu dữ liệu doanh nghiệp hàng đầu
      </span>
      <h1 class="text-3xl md:text-4xl font-extrabold text-on-surface tracking-tight mb-3">
        Tra cứu thông tin doanh nghiệp
      </h1>
      <p class="text-sm md:text-base text-slate-500 max-w-2xl mx-auto leading-relaxed">
        Tra cứu nhanh mã số thuế, người đại diện, địa chỉ và ngành nghề kinh doanh của hàng triệu doanh nghiệp trên toàn
        quốc.
      </p>
    </div>
  </section>

  <!-- Main Content Area -->
  <main class="flex-grow max-w-[1280px] w-full mx-auto px-4 md:px-6 pb-12">

    <h2 class="text-lg font-bold text-on-surface mb-4 flex items-center gap-2">
      <span class="w-1 h-5 rounded-full bg-primary"></span>
      <?= e($pageTitle) ?>
    </h2>

    <!-- Bộ lọc (Filters Form) -->
    <form method="GET" action="index.php" class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 mb-6">
      <input type="hidden" name="page" value="1">

      <!-- Hàng 1: Các select lọc trải đều chiếm hết các cột -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
        <!-- Tỉnh / Thành -->
        <div class="flex flex-col">
          <label class="text-xs font-semibold text-slate-500 mb-1.5">Tỉnh / Thành</label>
          <select name="tinh" class="form-select-sm">
            <option value="0">Tất cả</option>
            <?php
            $prevMienTay = null;
            foreach ($tinhList as $t):
              if ($prevMienTay != (bool) $t['mien_tay']):
                if ($t['mien_tay']): ?>
                  <optgroup label="── Miền Tây ──">
                  <?php else: ?>
                  </optgroup>
                  <optgroup label="── Tỉnh thành khác ──">
                  <?php endif;
                $prevMienTay = (bool) $t['mien_tay'];
              endif;
              ?>
                <option value="<?= $t['id'] ?>" <?= $filterTinh == (int) $t['id'] ? 'selected' : '' ?>>
                  <?= e($t['ten']) ?>
                </option>
              <?php endforeach;
            if ($prevMienTay != null)
              echo '</optgroup>'; ?>
          </select>
        </div>

        <!-- Phường / Xã -->
        <div class="flex flex-col">
          <label class="text-xs font-semibold text-slate-500 mb-1.5">Phường / Xã</label>
          <select name="phuong" class="form-select-sm" <?= empty($phuongList) ? 'disabled' : '' ?>>
            <option value="0">Tất cả</option>
            <?php foreach ($phuongList as $p): ?>
              <option value="<?= $p['id'] ?>" <?= $filterPhuong == (int) $p['id'] ? 'selected' : '' ?>>
                <?= e($p['ten']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Loại hình DN -->
        <div class="flex flex-col">
          <label class="text-xs font-semibold text-slate-500 mb-1.5">Loại hình DN</label>
          <select name="loai" class="form-select-sm">
            <option value="0">Tất cả</option>
            <?php foreach ($loaiList as $l): ?>
              <option value="<?= $l['id'] ?>" <?= $filterLoai == (int) $l['id'] ? 'selected' : '' ?>>
                <?= e($l['ten']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Ngành nghề -->
        <div class="flex flex-col">
          <label class="text-xs font-semibold text-slate-500 mb-1.5">Ngành nghề</label>
          <select name="nganh" class="form-select-sm">
            <option value="0">Tất cả</option>
            <?php foreach ($nganhList as $nn): ?>
              <option value="<?= $nn['id'] ?>" <?= $filterNganh == (int) $nn['id'] ? 'selected' : '' ?>>
                <?= e($nn['ten']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <!-- Hàng 2: Thanh tìm kiếm chiếm 9 cột, các nút lọc chiếm 3 cột (căn phải) -->
      <div class="flex flex-col md:flex-row gap-4 items-end">
        <div class="w-full md:w-3/4 flex flex-col">
          <label class="text-xs font-semibold text-slate-500 mb-1.5">Tìm kiếm</label>
          <input autocomplete="false" type="text" name="search"
            class="w-full text-sm h-[31px] px-3 rounded-lg border border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition duration-150 outline-none"
            placeholder="Nhập tên công ty, mã số thuế hoặc người đại diện..." value="<?= e($filterSearch ?? '') ?>">
        </div>
        <div class="w-full md:w-1/4 flex gap-2 justify-end">
          <a href="index.php"
            class="px-4 py-1.5 text-sm font-semibold rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 transition duration-150 flex-1 text-center">
            Xoá lọc
          </a>
          <button type="submit"
            class="px-5 py-1.5 text-sm font-semibold rounded-lg bg-primary hover:bg-primary-container text-white transition duration-150 flex-1">
            Lọc
          </button>
        </div>
      </div>
    </form>

    <!-- Kết quả thống kê -->
    <div class="flex justify-between items-center mb-3">
      <span class="text-xs text-slate-500">
        Tìm thấy <strong class="text-on-surface font-semibold"><?= number_format($total) ?></strong> doanh nghiệp
        <?= $total > 0 ? " (trang {$page}/{$totalPages})" : '' ?>
      </span>
    </div>

    <!-- Danh sách dữ liệu -->
    <?php if (empty($rows)): ?>
      <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 text-sm text-blue-700">
        Không có doanh nghiệp nào phù hợp.
      </div>
    <?php else: ?>
      <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden mb-6">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse align-middle">
            <thead>
              <tr class="bg-slate-50/70 border-b border-slate-100">
                <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-500 w-12">#</th>
                <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-500">Tên công ty</th>
                <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-500 w-36">MST</th>
                <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-500">Người đại diện</th>
                <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-500">Ngành nghề chính</th>
                <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-500 w-44">Tỉnh / Thành</th>
                <th class="px-4 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-500 w-44">Tình trạng</th>
                <th class="px-4 py-3 w-24"></th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <?php foreach ($rows as $i => $dn): ?>
                <tr class="hover:bg-slate-50/40 transition duration-150">
                  <td class="px-4 py-3.5 text-xs font-mono text-slate-400"><?= $offset + $i + 1 ?></td>
                  <td class="px-4 py-3.5">
                    <a href="chi-tiet.php?mst=<?= urlencode($dn['mst']) ?>"
                      class="font-semibold text-primary hover:underline hover:text-primary-container transition duration-150 block text-sm">
                      <?= e($dn['ten_cong_ty']) ?>
                    </a>
                  </td>
                  <td class="px-4 py-3.5">
                    <span
                      class="inline-block px-2 py-0.5 rounded bg-slate-50 border border-slate-200 text-xs font-mono font-semibold text-slate-600 tracking-tight">
                      <?= e($dn['mst']) ?>
                    </span>
                  </td>
                  <td class="px-4 py-3.5 text-xs text-slate-600">
                    <span class="flex items-center gap-1.5">
                      <i class="bi bi-person text-slate-400"></i>
                      <?= e($dn['nguoi_dai_dien'] ?: 'Chưa rõ') ?>
                    </span>
                  </td>
                  <td class="px-4 py-3.5 text-xs text-slate-500">
                    <div class="line-clamp-2 max-w-[240px] leading-relaxed" title="<?= e($dn['nganh_ten'] ?? '') ?>">
                      <?= e($dn['nganh_ten'] ?: 'Chưa rõ') ?>
                    </div>
                  </td>
                  <td class="px-4 py-3.5 text-xs text-slate-600">
                    <span class="flex items-center gap-1.5">
                      <i class="bi bi-geo-alt text-red-400"></i>
                      <?= e($dn['tinh_ten'] ?: 'Chưa rõ') ?>
                    </span>
                  </td>
                  <td class="px-4 py-3.5">
                    <?php
                    $ts = $dn['tinh_trang'] ?? '';
                    $badgeStyle = 'bg-slate-50 text-slate-600 border-slate-200';
                    if (str_contains(mb_strtolower($ts, 'UTF-8'), 'hoạt động')) {
                      $badgeStyle = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                    } elseif (str_contains(mb_strtolower($ts, 'UTF-8'), 'chờ xác minh') || str_contains(mb_strtolower($ts, 'UTF-8'), 'tạm ngừng')) {
                      $badgeStyle = 'bg-amber-50 text-amber-600 border-amber-200';
                    } elseif (str_contains(mb_strtolower($ts, 'UTF-8'), 'ngừng hoạt động')) {
                      $badgeStyle = 'bg-red-50 text-red-700 border-red-200';
                    }
                    ?>
                    <span
                      class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold border <?= $badgeStyle ?> max-w-[160px] truncate align-middle text-center" title="<?= e($ts) ?>">
                      <?= e($ts ?: 'Chưa rõ') ?>
                    </span>
                  </td>
                  <td class="px-4 py-3.5 text-right">
                    <a href="chi-tiet.php?mst=<?= urlencode($dn['mst']) ?>"
                      class="inline-flex items-center justify-center px-3 py-1 rounded-md text-xs font-semibold border border-primary/20 text-primary hover:bg-primary hover:text-white transition duration-150 whitespace-nowrap">
                      Chi tiết
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Phân trang (Pagination) -->
      <?php if ($totalPages > 1): ?>
        <nav class="flex justify-center items-center space-x-1.5 mt-6 mb-8">
          <?php if ($page > 1): ?>
            <a class="px-3 py-1.5 rounded-lg text-sm border border-slate-200 text-slate-600 hover:bg-slate-50 transition duration-150"
              href="<?= e($qs(['page' => $page - 1])) ?>">
              ‹ Trước
            </a>
          <?php endif; ?>

          <?php
          $start = max(1, $page - 3);
          $end = min($totalPages, $page + 3);
          if ($start > 1):
            ?>
            <a class="px-3 py-1.5 rounded-lg text-sm border border-slate-200 text-slate-600 hover:bg-slate-50 transition duration-150"
              href="<?= e($qs(['page' => 1])) ?>">1</a>
            <?php if ($start > 2): ?>
              <span class="px-2 text-slate-400 text-sm">…</span>
            <?php endif; ?>
          <?php endif; ?>

          <?php for ($p = $start; $p <= $end; $p++): ?>
            <a class="px-3 py-1.5 rounded-lg text-sm border transition duration-150 <?= $p == $page ? 'bg-primary border-primary text-white font-semibold' : 'border-slate-200 text-slate-600 hover:bg-slate-50' ?>"
              href="<?= e($qs(['page' => $p])) ?>">
              <?= $p ?>
            </a>
          <?php endfor; ?>

          <?php if ($end < $totalPages): ?>
            <?php if ($end < $totalPages - 1): ?>
              <span class="px-2 text-slate-400 text-sm">…</span>
            <?php endif; ?>
            <a class="px-3 py-1.5 rounded-lg text-sm border border-slate-200 text-slate-600 hover:bg-slate-50 transition duration-150"
              href="<?= e($qs(['page' => $totalPages])) ?>">
              <?= $totalPages ?>
            </a>
          <?php endif; ?>

          <?php if ($page < $totalPages): ?>
            <a class="px-3 py-1.5 rounded-lg text-sm border border-slate-200 text-slate-600 hover:bg-slate-50 transition duration-150"
              href="<?= e($qs(['page' => $page + 1])) ?>">
              Sau ›
            </a>
          <?php endif; ?>
        </nav>
      <?php endif; ?>
    <?php endif; ?>

  </main>

  <!-- Footer Section -->
  <footer class="bg-white border-t border-slate-100 py-6 text-center text-xs text-slate-400">
    <div class="max-w-[1280px] mx-auto px-4 md:px-6">
      Dữ liệu tổng hợp từ <a href="https://masothue.com" target="_blank" rel="noopener"
        class="text-primary hover:underline font-medium">masothue.com</a> — chỉ phục vụ mục đích tra cứu.
    </div>
  </footer>

  <!-- Khởi tạo Tom Select và tự động lọc phường xã client-side -->
  <script>
    const allPhuong = <?= json_encode($allPhuongList) ?>;

    document.addEventListener("DOMContentLoaded", function () {
      const tinhEl = document.querySelector('select[name="tinh"]');
      const phuongEl = document.querySelector('select[name="phuong"]');
      const loaiEl = document.querySelector('select[name="loai"]');
      const nganhEl = document.querySelector('select[name="nganh"]');

      let tsPhuong = null;
      if (phuongEl) {
        tsPhuong = new TomSelect(phuongEl, {
          create: false,
          controlInput: '<input>'
        });
      }

      if (tinhEl) {
        new TomSelect(tinhEl, {
          create: false,
          controlInput: '<input>',
          onChange: function (tinhId) {
            tinhId = parseInt(tinhId) || 0;
            if (tsPhuong) {
              // Clear current options
              tsPhuong.clearOptions();
              // Add default Option
              tsPhuong.addOption({ value: '0', text: 'Tất cả' });

              // Filter and populate new options
              const filtered = allPhuong.filter(p => parseInt(p.tinh_thanh_id) === tinhId);
              filtered.forEach(p => {
                tsPhuong.addOption({ value: p.id, text: p.ten });
              });

              tsPhuong.setValue('0');
              if (tinhId === 0 || filtered.length === 0) {
                tsPhuong.disable();
              } else {
                tsPhuong.enable();
              }
            }
          }
        });
      }

      if (loaiEl) {
        new TomSelect(loaiEl, {
          create: false,
          controlInput: '<input>'
        });
      }

      if (nganhEl) {
        new TomSelect(nganhEl, {
          create: false,
          controlInput: '<input>'
        });
      }
    });
  </script>

</body>

</html>