<?php
namespace Etsy;

class Listing extends Etsy
{
  protected $listing_id = null;
  
  public function __construct($config)
  {
    if (!isset($config['listing_id']) || empty($config['listing_id']))
    {
      throw new \Exception('listing_id must be provided to constructor');
    }
    
    $this->listing_id = $config['listing_id'];
    
    // telling the object to query for the MainImage, Shop data every time
    $this->setApiParameter('includes', 'Shop,MainImage');
    
    parent::__construct($config);
  }
  
  public function getMainImageUrl75x75()
  {
    $data = $this->getData();

    return $data['results'][0]['MainImage']['url_75x75'];
  }

  public function getTitle()
  {
    $data = $this->getData();
    
    return $data['results'][0]['title'];
  }

  public function getUrl()
  {
    $data = $this->getData();
    
    return $data['results'][0]['url'];
  }
  
  public function getShopName()
  {
    $data = $this->getData();
    
    return $data['results'][0]['Shop']['shop_name'];
  }
  
  public function getRelativeApiUrl()
  {
    return sprintf('/listings/%s', $this->listing_id);
  }
}
