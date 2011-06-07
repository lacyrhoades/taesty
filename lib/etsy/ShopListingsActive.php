<?php
namespace Etsy;

class ShopListingsActive extends Etsy
{
  protected $shop_id = null;
  
  public function __construct($config)
  {
    if (!isset($config['shop_id']) || empty($config['shop_id']))
    {
      throw new \Exception('shop_id must be provided to constructor');
    }
    
    $this->setApiParameter('limit', '2');
    
    $this->shop_id = $config['shop_id'];
    
    parent::__construct($config);
  }
  
  public function getListings()
  {
    $data = $this->getData();
    
    $listings = array();
    
    foreach ($data['results'] as $result)
    {
      $config = $this->config;
      $config['listing_id'] = $result['listing_id'];
      $listings[] = new Listing($config);
    }
    
    return $listings;
  }
  
  public function getRelativeApiUrl()
  {
    return sprintf('/shops/%s/listings/active', $this->shop_id);
  }
}
