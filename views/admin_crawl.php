<?php
$adminTitle = 'Quản lý Crawler';
require_once __DIR__ . '/admin_layout_header.php';
?>

<!-- Header Section -->
<div class="flex flex-col md:flex-row md:items-end justify-between gap-md mb-2">
  <div>
    <h2 class="font-headline-sm text-headline-sm font-semibold text-on-surface mb-xs">Quản lý Crawler</h2>
    <p class="font-body-md text-body-md text-on-surface-variant">Điều phối quá trình thu thập dữ liệu doanh nghiệp từ nguồn.</p>
  </div>
  <div class="flex gap-sm">
    <a href="logs.php" class="flex items-center gap-xs px-md py-2 border border-outline-variant rounded-lg bg-surface-container-lowest text-on-surface font-label-md text-label-md hover:bg-surface-container-low transition-colors no-underline">
      <span class="material-symbols-outlined" style="font-size: 18px;">history</span>
      Lịch sử cào
    </a>
    <a href="queue.php" class="flex items-center gap-xs px-md py-2 bg-primary text-on-primary rounded-lg font-label-md text-label-md hover:bg-primary-container transition-colors shadow-sm no-underline">
      <span class="material-symbols-outlined" style="font-size: 18px;">list_alt</span>
      Hàng đợi cào
    </a>
  </div>
</div>

<!-- KPI Cards Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-md mb-6">
  <!-- Card 1: Tổng Doanh nghiệp -->
  <div class="bg-surface-container-lowest border border-outline-variant p-md rounded-xl shadow-[0_4px_6px_-1px_rgb(0,0,0,0.05)]">
    <div class="flex items-center justify-between mb-sm">
      <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Tổng doanh nghiệp</span>
      <div class="p-1.5 bg-surface-container-low rounded-md">
        <span class="material-symbols-outlined text-primary" style="font-size: 18px;">database</span>
      </div>
    </div>
    <div class="font-headline-md text-headline-md text-on-surface font-bold"><?= number_format($totalDn) ?></div>
    <div class="mt-xs text-slate-400 font-label-sm text-label-sm">Đã lưu trong CSDL</div>
  </div>
  
  <!-- Card 2: URL đang chờ -->
  <div class="bg-surface-container-lowest border border-outline-variant p-md rounded-xl shadow-[0_4px_6px_-1px_rgb(0,0,0,0.05)]">
    <div class="flex items-center justify-between mb-sm">
      <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">URL đang chờ</span>
      <div class="p-1.5 bg-surface-container-high rounded-md">
        <span class="material-symbols-outlined text-[#d97706]" style="font-size: 18px;">hourglass_empty</span>
      </div>
    </div>
    <div class="font-headline-md text-headline-md text-[#d97706] font-bold"><?= (int) ($statsRaw['cho'] ?? 0) ?></div>
    <div class="mt-xs text-on-surface-variant font-label-sm text-label-sm">Đang xếp hàng chờ quét</div>
  </div>
  
  <!-- Card 3: URL thành công -->
  <div class="bg-surface-container-lowest border border-outline-variant p-md rounded-xl shadow-[0_4px_6px_-1px_rgb(0,0,0,0.05)]">
    <div class="flex items-center justify-between mb-sm">
      <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">URL thành công</span>
      <div class="p-1.5 bg-surface-container-high rounded-md">
        <span class="material-symbols-outlined text-tertiary-container" style="font-size: 18px;">check_circle</span>
      </div>
    </div>
    <div class="font-headline-md text-headline-md text-tertiary-container font-bold"><?= (int) ($statsRaw['thanh_cong'] ?? 0) ?></div>
    <div class="mt-xs text-on-surface-variant font-label-sm text-label-sm">Quét và lưu hoàn tất</div>
  </div>
  
  <!-- Card 4: URL thất bại -->
  <div class="bg-surface-container-lowest border border-outline-variant p-md rounded-xl shadow-[0_4px_6px_-1px_rgb(0,0,0,0.05)]">
    <div class="flex items-center justify-between mb-sm">
      <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">URL thất bại</span>
      <div class="p-1.5 bg-error-container rounded-md">
        <span class="material-symbols-outlined text-on-error-container" style="font-size: 18px;">error</span>
      </div>
    </div>
    <div class="font-headline-md text-headline-md text-error font-bold"><?= (int) ($statsRaw['that_bai'] ?? 0) ?></div>
    <div class="mt-xs text-error font-label-sm text-label-sm">Cần kiểm tra/Cào lại</div>
  </div>
</div>

