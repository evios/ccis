<center>
<div id="tabclient" class="css-tabs">
	<ul class="menu"> 
		<li><a href="index.php?view=phone" <?if($_GET['view']=="phone")echo "class='activeLink'";?>> Orders from Phone </a></li>
		<li><div id="cc_info" class="top_right_num"></div><a id='site_orders' href="index.php?view=site" <?if($_GET['view']=="site")echo "class='activeLink'";?>> Orders from Site </a></li>
		<li><a href="index.php?view=orders" <?if($_GET['view']=="orders")echo "class='activeLink'";?>> Processed Orders </a></li>
	</ul> 
</div>

<?
/// Set timezone
date_default_timezone_set('Europe/Kiev');

require_once('used_distributors.php');

if ($view=$_GET['view'])
{
	switch ($view)
	{
		case 'phone':
			echo "<form action='search.php' method='get' onsubmit=\"return false;\">";
			echo "<input class='rounded-textbox-search' type=\"text\" onkeyup=\"javascript:ajaxpage('search.php?operator=$operator&q='+this.value, 'contentarea');\" size=100>";
/*			echo "<input name='q' class='rounded-textbox-search' type=\"text\" size=100>";
			echo "<input type='hidden' value='$operator' name='operator'>";
			echo "<input type='submit' value='search'>";
			echo "</form>";
*/
			echo "<div id=\"contentarea\">";
			echo "</div>";
			break;
		case 'site':
			include ('site_orders.php');
			break;
		case 'orders':
			include ('processed_orders.php');
			break;

	}
}

?>
</center>
