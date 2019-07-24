<?
///
/// Initialize variables
///
$sample_view='operators';
$detail_view='operator';
$detail_url='index.php?view=operator&';

//$host_DB='.ua';
//$user_DB='cc_user';
//$pass_DB='cc_password';
$db_operators='Operators';
$table='auth';

//$path='/etc/osis/asterisk/devices/phones/';
$path='configs/'; /// !!! FOR TESTS !!! ///
$path_for_include="/home/jenia/www/ccis/admin/configs/";
$file_phones='conf_phones';
$file_conf_phone='conf_phone_';
$field_phone='login';
//$file_extensions='conf_extensions';
//$file_conf_extension='conf_extension_';
//$field_extension='extension';

$template_path='templates/';
//$template_path=''; /// !!! FOR TESTS !!! ///
$template_phones_file='phone_template';

$fields_sample_array = array('login', 'userlevel', 'consultant'); // Fields that shown in bulk view (List all stores)
$main_field='login'; // == $column_names[0]. This is parameter for POST request

/// Variables for MAC Validation
// http://www.usermadetutorials.com/2010/07/php-validate-mac-address-using-regex/
//$validation_pattern = '/^[a-f0-9]{12}$/i'; // 12 symbols, no ':', '.', '-'.  
//$validation_string_length='12';

/// Error Messages
$error_add_message='Error: This Store also present in DB, Please enter another Store Name, or delete this Store.<br>';
//$error_validation='Please Enter Valid MAC Address.<br> 12 symbols, lower case, without any special character.';
$error_case_default='Please Load Page Correctly ( error:store.php -> switch (action) -> case default )';

?>
