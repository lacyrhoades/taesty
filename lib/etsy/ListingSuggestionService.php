<?php
namespace Etsy;

use \Etsy\User as User;
use \Etsy\ShopListingsActive as ShopListingsActive;

class ListingSuggestionService
{
  public function fetchSuggestedShopsForUsername($username)
  {
    $config = array(
      'api_key'=>'b66j5zvfu3fg228k8mudecpn',
      'base_url'=>'http://openapi.etsy.com/v2/sandbox',
      'user_id'=>$username,
      'database_handle'=>$this->getDatabaseHandle());

    $user = new User($config);
    
    $results = array();

    foreach ($user->getFavoriteUsers() as $fav_user)
    {
      $details = array();
      $details['login_name'] = $fav_user->getLoginName();
      $details['shop_id'] = $fav_user->getShopId();
      $results[] = $details;
    }
    
    return $results;
  }
  
  public function fetchActiveListingsForShop($shop_id)
  {
    $config = array(
      'api_key'=>'b66j5zvfu3fg228k8mudecpn',
      'base_url'=>'http://openapi.etsy.com/v2/sandbox',
      'shop_id'=>$shop_id,
      'database_handle'=>$this->getDatabaseHandle());

    $shopListingsActive = new ShopListingsActive($config);
    
    $shopListingsActive->setDatabaseHandle($this->getDatabaseHandle());

    $results = array();

    foreach ($shopListingsActive->getListings() as $listing)
    {
      $details = array();
      $details['title'] = $listing->getTitle();
      $details['url'] = $listing->getUrl();
      $details['main_image_src_75x75'] = $listing->getMainImageUrl75x75();
      $details['shop_name'] = $listing->getShopName();
      $results[] = $details;
    }
    
    return $results;
  }
  
  protected function getDatabaseHandle()
  {
    $connection = new \PDO('mysql:host=localhost;dbname=taesty', 'root', 'root');
    
    return $connection;
  }
}
