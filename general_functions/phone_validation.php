<script type="text/javascript">
function phone_validation($phone)
{
//$error_validation='Please Enter Valid MAC Address.<br> 12 symbols, lower case, without any special character.';
//	$chars = array(" ", "-", "/", "+", ":"); 
//	$right_phone = str_replace($chars, "", $phone); // replace " ", "-", "/", "+", ":" with ""
	$validation_string_length[0]='13';
	$validation_string_length[1]='12';
	$validation_string_length[2]='11';
	$validation_string_length[3]='10';
	$validation_string_length[4]='7';
	/// phone validation (only numbers, and length)
	for($i=0;$i<sizeof($validation_string_length);$i++)
	{
 		$validation_pattern="/^[0-9]{{$validation_string_length[$i]}}/";
		if ( preg_match($validation_pattern, $phone) && strlen($phone)==$validation_string_length[$i])
		{
//			echo "valid";
			return true; 
			break;
		}
	}
}
</script>
