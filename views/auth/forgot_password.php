<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - StudyHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Montserrat:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }

        :root {
            /* Dark Mode (Default) */
            --bg-color-1: #0f172a;
            --bg-color-2: #1e1b4b;
            --bg-color-3: #0f172a;
            --text-main: #f1f5f9;
            --text-muted: #cbd5e1;
            --input-bg: rgba(255, 255, 255, 0.06);
            --input-border: rgba(255, 255, 255, 0.12);
            --card-bg: rgba(255, 255, 255, 0.08);
            --card-border: rgba(255, 255, 255, 0.12);
        }

        body.light-mode {
            --bg-color-1: #eef2ff;
            --bg-color-2: #e0e7ff;
            --bg-color-3: #eef2ff;
            --text-main: #1e293b;
            --text-muted: #475569;
            --input-bg: rgba(255, 255, 255, 0.7);
            --input-border: rgba(0, 0, 0, 0.1);
            --card-bg: rgba(255, 255, 255, 0.85);
            --card-border: rgba(0, 0, 0, 0.05);
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, var(--bg-color-1) 0%, var(--bg-color-2) 50%, var(--bg-color-3) 100%);
            position: relative;
            overflow: hidden;
            transition: background 0.3s;
        }

        /* Animated background orbs */
        body::before, body::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.4;
            animation: float 8s ease-in-out infinite;
        }
        body::before {
            width: 400px; height: 400px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            top: -10%; left: -5%;
        }
        body::after {
            width: 350px; height: 350px;
            background: linear-gradient(135deg, #14b8a6, #06b6d4);
            bottom: -15%; right: -5%;
            animation-delay: -4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) scale(1); }
            50% { transform: translateY(-30px) scale(1.05); }
        }

        .auth-container {
            width: 100%;
            max-width: 420px;
            padding: 2.5rem 2rem;
            margin: 1rem;
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--card-border);
            border-radius: 1.5rem;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            position: relative;
            z-index: 1;
            animation: scaleIn 0.5s ease forwards;
        }

        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.95) translateY(10px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        .logo {
            text-align: center;
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 2rem;
            display: block;
            text-decoration: none;
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(135deg, #818cf8, #a78bfa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .form-group { margin-bottom: 1.25rem; }
        .form-group label {
            display: block;
            margin-bottom: 0.4rem;
            font-weight: 500;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            background: var(--input-bg);
            border: 1.5px solid var(--input-border);
            border-radius: 0.75rem;
            outline: none;
            color: var(--text-main);
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control::placeholder { color: #64748b; }
        .form-control:focus {
            border-color: #818cf8;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
            background: rgba(255, 255, 255, 0.08);
        }

        .remember-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }
        .remember-row label { margin-bottom: 0; cursor: pointer; color: var(--text-muted); font-size: 0.9rem; }
        .remember-row input { accent-color: #6366f1; }

        .btn {
            width: 100%;
            padding: 0.8rem;
            border: none;
            border-radius: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.95rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            margin-bottom: 0.85rem;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.35);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.45);
        }

        .btn-google {
            background: rgba(255, 255, 255, 0.08);
            color: #e2e8f0;
            border: 1.5px solid rgba(255, 255, 255, 0.12);
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 0.8rem;
            border-radius: 0.75rem;
            transition: all 0.3s;
        }
        .btn-google:hover { background: rgba(255, 255, 255, 0.12); }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.25rem 0;
            color: #64748b;
            font-size: 0.85rem;
        }
        .divider::before, .divider::after { content: ''; flex: 1; border-bottom: 1px solid rgba(255, 255, 255, 0.08); }
        .divider:not(:empty)::before { margin-right: 0.75rem; }
        .divider:not(:empty)::after { margin-left: 0.75rem; }

        .auth-links {
            text-align: center;
            margin-top: 1.25rem;
            color: #94a3b8;
            font-size: 0.9rem;
        }
        .auth-links a {
            color: #818cf8;
            text-decoration: none;
            font-weight: 600;
        }
        .auth-links a:hover { text-decoration: underline; }
        .theme-toggle-floating {
            position: absolute;
            top: 2rem;
            right: 2rem;
            z-index: 100;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            color: var(--text-main);
            width: 45px; height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        .theme-toggle-floating:hover {
            transform: scale(1.1);
        }
        .back-floating {
            position: absolute;
            top: 2rem;
            left: 2rem;
            z-index: 100;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            color: var(--text-main);
            width: 45px; height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        .back-floating:hover {
            transform: scale(1.1);
            color: var(--primary);
        }
    </style>
    <script>
        if (localStorage.getItem('theme') === 'light') {
            document.documentElement.classList.add('light-mode-pending');
        }
    </script>
</head>
<body class="<?= isset($_COOKIE['theme']) && $_COOKIE['theme'] == 'light' ? 'light-mode' : '' ?>">
    <script>
        if (localStorage.getItem('theme') === 'light') document.body.classList.add('light-mode');
        document.documentElement.classList.remove('light-mode-pending');
    </script>
    
    <a href="<?= BASE_URL ?>/" class="back-floating" title="Kembali ke Beranda">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <button class="theme-toggle-floating" id="themeToggleBtn" onclick="toggleAuthTheme()" title="Ganti Tema">
        <i class="fa-solid fa-sun"></i>
    </button>

    <div class="auth-container">
        <a href="<?= BASE_URL ?>/" class="logo"><i class="fa-solid fa-graduation-cap"></i> StudyHub</a>
        
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <h2 style="color: var(--text-main); font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem;">Lupa Password?</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Masukkan email Anda dan kami akan mengirimkan link untuk mereset password Anda.</p>
        </div>

        <form action="<?= BASE_URL ?>/forgot-password" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" required placeholder="nama@email.com">
            </div>
            
            <button type="submit" class="btn btn-primary" style="margin-top: 0.5rem;">Kirim Link Reset Password</button>
        </form>

        <div class="auth-links" style="margin-top: 1.5rem;">
            Ingat password Anda? <a href="<?= BASE_URL ?>/login">Login di sini</a>
        </div>
    </div>

    <script>
        const themeBtn = document.getElementById('themeToggleBtn');
        if (document.body.classList.contains('light-mode') || localStorage.getItem('theme') === 'light') {
            if(themeBtn) themeBtn.innerHTML = '<i class="fa-solid fa-moon"></i>';
        }

        function toggleAuthTheme() {
            const isLight = document.body.classList.toggle('light-mode');
            localStorage.setItem('theme', isLight ? 'light' : 'dark');
            document.cookie = "theme=" + (isLight ? 'light' : 'dark') + "; path=/; max-age=31536000";
            if(themeBtn) themeBtn.innerHTML = isLight ? '<i class="fa-solid fa-moon"></i>' : '<i class="fa-solid fa-sun"></i>';
        }
    </script>
</body>
</html>
