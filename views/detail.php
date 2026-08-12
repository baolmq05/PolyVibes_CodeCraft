<?php
$uploadUrl = 'uploads/nganh-nghe/';
if (!empty($dn['nganh_hinh_anh'])) {
    $uploadUrl = 'uploads/nganh-nghe/';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle) ?> (MST: <?= e($dn['mst']) ?>) – ThongTinDN</title>
  <meta name="description" content="Thông tin doanh nghiệp <?= e($pageTitle) ?>, MST <?= e($dn['mst']) ?>, <?= e($dn['tinh_ten'] ?? '') ?>.">
  
  <!-- Google Fonts (Inter) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Bootstrap Icons (for icon support) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
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
</head>
<body class="bg-background text-on-surface font-sans antialiased min-h-screen flex flex-col">

  <!-- Header Section (Glassmorphic) -->
  <header class="sticky top-0 z-50 backdrop-blur-md bg-white/75 border-b border-slate-100 transition-all">
    <div class="max-w-[1280px] mx-auto px-4 md:px-6 h-16 flex items-center justify-between">
      <!-- Left: Tên website -->
      <a class="flex items-center gap-2 font-bold text-lg text-primary tracking-tight" href="index.php">
        <span>Thông Tin Doanh Nghiệp</span>
      </a>
      
      <!-- Right: Nút Admin -->
      <a href="admin/crawl.php" class="text-xs px-3.5 py-1.5 rounded-lg border border-primary/20 text-primary font-medium hover:bg-primary hover:text-white transition duration-200">
        Admin
      </a>
    </div>
  </header>

  <!-- Main Content Area -->
  <main class="flex-grow max-w-[960px] w-full mx-auto px-4 md:px-6 py-8">
    
    <!-- Breadcrumb -->
    <nav class="flex items-center space-x-2 text-xs text-slate-500 mb-6 py-2">
      <a href="index.php" class="hover:text-primary transition duration-150">Trang chủ</a>
      <span class="text-slate-300">/</span>
      <?php if ($dn['nganh_ten']): ?>
      <a href="index.php?nganh=<?= (int)$dn['nganh_nghe_id'] ?>" class="hover:text-primary transition duration-150">
        <?= e($dn['nganh_ten']) ?>
      </a>
      <span class="text-slate-300">/</span>
      <?php endif; ?>
      <span class="text-slate-400 font-medium truncate max-w-[250px]" title="<?= e($dn['ten_cong_ty']) ?>">
        <?= e($dn['ten_cong_ty']) ?>
      </span>
    </nav>

    <!-- Company Header Card -->
    <div class="bg-white rounded-xl border border-slate-100 p-6 shadow-sm mb-6">
      <div class="flex flex-col md:flex-row items-start gap-4">
        <!-- Industry Icon/Image -->
        <?php if ($dn['nganh_hinh_anh']): ?>
        <img src="<?= $uploadUrl . e($dn['nganh_hinh_anh']) ?>"
             class="w-16 h-16 object-cover rounded-xl border border-slate-100 flex-shrink-0"
             alt="<?= e($dn['nganh_ten'] ?? '') ?>">
        <?php else: ?>
        <div class="w-16 h-16 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between justify-content-center text-3xl flex-shrink-0 select-none">
          <span class="m-auto text-slate-400">🏢</span>
        </div>
        <?php endif; ?>

        <!-- Company Names and Badges -->
        <div class="flex-grow">
          <h1 class="text-xl md:text-2xl font-extrabold text-on-surface tracking-tight leading-snug mb-1">
            <?= e($dn['ten_cong_ty']) ?>
          </h1>
          <?php if ($dn['ten_quoc_te']): ?>
          <div class="text-xs text-slate-400 font-medium mb-0.5">
            <span class="text-slate-500">Tên quốc tế:</span> <?= e($dn['ten_quoc_te']) ?>
          </div>
          <?php endif; ?>
          <?php if ($dn['ten_viet_tat']): ?>
          <div class="text-xs text-slate-400 font-medium mb-1">
            <span class="text-slate-500">Tên viết tắt:</span> <?= e($dn['ten_viet_tat']) ?>
          </div>
          <?php endif; ?>
          
          <!-- Badges -->
          <div class="flex flex-wrap gap-1.5 mt-3">
            <?php 
            $ts = $dn['tinh_trang'] ?? ''; 
            $statusStyle = 'bg-slate-50 text-slate-600 border-slate-200';
            if (str_contains(mb_strtolower($ts, 'UTF-8'), 'hoạt động')) {
                $statusStyle = 'bg-emerald-50 text-emerald-700 border-emerald-200';
            } elseif (str_contains(mb_strtolower($ts, 'UTF-8'), 'chờ xác minh') || str_contains(mb_strtolower($ts, 'UTF-8'), 'tạm ngừng')) {
                $statusStyle = 'bg-amber-50 text-amber-600 border-amber-200';
            } elseif (str_contains(mb_strtolower($ts, 'UTF-8'), 'ngừng')) {
                $statusStyle = 'bg-red-50 text-red-700 border-red-200';
            }
            ?>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border <?= $statusStyle ?>">
              <?= e($ts ?: 'Chưa rõ') ?>
            </span>
            <?php if ($dn['loai_ten']): ?>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
              <?= e($dn['loai_ten']) ?>
            </span>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Details Section -->
    <h2 class="text-lg font-bold text-on-surface mb-4 flex items-center gap-2">
      <span class="w-1 h-5 rounded-full bg-primary"></span>
      Thông tin chi tiết
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
      <?php
      $fields = [
          ['label' => 'Mã số thuế',        'value' => $dn['mst'],             'icon' => 'card-text'],
          ['label' => 'Người đại diện',    'value' => $dn['nguoi_dai_dien'],  'icon' => 'person'],
          ['label' => 'Điện thoại',        'value' => $dn['dien_thoai'],      'icon' => 'telephone'],
          ['label' => 'Ngành nghề chính',  'value' => $dn['nganh_ten'],       'icon' => 'briefcase'],
          ['label' => 'Loại hình DN',      'value' => $dn['loai_ten'],        'icon' => 'building'],
          ['label' => 'Địa chỉ',           'value' => $dn['dia_chi'],         'icon' => 'geo-alt'],
          ['label' => 'Địa chỉ Thuế',      'value' => $dn['dia_chi_thue'],    'icon' => 'receipt'],
          ['label' => 'Tỉnh / Thành phố',  'value' => $dn['tinh_ten'],        'icon' => 'map'],
          ['label' => 'Phường / Xã',       'value' => $dn['phuong_ten'],      'icon' => 'geo'],
          ['label' => 'Ngày hoạt động',    'value' => $dn['ngay_hoat_dong'],  'icon' => 'calendar-event'],
          ['label' => 'Quản lý bởi',       'value' => $dn['quan_ly_boi'],     'icon' => 'shield-check'],
      ];

      foreach ($fields as $field):
          if (empty($field['value'])) continue;
      ?>
      <div class="bg-white rounded-xl border border-slate-100 p-4 shadow-sm hover:shadow-md/5 transition duration-200">
        <div class="flex items-center gap-2 mb-1.5 text-slate-400">
          <i class="bi bi-<?= $field['icon'] ?> text-primary"></i>
          <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500"><?= e($field['label']) ?></span>
        </div>
        <div class="text-sm font-semibold text-on-surface break-words leading-relaxed">
          <?= e($field['value']) ?>
        </div>
      </div>
      <?php endforeach; ?>

      <!-- Source & Last Updated Cells -->
      <?php if (!empty($dn['url_nguon'])): ?>
      <div class="bg-slate-50 rounded-xl border border-slate-200 p-4 shadow-sm hover:shadow-md/5 transition duration-200">
        <div class="flex items-center gap-2 mb-1.5 text-slate-400">
          <i class="bi bi-link-45deg text-primary"></i>
          <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Nguồn dữ liệu</span>
        </div>
        <div class="text-sm font-semibold leading-relaxed">
          <a href="<?= e($dn['url_nguon']) ?>" target="_blank" rel="noopener noreferrer nofollow" class="text-primary hover:underline hover:text-primary-container transition duration-150">
            Xem trên masothue.com ↗
          </a>
        </div>
      </div>
      <?php endif; ?>

      <?php if (!empty($dn['ngay_cap_nhat'])): ?>
      <div class="bg-slate-50 rounded-xl border border-slate-200 p-4 shadow-sm hover:shadow-md/5 transition duration-200">
        <div class="flex items-center gap-2 mb-1.5 text-slate-400">
          <i class="bi bi-clock-history text-primary"></i>
          <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Cập nhật lần cuối</span>
        </div>
        <div class="text-sm font-semibold text-slate-500 leading-relaxed">
          <?= e(date('d/m/Y H:i', strtotime($dn['ngay_cap_nhat']))) ?>
        </div>
      </div>
      <?php endif; ?>
    </div>

    <!-- Navigation Action Buttons -->
    <div class="flex flex-wrap gap-3 mt-8 mb-4 border-t border-slate-100 pt-6">
      <a href="index.php" class="px-4 py-2 text-sm font-semibold rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 transition duration-150">
        Quay lại danh sách
      </a>
      
      <?php if ($dn['tinh_thanh_id']): ?>
      <a href="index.php?tinh=<?= (int)$dn['tinh_thanh_id'] ?>" class="px-4 py-2 text-sm font-semibold rounded-lg border border-primary/20 text-primary hover:bg-primary hover:text-white transition duration-150">
        Xem doanh nghiệp khác cùng tỉnh
      </a>
      <?php endif; ?>
      
      <?php if ($dn['nganh_nghe_id']): ?>
      <a href="index.php?nganh=<?= (int)$dn['nganh_nghe_id'] ?>" class="px-4 py-2 text-sm font-semibold rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 transition duration-150">
        Xem doanh nghiệp khác cùng ngành
      </a>
      <?php endif; ?>
    </div>

  </main>

  <!-- Footer Section -->
  <footer class="bg-white border-t border-slate-100 py-6 text-center text-xs text-slate-400 mt-auto">
    <div class="max-w-[1280px] mx-auto px-4 md:px-6">
      Dữ liệu tổng hợp từ <a href="https://masothue.com" target="_blank" rel="noopener" class="text-primary hover:underline font-medium">masothue.com</a> — chỉ phục vụ mục đích tra cứu.
    </div>
  </footer>

</body>
</html>
