<?php
require_once __DIR__ . '../config.php';

header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self';");

// Функція для санітарної очистки даних
function sanitize_input($data, $filter = FILTER_SANITIZE_STRING) {
    return filter_var($data, $filter);
}

// Функція для перевірки ID (тільки числа)
function validate_id($id) {
    return filter_var($id, FILTER_VALIDATE_INT);
}

// Обработка удаления по ID
if (isset($_GET['delete_id'])) {
    $id = sanitize_input($_GET['delete_id'], FILTER_SANITIZE_NUMBER_INT);
    
    if (validate_id($id)) {
        // Перевірка на існування ID в базі даних перед видаленням
        $query = "SELECT COUNT(*) FROM professor WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            // Удаляем
            $query = "DELETE FROM professor WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                // Перенумеровка всех оставшихся
                $conn->query("SET @count = 0"); // Счетчик
                $conn->query("UPDATE professor SET id = @count:=@count + 1 ORDER BY id");

                // Сбрасываем автоинкремент после пересчета
                $conn->query("ALTER TABLE professor AUTO_INCREMENT = 1");

                // Перенаправляем на страницу
                header('Location: professor.php');
                exit;
            } else {
                $deleteMessage = "Ошибка: " . $conn->error;
            }
        } else {
            $deleteMessage = "Неверный ID для удаления.";
        }
    } else {
        $deleteMessage = "Неверный ID для удаления.";
    }
}

// Обработка сортировки данных
$sort_column = sanitize_input($_GET['sort'] ?? 'id');
$sort_direction = sanitize_input($_GET['dir'] ?? 'ASC');
$sort_direction = strtoupper($sort_direction) === 'ASC' ? 'ASC' : 'DESC';

// Список разрешенных столбцов и направлений сортировки
$allowed_columns = ['id', 'name', 'email', 'phone_number', 'office_number', 'specialization', 'date_of_hire', 'years_of_experience', 'status', 'salary'];
$allowed_directions = ['ASC', 'DESC'];

if (!in_array($sort_column, $allowed_columns)) {
    $sort_column = 'id';
}

if (!in_array($sort_direction, $allowed_directions)) {
    $sort_direction = 'ASC';
}

// Запрос на получение данных с сортировкой
$query = "SELECT * FROM professor ORDER BY $sort_column $sort_direction";
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
    <title>Професор</title>
    <link rel="stylesheet" href="styles.css">
    <script src="scripts/theme.js" defer></script>
</head>
<body>
    <button id="theme-toggle">Темна тема</button>
    <h1>Професор</h1>

    <?php if (isset($deleteMessage)) { echo "<p>$deleteMessage</p>"; } ?>

    <table class="styled-table">
        <tr>
            <th><a href="?sort=id&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Професор</a></th>
            <th><a href="?sort=name&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Ім'я</a></th>
            <th><a href="?sort=email&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Емейл</a></th>
            <th><a href="?sort=phone_number&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Телефон</a></th>
            <th><a href="?sort=office_number&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Номер Офісу</a></th>
            <th><a href="?sort=specialization&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Спеціалізація</a></th>
            <th><a href="?sort=date_of_hire&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Дата прийому</a></th>
            <th><a href="?sort=years_of_experience&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Опит</a></th>
            <th><a href="?sort=status&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Статус</a></th>
            <th><a href="?sort=salary&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Зарплата</a></th>
            <th>Дії</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['phone_number']) ?></td>
                <td><?= htmlspecialchars($row['office_number']) ?></td>
                <td><?= htmlspecialchars($row['specialization']) ?></td>
                <td><?= htmlspecialchars($row['date_of_hire']) ?></td>
                <td><?= htmlspecialchars($row['years_of_experience']) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
                <td><?= htmlspecialchars($row['salary']) ?></td>
                <td>
                    <!-- Ссылка на редактирование -->
                    <a href="edit/professor/edit_professor.php?id=<?= htmlspecialchars($row['id']) ?>">Редагувати</a> |
                    <!-- Ссылка на удаление -->
                    <a href="?delete_id=<?= htmlspecialchars($row['id']) ?>" onclick="return confirm('Ви впевнені, що хочете видалити це замовлення?')">Видалити</a>
                </td>
            </tr>
        <?php } ?>
    </table>

    <?php $conn->close(); ?>

    <br>
    <button class="button" onclick="window.location.href='index.php'">Повернутися на головну</button>
    <button class="button" onclick="window.location.href='edit/professor/add_professor.php'">Додати нового професора</button>
</body>
</html>
