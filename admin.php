<?php
// Use absolute path for config.php
require_once __DIR__ . '/config.php';  // Connect to config file

header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self';");

// Deleting record by ID
if (isset($_GET['delete_id'])) {
    $id = filter_input(INPUT_GET, 'delete_id', FILTER_SANITIZE_NUMBER_INT);
    
    // Sanitize and validate ID
    if ($id && filter_var($id, FILTER_VALIDATE_INT)) {
        // Prepared statement for deletion
        $query = "DELETE FROM `admin` WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $id);
        
        if ($stmt->execute()) {
            // Reorder IDs and reset AUTO_INCREMENT
            $conn->query("SET @count = 0"); // Counter
            $conn->query("UPDATE `admin` SET id = @count:=@count + 1 ORDER BY id");
            $conn->query("ALTER TABLE `admin` AUTO_INCREMENT = 1");

            header('Location: admin.php');
            exit;
        } else {
            $deleteMessage = "Помилка: " . $conn->error;
        }
    } else {
        $deleteMessage = "Невірний ID для видалення.";
    }
}

// Sorting logic
$sort_column = filter_input(INPUT_GET, 'sort', FILTER_SANITIZE_STRING) ?? 'id';
$sort_direction = filter_input(INPUT_GET, 'dir', FILTER_SANITIZE_STRING) ?? 'ASC';

// Allowed columns and directions for sorting
$allowed_columns = ['id', 'name', 'email', 'phone_number', 'role', 'date_of_joining', 'last_login', 'status'];
$allowed_directions = ['ASC', 'DESC'];

// Validate sorting column and direction
if (!in_array($sort_column, $allowed_columns)) {
    $sort_column = 'id'; 
}

if (!in_array(strtoupper($sort_direction), $allowed_directions)) {
    $sort_direction = 'ASC';
}

// Query to fetch sorted data
$query = "SELECT * FROM `admin` ORDER BY $sort_column $sort_direction";
$result = $conn->query($query);

// Error handling for query execution
if (!$result) {
    die("Error executing query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Адмін</title>
    <link rel="stylesheet" href="styles.css">
    <script src="scripts/theme.js" defer></script>
</head>
<body>
    <button id="theme-toggle">Темна тема</button>
    <h1>Адмін</h1>

    <?php if (isset($deleteMessage)) { echo "<p>$deleteMessage</p>"; } ?>

    <table class="styled-table">
        <tr>
            <th><a href="?sort=id&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">ID</a></th>
            <th><a href="?sort=name&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Ім'я</a></th>
            <th><a href="?sort=email&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Емейл</a></th>
            <th><a href="?sort=phone_number&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Контактний номер</a></th>
            <th><a href="?sort=role&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Роль</a></th>
            <th><a href="?sort=date_of_joining&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Дата приєднання</a></th>
            <th><a href="?sort=last_login&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">ласт логин</a></th>
            <th><a href="?sort=status&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">статус</a></th>
            <th>Дії</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['phone_number']) ?></td>
                <td><?= htmlspecialchars($row['role']) ?></td>
                <td><?= htmlspecialchars($row['date_of_joining']) ?></td>
                <td><?= htmlspecialchars($row['last_login']) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
                <td>
                    <a href="edit/admin/edit_admin.php?id=<?= htmlspecialchars($row['id']) ?>">Редагувати</a> |
                    <a href="?delete_id=<?= htmlspecialchars($row['id']) ?>" onclick="return confirm('Ви впевнені, що хочете видалити цього консультанта?')">Видалити</a>
                </td>
            </tr>
        <?php } ?>
    </table>

    <?php $conn->close(); ?>

    <br>
    <button class="button" onclick="window.location.href='index.php'">Повернутися на головну</button>
    <button class="button" onclick="window.location.href='edit/admin/add_admin.php'">Додати нового Адміна</button>
</body>
</html>
