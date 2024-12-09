<?php
require_once __DIR__ . '/../../config.php';

// Получаем ID студента из GET запроса
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!$id) {
    header('Location: /Ins/student.php');
    exit;
}

// Запрос для получения текущих данных студента
$query = "SELECT * FROM student WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: /Ins/student.php');
    exit;
}

$student = $result->fetch_assoc();
$stmt->close();

// Проверка, если форма отправлена
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из формы
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $phone_number = filter_input(INPUT_POST, 'phone_number', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $major = filter_input(INPUT_POST, 'major', FILTER_SANITIZE_STRING);
    $date_of_birth = filter_input(INPUT_POST, 'date_of_birth', FILTER_SANITIZE_STRING);
    $GPA = filter_input(INPUT_POST, 'GPA', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
    $current_year = filter_input(INPUT_POST, 'current_year', FILTER_SANITIZE_NUMBER_INT);
    $year_of_enrollment = filter_input(INPUT_POST, 'year_of_enrollment', FILTER_SANITIZE_NUMBER_INT);

    // Проверка, что все поля заполнены
    if ($name && $phone_number && $email && $major && $date_of_birth && $GPA && $address && $status && $current_year && $year_of_enrollment) {
        // Формируем запрос на обновление данных студента
        $update_query = "UPDATE student SET name = ?, phone_number = ?, email = ?, major = ?, date_of_birth = ?, GPA = ?, address = ?, status = ?, current_year = ?, year_of_enrollment = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("sssssdssiii", $name, $phone_number, $email, $major, $date_of_birth, $GPA, $address, $status, $current_year, $year_of_enrollment, $id);

        // Выполняем запрос на обновление
        if ($stmt->execute()) {
            header('Location: /Ins/student.php');  // Редирект на страницу студентов
            exit;
        } else {
            // Выводим сообщение об ошибке
            $errorMessage = "Ошибка при обновлении данных студента: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $errorMessage = "Заполните все поля!";
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Редагувати студента</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        /* Стили для светлой и темной темы */
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
        <h1>Редагувати студента</h1>

        <!-- Выводим ошибку, если она есть -->
        <?php if (isset($errorMessage)) { echo "<p>$errorMessage</p>"; } ?>

        <form action="edit_student.php?id=<?= $student['id'] ?>" method="POST">
            <div class="form-group">
                <label for="name">Ім'я студента:</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($student['name']) ?>" required>
            </div>

            <div class="form-group">
                <label for="phone_number">Телефон:</label>
                <input type="text" id="phone_number" name="phone_number" value="<?= htmlspecialchars($student['phone_number']) ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Електронна пошта:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" required>
            </div>

            <div class="form-group">
                <label for="major">Спеціальність:</label>
                <input type="text" id="major" name="major" value="<?= htmlspecialchars($student['major']) ?>" required>
            </div>

            <div class="form-group">
                <label for="date_of_birth">Дата народження:</label>
                <input type="date" id="date_of_birth" name="date_of_birth" value="<?= htmlspecialchars($student['date_of_birth']) ?>" required>
            </div>

            <div class="form-group">
                <label for="GPA">Бал НМТ:</label>
                <input type="number" step="0.01" id="GPA" name="GPA" value="<?= htmlspecialchars($student['GPA']) ?>" required>
            </div>

            <div class="form-group">
                <label for="address">Адреса:</label>
                <input type="text" id="address" name="address" value="<?= htmlspecialchars($student['address']) ?>" required>
            </div>

            <div class="form-group">
                <label for="status">Статус:</label>
                <input type="text" id="status" name="status" value="<?= htmlspecialchars($student['status']) ?>" required>
            </div>

            <div class="form-group">
                <label for="current_year">Поточний рік:</label>
                <input type="number" id="current_year" name="current_year" value="<?= htmlspecialchars($student['current_year']) ?>" required>
            </div>

            <div class="form-group">
                <label for="year_of_enrollment">Рік вступу:</label>
                <input type="number" id="year_of_enrollment" name="year_of_enrollment" value="<?= htmlspecialchars($student['year_of_enrollment']) ?>" required>
            </div>

            <button type="submit" class="button">Зберегти зміни</button>
        </form>

        <br>
        <button class="button" onclick="window.location.href='/Ins/student.php'">Назад до студентів</button>
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
