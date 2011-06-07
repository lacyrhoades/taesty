<?php
namespace Etsy;

use \Etsy\User as User;
use \Etsy\ShopListingsActive as ShopListingsActive;

class ListingSuggestionService
{
  protected $base_url = null;
  protected $api_key = null;
  
  public function __construct($base_url, $api_key)
  {
    $this->base_url = $base_url;
    $this->api_key = $api_key;
  }
  
  public function fetchSuggestedShopsForUsername($username)
  {
    $config = array(
      'api_key'=>$this->api_key,
      'base_url'=>$this->base_url,
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
      'api_key'=>$this->api_key,
      'base_url'=>$this->base_url,
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
  
  protected $database = null;
  
  public function setDatabaseHandle($handle)
  {
    $this->database = $handle;
  }
  
  public function getDatabaseHandle()
  {
    return $this->database;
  }
}
