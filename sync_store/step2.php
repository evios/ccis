<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">

</head>
<body>
<form action="init_store.php" name="step2" method="post">
<b> ШАГ 2 </b>
<br><br>
Добавьте в список товаров товары со следующими кодами и ценами. В наличии не менее 5000 штук:
<table border="1">
<tr>
<td>Код Товара</td>
<td>Цена Товара</td>
</tr>
<?
require_once ('product_codes.php');
for($i=0;$i<sizeof($product);$i++)
{
	echo "<tr><td>".$product[$i][0]."</td><td>".$product[$i][1]."</td></tr>";
}
?>
</table>
<br>
Купите следующие товары (Очень важно чтоб ети значения были именно в полях доставки а не в полях зарегистрированного пользователя, так как доставка может осуществлятся на другой адрес, чем адрес зарегистрированного пользователя):
<table border="1">
<tr>
<td>Код Товара</td>
<td>Цена Товара</td>
<td>Количество</td>
<td>Имя покупателя</td>
<td>Фамилия покупателя</td>
<td>Телефон покупателя</td>
<td>e-mail покупателя</td>
<td>Город доставки</td>
<td>Адрес покупателя</td>
<td>Способ доставки</td>
<td>Примечание</td>

</tr>
<?
$num_to_buy=2;
require_once ('product_codes.php');
for($i=0;$i<$num_to_buy;$i++)
{
	echo "<tr>";
	for($j=0;$j<sizeof($product[$i]);$j++)
	{
		echo "<td>".$product[$i][$j]."</td>";
	}
	echo "</tr>";
}

?>
</table>

<input name="Submit" type=submit value="Перейти к следующему шагу">
</form>
</body>
</html>
