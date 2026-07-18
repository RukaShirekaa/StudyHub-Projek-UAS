<?php

class AuthController {
    public function showLogin() {
        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function showRegister() {
        require_once __DIR__ . '/../views/auth/register.php';
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $pdo = Database::getInstance();
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                if (!$user['is_verified']) {
                    $loginError = 'Harap verifikasi email Anda terlebih dahulu.';
                    require_once __DIR__ . '/../views/auth/login.php';
                    return;
                }
                
                if (isset($_POST['remember'])) {
                    $token = bin2hex(random_bytes(32));
                    $stmt = $pdo->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                    $stmt->execute([$token, $user['id']]);
                    setcookie('remember_token', $token, time() + (86400 * 30), "/"); // 30 days
                }

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['user_name'] = $user['name'];

                // Update daily login streak
                require_once __DIR__ . '/../models/User.php';
                (new User())->updateStreak($user['id']);

                $dashUrl = BASE_URL . '/dashboard';
                header("Location: $dashUrl");
                exit;
            } else {
                $loginError = 'Email atau password salah.';
                require_once __DIR__ . '/../views/auth/login.php';
            }
        }
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if(strlen($password) < 6) {
                $regUrl = BASE_URL . '/register';
                echo "<script>alert('Password minimal 6 karakter.'); window.location.href='$regUrl';</script>";
                return;
            }
            
