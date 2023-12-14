<?php
if (!empty($_POST['exit'])) { // якщо натиснули вийти

    unset($_SESSION['login']); // очищуєм значення даного массиву

    // очищаємо всі змінні сесії
    session_unset();

    // знищуємо сесію до наступного входу
    session_destroy();
    // перезапускаєм сторінку щоб оновити її інакше до оновлення всі дані не переінклудяться і будуть на кучі
    header("Location: " . $_SERVER['PHP_SELF']);
}
?>

<div class="status">
    <div class="statusResult statusResult__access">
        <?= $_SESSION['login']; ?>
    </div>
    <div class="tempMessage" id="tempMess_auth_access">
        <?php
        if (empty($_SESSION['tempTextDisplayed'])) {
            // якщо така сесія порожня, виводимо текст
            // у цьому тексті ми виводим логін . та інформацію про успішну авторизацію чи реєстрацію
            echo 'Вітаємо - ' . $_SESSION['login'] . $_SESSION['enterResult'];
            $_SESSION['tempTextDisplayed'] = true;
        }
        ?>
    </div>
    <form method="post" action="">
        <input type="submit" name="exit" value="вийти" class="changeStatus submit changeStatus__exit">
    </form>
</div>

<!-- завдяки даному скрипту ми видалим вказаний елемент через 3 секунди -->
<script>
    // Пошук елемента за id
    var element = document.getElementById("tempMess_auth_access");

    // Встановлюємо затримку на видалення елемента
    setTimeout(function() {
        // Видаляємо елемент
        element.remove();
    }, 3000); // 3000 мілісекунд = 3 секунди
</script>