<?php require_once('../bootstrap.php');

$result = array();

$shop_id = $_REQUEST['shop_id'];

$pattern = '/(.+)/';

if (!preg_match($pattern, $shop_id))
{
  $result['error'] = true;
}
else
{
  $result['shop_id'] = $shop_id;
  
  $svc = new \Etsy\ListingSuggestionService();
  
  $connection = new \PDO('mysql:host=localhost;dbname=taesty', $db_user, $db_password);
  $svc->setDatabaseHandle($connection);
  
  try
  {
    $listings = $svc->fetchActiveListingsForShop($shop_id);
  }
  catch (Exception $e)
  {
    $listings = array();
    $result['error'] = true;
  }
  
  $result['results'] = $listings;
}

header('Content-Type: application/json');
echo json_encode($result);