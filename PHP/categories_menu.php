<?php
require_once('config/db.php');
require_once('config/helpers.php');

$categories_query = "SELECT * FROM categories ORDER BY cat_id ASC";
$categories_result = mysqli_query($conn, $categories_query);
?>

<style>
    /* Общий светлый контейнер-подложка */
    .categories-section-wrapper {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 30px 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        margin-bottom: 40px;
    }

    /* Карточка категории */
    .cat-block-card {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
        transition: transform 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
    }

    .cat-block-card:hover {
        transform: translateY(-4px);
        border-color: #cbd5e1;
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08);
    }

    /* Шапка карточки */
    .cat-block-header {
        background: #f8fafc;
        padding: 14px 18px;
        border-bottom: 1px solid #e2e8f0;
    }

    .cat-block-title {
        color: #0f172a;
        font-weight: 700;
        font-size: 1.05rem;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .cat-block-title:hover {
        color: #2563eb !important;
    }

    /* Тело карточки */
    .cat-block-body {
        padding: 16px 18px;
    }

    /* Ссылки на модели */
    .cat-block-item {
        color: #64748b;
        font-size: 0.92rem;
        text-decoration: none;
        padding: 4px 0;
        display: block;
        transition: color 0.2s ease, transform 0.2s ease;
    }

    .cat-block-item:hover {
        color: #2563eb !important;
        transform: translateX(4px);
    }

    /* Кнопка "Показать еще" */
    .cat-block-more {
        background: #f1f5f9;
        color: #2563eb;
        font-weight: 600;
        font-size: 0.85rem;
        text-decoration: none;
        padding: 8px 14px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        transition: all 0.2s ease;
        display: block;
        text-align: center;
    }

    .cat-block-more:hover {
        background-color: #2563eb;
        color: #ffffff !important;
        border-color: #2563eb;
    }
</style>

<!-- Общий светй контейнер -->
<div class="categories-section-wrapper">
    <div class="d-flex align-items-center justify-content-between mb-4 px-2">
        <h3 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-grid-3x3-gap-fill text-primary"></i> Категории транспорта
        </h3>
    </div>

    <div class="row g-4">
        <?php if ($categories_result && mysqli_num_rows($categories_result) > 0): ?>
            <?php while ($cat = mysqli_fetch_assoc($categories_result)): ?>
                <?php 
                    $current_cat_id = $cat['cat_id'];

                    // 1. Считаем общее количество моделей в категории
                    $total_models_q = mysqli_query($conn, "SELECT COUNT(*) as total FROM models WHERE cat_id = '$current_cat_id'");
                    $total_models = mysqli_fetch_assoc($total_models_q)['total'];

                    // 2. Выбираем первые 6 моделей
                    $models_query = "SELECT * FROM models WHERE cat_id = '$current_cat_id' LIMIT 6";
                    $models_result = mysqli_query($conn, $models_query);
                ?>

                <div class="col-12 col-md-6 col-lg-4">
                    <div class="cat-block-card h-100 d-flex flex-column">
                        
                        <!-- Шапка с названием категории -->
                        <div class="cat-block-header d-flex justify-content-between align-items-center">
                            <a href="category.php?id=<?= $cat['cat_id'] ?>" class="cat-block-title">
                                <?= htmlspecialchars($cat['cat_name']) ?>
                            </a>
                            <span class="badge bg-light text-primary border px-2.5 py-1.5 rounded-pill fs-7">
                                <?= $total_models ?>
                            </span>
                        </div>
                        
                        <!-- Список моделей -->
                        <div class="cat-block-body flex-grow-1 d-flex flex-column justify-content-between">
                            <ul class="list-unstyled mb-3">
                                <?php if ($models_result && mysqli_num_rows($models_result) > 0): ?>
                                    <?php while ($model = mysqli_fetch_assoc($models_result)): ?>
                                        <li>
                                            <a href="selectposters.php?brand=<?= urlencode($model['m_name']) ?>" class="cat-block-item">
                                                <i class="bi bi-chevron-right text-primary small me-1 opacity-75"></i><?= htmlspecialchars($model['m_name']) ?>
                                            </a>
                                        </li>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <li class="text-muted small py-1">Нет моделей</li>
                                <?php endif; ?>
                            </ul>

                            <!-- Кнопка "Показать еще" -->
                            <?php if ($total_models > 6): ?>
                                <div class="pt-2 border-top">
                                    <a href="category.php?id=<?= $cat['cat_id'] ?>" class="cat-block-more">
                                        Показать еще +<?= ($total_models - 6) ?> <i class="bi bi-arrow-right-short"></i>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-light text-center text-muted border">
                    Категории не найдены.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>