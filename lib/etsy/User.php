<?php
namespace Etsy;

class User extends Etsy
{
  protected $user_id = null;
  
  public function __construct($config)
  {
    if (!isset($config['user_id']) || empty($config['user_id']))
    {
      throw new \Exception('user_id must be provided to constructor');
    }
    
    $this->setApiParameter('includes', 'Shops');
    
    $this->user_id = $config['user_id'];
    
    parent::__construct($config);
  }
  
  public function getLoginName()
  {
    $data = $this->getData();

    return $data['results'][0]['login_name'];
  }
  
  public function getShopId()
  {
    // must we make a fresh request to populate the "shops" data
    if (!isset($data['results'][0]['Shops']))
    {
      // reset the cached data to false
      $this->resetDataCache();

      // add includes = Shops to params list
      $this->setApiParameter('includes', 'Shops');
    }

    $data = $this->getData();

    // have we got a shop?
    if (isset($data['results'][0]['Shops']))
    {
      return $data['results'][0]['Shops'][0]['shop_id'];
    }
    else
    {
      return false;
    }
  }
  
  public function getShopListingsActive()
  {
    $config = $this->config;
    unset($config['user_id']);
    $config['shop_id'] = $this->getShopId();
    $shopListingsActive = new ShopListingsActive($config);
    return $shopListingsActive;
  }
  
  public function getFavoriteUsers()
  {
    $config = $this->config;
    $favorites = new FavoriteUser($config);
    
    $users = $favorites->findAllUserFavoriteUsers();
    
    return $users;
  }
  
  public function getRelativeApiUrl()
  {
    return sprintf('/users/%s', $this->user_id);
  }
}
