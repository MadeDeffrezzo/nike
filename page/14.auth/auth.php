<?php
session_start();
$conn = new mysqli("localhost", "root", "", "nike");
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']); // Экранируем пароль

    // Проверка пользователя
    $query = "SELECT id, password, role FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        // Проверка пароля в открытом виде
        if ($password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            // Перенаправление в зависимости от роли
            if ($user['role'] === 'admin') {
                header("Location: /page/16.admin/admin.php");
            } else {
                header("Location: /page/15.profile/profile.php"); // Обычные пользователи идут на главную
            }
            exit;
        } else {
            $error = "Неверный пароль!";
        }
    } else {
        $error = "Пользователь с таким email не найден!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/header_footer.css">
    <link rel="stylesheet" href="reg1.css">
    <title>Товар</title>
</head>

<body>
    <header>
        <div class="header_top">
            <div class="top_box">
                <img src="../../img/pngwing 19.png" alt="" class="img1">
                <h3>Укажите регион доставки</h3>
            </div>

            <a href="../7.Delivery/Delivery.php">
                <div class="top_box">
                    <img src="../../img/pngwing 13.png" alt="" class="img2">
                    <h3>Больше способов доставки</h3>
                </div>
            </a>

            <a href="../8.Delivery2/Delivery2.php">
                <div class="top_box">
                    <img src="../../img/pngwing 6.png" alt="" class="img3">
                    <h3>Магазины Nike</h3>
                </div>
            </a>

        </div>

    </header>

    <main>
        <div class="reg">
  
          <form action="" method="POST">
          <h1>Вход</h1>
  
  <div class="lbl">
                  <label for="email">Email</label>
                  <input type="email" name="email" id="email" placeholder="Введите почту" required>
  </div>

  
  <div class="lbl">
                  <label for="password">Пароль</label>
                  <input type="password" name="password" id="password" placeholder="Введите пароль" required>
  </div>
  
              <button type="submit">Войти</button>
              <p>Нет аккаунта? <a href="../13.reg/reg.php">Зарегистрироваться</a></p>
          </form>
        </div>
      </main>

    <footer>
        <div class="footer_top">
            <div class="footer_logo">
                <a href="../../index.php">
                    <img src="../../img/pngwing 14.png" alt="">
                </a>
            </div>

            <div class="footer_nav">
                <div class="footer_nbox">
                    <a href="../2.help/help.php">
                        <h3>Помощь</h3>
                    </a>
                    <ul>
                        <li><a href="">Мои заказы</a></li>
                        <li><a href="../7.Delivery/Delivery.php">Условия доставки</a></li>
                        <li><a href="../10.gift_certificates/gift_certificates.php">Подарочные сертификаты</a></li>
                    </ul>
                </div>

                <div class="footer_nbox">
                    <a href="../1.Onas/onas.php">
                        <h3>О нас</h3>
                    </a>

                    <ul>
                        <li><a href="">Журнал</a></li>
                        <li><a href="">Публичная оферта</a></li>
                        <li><a href="../9.job/job.php">Вакансии</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="footer_bottom">
            <p>© 2023 Nike, Inc. All Rights Reserved</p>
        </div>
    </footer>
</body>

</html>