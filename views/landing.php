<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyHub - Platform Belajar Cerdas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Montserrat:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --primary-gradient: linear-gradient(135deg, #6366f1, #8b5cf6);
            --secondary: #14b8a6;
            
            /* Dark Mode (Default for Landing) */
            --bg-color: #0f172a;
            --text-main: #e2e8f0;
            --text-muted: #94a3b8;
            --nav-bg: rgba(15, 23, 42, 0.8);
            --card-bg: rgba(255, 255, 255, 0.04);
            --card-border: rgba(255, 255, 255, 0.06);
            --badge-bg: rgba(30, 41, 59, 0.9);
        }

        body.light-mode {
            --bg-color: #f8fafc;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --nav-bg: rgba(255, 255, 255, 0.85);
            --card-bg: rgba(255, 255, 255, 0.95);
            --card-border: rgba(0, 0, 0, 0.08);
            --badge-bg: rgba(255, 255, 255, 0.95);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: var(--bg-color); color: var(--text-main); overflow-x: hidden; transition: background 0.3s, color 0.3s; }
        h1, h2, h3, .logo { font-family: 'Montserrat', sans-serif; }

        /* Navbar */
        .glass-nav {
            background: var(--nav-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            position: fixed;
            top: 0; width: 100%; z-index: 1000;
            padding: 0.85rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }

        .logo {
            font-size: 1.4rem;
            font-weight: 700;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }

        .nav-links a.nav-link {
            text-decoration: none;
            color: var(--text-muted);
            font-weight: 500;
            transition: color 0.3s;
            font-size: 0.95rem;
        }
        .nav-links a.nav-link:hover { color: var(--primary); }

        .btn {
            padding: 0.65rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            cursor: pointer;
            border: none;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-outline {
            background: transparent;
            color: #818cf8;
            border: 2px solid rgba(129, 140, 248, 0.4);
        }
        .btn-outline:hover { background: rgba(99, 102, 241, 0.1); border-color: #818cf8; color: white; }

        .btn-primary {
            background: var(--primary-gradient);
            color: white;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.35);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(99, 102, 241, 0.45);
        }

        /* Hero */
        .hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8rem 5% 5rem 5%;
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }

        /* Decorative bg */
        .hero::before {
            content: '';
            position: absolute;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.15), transparent 70%);
            top: -10%; left: -5%;
            border-radius: 50%;
            animation: float 10s ease-in-out infinite;
        }
        .hero::after {
            content: '';
            position: absolute;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(20, 184, 166, 0.1), transparent 70%);
            bottom: -20%; right: -10%;
            border-radius: 50%;
            animation: float 8s ease-in-out infinite reverse;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-30px); }
        }

        .hero-text { flex: 1; padding-right: 3rem; position: relative; z-index: 1; }
        .hero-text h1 {
            font-size: 3.25rem;
            font-weight: 800;
            line-height: 1.15;
            margin-bottom: 1.5rem;
            color: var(--text-main);
        }
        .hero-text h1 span {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero-text p {
            font-size: 1.1rem;
            color: var(--text-muted);
            margin-bottom: 2.5rem;
            line-height: 1.7;
            max-width: 520px;
        }
        .hero-actions { display: flex; gap: 1rem; flex-wrap: wrap; }

        .hero-image { flex: 1; display: flex; justify-content: center; position: relative; z-index: 1; }

        .hero-visual {
            width: 420px; height: 340px;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.15), rgba(20, 184, 166, 0.1));
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            /* overflow: hidden; removed so badges are not clipped */
        }

        .hero-visual i {
            font-size: 5rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: float 4s ease-in-out infinite;
        }

        /* Floating badges */
        .float-badge {
            position: absolute;
            background: var(--badge-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--card-border);
            border-radius: 1rem;
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-main);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            z-index: 2;
        }

        .float-badge.top-right {
            top: -15px; right: -25px;
            animation: float 5s ease-in-out infinite;
        }
        .float-badge.bottom-left {
            bottom: -15px; left: -25px;
            animation: float 6s ease-in-out infinite reverse;
        }

        /* Demo Section */
        .demo-section {
            padding: 6rem 5%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 4rem;
        }
        #demo { background: rgba(99, 102, 241, 0.08); }
        #demo-amba { background: var(--bg-color); }
        .demo-text {
            flex: 1;
        }
        .demo-text h2 {
            font-size: 2.25rem;
            line-height: 1.3;
            margin-bottom: 1rem;
            color: var(--text-main);
        }
        .demo-text p {
            color: var(--text-muted);
            font-size: 1.1rem;
            line-height: 1.7;
            margin-bottom: 2rem;
        }
        .demo-visual {
            flex: 1;
            display: flex;
            justify-content: center;
            max-width: 500px;
        }

        @media (max-width: 768px) {
            .demo-section {
                flex-direction: column;
                text-align: center;
                gap: 2rem;
            }
            .demo-text h2 {
                font-size: 1.85rem;
            }
            .demo-visual {
                max-width: 100%;
                height: 300px;
            }
        }

        /* AI Quiz Simulator */
        .simulator-window {
            width: 100%; 
            height: 350px;
            background: var(--surface);
            border-radius: 1.5rem;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            position: relative;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        body.dark-mode .simulator-window {
            background: #1e293b;
        }
        .simulator-header {
            background: rgba(0,0,0,0.05);
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            border-bottom: 1px solid var(--card-border);
        }
        .sim-dot { width: 10px; height: 10px; border-radius: 50%; }
        .sim-dot.red { background: #ff5f56; }
        .sim-dot.yellow { background: #ffbd2e; }
        .sim-dot.green { background: #27c93f; }
        
        .simulator-body {
            flex: 1;
            position: relative;
            background: var(--bg-color);
        }
        
        /* Step 1 */
        .sim-step-1 {
            position: absolute; inset: 0; padding: 1.5rem;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            animation: simStep1 10s infinite;
        }
        .sim-file {
            width: 60px; height: 75px; background: rgba(99, 102, 241, 0.1); border-radius: 0.5rem;
            border: 2px dashed var(--primary);
            display: flex; align-items: center; justify-content: center;
            color: var(--primary); font-size: 1.8rem; margin-bottom: 0.75rem;
        }
        .sim-btn {
            background: var(--primary); color: white; padding: 0.6rem 1.25rem; border-radius: 0.5rem; font-size: 0.85rem; font-weight: bold;
            animation: simBtnClick 10s infinite;
            display: flex; align-items: center; gap: 0.5rem;
        }
        
        /* Step 2 */
        .sim-step-2 {
            position: absolute; inset: 0; padding: 1.5rem;
            display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 1rem;
            opacity: 0; pointer-events: none;
            animation: simStep2 10s infinite;
        }
        .sim-spinner {
            width: 35px; height: 35px; border: 3px solid rgba(99, 102, 241, 0.2);
            border-top-color: var(--primary); border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        /* Step 3 */
        .sim-step-3 {
            position: absolute; inset: 0; padding: 1.5rem;
            opacity: 0; pointer-events: none;
            animation: simStep3 10s infinite;
            display: flex; flex-direction: column; justify-content: center;
        }
        .sim-q { font-size: 0.95rem; font-weight: 700; margin-bottom: 1rem; color: var(--text-main); line-height: 1.4; }
        .sim-opt { 
            padding: 0.6rem 0.8rem; border: 1px solid var(--card-border); border-radius: 0.5rem; margin-bottom: 0.5rem; font-size: 0.8rem; color: var(--text-muted);
            display: flex; align-items: center; gap: 0.5rem;
        }
        .sim-opt.correct {
            animation: simOptCorrect 10s infinite;
        }
        .sim-opt.correct .fa-circle { animation: simIconHide 10s infinite; }
        .sim-opt.correct .fa-circle-check { display: none; animation: simIconShow 10s infinite; }
        
        /* Cursor */
        .sim-cursor {
            position: absolute; width: 20px; height: 20px; color: var(--text-main);
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
            z-index: 10;
            top: 80%; left: 80%;
            animation: simCursorMove 10s infinite;
            font-size: 1.25rem;
        }
        
        @keyframes spin { to { transform: rotate(360deg); } }
        
        @keyframes simStep1 {
            0%, 20% { opacity: 1; transform: scale(1); }
            25%, 100% { opacity: 0; transform: scale(0.95); }
        }
        @keyframes simBtnClick {
            0%, 15% { transform: scale(1); background: var(--primary); }
            18% { transform: scale(0.95); background: var(--primary-hover); }
            20%, 100% { transform: scale(1); background: var(--primary); }
        }
        
        @keyframes simStep2 {
            0%, 24% { opacity: 0; transform: scale(0.95); }
            25%, 50% { opacity: 1; transform: scale(1); }
            55%, 100% { opacity: 0; transform: scale(1.05); }
        }
        
        @keyframes simStep3 {
            0%, 54% { opacity: 0; transform: translateY(10px); }
            55%, 95% { opacity: 1; transform: translateY(0); }
            100% { opacity: 0; transform: translateY(-10px); }
        }
        
        @keyframes simOptCorrect {
            0%, 75% { background: transparent; border-color: var(--card-border); color: var(--text-muted); }
            76%, 100% { background: rgba(39, 201, 63, 0.1); border-color: #27c93f; color: #27c93f; font-weight: bold; }
        }
        @keyframes simIconHide {
            0%, 75% { display: block; }
            76%, 100% { display: none; }
        }
        @keyframes simIconShow {
            0%, 75% { display: none; }
            76%, 100% { display: block; color: #27c93f; }
        }
        
        @keyframes simCursorMove {
            0% { top: 80%; left: 80%; opacity: 0; }
            5% { opacity: 1; }
            15% { top: 68%; left: 48%; }
            18% { top: 68%; left: 48%; transform: scale(0.8); }
            22% { top: 80%; left: 60%; transform: scale(1); }
            25%, 54% { opacity: 0; top: 80%; left: 60%; }
            55% { opacity: 1; top: 80%; left: 80%; }
            70% { top: 55%; left: 15%; }
            75% { top: 55%; left: 15%; transform: scale(0.8); }
            80%, 95% { top: 65%; left: 25%; transform: scale(1); opacity: 1; }
            100% { opacity: 0; }
        }
        
        /* Amba AI Simulator */
        @media (min-width: 769px) {
            .demo-section-alt {
                flex-direction: row-reverse;
            }
        }
        .sim-msg-left {
            align-self: flex-start;
            max-width: 80%;
        }
        .sim-msg-right {
            align-self: flex-end;
            max-width: 80%;
            animation: simChatRight 10s infinite;
            opacity: 0;
            animation-fill-mode: forwards;
        }
        .sim-msg-left.sim-typing {
            max-width: 85%;
            animation: simChatLeft 10s infinite;
            opacity: 0;
            animation-fill-mode: forwards;
        }
        .sim-bubble-left {
            background: rgba(99, 102, 241, 0.1);
            color: var(--text-main);
            padding: 0.75rem 1rem;
            border-radius: 1rem 1rem 1rem 0;
            font-size: 0.85rem;
            border: 1px solid var(--card-border);
            position: relative;
            min-height: 20px;
        }
        .sim-bubble-right {
            background: var(--primary);
            color: white;
            padding: 0.75rem 1rem;
            border-radius: 1rem 1rem 0 1rem;
            font-size: 0.85rem;
        }
        .sim-dots {
            display: flex; gap: 4px; align-items: center; height: 16px;
            animation: simChatDots 10s infinite;
        }
        .sim-dots span { width: 6px; height: 6px; background: var(--primary); border-radius: 50%; animation: simDotBounce 1s infinite alternate; }
        .sim-dots span:nth-child(2) { animation-delay: 0.2s; }
        .sim-dots span:nth-child(3) { animation-delay: 0.4s; }
        
        .sim-answer {
            animation: simChatAnswer 10s infinite;
            line-height: 1.5;
        }

        @keyframes simChatRight {
            0%, 15% { opacity: 0; transform: translateY(10px); }
            20%, 100% { opacity: 1; transform: translateY(0); }
        }
        @keyframes simChatLeft {
            0%, 30% { opacity: 0; transform: translateY(10px); }
            35%, 100% { opacity: 1; transform: translateY(0); }
        }
        @keyframes simChatDots {
            0%, 34% { opacity: 0; position: absolute; visibility: hidden; }
            35%, 55% { opacity: 1; position: relative; visibility: visible; }
            56%, 100% { opacity: 0; position: absolute; visibility: hidden; }
        }
        @keyframes simChatAnswer {
            0%, 55% { opacity: 0; position: absolute; visibility: hidden; }
            56%, 100% { opacity: 1; position: relative; visibility: visible; }
        }
        @keyframes simDotBounce {
            from { transform: translateY(0); }
            to { transform: translateY(-4px); }
        }


        .features {
            padding: 6rem 5%;
            position: relative;
            background: rgba(16, 185, 129, 0.08);
        }

        .features-header {
            text-align: center;
            margin-bottom: 3.5rem;
        }
        .features-header h2 {
            font-size: 2.25rem;
            margin-bottom: 0.75rem;
            color: var(--text-main);
        }
        .features-header p {
            color: var(--text-muted);
            max-width: 550px;
            margin: 0 auto;
            font-size: 1.05rem;
            line-height: 1.6;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 300px), 1fr));
            gap: 1.5rem;
        }

        .feature-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 1.25rem;
            padding: 2rem 1.75rem;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        }
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: var(--primary-gradient);
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }
        .feature-card:hover {
            transform: translateY(-6px);
            border-color: rgba(99, 102, 241, 0.3);
            box-shadow: 0 15px 40px rgba(99, 102, 241, 0.1);
        }
        .feature-card:hover::before { transform: scaleX(1); }

        .feature-icon {
            width: 56px; height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            border-radius: 1rem;
            margin-bottom: 1.25rem;
        }

        .feature-card h3 { margin-bottom: 0.75rem; font-size: 1.15rem; color: #f1f5f9; }
        .feature-card h3 { margin-bottom: 0.75rem; font-size: 1.15rem; color: var(--text-main); }
        .feature-card p { color: var(--text-muted); line-height: 1.6; font-size: 0.95rem; }

        /* Footer */
        .landing-footer {
            text-align: center;
            padding: 2rem;
            color: var(--text-muted);
            border-top: 1px solid var(--card-border);
            margin-top: 2rem;
        }

        @media (max-width: 768px) {
            .hero { flex-direction: column; text-align: center; padding-top: 8rem; }
            .hero-text { padding-right: 0; margin-bottom: 3rem; }
            .hero-actions { justify-content: center; }
            .hero-visual { width: 320px; max-width: 100%; height: 250px; margin: 0 auto; flex-shrink: 0; }
            .hero-visual i { font-size: 4rem; }
            .float-badge { font-size: 0.75rem; padding: 0.5rem 0.75rem; }
            .float-badge.top-right { top: -10px; right: -10px; }
            .float-badge.bottom-left { bottom: -10px; left: -10px; }
            .nav-links { gap: 0.5rem; }
            .nav-links .nav-link, .nav-links .btn-primary { display: none; }
            .nav-links .btn-outline { padding: 0.4rem 0.8rem; font-size: 0.85rem; }
            .hero-text h1 { font-size: 2.5rem; }
            .hero-text p { margin: 0 auto 2rem; }
            .hero-actions { justify-content: center; }
            .hero-image { margin-top: 2rem; }
            .nav-links a:not(.btn) { display: none; }

            .features-header h2 { font-size: 1.75rem; }
            .features-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 480px) {
            .glass-nav { padding: 0.75rem 1rem; }
            .hero-text h1 { font-size: 1.85rem; }
            .hero { padding: 6rem 1rem 2rem; }
            .features { padding: 4rem 1rem; }
        }
    </style>
    <script>
        // Apply theme immediately to prevent FOUC
        if (localStorage.getItem('theme') === 'light') {
            document.documentElement.classList.add('light-mode-pending');
        }
    </script>
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>/assets/img/favicon.png">
</head>
<body class="<?= isset($_COOKIE['theme']) && $_COOKIE['theme'] == 'light' ? 'light-mode' : '' ?>">
    <script>
        if (localStorage.getItem('theme') === 'light') document.body.classList.add('light-mode');
        document.documentElement.classList.remove('light-mode-pending');
    </script>
    <nav class="glass-nav">
        <a href="<?= BASE_URL ?>/" class="logo"><i class="fa-solid fa-graduation-cap"></i> StudyHub</a>
        <div class="nav-links">
            <a href="#features" class="nav-link">Fitur</a>
            <button onclick="toggleLandingTheme()" id="themeToggleBtn" class="btn btn-outline" style="padding: 0.5rem 0.85rem;" title="Ganti Tema">
                <i class="fa-solid fa-sun"></i>
            </button>
            <a href="<?= BASE_URL ?>/login" class="btn btn-outline">Masuk</a>
            <a href="<?= BASE_URL ?>/register" class="btn btn-primary">Daftar</a>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-text">
            <h1>Platform Belajar Cerdas dengan <span>Bantuan AI</span></h1>
            <p>Tingkatkan produktivitas belajarmu. Unggah materi PDF, buat quiz otomatis, diskusi di forum, dan tanyakan langsung pada AI Assistant.</p>
            <div class="hero-actions">
                <a href="<?= BASE_URL ?>/register" class="btn btn-primary" style="padding: 0.85rem 2rem; font-size: 1rem;">
                    <i class="fa-solid fa-rocket"></i> Mulai Belajar Sekarang
                </a>
                <a href="#features" class="btn btn-outline" style="padding: 0.85rem 2rem; font-size: 1rem;">
                    <i class="fa-solid fa-sparkles"></i> Lihat Fitur
                </a>
            </div>
        </div>
        <div class="hero-image">
            <div class="hero-visual">
                <i class="fa-solid fa-laptop-code"></i>
                <div class="float-badge top-right">
                    <i class="fa-solid fa-brain" style="color: #a78bfa;"></i> AI-Powered
                </div>
                <div class="float-badge bottom-left">
                    <i class="fa-solid fa-bolt" style="color: #fbbf24;"></i> Instant Quiz
                </div>
            </div>
        </div>
    </section>

    <section class="demo-section" id="demo">
        <div class="demo-text">
            <h2>Ubah PDF Jadi Quiz dalam Hitungan Detik</h2>
            <p>Tidak perlu repot membuat soal sendiri. Cukup unggah materi kuliahmu dalam format PDF, dan AI StudyHub akan otomatis membacanya lalu membuatkan quiz interaktif untuk menguji pemahamanmu.</p>
            <a href="<?= BASE_URL ?>/register" class="btn btn-primary"><i class="fa-solid fa-wand-magic-sparkles"></i> Coba Gratis Sekarang</a>
        </div>
        <div class="demo-visual">
            <div class="simulator-window">
                <div class="simulator-header">
                    <div class="sim-dot red"></div>
                    <div class="sim-dot yellow"></div>
                    <div class="sim-dot green"></div>
                    <div style="margin-left: 0.5rem; font-size: 0.7rem; color: var(--text-muted); opacity: 0.7;">StudyHub AI Quiz</div>
                </div>
                <div class="simulator-body">
                    <!-- Step 1: Upload PDF -->
                    <div class="sim-step-1">
                        <div class="sim-file"><i class="fa-solid fa-file-pdf"></i></div>
                        <div style="font-size: 0.85rem; font-weight: bold; margin-bottom: 1.25rem; color: var(--text-main);">Materi_Kuliah.pdf</div>
                        <div class="sim-btn"><i class="fa-solid fa-wand-magic-sparkles"></i> Generate AI Quiz</div>
                    </div>
                    
                    <!-- Step 2: Loading -->
                    <div class="sim-step-2">
                        <div class="sim-spinner"></div>
                        <div style="font-size: 0.85rem; color: var(--text-muted);">AI sedang membaca PDF...</div>
                    </div>
                    
                    <!-- Step 3: Quiz -->
                    <div class="sim-step-3">
                        <div class="sim-q">Apa fungsi utama AI StudyHub?</div>
                        <div class="sim-opt"><i class="fa-regular fa-circle"></i> Hanya membaca buku</div>
                        <div class="sim-opt correct"><i class="fa-regular fa-circle-check"></i><i class="fa-regular fa-circle"></i> Membuat quiz otomatis</div>
                        <div class="sim-opt"><i class="fa-regular fa-circle"></i> Menonton video</div>
                    </div>
                    
                    <!-- Cursor -->
                    <i class="fa-solid fa-arrow-pointer sim-cursor"></i>
                </div>
            </div>
        </div>
    </section>

    <section class="demo-section demo-section-alt" id="demo-amba">
        <div class="demo-text">
            <h2>Diskusi 24/7 dengan Asisten AI Pribadi</h2>
            <p>Amba AI selalu siap membantumu. Dari menjelaskan materi yang sulit, meringkas dokumen, hingga memutarkan video pembelajaran di YouTube. Cukup ketik apa yang kamu butuhkan!</p>
            <a href="<?= BASE_URL ?>/register" class="btn btn-outline" style="color: var(--primary); border-color: var(--primary);"><i class="fa-solid fa-robot"></i> Tanya Amba AI Sekarang</a>
        </div>
        <div class="demo-visual">
            <div class="simulator-window" style="height: 380px;">
                <div class="simulator-header">
                    <div class="sim-dot red"></div>
                    <div class="sim-dot yellow"></div>
                    <div class="sim-dot green"></div>
                    <div style="margin-left: 0.5rem; font-size: 0.7rem; color: var(--text-muted); opacity: 0.7;">Amba AI Chat</div>
                </div>
                <div class="simulator-body" style="padding: 1.25rem; display: flex; flex-direction: column; gap: 1rem; background: var(--bg-color);">
                    <!-- Chat 1 -->
                    <div class="sim-msg-left">
                        <div style="font-size: 0.7rem; color: var(--text-muted); margin-bottom: 4px; font-weight: 600;">Amba AI</div>
                        <div class="sim-bubble-left">Halo! Ada yang bisa saya bantu terkait materi kuliahmu?</div>
                    </div>
                    <!-- Chat 2 (User) -->
                    <div class="sim-msg-right">
                        <div class="sim-bubble-right">Tolong jelaskan tentang OOP dong, pakai bahasa yang gampang dimengerti!</div>
                    </div>
                    <!-- Chat 3 (AI Typing -> Answer) -->
                    <div class="sim-msg-left sim-typing">
                        <div style="font-size: 0.7rem; color: var(--text-muted); margin-bottom: 4px; font-weight: 600;">Amba AI</div>
                        <div class="sim-bubble-left">
                            <div class="sim-dots"><span></span><span></span><span></span></div>
                            <div class="sim-answer">
                                Tentu! Bayangkan <strong>OOP</strong> (Object-Oriented Programming) seperti pabrik mobil. <br><br>
                                🚗 <strong>Class</strong> adalah blueprint/rancangannya.<br>
                                🚙 <strong>Object</strong> adalah mobil fisiknya yang sudah jadi.<br><br>
                                Semoga ini membantu belajarmu!
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="features" id="features">
        <div class="features-header">
            <h2>Semua yang Kamu Butuhkan untuk Kuliah</h2>
            <p>Platform all-in-one yang dirancang khusus untuk memaksimalkan potensi belajarmu.</p>
        </div>
        
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(59, 130, 246, 0.12); color: #60a5fa;">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <h3>Ruang Belajar Privat</h3>
                <p>Materi kuliah dan catatanmu tersimpan secara privat. Hanya kamu yang bisa melihat dan mengaksesnya.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(99, 102, 241, 0.12); color: #818cf8;">
                    <i class="fa-solid fa-robot"></i>
                </div>
                <h3>AI Study Assistant</h3>
                <p>Diskusi langsung dengan AI yang sudah membaca materi kuliahmu. Tanyakan apapun yang belum kamu pahami.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(168, 85, 247, 0.12); color: #c084fc;">
                    <i class="fa-solid fa-gamepad"></i>
                </div>
                <h3>Quiz Interaktif</h3>
                <p>Ubah PDF materi kuliah menjadi quiz interaktif secara otomatis. Efektif untuk menguji pemahamanmu.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(20, 184, 166, 0.12); color: #5eead4;">
                    <i class="fa-solid fa-users"></i>
                </div>
                <h3>Forum Diskusi</h3>
                <p>Bertanya dan berdiskusi dengan sesama mahasiswa lintas program studi. Solusi tepat saat kamu mentok.</p>
            </div>
        </div>
    </section>

    <footer class="landing-footer">
        <p>© <?= date('Y') ?> StudyHub. Dibuat untuk mahasiswa, oleh mahasiswa.</p>
    </footer>

    <script>
        // Set initial icon
        const themeBtn = document.getElementById('themeToggleBtn');
        if (document.body.classList.contains('light-mode') || localStorage.getItem('theme') === 'light') {
            if(themeBtn) themeBtn.innerHTML = '<i class="fa-solid fa-moon"></i>';
        }

        function toggleLandingTheme() {
            const isLight = document.body.classList.toggle('light-mode');
            localStorage.setItem('theme', isLight ? 'light' : 'dark');
            document.cookie = "theme=" + (isLight ? 'light' : 'dark') + "; path=/; max-age=31536000";
            if(themeBtn) themeBtn.innerHTML = isLight ? '<i class="fa-solid fa-moon"></i>' : '<i class="fa-solid fa-sun"></i>';
        }
    </script>
</body>
</html>
