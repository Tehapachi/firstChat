<?php
// заборонити кешування браузером
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

$enterLogin = null; // сюди дуде занесено введений юзером логін
$userPassword = null; // в цю змінну буде занесено пароль якщо він співпаде а як ні залишиться null

// підключаєм файл підключення до бд
require_once '../include/connect.php';

// кнопка натиснута ? базове значення ні
$submitActive = false;

// Якщо запит передано методом POST = присвоюєм введені дані змінним
if ($_SERVER['REQUEST_METHOD'] === 'POST') { // перевіряєм чи дані передані нам через метод POST
    $enterLogin = $_POST["login"];
    $enterPassword = $_POST["password"];
    $submitActive = true; // кнопка натиснута
}

$sql = "SELECT * FROM users";
if ($users = $db->query($sql)) { // вибрати з запиту вказаного на строку вище
    foreach ($users as $row) { // звертаємось до елементів таблиці users
        // перебираєм всі дані всіх юзерів
        $userId = $row["id"]; // витягуєм перший елемент тобто id
        $userLogin = $row["login"]; // витягуєм другий елемент
        $userPassword = $row["password"]; // і 3 елемент
        // і так по колу всі елементи таблиці
        if ($userLogin === $enterLogin) {
            break; // перериваєм цикл на логіні який співпав і на елементі таблиці в якому він знаходиться
            //і ми отримуєм його id та пароль у змінні вище до яких ми тепер можемо звертатись та звіряти з ними
        }
    }
}
if ($submitActive === true) { // якщо кнопка натиснута
    // Перевіряєм чи зпівпали Паролі
    if ($enterPassword === $userPassword) { // якщо введений пароль зпівпав з паролем в бд
        session_start();
        $_SESSION['login'] = $enterLogin; // заносим логін в масив сесії

        // заносим результат реєстрації у масив сесії /
        /// для подальшого виводу в повідомленні яке приховається за пару секунд
        $_SESSION['enterResult'] = " ви успішно авторизовані";

        //перенаправляєм юзера на головну
        header('Location: /');
        exit(); // і закриваєм скрипт
    } else {
        $authStatus = "Ви ввели не вірні дані";
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
    <link rel="stylesheet" href="../style/main.css">
    <title>Document</title>
</head>
<body>
<div class="wrapper wrapper__entry">
    <header>
        <form method="post" action="">
            <div class="enter">
                <input class="input loginInput" name="login" maxlength="25" type="text" placeholder="Логін">
                <input class="input passwordInput" name="password" maxlength="25" type="password" placeholder="Пароль">
                <input class="submit" type="submit" value="увійти" name="loginSubmit">
            </div>
            <div class="status">
                <div class="statusResult">
                    <?php
                    if (!empty($authStatus)) { // якщо статус авторизації не є порожнім
                        echo $authStatus; // тоді виводим його
                    }
                    ?>
                </div>
                <a href="reg.php" class="changeStatus">реєстрація </a>
            </div>
        </form>
    </header>
</div>
</body>

