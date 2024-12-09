<?php
require_once __DIR__ . '/../../config.php';

// Отримуємо ID адміністратора з GET запиту
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!$id) {
    header('Location: /Ins/admin.php');
    exit;
}

// Запит для отримання поточних даних адміністратора
$query = "SELECT * FROM admin WHERE id = $id";
$result = $conn->query($query);
if ($result->num_rows === 0) {
    header('Location: /Ins/admin.php');
    exit;
}

$admin = $result->fetch_assoc();

// Перевірка, якщо форма була відправлена
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Отримуємо дані з форми
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone_number = filter_input(INPUT_POST, 'phone_number', FILTER_SANITIZE_NUMBER_INT);
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);

    // Перевірка, що всі поля заповнені
    if ($name && $email && $phone_number && $role) {
        // Формуємо запит для оновлення даних адміністратора
        $update_query = "UPDATE admin SET name='$name', email='$email', phone_number='$phone_number', role='$role' WHERE id=$id";

        // Виконуємо запит на оновлення
        if ($conn->query($update_query) === TRUE) {
            header('Location: /Ins/admin.php');  // Редирект на сторінку адміністраторів
            exit;
        } else {
            // Виводимо повідомлення про помилку
            $errorMessage = "Помилка при оновленні даних адміністратора: " . $conn->error;
        }
    } else {
        $errorMessage = "Заповніть усі поля!";
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Редагувати адміністратора</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        /* Стилі для світлої та темної теми */
        body.light-mode { background-color: #ffffff; color: #333333; }
        body.dark-mode { background-color: #333333; color: #ffffff; }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: inherit;
        }
        h1 { text-align: center; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="email"], input[type="number"] {
            width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;
        }
        .button {
            display: inline-block; padding: 10px 20px;
            background-color: #4CAF50; color: #fff; border: none; border-radius: 4px; cursor: pointer;
        }
        .button:hover { background-color: #45a049; }
        #theme-toggle {
            position: fixed; top: 20px; right: 20px;
            padding: 10px; cursor: pointer; background-color: #ff9800; color: #fff; border-radius: 5px;
        }
        #theme-toggle:hover { background-color: #e68900; }
    </style>
</head>
<body class="light-mode">
    <div id="theme-toggle">Темна тема</div>
    <div class="container">
        <h1>Редагувати адміністратора</h1>

        <!-- Виводимо помилку, якщо вона є -->
        <?php if (isset($errorMessage)) { echo "<p>$errorMessage</p>"; } ?>

        <form action="/Ins/edit_admin.php?id=<?= $admin['id'] ?>" method="POST">
            <div class="form-group">
                <label for="name">Ім'я:</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($admin['name']) ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Емейл:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($admin['email']) ?>" required>
            </div>

            <div class="form-group">
                <label for="phone_number">Контактний номер:</label>
                <input type="text" id="phone_number" name="phone_number" value="<?= htmlspecialchars($admin['phone_number']) ?>" required>
            </div>

            <div class="form-group">
                <label for="role">Роль:</label>
                <input type="text" id="role" name="role" value="<?= htmlspecialchars($admin['role']) ?>" required>
            </div>

            <button type="submit" class="button">Зберегти зміни</button>
        </form>

        <br>
        <button class="button" onclick="window.location.href='/Ins/admin.php'">Назад до адміністраторів</button>
    </div>

    <script>
        // Перевірка збереженої теми і встановлення при завантаженні сторінки
        if (localStorage.getItem("theme") === "dark") {
            document.body.classList.replace("light-mode", "dark-mode");
            document.getElementById("theme-toggle").textContent = "Світла тема";
        }

        // Перемикання теми та збереження вибору
        const themeToggle = document.getElementById("theme-toggle");
        themeToggle.addEventListener("click", () => {
            document.body.classList.toggle("dark-mode");
            document.body.classList.toggle("light-mode");
            const isDarkMode = document.body.classList.contains("dark-mode");
            themeToggle.textContent = isDarkMode ? "Світла тема" : "Темна тема";
            localStorage.setItem("theme", isDarkMode ? "dark" : "light");
        });
    </script>
</body>
</html>
