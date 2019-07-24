<?
///
/// Initialize variables
///
$sample_url='list.php';
$detail_url='detail_view.php';

$db_DB='Stores';
$table='List';

$fields_sample_array = array('mac', 'extension', 'username', 'state'); // Fields that shown in bulk view (List all devices, users, phones, trunks ...)
$main_field='mac'; // == $column_names[0]. This is parameter for POST request

/// Variables for MAC Validation
// http://www.usermadetutorials.com/2010/07/php-validate-mac-address-using-regex/
$validation_pattern = '/^[a-f0-9]{12}$/i'; // 12 symbols, no ':', '.', '-'.  
$validation_string_length='12';

/// Error Messages
$error_add_message='Error: This MAC address also present in DB, Please enter another MAC address, or delete this MAC address.<br>';
$error_validation='Please Enter Valid MAC Address.<br> 12 symbols, lower case, without any special character.';
$error_case_default='Please Load Page Correctly ( error:phone.php -> switch (action) -> case default )';

?>
