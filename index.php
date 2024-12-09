<?php
session_start();
require_once 'config.php';

// Встановлення заголовків безпеки
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self';");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");



// Перевірка сесійних змінних
$firstName = isset($_SESSION['first_name']) ? sanitizeInput($_SESSION['first_name']) : '';
$lastName = isset($_SESSION['last_name']) ? sanitizeInput($_SESSION['last_name']) : '';
$fullName = trim($firstName . ' ' . $lastName);

// Обрізання довгих імен
if (mb_strlen($fullName) > 20) {
    $fullName = mb_substr($fullName, 0, 17) . '...';
}

?>
<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instityte</title>
    <link rel="stylesheet" href="styles.css">
    <script src="scripts/theme.js" defer></script>
</head>
<body>
    <h1>Instityte</h1>
    <button id="theme-toggle">Темна тема</button>

    <div class="user-info">
        <?php if (isset($_SESSION['user_id'])): ?>
            <span class="welcome-text"><?= $fullName ?></span>
            <button class="button logout-button" onclick="window.location.href='index.php?action=logout'">Вийти</button>
        <?php else: ?>
            <span class="welcome-text">Ви  увійшли в систему</span>
        <?php endif; ?>
    </div>

    <button class="menu-button" onclick="window.location.href='admin.php'">Адмін</button>
    <button class="menu-button" onclick="window.location.href='cource.php'">Курс</button>
    <button class="menu-button" onclick="window.location.href='professor.php'">Професор</button>
    <button class="menu-button" onclick="window.location.href='student.php'">Студент</button>
    <div class="version">Версія 1.0.0</div>
</body>
</html>