            $pdo = Database::getInstance();
            // Cek email exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $regUrl = BASE_URL . '/register';
                echo "<script>alert('Email sudah terdaftar.'); window.location.href='$regUrl';</script>";
                return;
            }
            
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $verificationCode = md5(uniqid("studyhub", true));
            
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, verification_code) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$name, $email, $hashedPassword, $verificationCode])) {
                $this->sendVerificationEmail($email, $name, $verificationCode);
                $loginUrl = BASE_URL . '/login';
                echo "<script>alert('Pendaftaran berhasil! Silakan cek email Anda untuk link verifikasi.'); window.location.href='$loginUrl';</script>";
            } else {
                $regUrl = BASE_URL . '/register';
                echo "<script>alert('Terjadi kesalahan saat mendaftar.'); window.location.href='$regUrl';</script>";
            }
        }
    }

    private function sendVerificationEmail($email, $name, $code) {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['SMTP_USER'];
            $mail->Password   = $_ENV['SMTP_PASS'];
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = $_ENV['SMTP_PORT'];
            
            $mail->setFrom($_ENV['SMTP_USER'], 'StudyHub');
            $mail->addAddress($email, $name);
            
            $mail->isHTML(true);
            $mail->Subject = 'Verifikasi Akun StudyHub Anda';
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            // Detect if we are in a subfolder like StudyHub
            $basePath = dirname($_SERVER['SCRIPT_NAME']);
            $basePath = str_replace('\\', '/', $basePath);
            if ($basePath === '/') $basePath = '';
            
            $verifyUrl = $protocol . '://' . $host . $basePath . '/verify?code=' . $code;
            
            $mail->Body = "Halo $name,<br><br>Terima kasih telah mendaftar di StudyHub. Silakan klik link di bawah ini untuk memverifikasi akun Anda:<br><br><a href='$verifyUrl'>$verifyUrl</a><br><br>Terima kasih,<br>Tim StudyHub";
            
            $mail->send();
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    }

    public function verifyEmail() {
        $code = $_GET['code'] ?? '';
        if ($code) {
            $pdo = Database::getInstance();
            $stmt = $pdo->prepare("UPDATE users SET is_verified = TRUE, verification_code = NULL WHERE verification_code = ?");
            $stmt->execute([$code]);
            $loginUrl = BASE_URL . '/login';
            if ($stmt->rowCount() > 0) {
                echo "<script>alert('Verifikasi berhasil! Silakan login.'); window.location.href='$loginUrl';</script>";
            } else {
                echo "<script>alert('Kode verifikasi tidak valid atau sudah digunakan.'); window.location.href='$loginUrl';</script>";
            }
        }
    }

    public function logout() {
        session_start();
        
        if (isset($_COOKIE['remember_token'])) {
            $pdo = Database::getInstance();
            $stmt = $pdo->prepare("UPDATE users SET remember_token = NULL WHERE remember_token = ?");
            $stmt->execute([$_COOKIE['remember_token']]);
            setcookie('remember_token', '', time() - 3600, "/");
        }

        session_destroy();
        $redirectUrl = BASE_URL . '/';
        header("Location: $redirectUrl");
        exit;
    }

    public function showForgotPassword() {
        require_once __DIR__ . '/../views/auth/forgot_password.php';
    }

    public function forgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $pdo = Database::getInstance();
            
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user) {
                $token = bin2hex(random_bytes(32));
                // Set expiry for 1 hour
                $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = ?");
                $stmt->execute([$token, $email]);
                
                $this->sendResetPasswordEmail($email, $user['name'], $token);
            }
            
            // Always show success message for security to prevent email enumeration
            $forgotUrl = BASE_URL . '/forgot-password';
            echo "<script>alert('Jika email tersebut terdaftar, kami telah mengirimkan link reset password.'); window.location.href='$forgotUrl';</script>";
        }
    }

    private function sendResetPasswordEmail($email, $name, $token) {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['SMTP_USER'];
            $mail->Password   = $_ENV['SMTP_PASS'];
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = $_ENV['SMTP_PORT'];
            
            $mail->setFrom($_ENV['SMTP_USER'], 'StudyHub');
            $mail->addAddress($email, $name);
            
            $mail->isHTML(true);
            $mail->Subject = 'Reset Password StudyHub';
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            
            $basePath = dirname($_SERVER['SCRIPT_NAME']);
            $basePath = str_replace('\\', '/', $basePath);
            if ($basePath === '/') $basePath = '';
            
            $resetUrl = $protocol . '://' . $host . $basePath . '/reset-password?token=' . $token;
            
            $mail->Body = "Halo $name,<br><br>Anda telah meminta untuk mereset password akun StudyHub Anda. Silakan klik link di bawah ini untuk membuat password baru:<br><br><a href='$resetUrl'>$resetUrl</a><br><br>Jika Anda tidak meminta reset password, abaikan email ini.<br><br>Terima kasih,<br>Tim StudyHub";
            
            $mail->send();
        } catch (Exception $e) {
            error_log("Reset password mail could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    }

    public function showResetPassword() {
        $token = $_GET['token'] ?? '';
        if (!$token) {
            $loginUrl = BASE_URL . '/login';
            echo "<script>alert('Token tidak valid.'); window.location.href='$loginUrl';</script>";
            return;
        }
        
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expires > NOW()");
        $stmt->execute([$token]);
        if (!$stmt->fetch()) {
            $loginUrl = BASE_URL . '/login';
            echo "<script>alert('Token tidak valid atau sudah kadaluarsa.'); window.location.href='$loginUrl';</script>";
            return;
        }
        
        require_once __DIR__ . '/../views/auth/reset_password.php';
    }

    public function resetPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['token'] ?? '';
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';
            
            if (!$token || strlen($password) < 6 || $password !== $password_confirm) {
                echo "<script>alert('Data tidak valid. Password minimal 6 karakter dan harus cocok.'); window.history.back();</script>";
                return;
            }
            
            $pdo = Database::getInstance();
            $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expires > NOW()");
            $stmt->execute([$token]);
            $user = $stmt->fetch();
            
            if ($user) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
                $stmt->execute([$hashedPassword, $user['id']]);
                
                $loginUrl = BASE_URL . '/login';
                echo "<script>alert('Password berhasil direset! Silakan login dengan password baru.'); window.location.href='$loginUrl';</script>";
            } else {
                $loginUrl = BASE_URL . '/login';
                echo "<script>alert('Token tidak valid atau sudah kadaluarsa.'); window.location.href='$loginUrl';</script>";
            }
        }
    }

    private function getGoogleProvider() {
        return new \League\OAuth2\Client\Provider\Google([
            'clientId'     => $_ENV['GOOGLE_CLIENT_ID'],
            'clientSecret' => $_ENV['GOOGLE_CLIENT_SECRET'],
            'redirectUri'  => $_ENV['GOOGLE_REDIRECT_URI'],
        ]);
    }

    public function googleLogin() {
        $provider = $this->getGoogleProvider();
        $authUrl = $provider->getAuthorizationUrl();
        $_SESSION['oauth2state'] = $provider->getState();
        header('Location: ' . $authUrl);
        exit;
    }

    public function googleCallback() {
        $provider = $this->getGoogleProvider();
        
        if (empty($_GET['state']) || (isset($_SESSION['oauth2state']) && $_GET['state'] !== $_SESSION['oauth2state'])) {
            if (isset($_SESSION['oauth2state'])) {
                unset($_SESSION['oauth2state']);
            }
            $loginUrl = BASE_URL . '/login';
            echo "<script>alert('Invalid State'); window.location.href='$loginUrl';</script>";
            exit;
        }

        try {
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $_GET['code']
            ]);

            $ownerDetails = $provider->getResourceOwner($token);
            $googleId = $ownerDetails->getId();
            $email = $ownerDetails->getEmail();
            $name = $ownerDetails->getName();
            $avatar = $ownerDetails->getAvatar();

            $pdo = Database::getInstance();
            
            // Cek jika user sudah ada berdasarkan google_id
            $stmt = $pdo->prepare("SELECT * FROM users WHERE google_id = ?");
            $stmt->execute([$googleId]);
            $user = $stmt->fetch();

            if (!$user) {
                // Cek jika user ada berdasarkan email
                $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();
                
                if ($user) {
                    // Update user dengan google_id
                    $stmt = $pdo->prepare("UPDATE users SET google_id = ?, is_verified = TRUE WHERE id = ?");
                    $stmt->execute([$googleId, $user['id']]);
                } else {
                    // Buat user baru
                    $stmt = $pdo->prepare("INSERT INTO users (google_id, name, email, is_verified, photo) VALUES (?, ?, ?, TRUE, ?)");
                    $stmt->execute([$googleId, $name, $email, $avatar]);
                    $userId = $pdo->lastInsertId();
                    
                    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                    $stmt->execute([$userId]);
                    $user = $stmt->fetch();
                }
            }

            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['user_name'] = $user['name'];

            // Update daily login streak
            require_once __DIR__ . '/../models/User.php';
            (new User())->updateStreak($user['id']);

            $dashUrl = BASE_URL . '/dashboard';
            header("Location: $dashUrl");
            exit;
            
        } catch (Exception $e) {
            $loginUrl = BASE_URL . '/login';
            echo "<script>alert('Gagal mengambil access token dari Google'); window.location.href='$loginUrl';</script>";
            exit;
        }
    }
}
