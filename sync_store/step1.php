<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">

</head>
<body>
<form action="step2.php" name="step1" method="post">
<b> ШАГ 1 </b>
<br><br>
Создайте пользователя <b>db_osis</b>, и дайте ему права <b>SELECT</b> с адреса <b><?=$_SERVER['SERVER_ADDR']?></b> на Базу Данных Вашего Интернет-Магазина. <b>Пароль</b> не менее 8 символов, и должен содержать буквы верхнего и нижнего реестра, цифры и знаки.<br><br>
<small>Права Select не позволят пользователю вносить какие либо изменения в Вашу БД.</small>
<br><br>
<input name="Submit" type=submit value="Перейти к следующему шагу">
</form>



</body>
</html>
