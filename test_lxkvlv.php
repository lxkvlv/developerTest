<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?>

<?

$params = [
    'select' => ['NAME','PROPERTY_COLOR']
];

print_r('<PRE>');
print_r( \DeveloperTest\IB::get( 1, $params) );
print_r('</PRE>');

?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>