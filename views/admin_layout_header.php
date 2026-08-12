<?php
$currentUri = $_SERVER['REQUEST_URI'];
$menuItems = [
  ['label' => 'Tổng quan',      'url' => 'index.php',          'icon' => 'dashboard'],
  ['label' => 'Trình thu thập', 'url' => 'crawl.php',          'icon' => 'terminal'],
  ['label' => 'Hàng đợi cào',   'url' => 'queue.php',          'icon' => 'list_alt'],
  ['label' => 'Lịch sử cào',    'url' => 'logs.php',           'icon' => 'history'],
  ['label' => 'Doanh nghiệp',   'url' => 'doanh-nghiep.php',   'icon' => 'database'],
  ['label' => 'Ngành nghề',     'url' => 'danh-muc.php',       'icon' => 'category'],
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>Admin - <?= e($adminTitle ?? 'Quản lý') ?> | Thông Tin Doanh Nghiệp</title>
  
  <!-- Bootstrap 5 & Icons (for legacy compatibility with other admin pages) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  <!-- Material Symbols Outlined -->
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
  
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Google Fonts (Inter & JetBrains Mono) -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet"/>
  
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  
  <!-- Tailwind Config -->
  <script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          "colors": {
            "surface-container-lowest": "#ffffff",
            "tertiary-fixed-dim": "#4edea3",
            "outline": "#777587",
            "on-surface-variant": "#464555",
            "surface-container": "#e5eeff",
            "secondary-fixed-dim": "#adc6ff",
            "on-primary-fixed-variant": "#3323cc",
            "surface-container-highest": "#d3e4fe",
            "on-secondary-container": "#fefcff",
            "error-container": "#ffdad6",
            "on-error-container": "#93000a",
            "outline-variant": "#c7c4d8",
            "secondary": "#0058be",
            "tertiary-fixed": "#6ffbbe",
            "surface-bright": "#f8f9ff",
            "secondary-fixed": "#d8e2ff",
            "inverse-primary": "#c3c0ff",
            "tertiary-container": "#006e4b",
            "on-primary-fixed": "#0f0069",
            "on-primary": "#ffffff",
            "inverse-surface": "#213145",
            "on-tertiary-container": "#67f4b7",
            "error": "#ba1a1a",
            "on-secondary-fixed-variant": "#004395",
            "background": "#f8f9ff",
            "surface-variant": "#d3e4fe",
            "on-tertiary-fixed": "#002113",
            "primary": "#3525cd",
            "on-secondary": "#ffffff",
            "surface-tint": "#4d44e3",
            "on-secondary-fixed": "#001a42",
            "on-background": "#0b1c30",
            "primary-fixed-dim": "#c3c0ff",
            "surface-container-high": "#dce9ff",
            "inverse-on-surface": "#eaf1ff",
            "on-primary-container": "#dad7ff",
            "on-error": "#ffffff",
            "surface": "#f8f9ff",
            "surface-container-low": "#eff4ff",
            "secondary-container": "#2170e4",
            "on-tertiary": "#ffffff",
            "on-surface": "#0b1c30",
            "surface-dim": "#cbdbf5",
            "primary-fixed": "#e2dfff",
            "tertiary": "#005338",
            "on-tertiary-fixed-variant": "#005236",
            "primary-container": "#4f46e5"
          },
          "borderRadius": {
            "DEFAULT": "0.25rem",
            "lg": "0.5rem",
            "xl": "0.75rem",
            "full": "9999px"
          },
          "spacing": {
            "gutter": "24px",
            "base": "4px",
            "lg": "24px",
            "xl": "32px",
            "2xl": "48px",
            "xs": "8px",
            "md": "16px",
            "sm": "12px",
            "container-max": "1280px",
            "margin-mobile": "16px"
          },
          "fontFamily": {
            "sans": ["Inter", "sans-serif"],
            "display-lg": ["Inter", "sans-serif"],
            "headline-sm": ["Inter", "sans-serif"],
            "label-sm": ["Inter", "sans-serif"],
            "body-md": ["Inter", "sans-serif"],
            "headline-md": ["Inter", "sans-serif"],
            "display-lg-mobile": ["Inter", "sans-serif"],
            "body-lg": ["Inter", "sans-serif"],
            "label-md": ["Inter", "sans-serif"],
            "mono": ["JetBrains Mono", "monospace"]
          },
          "fontSize": {
            "display-lg": ["48px", { "lineHeight": "1.1", "letterSpacing": "-0.02em", "fontWeight": "700" }],
            "headline-sm": ["20px", { "lineHeight": "1.4", "fontWeight": "600" }],
            "label-sm": ["12px", { "lineHeight": "1.2", "fontWeight": "600" }],
            "body-md": ["16px", { "lineHeight": "1.5", "fontWeight": "400" }],
            "headline-md": ["24px", { "lineHeight": "1.3", "letterSpacing": "-0.01em", "fontWeight": "600" }],
            "display-lg-mobile": ["32px", { "lineHeight": "1.2", "letterSpacing": "-0.02em", "fontWeight": "700" }],
            "body-lg": ["18px", { "lineHeight": "1.6", "fontWeight": "400" }],
            "label-md": ["14px", { "lineHeight": "1.4", "letterSpacing": "0.01em", "fontWeight": "500" }]
          }
        }
      }
    }
  </script>
