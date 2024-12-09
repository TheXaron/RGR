<?php
// Используем абсолютный путь к файлу config.php
require_once __DIR__ . '/config.php';  // Подключаем файл конфигурации

header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self';");

// Обработка удаления  по ID
if (isset($_GET['delete_id'])) {
    $id = filter_input(INPUT_GET, 'delete_id', FILTER_SANITIZE_NUMBER_INT);
    if ($id) {
        // Удаляем запись 
        $query = "DELETE FROM cource WHERE id = $id";
        if ($conn->query($query) === TRUE) {
            // Пересчитываем ID всех оставшихся записей
            $conn->query("SET @count = 0"); // Счетчик
            $conn->query("UPDATE cource SET cource_id = @count:=@count + 1 ORDER BY id");

            // Сбрасываем автоинкремент после пересчета
            $conn->query("ALTER TABLE cource AUTO_INCREMENT = 1");

            // Перенаправляем на страницу 
            header('Location: cource.php');
            exit;
        } else {
            $deleteMessage = "Помилка: " . $conn->error;
        }
    } else {
        $deleteMessage = "Невірний ID для видалення.";
    }
}

// Обработка сортировки данных
$sort_column = filter_input(INPUT_GET, 'sort', FILTER_SANITIZE_STRING) ?? 'id';
$sort_direction = filter_input(INPUT_GET, 'dir', FILTER_SANITIZE_STRING) ?? 'ASC';
$sort_direction = strtoupper($sort_direction) === 'ASC' ? 'ASC' : 'DESC';

// Список разрешенных столбцов и направлений сортировки
$allowed_columns = ['id', 'name', 'credits', 'admin_id', 'professor_id', 'description', 'student_id'];
$allowed_directions = ['ASC', 'DESC'];

if (!in_array($sort_column, $allowed_columns)) {
    $sort_column = 'id'; 
}

if (!in_array($sort_direction, $allowed_directions)) {
    $sort_direction = 'ASC';
}

// Запрос на получение с сортировкой
$query = "SELECT * FROM cource ORDER BY $sort_column $sort_direction";
$result = $conn->query($query);

// Проверка на успешность выполнения запроса
if (!$result) {
    die("Ошибка выполнения запроса: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Курс</title>
    <link rel="stylesheet" href="styles.css">
    <script src="scripts/theme.js" defer></script>
</head>
<body>
    <button id="theme-toggle">Темна тема</button>
    <h1>Курс</h1>

    <?php if (isset($deleteMessage)) { echo "<p>$deleteMessage</p>"; } ?>

    <table class="styled-table">
        <tr>
            <th><a href="?sort=id&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">ID</a></th>
            <th><a href="?sort=name&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Ім'я</a></th>
            <th><a href="?sort=credits&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Кредит</a></th>
            <th><a href="?sort=admin_id&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Админ</a></th>
            <th><a href="?sort=professor_id&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Професор</a></th>
            <th><a href="?sort=description&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Опис</a></th>
            <th><a href="?sort=student_id&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Студент</a></th>
            <th>Дії</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['credits']) ?></td>
                <td><?= htmlspecialchars($row['admin_id']) ?></td>
                <td><?= htmlspecialchars($row['professor_id']) ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td><?= htmlspecialchars($row['student_id']) ?></td>
                <td>
                    <!-- Ссылка на редактирование клиента -->
                    <a href="edit/cource/edit_cource.php?id=<?= htmlspecialchars($row['id']) ?>">Редагувати</a> |
                    <!-- Ссылка на удаление клиента -->
                    <a href="?delete_id=<?= htmlspecialchars($row['id']) ?>" onclick="return confirm('Ви впевнені, що хочете видалити цього клієнта?')">Видалити</a>
                </td>
            </tr>
        <?php } ?>
    </table>

    <?php $conn->close(); ?>

    <br>
    <button class="button" onclick="window.location.href='index.php'">Повернутися на головну</button>
    <button class="button" onclick="window.location.href='/Ins/edit/cource/add_cource.php'">Додати новий Курс</button>
</body>
</html>
