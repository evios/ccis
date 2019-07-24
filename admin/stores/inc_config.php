<?
///
/// Initialize variables
///
$sample_view='stores';
$detail_view='store';
$detail_url='index.php?view=store&';

//$host_DB='madek-dc.madek.ua';
//$user_DB='cc_user';
//$pass_DB='cc_password';
$db_stores='Stores';
$table='List';
$db_operators='Operators';
$table_operators='auth';

//$path='/etc/osis/asterisk/devices/phones/';
$path='configs/'; /// !!! FOR TESTS !!! ///
$path_for_include="/home/jenia/www/ccis/admin/configs/";
$file_queues='conf_queues';
$file_conf_queue='conf_queue_';
$file_extensions='conf_extensions';
$file_conf_extension='conf_extension_';
//$field_queue='name';
//$file_extensions='conf_extensions';
//$file_conf_extension='conf_extension_';
//$field_extension='extension';

$template_path='templates/';
$template_queue_file='queue_template';
$template_extension_file='extension_template';
$template_queue_operator_file='queue_operator_template';
$template_extension_operator_file='extension_operator_template';

$fields_sample_array = array('name', 'fullName', 'url', 'price_update_date', 'orders_update_date'); // Fields that shown in bulk view (List all stores)
$main_field='name'; // == $column_names[0]. This is parameter for POST request
$pstnNumber_field='lineID';

/// Variables for MAC Validation
// http://www.usermadetutorials.com/2010/07/php-validate-mac-address-using-regex/
//$validation_pattern = '/^[a-f0-9]{12}$/i'; // 12 symbols, no ':', '.', '-'.  
//$validation_string_length='12';

/// Error Messages
$error_add_message='Error: This Store also present in DB, Please enter another Store Name, or delete this Store.<br>';
//$error_validation='Please Enter Valid MAC Address.<br> 12 symbols, lower case, without any special character.';
$error_case_default='Please Load Page Correctly ( error:store.php -> switch (action) -> case default )';

?>
