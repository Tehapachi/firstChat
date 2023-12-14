<?php
// заборонити кешування браузером
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

// підключаєм файл підключення до бд
require_once '../include/connect.php';

// в цій змінній буде занесена помилка на якій спотикнеться процес
$errorStatus = null;

// змінна для перевірки чи логін вільний
// базово встановлено true на випадок якщо база даних ще пуста і не виконається умова по перевірці в базі нище
$checkLoginFree = true;

// в цю змінну буде занесено введений юзером логін
$regLogin = null;

// в цю змінну буде занесено значення при успішній реєстрації
$regAccess = null;

function test_input($data)
{ // функція перевіряє введене значення і виконує наступні дії:
    $data = trim($data); // обрізка пробілів з обох країв тексту
    $data = stripslashes($data); // видаляє слеши (\), які можуть бути додані до введеної строки для захисту від виконання атак на безпеку.
    $data = htmlspecialchars($data); // - перетворює спеціальні символи, такі як < та >, у спеціальні коди, такі як &lt; та &gt;, щоб запобігти виконанню відібраного HTML-коду.

    if (preg_match("/[~`!#$%\^&*+=\-\[\]\\';,\/{}|\\\":<>\?]/", $data) || preg_match("/\s/", $data)) {
        $data = null; // ліквідуєм змінну яку будем перевіряти в майбутньому
    }
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") { // перевіряєм чи дані передані нам через метод POST
    $regLogin = test_input($_POST["login"]);
    $regPassword = test_input($_POST["password"]);
}

$sql = "SELECT * FROM users";
if ($users = $db->query($sql)) { // вибрати з запиту вказаного на строку вище
    foreach ($users as $row) { // звертаємось до елементів таблиці users
        // перебираєм всі дані всіх юзерів
        $userLogin = $row["login"]; // витягуєм другий елемент
        // і так по колу всі елементи таблиці
        if ($userLogin === $regLogin) {
            $errorStatus = "Даний логін вже зайнятий";
            $checkLoginFree = false; // логін зайнятий
            break; // перериваєм цикл на логіні який співпав
        } else {
            $checkLoginFree = true; // логін вільний
        }
    }
}
// перевіряємо дані введені юзером
if (isset($_POST["loginSubmit"])) { // якщо кнопка форми натиснута
// тоді зпрацьовують наступні перевірки >

// перевіряєм чи введено логін та пароль
    if (empty($regLogin) || empty($regPassword)) { // якщо порожні
        $errorStatus = "Ви невказали дані чи використали недопустимі символи"; // заносим даний текст в статус помилок і перевірки закінчуються тут
    } else { //якщо в них є якісь дані переходим до наступних перевірок >
// інакше якщо { довжина введених символів менша за 5 символів
        if (strlen($regLogin) < 3 || strlen($regPassword) < 3) {
            $errorStatus = "Логін та пароль повинні містити мінімум 4 символів."; // заносим в змінну текст помилки і стоп
        } // якщо ж їх довжина більше 5 символів то ок йдем далі >
        else {
            // якщо є символи які не входять до до діапану латинських літер
            if (preg_match('/[^\x20-\x7f]/', $regLogin) || preg_match('/[^\x20-\x7f]/', $regPassword)) { // (\x20-\x7f) це діапазон лат
                $errorStatus = 'Ви можете використовувати тільки латинські літери.'; // заносим текст помилки
//якщо ж все ок то йдемо далі >
            } elseif ($checkLoginFree) { // якщо все ок і якщо змінна для перевірки чи вільний логін true
                // заносим дані в базу  реєстрація пройшла успішно
                mysqli_query($db, "INSERT INTO `users` (`id`, `login`, `password`) VALUES (NULL, '$regLogin', '$regPassword')");

                // запускаєм сесію
                session_start();

                // заносим логін в масив сесії login
                $_SESSION['login'] = $regLogin;

                // заносим результат реєстрації у масив сесії /
                /// для подальшого виводу в повідомленні яке приховається за пару секунд
                $_SESSION['enterResult'] = " ви успішно зареєстровані";

                //перенаправляєм юзера на головну
                header('Location: /');
                exit(); // і закриваєм скрипт
            }
        }
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
                <input class="submit" type="submit" value="зареєструватись" name="loginSubmit">
            </div>
            <div class="status">
                <a href="auth.php" class="changeStatus"> авторизація </a>
                <div class="statusResult">
                    <?php // тут буде виводитись Статус помилок якщо вони виникнуть
                    if ($errorStatus !== null) { // якщо змінна не null
                        echo $errorStatus; // виводим її значення на екран
                    }

                    // тут буде виводитись Статус Реєстрації
                    if ($regAccess !== null) { // якщо змінна не null
                        echo "$regAccess"; // виводим її значення на екран
                    }
                    ?>
                </div>
            </div>
        </form>
    </header>
</div>
</body>

