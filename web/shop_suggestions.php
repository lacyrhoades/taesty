<?php require_once('../bootstrap.php');

$result = array();

$username = $_REQUEST['username'];

$pattern = '/(.+)/';

if (!preg_match($pattern, $username))
{
  $result['error'] = true;
}
else
{
  $result['username'] = $username;
  
  $svc = new \Etsy\ListingSuggestionService();
  
  try
  {
    $shops = $svc->fetchSuggestedShopsForUsername($username);
  }
  catch (Exception $e)
  {
    $shops = array();
    $result['error'] = true;
  }
  
  $result['results'] = $shops;
}

header('Content-Type: application/json');
echo json_encode($result);