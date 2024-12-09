<?php
// Подключаем файл конфигурации
require_once __DIR__ . '/../../config.php';  

// Получаем ID курса, который нужно редактировать
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!$id) {
    header('Location: /Ins/cource.php');
    exit;
}

// Запрос для получения текущих данных курса
$query = "SELECT * FROM cource WHERE id = $id";
$result = $conn->query($query);
if ($result->num_rows === 0) {
    header('Location: /Ins/cource.php');
    exit;
}

$cource = $result->fetch_assoc();

// Обработка отправки формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из формы
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $credits = filter_input(INPUT_POST, 'credits', FILTER_SANITIZE_NUMBER_INT);
    $admin_id = filter_input(INPUT_POST, 'admin_id', FILTER_SANITIZE_NUMBER_INT);
    $professor_id = filter_input(INPUT_POST, 'professor_id', FILTER_SANITIZE_NUMBER_INT);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $student_id = filter_input(INPUT_POST, 'student_id', FILTER_SANITIZE_NUMBER_INT);

    // Проверка, что все поля заполнены
    if ($name && $credits && $admin_id && $professor_id && $description && $student_id) {
        // Формируем запрос на обновление данных курса
        $update_query = "UPDATE cource SET 
                         name='$name', credits=$credits, admin_id=$admin_id, professor_id=$professor_id, 
                         description='$description', student_id=$student_id WHERE id=$id";

        // Выполняем запрос на обновление
        if ($conn->query($update_query) === TRUE) {
            // Перенаправляем на страницу курса
            header('Location: /Ins/cource.php');
            exit;
        } else {
            // Выводим сообщение об ошибке
            $errorMessage = "Ошибка при обновлении данных курса: " . $conn->error;
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
    <title>Редагувати курс</title>
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
        input[type="text"], input[type="number"] {
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
        <h1>Редагувати курс</h1>

        <!-- Выводим ошибку, если она есть -->
        <?php if (isset($errorMessage)) { echo "<p>$errorMessage</p>"; } ?>

        <form action="edit_cource.php?id=<?= $cource['id'] ?>" method="POST">
            <div class="form-group">
                <label for="name">Ім'я курсу:</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($cource['name']) ?>" required>
            </div>

            <div class="form-group">
                <label for="credits">Кредитів:</label>
                <input type="number" id="credits" name="credits" value="<?= htmlspecialchars($cource['credits']) ?>" required>
            </div>

            <div class="form-group">
                <label for="admin_id">ID Адміна:</label>
                <input type="number" id="admin_id" name="admin_id" value="<?= htmlspecialchars($cource['admin_id']) ?>" required>
            </div>

            <div class="form-group">
                <label for="professor_id">ID Професора:</label>
                <input type="number" id="professor_id" name="professor_id" value="<?= htmlspecialchars($cource['professor_id']) ?>" required>
            </div>

            <div class="form-group">
                <label for="description">Опис:</label>
                <input type="text" id="description" name="description" value="<?= htmlspecialchars($cource['description']) ?>" required>
            </div>

            <div class="form-group">
                <label for="student_id">ID Студента:</label>
                <input type="number" id="student_id" name="student_id" value="<?= htmlspecialchars($cource['student_id']) ?>" required>
            </div>

            <button type="submit" class="button">Зберегти зміни</button>
        </form>

        <br>
        <button class="button" onclick="window.location.href='/Ins/cource.php'">Назад до курсів</button>
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
