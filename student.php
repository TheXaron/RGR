<?php
// Используем абсолютный путь к файлу config.php
require_once __DIR__ . '/config.php';  // Подключаем файл конфигурации

header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self';");

// Функция для проверки и очистки значений
function sanitize_input($data, $type = FILTER_SANITIZE_STRING) {
    return filter_var($data, $type);
}

// Обработка удаления продукта по ID
if (isset($_GET['delete_id'])) {
    $id = sanitize_input($_GET['delete_id'], FILTER_SANITIZE_NUMBER_INT);
    
    if ($id && filter_var($id, FILTER_VALIDATE_INT)) {
        // Удаляем запись продукта, используя подготовленные выражения для предотвращения SQL-инъекций
        $query = "DELETE FROM student WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $id); // 'i' означает целочисленный тип параметра
        if ($stmt->execute()) {
            // Пересчитываем ID всех оставшихся записей
            $conn->query("SET @count = 0"); // Счетчик
            $conn->query("UPDATE student SET id = @count:=@count + 1 ORDER BY id");

            // Сбрасываем автоинкремент после пересчета
            $conn->query("ALTER TABLE student AUTO_INCREMENT = 1");

            // Перенаправляем на страницу 
            header('Location: student.php');
            exit;
        } else {
            $deleteMessage = "Помилка: " . $conn->error;
        }
        $stmt->close();
    } else {
        $deleteMessage = "Невірний ID для видалення.";
    }
}

// Обработка сортировки данных
$sort_column = sanitize_input($_GET['sort'] ?? 'id', FILTER_SANITIZE_STRING);
$sort_direction = sanitize_input($_GET['dir'] ?? 'ASC', FILTER_SANITIZE_STRING);
$sort_direction = strtoupper($sort_direction) === 'ASC' ? 'ASC' : 'DESC';

// Список разрешенных столбцов и направлений сортировки
$allowed_columns = ['id', 'name', 'phone_number', 'email', 'major', 'date_of_birth', 'GPA', 'address', 'status', 'current_year', 'year_of_enrollment'];
$allowed_directions = ['ASC', 'DESC'];

if (!in_array($sort_column, $allowed_columns)) {
    $sort_column = 'id'; 
}

if (!in_array($sort_direction, $allowed_directions)) {
    $sort_direction = 'ASC';
}

// Запрос на получение всех продуктов с сортировкой
$query = "SELECT * FROM student ORDER BY $sort_column $sort_direction";
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
    <title>Студент</title>
    <link rel="stylesheet" href="styles.css">
    <script src="scripts/theme.js" defer></script>
</head>
<body>
    <button id="theme-toggle">Темна тема</button>
    <h1>Студент</h1>

    <?php if (isset($deleteMessage)) { echo "<p>$deleteMessage</p>"; } ?>

    <table class="styled-table">
        <tr>
            <th><a href="?sort=id&dir=<?= htmlspecialchars($sort_direction === 'ASC' ? 'DESC' : 'ASC') ?>">ID</a></th>
            <th><a href="?sort=name&dir=<?= htmlspecialchars($sort_direction === 'ASC' ? 'DESC' : 'ASC') ?>">Ім'я</a></th>
            <th><a href="?sort=phone_number&dir=<?= htmlspecialchars($sort_direction === 'ASC' ? 'DESC' : 'ASC') ?>">телефон</a></th>
            <th><a href="?sort=email&dir=<?= htmlspecialchars($sort_direction === 'ASC' ? 'DESC' : 'ASC') ?>">Емейл</a></th>
            <th><a href="?sort=major&dir=<?= htmlspecialchars($sort_direction === 'ASC' ? 'DESC' : 'ASC') ?>">major</a></th>
            <th><a href="?sort=date_of_birth&dir=<?= htmlspecialchars($sort_direction === 'ASC' ? 'DESC' : 'ASC') ?>">день народження</a></th>
            <th><a href="?sort=GPA&dir=<?= htmlspecialchars($sort_direction === 'ASC' ? 'DESC' : 'ASC') ?>">Балл НМТ</a></th>
            <th><a href="?sort=address&dir=<?= htmlspecialchars($sort_direction === 'ASC' ? 'DESC' : 'ASC') ?>">адресса</a></th>
            <th><a href="?sort=status&dir=<?= htmlspecialchars($sort_direction === 'ASC' ? 'DESC' : 'ASC') ?>">статус</a></th>
            <th><a href="?sort=current_year&dir=<?= htmlspecialchars($sort_direction === 'ASC' ? 'DESC' : 'ASC') ?>">current_year</a></th>
            <th><a href="?sort=year_of_enrollment&dir=<?= htmlspecialchars($sort_direction === 'ASC' ? 'DESC' : 'ASC') ?>">дата приєднання</a></th>
            <th>Дії</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['phone_number']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['major']) ?></td>
                <td><?= htmlspecialchars($row['date_of_birth']) ?></td>
                <td><?= htmlspecialchars($row['GPA']) ?></td>
                <td><?= htmlspecialchars($row['address']) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
                <td><?= htmlspecialchars($row['current_year']) ?></td>
                <td><?= htmlspecialchars($row['year_of_enrollment']) ?></td>
                <td>
                    <!-- Ссылка на редактирование продукта -->
                    <a href="edit/student/edit_student.php?id=<?= htmlspecialchars($row['id']) ?>">Редагувати</a> |
                    <!-- Ссылка на удаление продукта -->
                    <a href="?delete_id=<?= htmlspecialchars($row['id']) ?>" onclick="return confirm('Ви впевнені, що хочете видалити цей продукт?')">Видалити</a>
                </td>
            </tr>
        <?php } ?>
    </table>

    <?php $conn->close(); ?>

    <br>
    <button class="button" onclick="window.location.href='index.php'">Повернутися на головну</button>
    <button class="button" onclick="window.location.href='/Ins/edit/student/add_student.php'">Додати новий Студент</button>
</body>
</html>