<!-- Bento Grid: Control Center & Terminal & Queue Monitor -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-lg mb-6">
  <!-- Left Column: Controls (Trung tâm điều khiển) -->
  <div class="lg:col-span-1 flex flex-col gap-lg">
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-[0_4px_6px_-1px_rgb(0,0,0,0.05)] p-lg flex flex-col justify-between">
      <div>
        <h3 class="font-headline-sm text-headline-sm text-on-surface mb-md">Trung tâm điều khiển</h3>
        
        <!-- Form Crawl List -->
        <form method="GET" action="run_crawl.php" target="logframe" class="space-y-4">
          <input type="hidden" name="action" value="list">
          <div>
            <label class="block font-label-md text-label-md text-on-surface-variant mb-xs">Nguồn dữ liệu (Tỉnh/Thành)</label>
            <select name="tinh" class="w-full h-10 bg-surface-container-lowest border border-outline-variant rounded-lg px-sm font-body-md text-body-md focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none">
              <?php foreach ($tinhOptions as $v => $label): if ($v === '') continue; ?>
                <option value="<?= htmlspecialchars($v) ?>"><?= htmlspecialchars($label) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="block font-label-md text-label-md text-on-surface-variant mb-xs">Số trang quét danh sách (tối đa 100)</label>
            <input name="limit" class="w-full h-10 bg-surface-container-lowest border border-outline-variant rounded-lg px-sm font-body-md text-body-md focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none" type="number" value="5" min="1" max="100"/>
          </div>
          <button type="submit" class="w-full h-10 flex items-center justify-center gap-xs bg-primary hover:bg-primary-container text-on-primary rounded-lg font-label-md text-label-md transition-colors shadow-sm">
            <span class="material-symbols-outlined" style="font-size: 18px;">list_alt</span>
            Crawl danh sách
          </button>
        </form>

        <div class="border-t border-outline-variant/30 my-4"></div>

        <!-- Form Crawl Detail -->
        <form method="GET" action="run_crawl.php" target="logframe" class="space-y-4">
          <input type="hidden" name="action" value="detail">
          <div>
            <label class="block font-label-md text-label-md text-on-surface-variant mb-xs">Số bản ghi quét chi tiết / lần</label>
            <input name="limit" class="w-full h-10 bg-surface-container-lowest border border-outline-variant rounded-lg px-sm font-body-md text-body-md focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none" type="number" value="20" min="1" max="100"/>
          </div>
          <button type="submit" class="w-full h-10 flex items-center justify-center gap-xs bg-[#006e4b] hover:bg-emerald-700 text-white rounded-lg font-label-md text-label-md transition-colors shadow-sm">
            <span class="material-symbols-outlined" style="font-size: 18px;">view_headline</span>
            Crawl chi tiết từ hàng đợi
          </button>
        </form>
      </div>
    </div>
  </div>

  <!-- Right Column: Terminal & Queue Monitor -->
  <div class="lg:col-span-2 flex flex-col gap-lg">
    <!-- Terminal Panel -->
    <div class="bg-[#0F172A] border border-outline/20 rounded-xl shadow-[0_4px_6px_-1px_rgb(0,0,0,0.05)] overflow-hidden flex flex-col h-[300px]">
      <div class="flex items-center justify-between px-md py-2 bg-[#1E293B] border-b border-outline/10">
        <div class="flex items-center gap-2">
          <span class="material-symbols-outlined text-tertiary-fixed-dim" style="font-size: 16px;">terminal</span>
          <span class="font-mono text-xs text-outline-variant">crawl-stream.log</span>
        </div>
        <button class="text-xs text-outline-variant hover:text-white px-2.5 py-0.5 rounded border border-outline/20 bg-slate-800 hover:bg-slate-700 transition"
                onclick="document.querySelector('iframe[name=logframe]').src='about:blank'">
          Xoá log
        </button>
      </div>
      <div class="flex-grow bg-[#0F172A]">
        <iframe name="logframe" src="about:blank"
                style="width:100%;height:100%;border:0;background:#0F172A;color:#94A3B8;font-family:'JetBrains Mono', monospace;"
                class="bg-[#0F172A] rounded-bottom"></iframe>
      </div>
    </div>

    <!-- Queue Monitor -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-[0_4px_6px_-1px_rgb(0,0,0,0.05)] flex-1 p-lg overflow-hidden flex flex-col justify-between">
      <div>
        <div class="flex items-center justify-between mb-md">
          <h3 class="font-headline-sm text-headline-sm text-on-surface">Theo dõi hàng đợi</h3>
          <a href="queue.php" class="text-primary font-label-sm text-label-sm hover:underline no-underline">Xem tất cả</a>
        </div>
        <div class="overflow-y-auto space-y-2 max-h-[170px] pr-1">
          <?php if (empty($queueItems)): ?>
            <div class="text-center text-xs text-slate-400 py-6">Hàng đợi hiện tại đang trống.</div>
          <?php else: foreach ($queueItems as $qi): ?>
            <div class="flex items-center justify-between p-sm border border-outline-variant/40 rounded-lg hover:bg-surface-container-low transition-colors">
              <div class="flex items-center gap-sm">
                <span class="material-symbols-outlined text-outline-variant" style="font-size: 18px;">link</span>
                <span class="font-mono text-xs text-on-surface-variant truncate max-w-[200px] md:max-w-[400px]" title="<?= e($qi['url']) ?>">
                  <?= e($qi['url']) ?>
                </span>
              </div>
              <?php
              $status = $qi['trang_thai'];
              if ($status === 'thanh_cong'): ?>
                <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-700 font-semibold text-[10px] rounded-full border border-emerald-200">Thành công</span>
              <?php elseif ($status === 'dang_xu_ly'): ?>
                <span class="px-2.5 py-0.5 bg-[#fef3c7] text-[#92400e] font-semibold text-[10px] rounded-full border border-amber-200 flex items-center gap-1">
                  <span class="material-symbols-outlined animate-spin" style="font-size: 10px;">sync</span> Đang xử lý
                </span>
              <?php elseif ($status === 'cho'): ?>
                <span class="px-2.5 py-0.5 bg-slate-100 text-slate-600 font-semibold text-[10px] rounded-full border border-slate-200">Chờ</span>
              <?php else: ?>
                <span class="px-2.5 py-0.5 bg-red-50 text-red-700 font-semibold text-[10px] rounded-full border border-red-200">Thất bại</span>
              <?php endif; ?>
            </div>
          <?php endforeach; endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Workflow Stepper -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-[0_4px_6px_-1px_rgb(0,0,0,0.05)] p-lg mt-md">
  <h3 class="font-headline-sm text-headline-sm text-on-surface mb-xl">Luồng xử lý dữ liệu</h3>
  <div class="relative flex justify-between items-center w-full max-w-4xl mx-auto py-4">
    <!-- Connecting Line -->
    <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-surface-container-high z-0"></div>
    <!-- Active Line -->
    <div class="absolute left-0 top-1/2 -translate-y-1/2 w-[75%] h-1 bg-primary z-0 transition-all duration-500"></div>
    
    <!-- Step 1 -->
    <div class="relative z-10 flex flex-col items-center gap-xs">
      <div class="w-10 h-10 rounded-full bg-primary text-on-primary flex items-center justify-center shadow-md">
        <span class="material-symbols-outlined" style="font-size: 20px;">check</span>
      </div>
      <span class="font-label-sm text-label-sm text-on-surface font-semibold text-center whitespace-nowrap">Thu thập URL</span>
    </div>
    <!-- Step 2 -->
    <div class="relative z-10 flex flex-col items-center gap-xs">
      <div class="w-10 h-10 rounded-full bg-primary text-on-primary flex items-center justify-center shadow-md">
        <span class="material-symbols-outlined" style="font-size: 20px;">check</span>
      </div>
      <span class="font-label-sm text-label-sm text-on-surface font-semibold text-center whitespace-nowrap">Đưa vào hàng đợi</span>
    </div>
    <!-- Step 3 -->
    <div class="relative z-10 flex flex-col items-center gap-xs">
      <div class="w-10 h-10 rounded-full bg-primary text-on-primary flex items-center justify-center shadow-md">
        <span class="material-symbols-outlined" style="font-size: 20px;">check</span>
      </div>
      <span class="font-label-sm text-label-sm text-primary font-bold text-center whitespace-nowrap">Trích xuất chi tiết</span>
    </div>
    <!-- Step 4 -->
    <div class="relative z-10 flex flex-col items-center gap-xs">
      <div class="w-10 h-10 rounded-full bg-surface-container-lowest border-2 border-primary text-primary flex items-center justify-center shadow-md">
        <span class="material-symbols-outlined animate-spin" style="font-size: 20px;">sync</span>
      </div>
      <span class="font-label-sm text-label-sm text-primary font-bold text-center whitespace-nowrap font-semibold">Xử lý & Chuẩn hóa</span>
    </div>
    <!-- Step 5 -->
    <div class="relative z-10 flex flex-col items-center gap-xs opacity-50">
      <div class="w-10 h-10 rounded-full bg-surface-container-high text-on-surface-variant flex items-center justify-center">
        <span class="material-symbols-outlined" style="font-size: 20px;">task_alt</span>
      </div>
      <span class="font-label-sm text-label-sm text-on-surface-variant text-center whitespace-nowrap font-semibold">Lưu CSDL</span>
    </div>
  </div>
</div>

<?php
require_once __DIR__ . '/admin_layout_footer.php';
?>