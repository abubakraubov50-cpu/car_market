<?php
session_start();
require_once('../config/db.php');
require_once('../config/helpers.php');

check_auth();

$categories_res = mysqli_query($conn, "SELECT * FROM categories ORDER BY cat_name");

// Получаем список марок
$brands_res = mysqli_query($conn, "SELECT * FROM brands ORDER BY br_name");

// Получаем список моделей вместе с их br_id
$models_res = mysqli_query($conn, "SELECT * FROM models");
$all_models = [];
while ($row = mysqli_fetch_assoc($models_res)) {
    $all_models[] = $row;
}

$error = '';

if (isset($_POST['add_car'])) {
    $title       = sanitize($_POST['p_title']);
    $description = sanitize($_POST['p_description']);
    $price       = sanitize($_POST['p_price']);
    $year        = (int)$_POST['p_year']; // Принимаем год выпуска
    $cat_id      = (int)$_POST['cat_id'];
    $br_id       = (int)$_POST['br_id'];
    $m_id        = isset($_POST['m_id']) ? (int)$_POST['m_id'] : 0;
    $new_m_name  = isset($_POST['new_m_name']) ? sanitize($_POST['new_m_name']) : '';
    $user_id     = $_SESSION['user_id'];

    if ($m_id === 0 && !empty($new_m_name)) {
        // Проверяем, существует ли уже такая модель
        $check_model_q = mysqli_query($conn, "SELECT m_id FROM models WHERE m_name = '$new_m_name' AND br_id = '$br_id'");
        if (mysqli_num_rows($check_model_q) > 0) {
            $m_id = mysqli_fetch_assoc($check_model_q)['m_id'];
        } else {
            // Вставляем новую модель с правильным cat_id!
            mysqli_query($conn, "INSERT INTO models (m_name, br_id, cat_id) VALUES ('$new_m_name', '$br_id', '$cat_id')");
            $m_id = mysqli_insert_id($conn);
        }
    }

    if ($m_id === 0) {
        $error = "Пожалуйста, укажите или выберите модель!";
    } else {
        // === ПРОВЕРКА СООТВЕТСТВИЯ МОДЕЛИ И БРЕНДА НА СЕРВЕРЕ ===
        $check_query = "SELECT m_id FROM models WHERE m_id = '$m_id' AND br_id = '$br_id'";
        $check_res   = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($check_res) === 0) {
            $error = "Выбранная модель не соответствует выбранной марке автомобиля!";
        } else {
            // Обработка загрузки нескольких фотографий
            $uploaded_photos = [];
            
            if (isset($_FILES['p_photos']) && !empty($_FILES['p_photos']['name'][0])) {
                $total_files = count($_FILES['p_photos']['name']);
                
                // Папка для сохранения фото
                $upload_dir = '../../PHOTO/';
                
                for ($i = 0; $i < $total_files; $i++) {
                    $tmp_name = $_FILES['p_photos']['tmp_name'][$i];
                    $original_name = basename($_FILES['p_photos']['name'][$i]);
                    
                    $file_ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
                    $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
                    
                    if (in_array($file_ext, $allowed_extensions)) {
                        $new_file_name = uniqid('car_') . '.' . $file_ext;
                        $destination = $upload_dir . $new_file_name;
                        
                        if (move_uploaded_file($tmp_name, $destination)) {
                            $uploaded_photos[] = $new_file_name;
                        }
                    }
                }
            }

            if (empty($uploaded_photos)) {
                $error = "Пожалуйста, загрузите хотя бы одну фотографию!";
            } else {
                // Сохраняем объявление в таблицу poster вместе с p_year
                $query = "INSERT INTO poster (p_title, p_description, p_price, p_year, u_id, br_id, m_id, created_at) 
                          VALUES ('$title', '$description', '$price', '$year', '$user_id', '$br_id', '$m_id', NOW())";

                if (mysqli_query($conn, $query)) {
                    $post_id = mysqli_insert_id($conn);

                    // Сохраняем фотографии в таблицу photo
                    foreach ($uploaded_photos as $photo_path) {
                        $photo_path_esc = mysqli_real_escape_string($conn, $photo_path);
                        mysqli_query($conn, "INSERT INTO photo (post_id, path) VALUES ('$post_id', '$photo_path_esc')");
                    }
                    ?>
                    <script>
                        alert("Объявление успешно опубликовано!");
                        window.location.href = "profile.php";
                    </script>
                    <?php
                    exit();
                } else {
                    $error = "Ошибка при сохранении объявления: " . mysqli_error($conn);
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru" data-bs-theme="light" id="htmlRoot">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-i18n="page_title">Добавить автомобиль — ASO.tj</title>
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

        .add-card {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 20px;
            max-width: 650px;
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

        .form-control, .form-select {
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            padding: 10px 14px;
        }

        .form-control:focus, .form-select:focus {
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

    <!-- Основная карточка добавления авто -->
    <div class="add-card p-4 p-md-5 shadow-sm my-5">
        <div class="text-center mb-4">
            <a href="../selectposters.php" class="logo-brand mb-2 d-inline-block">ASO<span>.tj</span></a>
            <h3 class="fw-bold text-dark" data-i18n="add_title">Публикация автомобиля</h3>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 small mb-4 rounded-3"><?= $error ?></div>
        <?php endif; ?>
        
        <form action="add_car.php" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label fw-semibold small" data-i18n="label_cat">Категория транспорта:</label>
                <select name="cat_id" id="catSelect" class="form-select" required>
                    <option value="" data-i18n="select_cat_default">-- Выберите категорию --</option>
                    <?php while ($c = mysqli_fetch_assoc($categories_res)): ?>
                        <option value="<?= $c['cat_id'] ?>"><?= htmlspecialchars($c['cat_name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small" data-i18n="label_title">Заголовок объявления:</label>
                <input type="text" name="p_title" class="form-control" data-i18n-placeholder="placeholder_title" placeholder="Например: Продам в идеальном состоянии" required>
            </div>

            <div class="row mb-3">
                <div class="col-6">
                    <label class="form-label fw-semibold small" data-i18n="label_brand">Марка:</label>
                    <select name="br_id" id="brandSelect" class="form-select" required>
                        <option value="" data-i18n="select_brand_default">Выберите марку</option>
                        <?php while ($b = mysqli_fetch_assoc($brands_res)): ?>
                            <option value="<?= $b['br_id'] ?>"><?= htmlspecialchars($b['br_name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-6">
                    <label class="form-label fw-semibold small" data-i18n="label_model">Модель:</label>
                    <select name="m_id" id="modelSelect" class="form-select" disabled>
                        <option value="" data-i18n="select_model_default">Сначала выберите марку</option>
                        <?php foreach ($all_models as $m): ?>
                            <option value="<?= $m['m_id'] ?>" data-brand="<?= $m['br_id'] ?>">
                                <?= htmlspecialchars($m['m_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="mb-3" id="newModelBlock" style="display: none;">
                <label class="form-label text-primary fw-semibold small" data-i18n="label_new_model">Или введите название модели вручную:</label>
                <input type="text" name="new_m_name" id="newModelInput" class="form-control" placeholder="Например: Camry или X5">
            </div>

            <div class="row mb-3">
                <div class="col-6">
                    <label class="form-label fw-semibold small" data-i18n="label_price">Цена ($):</label>
                    <input type="number" name="p_price" class="form-control" placeholder="15000" required>
                </div>
                <div class="col-6">
                    <label class="form-label fw-semibold small" data-i18n="label_year">Год выпуска:</label>
                    <input type="number" name="p_year" class="form-control" placeholder="2020" min="1900" max="<?= date('Y') ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small" data-i18n="label_photos">Фотографии автомобиля (можно выбрать несколько):</label>
                <input type="file" name="p_photos[]" class="form-control" accept="image/*" multiple required>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold small" data-i18n="label_desc">Описание:</label>
                <textarea name="p_description" class="form-control" rows="4" data-i18n-placeholder="placeholder_desc" placeholder="Состояние идеальное, не бита, не крашена..." required></textarea>
            </div>

            <div class="d-flex justify-content-between gap-3">
                <a href="profile.php" class="btn btn-outline-secondary w-50 rounded-pill py-2.5 fw-semibold" data-i18n="btn_cancel">Отмена</a>
                <button type="submit" name="add_car" class="btn btn-primary w-50 fw-bold rounded-pill py-2.5 shadow-sm" data-i18n="btn_publish">Опубликовать</button>
            </div>
        </form>
    </div>

    <!-- Скрипты: логика выбора модели и мультиязычности (TJ / RU / EN) -->
    <script>
        const translations = {
            tj: {
                page_title: "Илова кардани мошин — ASO.tj",
                nav_back: "Ба профил",
                add_title: "Нашри эълони мошин",
                label_cat: "Категорияи нақлиёт:",
                select_cat_default: "-- Категорияро интихоб кунед --",
                label_title: "Сарлавҳаи эълон:",
                placeholder_title: "Масалан: Мефурӯшам дар ҳолати хуб",
                label_brand: "Марка:",
                select_brand_default: "Маркаро интихоб кунед",
                label_model: "Модел:",
                select_model_default: "Аввал маркаро интихоб кунед",
                label_new_model: "Ё номи моделро дастӣ ворид кунед:",
                label_price: "Нарх ($):",
                label_year: "Соли барориш:",
                label_photos: "Суратҳои мошин (якчандто интихоб кардан мумкин аст):",
                label_desc: "Тавсиф:",
                placeholder_desc: "Ҳолати хуб, задагӣ надорад...",
                btn_cancel: "Бекор кардан",
                btn_publish: "Интишор кардан"
            },
            ru: {
                page_title: "Добавить автомобиль — ASO.tj",
                nav_back: "Назад в профиль",
                add_title: "Публикация автомобиля",
                label_cat: "Категория транспорта:",
                select_cat_default: "-- Выберите категорию --",
                label_title: "Заголовок объявления:",
                placeholder_title: "Например: Продам в идеальном состоянии",
                label_brand: "Марка:",
                select_brand_default: "Выберите марку",
                label_model: "Модель:",
                select_model_default: "Сначала выберите марку",
                label_new_model: "Или введите название модели вручную:",
                label_price: "Цена ($):",
                label_year: "Год выпуска:",
                label_photos: "Фотографии автомобиля (можно выбрать несколько):",
                label_desc: "Описание:",
                placeholder_desc: "Состояние идеальное, не бита, не крашена...",
                btn_cancel: "Отмена",
                btn_publish: "Опубликовать"
            },
            en: {
                page_title: "Add Car — ASO.tj",
                nav_back: "Back to Profile",
                add_title: "Publish Car Listing",
                label_cat: "Vehicle Category:",
                select_cat_default: "-- Select Category --",
                label_title: "Listing Title:",
                placeholder_title: "E.g., Mint condition, single owner",
                label_brand: "Make:",
                select_brand_default: "Select Make",
                label_model: "Model:",
                select_model_default: "Select Make First",
                label_new_model: "Or enter model name manually:",
                label_price: "Price ($):",
                label_year: "Year:",
                label_photos: "Car Photos (multiple selection allowed):",
                label_desc: "Description:",
                placeholder_desc: "Perfect condition, no accidents...",
                btn_cancel: "Cancel",
                btn_publish: "Publish"
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

            document.querySelectorAll('[data-i18n-placeholder]').forEach(element => {
                const key = element.getAttribute('data-i18n-placeholder');
                if (translations[lang] && translations[lang][key]) {
                    element.placeholder = translations[lang][key];
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

        // Логика динамической фильтрации моделей по выбранному бренду
        const brandSelect = document.getElementById('brandSelect');
        const modelSelect = document.getElementById('modelSelect');
        const newModelBlock = document.getElementById('newModelBlock');
        const modelOptions = Array.from(modelSelect.querySelectorAll('option[data-brand]'));

        brandSelect.addEventListener('change', function() {
            const selectedBrand = this.value;

            if (!selectedBrand) {
                modelSelect.disabled = true;
                modelSelect.value = "";
                newModelBlock.style.display = "none";
                return;
            }

            modelSelect.disabled = false;
            modelSelect.value = "";
            newModelBlock.style.display = "block";

            modelOptions.forEach(option => {
                if (option.getAttribute('data-brand') === selectedBrand) {
                    option.hidden = false;
                    option.disabled = false;
                } else {
                    option.hidden = true;
                    option.disabled = true;
                }
            });
        });

        document.addEventListener('DOMContentLoaded', () => {
            const savedLang = localStorage.getItem('selectedLang') || 'tj';
            setLanguage(savedLang);
        });
    </script>
</body>
</html>