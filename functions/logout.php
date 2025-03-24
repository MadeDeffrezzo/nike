<?php
session_start(); // Запускаем сессию
session_unset(); // Удаляем все сессионные переменные
session_destroy(); // Уничтожаем сессию

header("Location: ../../page/14.auth/auth.php"); // Перенаправление на страницу входа
exit();
?>