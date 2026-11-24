<?php
session_start();
require_once('../config/db.php');
require_once('../config/helpers.php');

$error_message = "";

if (isset($_POST["register"])) {
    $username = sanitize($_POST["username"]);
    $phone    = sanitize($_POST["phone"]);
    $password = $_POST["password"];
    $email    = sanitize($_POST["email"]);

    // Проверяем, существует ли уже такой номер телефона в базе
    $check_phone = "SELECT id FROM users WHERE phone = '$phone'";
    $check_res = mysqli_query($conn, $check_phone);

    if (mysqli_num_rows($check_res) > 0) {
        $error_message = "Этот номер телефона уже зарегистрирован!";
    } else {
        // Хэшируем пароль
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Запрос на добавление пользователя
        $query = "INSERT INTO users (`username`, phone, `password`, email)
                  VALUES ('$username', '$phone', '$hashed_password', '$email')";
                  
        $res = mysqli_query($conn, $query);

        if ($res) {
            $_SESSION["user_id"]  = mysqli_insert_id($conn); 
            $_SESSION["username"] = $username;
            $_SESSION["role"]     = "user";
            $_SESSION["email"]    = $email;
            ?>
            <script>
                alert("Вы успешно зарегистрированы!");
                window.location.href = "../selectposters.php";
            </script>
            <?php
            exit();
        } else {
            $error_message = "Ошибка при регистрации!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-i18n="page_title">Регистрация — ASO.tj</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --primary-color: #0f172a;
            --accent-color: #2563eb;
            --bg-light: #f8fafc;
            --card-border: #e2e8f0;
        }

        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            background-color: var(--bg-light);
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .auth-card {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 20px;
            max-width: 450px;
            width: 100%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        }

        .logo-brand {
            font-family: 'Arial Black', sans-serif;
            font-size: 26px;
            font-weight: 900;
            color: var(--primary-color);
            text-decoration: none;
            letter-spacing: -0.5px;
        }

        .logo-brand span {
            color: var(--accent-color);
        }

        .form-control {
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            padding: 10px 14px;
        }

        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }
    </style>
</head>
<body>

    <!-- Верхняя панель: Навигация и Язык -->
    <div class="position-absolute top-0 start-0 end-0 p-4 d-flex justify-content-between align-items-center container">
        <a href="../selectposters.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-medium">
            <i class="bi bi-arrow-left me-1"></i> <span data-i18n="nav_home">На главную</span>
        </a>

        <div class="d-flex align-items-center gap-2">
            <!-- Переключатель Языка (TJ / RU / EN) -->
            <button class="btn btn-light btn-sm rounded-pill fw-bold border px-3" id="langToggle" type="button">
                <i class="bi bi-globe me-1"></i><span id="currentLang">TJ</span>
            </button>
        </div>
    </div>

    <!-- Основная карточка регистрации -->
    <div class="auth-card p-4 p-md-5 shadow-sm my-5">
        <div class="text-center mb-4">
            <a href="../selectposters.php" class="logo-brand mb-2 d-inline-block">ASO<span>.tj</span></a>
            <h3 class="fw-bold text-dark" data-i18n="reg_title">Регистрация</h3>
            <p class="text-muted small" data-i18n="reg_subtitle">Создайте аккаунт для публикации объявлений</p>
            
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger mt-3 py-2 small mb-0 rounded-3" role="alert">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
        </div>

        <form action="#" method="post">
            <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary" data-i18n="label_username">Имя пользователя:</label>
                <input type="text" maxlength="50" name="username" class="form-control" placeholder="Имя" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary" data-i18n="label_phone">Телефон:</label>
                <input type="tel" maxlength="15" name="phone" class="form-control" placeholder="+992..." required>
            </div>
            
            <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary" data-i18n="label_password">Пароль:</label>
                <input type="password" maxlength="20" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            
            <div class="mb-4">
                <label class="form-label small fw-semibold text-secondary">Email:</label>
                <input type="email" name="email" class="form-control" placeholder="name@example.com" required>
            </div>
            
            <button type="submit" name="register" class="btn btn-primary w-100 fw-bold py-2.5 rounded-pill shadow-sm mb-3" data-i18n="btn_register">
                Зарегистрироваться
            </button>

            <div class="text-center small">
                <span class="text-muted" data-i18n="has_account">Уже есть аккаунт?</span> 
                <a href="login.php" class="text-primary text-decoration-none fw-semibold ms-1" data-i18n="login_link">Войти</a>
            </div>
        </form>
    </div>

    <!-- Скрипт мультиязычности (TJ / RU / EN) -->
    <script>
        const translations = {
            tj: {
                page_title: "Сабти ном — ASO.tj",
                nav_home: "Саҳифаи асосӣ",
                reg_title: "Сабти ном",
                reg_subtitle: "Барои нашри эълонҳо ҳисоб эҷод кунед",
                label_username: "Номи корбар:",
                label_phone: "Телефон:",
                label_password: "Парол:",
                btn_register: "Сабти ном шудан",
                has_account: "Аллакай ҳисоб доред?",
                login_link: "Ворид шудан"
            },
            ru: {
                page_title: "Регистрация — ASO.tj",
                nav_home: "На главную",
                reg_title: "Регистрация",
                reg_subtitle: "Создайте аккаунт для публикации объявлений",
                label_username: "Имя пользователя:",
                label_phone: "Телефон:",
                label_password: "Пароль:",
                btn_register: "Зарегистрироваться",
                has_account: "Уже есть аккаунт?",
                login_link: "Войти"
            },
            en: {
                page_title: "Registration — ASO.tj",
                nav_home: "Home",
                reg_title: "Register",
                reg_subtitle: "Create an account to post listings",
                label_username: "Username:",
                label_phone: "Phone:",
                label_password: "Password:",
                btn_register: "Create Account",
                has_account: "Already have an account?",
                login_link: "Sign In"
            }
        };

        const langToggleBtn = document.getElementById('langToggle');
        const currentLangSpan = document.getElementById('currentLang');

        function setLanguage(lang) {
            localStorage.setItem('selectedLang', lang);
            currentLangSpan.textContent = lang.toUpperCase();

            document.querySelectorAll('[data-i18n]').forEach(element => {
                const key = element.getAttribute('data-i18n');
                if (translations[lang] && translations[lang][key]) {
                    element.textContent = translations[lang][key];
                }
            });
        }

        langToggleBtn.addEventListener('click', () => {
            const currentLang = localStorage.getItem('selectedLang') || 'tj';
            let newLang = 'ru';
            if (currentLang === 'tj') newLang = 'ru';
            else if (currentLang === 'ru') newLang = 'en';
            else newLang = 'tj';
            
            setLanguage(newLang);
        });

        document.addEventListener('DOMContentLoaded', () => {
            const savedLang = localStorage.getItem('selectedLang') || 'tj';
            setLanguage(savedLang);
        });
    </script>
</body>
</html>