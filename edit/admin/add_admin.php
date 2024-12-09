<?php
// Підключаємо файл конфігурації
require_once __DIR__ . '/../../config.php';

// Перевірка, чи надіслана форма
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Отримуємо дані з форми
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone_number = filter_input(INPUT_POST, 'phone_number', FILTER_SANITIZE_NUMBER_INT);
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);

    // Перевірка, що всі поля заповнені
    if ($name && $email && $phone_number && $role) {
        // SQL-запит для додавання нового адміністратора
        $insert_query = "INSERT INTO admin (name, email, phone_number, role) VALUES ('$name', '$email', '$phone_number', '$role')";

        // Виконуємо запит на додавання
        if ($conn->query($insert_query) === TRUE) {
            header('Location: /Ins/admin.php');  // Редирект на сторінку списку адміністраторів
            exit;
        } else {
            // Виведення повідомлення про помилку
            $errorMessage = "Помилка при додаванні адміністратора: " . $conn->error;
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
    <title>Додати адміністратора</title>
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
        <h1>Додати адміністратора</h1>

        <!-- Виводимо помилку, якщо вона є -->
        <?php if (isset($errorMessage)) { echo "<p>$errorMessage</p>"; } ?>

        <form action="add_admin.php" method="POST">
            <div class="form-group">
                <label for="name">Ім'я:</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="email">Емейл:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="phone_number">Контактний номер:</label>
                <input type="text" id="phone_number" name="phone_number" required>
            </div>

            <div class="form-group">
                <label for="role">Роль:</label>
                <input type="text" id="role" name="role" required>
            </div>

            <button type="submit" class="button">Додати адміністратора</button>
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
