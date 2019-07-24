<center>
<div id="tabclient" class="css-tabs">
	<ul class="menu"> 
		<li><a href="index.php?view=home" <?if($_GET['view']=="home")echo "class='activeLink'";?>> <?=$text_home?> </a></li>
		<li><a href="index.php?view=today" <?if($_GET['view']=="today")echo "class='activeLink'";?>> <?=$text_today?> </a></li>
		<li><a href="index.php?view=detail" <?if($_GET['view']=="detail")echo "class='activeLink'";?>> <?=$text_detail?> </a></li>
		<li><a href="index.php?view=history" <?if($_GET['view']=="history")echo "class='activeLink'";?>> <?=$text_history?> </a></li>
		<li><a href="index.php?view=orders" <?if($_GET['view']=="orders")echo "class='activeLink'";?>> <?=$text_orders?> </a></li>
		<li><a href="index.php?view=preferences" <?if($_GET['view']=="preferences")echo "class='activeLink'";?>> <?=$text_preferences?> </a></li>
	</ul> 
</div>


<?
/// Set timezone
date_default_timezone_set('Europe/Kiev');

if ($view=$_GET['view'])
{
	switch ($view)
	{
		case 'home':
			break;
		case 'today':
			include ('today.php');
			break;
		case 'detail':
			include ('detail.php');
			break;
		case 'history':
			include ('graph/index.php');
			break;
		case 'orders':
			include ('orders.php');
			break;
		case 'preferences':
			include ('preferences.php');
			break;
	}
}
?>
</center>
