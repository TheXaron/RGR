<?php
// Подключаем файл конфигурации
require_once __DIR__ . '/../../config.php';

// Проверка, если форма отправлена
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из формы
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone_number = filter_input(INPUT_POST, 'phone_number', FILTER_SANITIZE_STRING);
    $office_number = filter_input(INPUT_POST, 'office_number', FILTER_SANITIZE_STRING);
    $specialization = filter_input(INPUT_POST, 'specialization', FILTER_SANITIZE_STRING);
    $date_of_hire = filter_input(INPUT_POST, 'date_of_hire', FILTER_SANITIZE_STRING);
    $years_of_experience = filter_input(INPUT_POST, 'years_of_experience', FILTER_SANITIZE_NUMBER_INT);
    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
    $salary = filter_input(INPUT_POST, 'salary', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    // Проверка, что все поля заполнены
    if ($name && $email && $phone_number && $office_number && $specialization && $date_of_hire && $years_of_experience && $status && $salary) {
        // Формируем запрос для добавления нового профессора
        $insert_query = "INSERT INTO professor (name, email, phone_number, office_number, specialization, date_of_hire, years_of_experience, status, salary)
                         VALUES ('$name', '$email', '$phone_number', '$office_number', '$specialization', '$date_of_hire', '$years_of_experience', '$status', '$salary')";

        // Выполняем запрос на добавление
        if ($conn->query($insert_query) === TRUE) {
            header('Location: /Ins/professor.php');  // Редирект на страницу списка профессоров
            exit;
        } else {
            // Выводим сообщение об ошибке
            $errorMessage = "Ошибка при добавлении профессора: " . $conn->error;
        }
    } else {
        $errorMessage = "Заполните все поля!";
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Додати професора</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
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
        input[type="text"], input[type="number"], input[type="email"], input[type="date"], input[type="float"] {
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
        <h1>Додати професора</h1>

        <!-- Выводим ошибку, если она есть -->
        <?php if (isset($errorMessage)) { echo "<p>$errorMessage</p>"; } ?>

        <form action="add_professor.php" method="POST">
            <div class="form-group">
                <label for="name">Ім'я:</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="email">Емейл:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="phone_number">Телефон:</label>
                <input type="text" id="phone_number" name="phone_number" required>
            </div>

            <div class="form-group">
                <label for="office_number">Номер офісу:</label>
                <input type="text" id="office_number" name="office_number" required>
            </div>

            <div class="form-group">
                <label for="specialization">Спеціалізація:</label>
                <input type="text" id="specialization" name="specialization" required>
            </div>

            <div class="form-group">
                <label for="date_of_hire">Дата найму:</label>
                <input type="date" id="date_of_hire" name="date_of_hire" required>
            </div>

            <div class="form-group">
                <label for="years_of_experience">Кількість років досвіду:</label>
                <input type="number" id="years_of_experience" name="years_of_experience" required>
            </div>

            <div class="form-group">
                <label for="status">Статус:</label>
                <input type="text" id="status" name="status" required>
            </div>

            <div class="form-group">
                <label for="salary">Зарплата:</label>
                <input type="number" id="salary" name="salary" step="0.01" required>
            </div>

            <button type="submit" class="button">Додати професора</button>
        </form>

        <br>
        <button class="button" onclick="window.location.href='/Ins/professor.php'">Назад до професорів</button>
    </div>

    <script>
        // Проверка сохраненной темы и установка при загрузке страницы
        if (localStorage.getItem("theme") === "dark") {
            document.body.classList.replace("light-mode", "dark-mode");
            document.getElementById("theme-toggle").textContent = "Світла тема";
        }

        // Переключение темы и сохранение выбора
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
