<?php
namespace Etsy;

class FavoriteUser extends Etsy
{
  protected $user_id = null;
  
  public function __construct($config)
  {
    if (!isset($config['user_id']) || empty($config['user_id']))
    {
      throw new \Exception('user_id must be provided to constructor');
    }
    
    $this->setApiParameter('limit', '10');
    
    $this->user_id = $config['user_id'];
    
    parent::__construct($config);
  }
  
  public function findAllUserFavoriteUsers()
  {
    $data = $this->getData();
    
    $users = array();
    
    foreach ($data['results'] as $result)
    {
      $config = $this->config;
      $config['user_id'] = $result['target_user_id'];
      $users[] = new User($config);
    }
    
    return $users;
  }
  
  public function getRelativeApiUrl()
  {
    return sprintf('/users/%s/favorites/users', $this->user_id);
  }
}
