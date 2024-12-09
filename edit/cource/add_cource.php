<?php
// Підключаємо файл конфігурації
require_once __DIR__ . '/../../config.php';

header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self';");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $credits = filter_input(INPUT_POST, 'credits', FILTER_SANITIZE_NUMBER_INT);
    $admin_id = filter_input(INPUT_POST, 'admin_id', FILTER_SANITIZE_NUMBER_INT);
    $professor_id = filter_input(INPUT_POST, 'professor_id', FILTER_SANITIZE_NUMBER_INT);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $student_id = filter_input(INPUT_POST, 'student_id', FILTER_SANITIZE_NUMBER_INT);

    if ($name && $credits && $admin_id && $professor_id && $description && $student_id) {
        // Перевіряємо, чи існує вже курс з такою назвою
        $check_query = $conn->prepare("SELECT id FROM cource WHERE name = ?");
        $check_query->bind_param("s", $name);
        $check_query->execute();
        $check_query->store_result();

        if ($check_query->num_rows > 0) {
            $errorMessage = "Курс з такою назвою вже існує!";
        } else {
            // Знаходимо мінімальний відсутній ID, щоб ID залишалися неперервними
            $result = $conn->query("SELECT MIN(t1.id + 1) AS next_id
                                    FROM cource t1
                                    LEFT JOIN cource t2 ON t1.id + 1 = t2.id
                                    WHERE t2.id IS NULL");
            $row = $result->fetch_assoc();
            $new_id = $row['next_id'] ?? 1;

            // Вставляємо новий курс з вибраним ID
            $insert_query = $conn->prepare("INSERT INTO cource (id, name, credits, admin_id, professor_id, description, student_id) 
                                            VALUES (?, ?, ?, ?, ?, ?, ?)");
            $insert_query->bind_param("isiiisi", $new_id, $name, $credits, $admin_id, $professor_id, $description, $student_id);

            try {
                if ($insert_query->execute()) {
                    header('Location: /Ins/cource.php');
                    exit;
                } else {
                    $errorMessage = "Помилка при додаванні курсу: " . $conn->error;
                }
            } catch (mysqli_sql_exception $e) {
                $errorMessage = "Помилка: " . $e->getMessage();
            }
        }
    } else {
        $errorMessage = "Заповніть всі поля!";
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Додати новий курс</title>
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
        <h1>Додати новий курс</h1>

        <!-- Виведення помилки, якщо вона є -->
        <?php if (isset($errorMessage)) { echo "<p>$errorMessage</p>"; } ?>

        <form action="add_cource.php" method="POST">
            <div class="form-group">
                <label for="name">Назва курсу:</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="credits">Кредити:</label>
                <input type="number" id="credits" name="credits" required>
            </div>

            <div class="form-group">
                <label for="admin_id">ID Адміністратора:</label>
                <input type="number" id="admin_id" name="admin_id" required>
            </div>

            <div class="form-group">
                <label for="professor_id">ID Професора:</label>
                <input type="number" id="professor_id" name="professor_id" required>
            </div>

            <div class="form-group">
                <label for="description">Опис курсу:</label>
                <input type="text" id="description" name="description" required>
            </div>

            <div class="form-group">
                <label for="student_id">ID Студента:</label>
                <input type="number" id="student_id" name="student_id" required>
            </div>

            <button type="submit" class="button">Додати курс</button>
        </form>

        <br>
        <button class="button" onclick="window.location.href='/Ins/cource.php'">Назад до курсів</button>
    </div>

    <script>
        // Перевірка поточної теми на основі локального сховища
        if (localStorage.getItem("theme") === "dark") {
            document.body.classList.replace("light-mode", "dark-mode");
            document.getElementById("theme-toggle").textContent = "Світла тема";
        }

        const themeToggle = document.getElementById("theme-toggle");
        themeToggle.addEventListener("click", () => {
            // Перемикання теми між світлою та темною
            document.body.classList.toggle("dark-mode");
            document.body.classList.toggle("light-mode");
            const isDarkMode = document.body.classList.contains("dark-mode");
            themeToggle.textContent = isDarkMode ? "Світла тема" : "Темна тема";
            localStorage.setItem("theme", isDarkMode ? "dark" : "light");
        });
    </script>
</body>
</html>
