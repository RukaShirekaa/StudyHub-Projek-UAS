<?php
$user_notifications = [];
$user_unread_notifications = 0;
if (isset($_SESSION['user_id'])) {
    $pdo_notif = Database::getInstance();
    $stmt_notif = $pdo_notif->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
    $stmt_notif->execute([$_SESSION['user_id']]);
    $user_notifications = $stmt_notif->fetchAll();
    
    $stmt_unread = $pdo_notif->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = FALSE");
    $stmt_unread->execute([$_SESSION['user_id']]);
    $user_unread_notifications = $stmt_unread->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard' ?> - StudyHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Montserrat:wght@500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css?v=<?= time() ?>">
    <script src="<?= BASE_URL ?>/assets/js/main.js?v=<?= time() ?>" defer></script>
    <script>
        // FOUC prevention: apply sidebar & theme state BEFORE first paint
        (function() {
            if (localStorage.getItem('theme') === 'dark') {
                document.documentElement.classList.add('dark-mode-pending');
            }
            if (window.innerWidth > 768 && localStorage.getItem('sidebar') === 'collapsed') {
                document.documentElement.classList.add('sidebar-collapsed-pending');
            }
        })();
    </script>
    <style>
        /* Prevent sidebar animation flash on page load */
        html.sidebar-collapsed-pending .sidebar { width: 80px !important; transition: none !important; }
        html.sidebar-collapsed-pending .main-content { margin-left: 80px !important; transition: none !important; }
        html.dark-mode-pending body { background-color: #0c1222 !important; }
    </style>
</head>

<body>
    <!-- Mobile Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeMobileSidebar()"></div>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-brand" onclick="if(document.getElementById('sidebar').classList.contains('collapsed')) toggleSidebar()" data-title="Buka Sidebar">
                <i class="fa-solid fa-graduation-cap"></i> <span class="menu-text">StudyHub</span>
            </div>
            <button class="sidebar-toggle-btn" onclick="toggleSidebar()" data-title="Tutup Sidebar">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
        </div>
        <div class="sidebar-menu">
            <div class="menu-group-title">Utama</div>
            <a href="<?= BASE_URL ?>/dashboard" class="menu-item <?= ($active == 'dashboard') ? 'active' : '' ?>"
                data-title="Beranda">
                <i class="fa-solid fa-house"></i> <span class="menu-text">Beranda</span>
            </a>

            <div class="menu-group-title">Akademik</div>
            <a href="<?= BASE_URL ?>/materials" class="menu-item <?= ($active == 'materials') ? 'active' : '' ?>"
                data-title="Materi Kuliah">
                <i class="fa-solid fa-book"></i> <span class="menu-text">Materi Kuliah</span>
            </a>
            <a href="<?= BASE_URL ?>/quiz" class="menu-item <?= ($active == 'quiz') ? 'active' : '' ?>" data-title="AI Quiz">
                <i class="fa-solid fa-brain"></i> <span class="menu-text">AI Quiz</span>
            </a>
            <a href="<?= BASE_URL ?>/assistant" class="menu-item <?= ($active == 'assistant') ? 'active' : '' ?>"
                data-title="AI Assistant">
                <i class="fa-solid fa-robot"></i> <span class="menu-text">AI Assistant</span>
            </a>
            <a href="<?= BASE_URL ?>/forum" class="menu-item <?= ($active == 'forum') ? 'active' : '' ?>"
                data-title="Forum Diskusi">
                <i class="fa-solid fa-users"></i> <span class="menu-text">Forum Diskusi</span>
            </a>
            <a href="<?= BASE_URL ?>/notes" class="menu-item <?= ($active == 'notes') ? 'active' : '' ?>"
                data-title="Catatan">
                <i class="fa-solid fa-pen-to-square"></i> <span class="menu-text">Catatan</span>
            </a>

            <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <div class="menu-group-title">Khusus Admin</div>
            <a href="<?= BASE_URL ?>/admin" class="menu-item" data-title="Admin Panel" style="background: rgba(99, 102, 241, 0.1); color: var(--primary); font-weight: 600;">
                <i class="fa-solid fa-lock"></i> <span class="menu-text">Admin Panel</span>
            </a>
            <?php endif; ?>

            <a href="<?= BASE_URL ?>/logout" class="menu-item" data-title="Keluar" style="margin-top: auto; color: var(--accent-rose);">
                <i class="fa-solid fa-right-from-bracket"></i> <span class="menu-text">Keluar</span>
            </a>
        </div>
        <div class="sidebar-footer">
            <?php
            if (!isset($_SESSION['user_photo']) && isset($_SESSION['user_id'])) {
                $pdo = Database::getInstance();
                $stmt = $pdo->prepare("SELECT photo, role FROM users WHERE id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $u = $stmt->fetch();
                $_SESSION['user_photo'] = (!empty($u['photo'])) ? $u['photo'] : 'default.png';
                $_SESSION['user_role'] = $u['role'] ?? 'Mahasiswa';
            }
            $photoName = !empty($_SESSION['user_photo']) ? $_SESSION['user_photo'] : 'default.png';
            $photoPath = BASE_URL . '/assets/' . ($photoName !== 'default.png' ? 'uploads/' . $photoName : 'img/default.png');
            $userRole = $_SESSION['user_role'] ?? 'Mahasiswa';
            ?>
            <a href="<?= BASE_URL ?>/profile" class="sidebar-user" style="text-decoration:none;">
                <img src="<?= $photoPath ?>" alt="Profile Picture">
                <div class="sidebar-user-info">
                    <span class="sidebar-user-name"><?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></span>
                    <span class="sidebar-user-role"><?= htmlspecialchars(ucfirst($userRole)) ?></span>
                </div>
                <i class="fa-solid fa-chevron-down" style="margin-left: auto; font-size: 0.75rem; opacity: 0.5;"></i>
            </a>
        </div>
    </div>
    <div class="main-content" id="mainContent">
        <script>
            // Apply real classes now that DOM elements exist, then remove pending flags
            const isDark = localStorage.getItem('theme') === 'dark';
            if (isDark) {
                document.body.classList.add('dark-mode');
            }
            if (window.innerWidth > 768 && localStorage.getItem('sidebar') === 'collapsed') {
                document.getElementById('sidebar').classList.add('collapsed');
                document.getElementById('mainContent').classList.add('expanded');
            }
            // Remove pending classes & re-enable transitions after first frame
            requestAnimationFrame(() => {
                document.documentElement.classList.remove('sidebar-collapsed-pending', 'dark-mode-pending');
            });
            // Sync theme toggle icon after DOM ready
            document.addEventListener('DOMContentLoaded', () => {
                const themeBtn = document.getElementById('themeToggleBtn');
                if (themeBtn && isDark) {
                    themeBtn.innerHTML = '<i class="fa-solid fa-sun"></i>';
                }
            });
        </script>
        <div class="topbar">
            <style>
                .topbar-search {
                    display: flex;
                    align-items: center;
                    background: var(--surface-hover);
                    border: 1px solid var(--border-color);
                    border-radius: var(--radius-full);
                    padding: 0.5rem 1rem;
                    width: 350px;
                    transition: all var(--transition-fast);
                }
                .topbar-search:focus-within {
                    border-color: var(--primary);
                    background: var(--surface);
                    box-shadow: 0 0 0 3px var(--primary-glow);
                }
                .topbar-search i {
                    color: var(--text-muted);
                    margin-right: 0.75rem;
                }
                .topbar-search input {
                    border: 0px solid transparent !important;
                    background-color: transparent !important;
                    background: transparent !important;
                    box-shadow: none !important;
                    outline: none !important;
                    width: 100%;
                    padding: 0;
                    font-size: 0.9rem;
                    color: var(--text-main);
                }
                
                .topbar-actions {
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                }

                .btn-icon {
                    background: transparent;
                    border: none;
                    color: var(--text-muted);
                    width: 36px;
                    height: 36px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    cursor: pointer;
                    transition: all var(--transition-fast);
                    font-size: 1.1rem;
                }
                .btn-icon:hover {
                    background: var(--bg-color);
                    color: var(--text-main);
                }

                @media (max-width: 768px) {
                    .topbar-search { display: none; }
                }
            </style>
            
            <div style="display:flex; align-items:center; gap:0.75rem; flex: 1;">
                <button onclick="toggleSidebar()" class="btn-icon mobile-menu-btn" data-title="Menu">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <form action="<?= BASE_URL ?>/search" method="GET" class="topbar-search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="q" placeholder="Cari materi, forum, catatan, quiz..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                </form>
            </div>
            
            <div class="topbar-actions">
                <button class="btn-icon" id="themeToggleBtn" onclick="toggleTheme()" data-title="Ganti Mode">
                    <i class="fa-solid fa-moon"></i>
                </button>
                <div style="position: relative;">
                    <button class="btn-icon" data-title="Notifikasi" onclick="toggleNotifications()">
                        <i class="fa-regular fa-bell"></i>
                        <?php if($user_unread_notifications > 0): ?>
                        <span id="notifBadge" style="position: absolute; top: 8px; right: 8px; width: 8px; height: 8px; background: #ef4444; border-radius: 50%; box-shadow: 0 0 0 2px var(--surface);"></span>
                        <?php endif; ?>
                    </button>
                    
                    <div id="notificationDropdown" style="display: none; position: absolute; top: 100%; right: 0; margin-top: 0.5rem; width: 320px; background: var(--surface); border: 1px solid var(--border-color); border-radius: var(--radius-lg); box-shadow: 0 10px 40px rgba(0,0,0,0.1); z-index: 1000; flex-direction: column; overflow: hidden; transform-origin: top right; animation: dropdownIn 0.2s ease forwards;">
                        <style>
                            @keyframes dropdownIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
                            .notif-item:hover { background: var(--bg-color); }
                        </style>
                        <div style="padding: 1rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                            <h4 style="margin: 0; font-size: 1rem; font-weight: 600;">Notifikasi</h4>
                            <?php if($user_unread_notifications > 0): ?>
                            <span onclick="markNotifRead()" style="font-size: 0.75rem; color: var(--primary); font-weight: 600; cursor: pointer;">Tandai sudah dibaca</span>
                            <?php endif; ?>
                        </div>
                        <div style="max-height: 350px; overflow-y: auto; padding: 0.5rem;">
                            <?php if(empty($user_notifications)): ?>
                                <div style="padding: 1rem; text-align: center; color: var(--text-muted); font-size: 0.85rem;">Tidak ada notifikasi</div>
                            <?php else: ?>
                                <?php foreach($user_notifications as $notif): ?>
                                <div class="notif-item" style="display: flex; gap: 1rem; padding: 0.75rem; border-radius: var(--radius-md); transition: background 0.2s; cursor: pointer; position: relative;">
                                    <div style="width: 40px; height: 40px; border-radius: 50%; background: rgba(239, 68, 68, 0.1); color: #ef4444; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <i class="fa-solid <?= $notif['type'] == 'announcement' ? 'fa-bullhorn' : 'fa-bell' ?>"></i>
                                    </div>
                                    <div>
                                        <h5 style="margin: 0 0 0.25rem 0; font-size: 0.9rem; font-weight: 600; <?= $notif['is_read'] ? 'color: var(--text-muted);' : '' ?>">
                                            <?= $notif['type'] == 'announcement' ? 'Pengumuman Admin' : 'Notifikasi' ?>
                                        </h5>
                                        <p style="margin: 0; font-size: 0.8rem; color: var(--text-muted); line-height: 1.4;"><?= htmlspecialchars($notif['message']) ?></p>
                                        <span style="font-size: 0.7rem; color: var(--text-muted); opacity: 0.7; margin-top: 0.25rem; display: block;"><?= date('d M Y H:i', strtotime($notif['created_at'])) ?></span>
                                    </div>
                                    <?php if(!$notif['is_read']): ?>
                                    <div class="unread-dot" style="position: absolute; right: 0.75rem; top: 1.25rem; width: 8px; height: 8px; background: #ef4444; border-radius: 50%;"></div>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div style="padding: 0.75rem; border-top: 1px solid var(--border-color); text-align: center;">
                            <span style="font-size: 0.85rem; color: var(--text-muted);">Menampilkan 10 notifikasi terbaru</span>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                function toggleNotifications() {
                    const dropdown = document.getElementById('notificationDropdown');
                    dropdown.style.display = dropdown.style.display === 'none' ? 'flex' : 'none';
                }
                function markNotifRead() {
                    fetch('<?= BASE_URL ?>/notifications/read', {
                        method: 'POST'
                    }).then(response => {
                        const badge = document.getElementById('notifBadge');
                        if(badge) badge.style.display = 'none';
                        document.querySelectorAll('.unread-dot').forEach(el => el.style.display = 'none');
                    });
                }
                document.addEventListener('click', function(event) {
                    const notifContainer = document.querySelector('button[data-title="Notifikasi"]').parentElement;
                    if (notifContainer && !notifContainer.contains(event.target)) {
                        const dropdown = document.getElementById('notificationDropdown');
                        if (dropdown) dropdown.style.display = 'none';
                    }
                });
            </script>
        </div>
        <div class="content-area">
