<?php
session_start();
require_once('../config/db.php');
require_once('../config/helpers.php');

check_auth();

$user_id = $_SESSION['user_id'];

// Получаем текущие данные пользователя
$query = "SELECT * FROM users WHERE id = '$user_id'";
$res = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($res);

$error = '';
$success = '';

if (isset($_POST['update_profile'])) {
    $username = sanitize($_POST['username']);
    $phone    = $user['phone'];
    $email    = $user['email'];
    $password = $_POST['password'];

    // Проверяем, не занято ли имя пользователя другим аккаунтом
    $check_query = "SELECT id FROM users WHERE username = '$username' AND id != '$user_id'";
    $check_res = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_res) > 0) {
        $error = "Это Имя пользователя уже занято другим аккаунтом!";
    } else {
        // Если введен новый пароль, обновляем его вместе с именем, иначе обновляем только имя
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $update_query = "UPDATE users SET username = '$username', password = '$hashed_password' WHERE id = '$user_id'";
        } else {
            $update_query = "UPDATE users SET username = '$username' WHERE id = '$user_id'";
        }

        if (mysqli_query($conn, $update_query)) {
            $_SESSION['username'] = $username; // Обновляем сессию
            ?>
            <script>
                alert("Профиль успешно обновлен!");
                window.location.href = "profile.php";
            </script>
            <?php
            exit();
        } else {
            $error = "Ошибка при обновлении профиля: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-i18n="page_title">Редактирование профиля — ASO.tj</title>
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

        .edit-card {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 20px;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        }

        .logo-brand {
            font-family: 'Arial Black', sans-serif;
            font-size: 24px;
            font-weight: 900;
            color: var(--primary-color);
            text-decoration: none;
        }

        .logo-brand span {
            color: var(--accent-color);
        }

        .form-control {
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            padding: 10px 14px;
        }

        .form-control:read-only {
            background-color: #f1f5f9;
            color: #64748b;
        }

        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }
    </style>
</head>
<body>

    <!-- Верхняя панель: Назад и Язык -->
    <div class="position-absolute top-0 start-0 end-0 p-4 d-flex justify-content-between align-items-center container">
        <a href="profile.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-medium">
            <i class="bi bi-arrow-left me-1"></i> <span data-i18n="nav_back">Назад в профиль</span>
        </a>

        <div class="d-flex align-items-center gap-2">
            <!-- Переключатель Языка (TJ / RU / EN) -->
            <button class="btn btn-light btn-sm rounded-pill fw-bold border px-3" id="langToggle" type="button">
                <i class="bi bi-globe me-1"></i><span id="currentLang">TJ</span>
            </button>
        </div>
    </div>

    <!-- Основная карточка редактирования -->
    <div class="edit-card p-4 p-md-5 shadow-sm my-5">
        <div class="text-center mb-4">
            <a href="../selectposters.php" class="logo-brand mb-2 d-inline-block">ASO<span>.tj</span></a>
            <h3 class="fw-bold text-dark" data-i18n="edit_title">Редактировать профиль</h3>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 small mb-4 rounded-3"><?= $error ?></div>
        <?php endif; ?>

        <form action="" method="post">
            <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary" data-i18n="label_username">Имя пользователя:</label>
                <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary" data-i18n="label_phone">Телефон (изменить нельзя):</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>" readonly disabled>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary" data-i18n="label_email">Email (изменить нельзя):</label>
                <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" readonly disabled>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-semibold text-secondary" data-i18n="label_password">Новый пароль (оставьте пустым, если не хотите менять):</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••">
            </div>

            <div class="d-flex justify-content-between gap-3">
                <a href="profile.php" class="btn btn-outline-secondary w-50 rounded-pill py-2.5 fw-semibold" data-i18n="btn_cancel">Отмена</a>
                <button type="submit" name="update_profile" class="btn btn-primary w-50 fw-bold rounded-pill py-2.5 shadow-sm" data-i18n="btn_save">Сохранить</button>
            </div>
        </form>
    </div>

    <!-- Скрипт мультиязычности (TJ / RU / EN) -->
    <script>
        const translations = {
            tj: {
                page_title: "Таҳрири профил — ASO.tj",
                nav_back: "Ба профил",
                edit_title: "Таҳрири профил",
                label_username: "Номи корбар:",
                label_phone: "Телефон (иваз карда намешавад):",
                label_email: "Электронӣ (иваз карда намешавад):",
                label_password: "Пароли нав (агар иваз кардан нахоҳед, холӣ гузоред):",
                btn_cancel: "Бекор кардан",
                btn_save: "Захира кардан"
            },
            ru: {
                page_title: "Редактирование профиля — ASO.tj",
                nav_back: "Назад в профиль",
                edit_title: "Редактировать профиль",
                label_username: "Имя пользователя:",
                label_phone: "Телефон (изменить нельзя):",
                label_email: "Email (изменить нельзя):",
                label_password: "Новый пароль (оставьте пустым, если не хотите менять):",
                btn_cancel: "Отмена",
                btn_save: "Сохранить"
            },
            en: {
                page_title: "Edit Profile — ASO.tj",
                nav_back: "Back to Profile",
                edit_title: "Edit Profile",
                label_username: "Username:",
                label_phone: "Phone (cannot be changed):",
                label_email: "Email (cannot be changed):",
                label_password: "New password (leave blank if you don't want to change):",
                btn_cancel: "Cancel",
                btn_save: "Save Changes"
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