<?php

// запускаєм сесію
session_start();

// заборонити кешування браузером
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

// підключаєм файл підключення до бд
require_once 'include/connect.php';

// сюди буде занесено логін
$author = null;

// сюди буде занесено повідомлення
$message = null;

// тут будуть відловлюватись повідомлення про помилки
$errorMessStatus = null;

// перевіряєм чи дані передані нам через метод POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $message = $_POST['message'];

    // якщо сесія логіну порожня
    if (empty($_SESSION['login'])) {
        $author = 'гість'; // то автором повідомлення буде гість
    } else { // інакше автором буде логін автора
        $author = $_SESSION['login'];
    }

    if (!empty($message)) {
        mysqli_query($db, "INSERT INTO `posts` (`id`, `author`, `content`) VALUES (NULL, '$author', '$message')");
    } else {
        $errorMessStatus = " Ви не вказали текст повідомлення ";
    }
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="style/main.css">
    <title>Document</title>
</head>
<body>
<div class="wrapper">
    <header>
        <?php
        if (empty($_SESSION['login'])) {
            echo " <div class=\"enter\">
                        <a href=\"pages/auth.php\" class=\"entry entry__login\">увійти</a>
                        <a href=\"pages/reg.php\" class=\"entry entry__singUp\">зареєструватись</a>
                   </div> ";
        } else {
            include 'include/sessionTrue.php';
        }
        ?>
    </header>
    <main>
        <?php

        // SQL запит на вибірку даних
        $sql = "SELECT id, author, content FROM posts ORDER BY id DESC LIMIT 8";
        $result = $db->query($sql);

        // Перевірка наявності результатів запиту
        if ($result->num_rows > 0) {
            // Вивід даних у окремі div-елементи
            while ($row = $result->fetch_assoc()) {
                echo "<div class=\"message\">";
                echo "<div class=\"authorMessage\">" . $row["author"] . "</div>";
                echo "<div class=\"messageContent\">" . $row["content"] . "</div>";
                echo "</div>";
            }
        } else {
            echo "0 results";
        }

        // Закриття підключення до бази даних
        $db->close();

        ?>
    </main>
    <footer>
        <div class="addMessage">
            <form method="post" action="">
                <textarea class="addMessage_text" name="message" placeholder="Введіть текст"></textarea>
                <input class="addMessage_input" type="submit" name="addMessage">
                <div class="messError">
                    <?= $errorMessStatus ?>
                </div>
            </form>
        </div>
    </footer>
</div>
</body>
</html>
