<?php
namespace Etsy;

abstract class Etsy
{
  protected $config;
  protected $api_key;
  protected $base_url;
  
  public function __construct($config)
  {
    $this->config = $config;
    
    if (!isset($config['api_key']))
    {
      throw new \Exception('api_key must be supplied in constructor');
    }
    
    $this->api_key = $config['api_key'];
    
    if (!isset($config['base_url']))
    {
      throw new \Exception('base_url must be supplied in constructor');
    }
    
    $this->base_url = $config['base_url'];
    
    $this->setDatabaseHandle($config['database_handle']);
  }
  
  protected $data = null;
  
  public function resetDataCache()
  {
    $this->data = null;
  }
  
  public function getData()
  {
    if (!$this->data)
    {
      $full_url = $this->getFullApiUrl();

      $data = $this->makeCurlRequest($full_url);

      if ($data)
      {
        $this->data = json_decode($data, true);
      }
      else
      {
        $this->data = array();
      }
    }
    
    return $this->data;
  }
  
  protected function makeCurlRequest($url)
  {
    if ($data = $this->lookupCachedResult($url))
    {
      return $data;
    }
    
    $ch = \curl_init(); 
    \curl_setopt($ch, CURLOPT_URL, $url); 
    \curl_setopt($ch, CURLOPT_HEADER, 0); 
    \curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    $data = \curl_exec($ch);
    $status = \intval(curl_getinfo($ch, CURLINFO_HTTP_CODE));
    if ($status != 200)
    {
      throw new \Exception(\sprintf('Error in request to api, HTTP Status: %s for URL: %s', $status, $url));
    }
    \curl_close($ch);
    
    $this->storeCachedResult($url, $data);
    
    return $data;
  }
  
  public function getFullApiUrl()
  {
    $base_url = $this->base_url;
    $relative_url = $this->getRelativeApiUrl();
    $parameters = \http_build_query($this->getApiParameters());

    $full_url =  \sprintf('%s/public%s?%s', $base_url, $relative_url, $parameters);

    return $full_url;
  }
  
  protected $api_parameters = null;
  
  public function getApiParameters()
  {
    if (!$this->api_parameters)
    {
      $this->api_parameters = array();
    }
    
    $this->api_parameters['api_key'] = $this->api_key;
    
    return $this->api_parameters;
  }
  
  public function setApiParameter($key, $value)
  {
    $params = $this->getApiParameters();
    
    $params[$key] = $value;
    
    $this->api_parameters = $params;
  }
  
  abstract public function getRelativeApiUrl();
  
  protected $database = null;
  
  public function setDatabaseHandle($handle)
  {
    $this->database = $handle;
  }
  
  public function getDatabaseHandle()
  {
    return $this->database;
  }
  
  protected function lookupCachedResult($url)
  {
    if (!$this->getDatabaseHandle())
    {
      return false;
    }
    
    $connection = $this->getDatabaseHandle();
    
    $sql = \sprintf('SELECT * FROM url_cache c WHERE c.url LIKE ?');
    
    $statement = $connection->prepare($sql);
    
    $params = array($url);
    
    $statement->execute($params);
    
    if ($statement->rowCount() > 0)
    {
      $results = $statement->fetchAll();
      return $results[0]['response'];
    }
    else
    {
      return false;
    }
  }
  
  protected function storeCachedResult($url, $data)
  {
    if (!$this->getDatabaseHandle())
    {
      return false;
    }
    
    $connection = $this->getDatabaseHandle();
    
    $sql = \sprintf('INSERT INTO url_cache (url, response, created_at) VALUES (?, ?, now())');
    
    $statement = $connection->prepare($sql);
    
    $params = array($url, $data);
    
    $statement->execute($params);
  }
}
