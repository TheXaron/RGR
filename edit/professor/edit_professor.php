<?php
require_once __DIR__ . '/../../config.php';

// Получаем ID профессора из GET запроса
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!$id) {
    header('Location: /Ins/professor.php');  // Перенаправляем на страницу профессоров, если ID не указан
    exit;
}

// Запрос для получения текущих данных профессора
$query = "SELECT * FROM `professor` WHERE id = $id";
$result = $conn->query($query);

if ($result->num_rows === 0) {
    header('Location: /Ins/professor.php');  // Перенаправляем, если профессор с таким ID не найден
    exit;
}

$professor = $result->fetch_assoc();

// Проверка, если форма отправлена
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из формы
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $specialization = filter_input(INPUT_POST, 'specialization', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

    // Проверка, что все поля заполнены
    if ($name && $specialization && $email) {
        // Формируем запрос на обновление данных профессора
        $update_query = "UPDATE `professor` 
                         SET name='$name', specialization='specialization', email='$email' 
                         WHERE id=$id";

        // Выполняем запрос на обновление
        if ($conn->query($update_query) === TRUE) {
            header('Location: /Ins/professor.php');  // Редирект на страницу списка профессоров
            exit;
        } else {
            // Выводим сообщение об ошибке
            $errorMessage = "Ошибка при обновлении данных профессора: " . $conn->error;
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
    <title>Редагувати професора</title>
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
        input[type="text"], input[type="email"] {
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
        <h1>Редагувати професора</h1>

        <!-- Выводим ошибку, если она есть -->
        <?php if (isset($errorMessage)) { echo "<p style='color:red;'>$errorMessage</p>"; } ?>

        <form action="edit_professor.php?id=<?= $professor['id'] ?>" method="POST">
            <div class="form-group">
                <label for="name">Ім'я:</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($professor['name'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="specialization">Спеціальзация:</label>
                <!-- Added a check for department value -->
                <input type="text" id="specialization" name="specialization" value="<?= htmlspecialchars($professor['specialization'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($professor['email'] ?? '') ?>" required>
            </div>

            <button type="submit" class="button">Зберегти зміни</button>
        </form>

        <br>
        <button class="button" onclick="window.location.href='/Ins/professor.php'">Назад до списку професорів</button>
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