</head>
<body class="bg-background text-on-surface antialiased flex font-sans min-h-screen">

  <!-- SideNavBar (Shared Component) -->
  <nav class="fixed left-0 top-0 h-full w-[280px] hidden lg:flex flex-col bg-on-background border-r border-outline/20 shadow-xl z-50">
    <!-- Header -->
    <div class="px-lg py-xl border-b border-outline/20">
      <div class="flex items-center gap-md mb-lg">
        <div class="w-10 h-10 rounded-full bg-primary/30 flex items-center justify-center border-2 border-outline/30 text-white font-bold select-none">
          A
        </div>
        <div>
          <h2 class="font-headline-sm text-headline-sm text-white font-black">Admin Terminal</h2>
          <p class="font-label-sm text-label-sm text-outline-variant">Hệ thống quản trị</p>
        </div>
      </div>
      <a href="../index.php" class="w-full flex items-center justify-center gap-sm bg-primary/20 text-tertiary-fixed-dim font-label-md text-label-md py-sm rounded-lg hover:bg-white/5 transition-all cursor-pointer select-none border border-primary/30 text-center no-underline">
        <span class="material-symbols-outlined" style="font-size: 18px;">home</span>
        Xem trang chủ
      </a>
    </div>

    <!-- Navigation Links -->
    <div class="flex flex-col h-full py-md gap-base overflow-y-auto">
      <?php foreach ($menuItems as $item): 
        $isActive = str_contains($currentUri, '/admin/' . $item['url']) 
                 || (str_ends_with(rtrim($currentUri, '/'), '/admin') && $item['url'] === 'index.php');
        if ($isActive):
      ?>
        <a class="flex items-center gap-3 bg-primary/20 text-tertiary-fixed-dim border-l-4 border-primary px-4 py-3 cursor-pointer select-none no-underline" href="<?= $item['url'] ?>">
          <span class="material-symbols-outlined"><?= $item['icon'] ?></span>
          <span class="font-label-md text-label-md font-semibold"><?= $item['label'] ?></span>
        </a>
      <?php else: ?>
        <a class="flex items-center gap-3 text-outline-variant hover:text-white px-4 py-3 hover:bg-white/5 transition-all cursor-pointer select-none no-underline" href="<?= $item['url'] ?>">
          <span class="material-symbols-outlined"><?= $item['icon'] ?></span>
          <span class="font-label-md text-label-md"><?= $item['label'] ?></span>
        </a>
      <?php endif; endforeach; ?>
    </div>

    <!-- Footer -->
    <div class="mt-auto px-md py-lg border-t border-outline/20">
      <a class="flex items-center gap-3 text-outline-variant hover:text-white px-4 py-3 hover:bg-white/5 transition-all cursor-pointer select-none no-underline" href="../index.php">
        <span class="material-symbols-outlined">logout</span>
        <span class="font-label-md text-label-md">Thoát Admin</span>
      </a>
    </div>
  </nav>

  <!-- Main Content Area -->
  <main class="flex-grow lg:pl-[280px] flex flex-col min-h-screen overflow-x-hidden">
    <!-- TopNavBar -->
    <header class="sticky top-0 z-40 w-full bg-surface-bright/80 backdrop-blur-md border-b border-outline-variant/50 flex justify-between items-center px-lg h-16">
      <div class="flex items-center gap-md">
        <!-- Sidebar Toggle for Mobile -->
        <button id="mobile-menu-btn" class="lg:hidden p-2 text-on-surface hover:bg-surface-container-low rounded-lg transition" onclick="toggleMobileMenu()">
          <span class="material-symbols-outlined">menu</span>
        </button>
        <h1 class="font-headline-sm text-headline-sm font-semibold text-on-surface"><?= e($adminTitle ?? 'Dashboard') ?></h1>
      </div>
      
      <div class="flex items-center gap-sm">
        <a href="../index.php" target="_blank" class="p-2 text-on-surface-variant hover:bg-surface-container-low rounded-full transition-all duration-200" title="Xem trang chủ">
          <span class="material-symbols-outlined">open_in_new</span>
        </a>
        <div class="h-8 w-px bg-outline-variant/50 mx-xs"></div>
        <div class="w-8 h-8 rounded-full bg-primary/30 flex items-center justify-center border border-outline-variant text-primary font-bold text-xs select-none">
          A
        </div>
      </div>
    </header>

    <!-- Mobile Navigation Drawer Overlay -->
    <div id="mobile-menu-overlay" class="fixed inset-0 bg-black/50 z-50 hidden transition-opacity duration-300" onclick="toggleMobileMenu()"></div>
    <div id="mobile-menu" class="fixed left-0 top-0 h-full w-[280px] bg-on-background border-r border-outline/20 shadow-xl z-50 -translate-x-full transition-transform duration-300 flex flex-col">
      <div class="px-lg py-xl border-b border-outline/20 flex items-center justify-between">
        <div class="flex items-center gap-md">
          <div class="w-10 h-10 rounded-full bg-primary/30 flex items-center justify-center border-2 border-outline/30 text-white font-bold select-none">
            A
          </div>
          <div>
            <h2 class="font-headline-sm text-headline-sm text-white font-black">Admin Terminal</h2>
            <p class="font-label-sm text-label-sm text-outline-variant">Hệ thống quản trị</p>
          </div>
        </div>
        <button class="text-white hover:text-outline-variant" onclick="toggleMobileMenu()">
          <span class="material-symbols-outlined">close</span>
        </button>
      </div>

      <div class="flex flex-col h-full py-md gap-base overflow-y-auto">
        <?php foreach ($menuItems as $item): 
          $isActive = str_contains($currentUri, '/admin/' . $item['url']) 
                   || (str_ends_with(rtrim($currentUri, '/'), '/admin') && $item['url'] === 'index.php');
          if ($isActive):
        ?>
          <a class="flex items-center gap-3 bg-primary/20 text-tertiary-fixed-dim border-l-4 border-primary px-4 py-3 cursor-pointer select-none no-underline" href="<?= $item['url'] ?>">
            <span class="material-symbols-outlined"><?= $item['icon'] ?></span>
            <span class="font-label-md text-label-md font-semibold"><?= $item['label'] ?></span>
          </a>
        <?php else: ?>
          <a class="flex items-center gap-3 text-outline-variant hover:text-white px-4 py-3 hover:bg-white/5 transition-all cursor-pointer select-none no-underline" href="<?= $item['url'] ?>">
            <span class="material-symbols-outlined"><?= $item['icon'] ?></span>
            <span class="font-label-md text-label-md"><?= $item['label'] ?></span>
          </a>
        <?php endif; endforeach; ?>
      </div>
    </div>

    <!-- Script to toggle mobile menu -->
    <script>
      function toggleMobileMenu() {
        const overlay = document.getElementById('mobile-menu-overlay');
        const menu = document.getElementById('mobile-menu');
        const isHidden = menu.classList.contains('-translate-x-full');
        if (isHidden) {
          overlay.classList.remove('hidden');
          menu.classList.remove('-translate-x-full');
        } else {
          overlay.classList.add('hidden');
          menu.classList.add('-translate-x-full');
        }
      }
    </script>

    <!-- Page Canvas Content -->
    <div class="p-gutter md:p-lg flex-1 flex flex-col gap-lg max-w-container-max mx-auto w-full overflow-x-hidden">
