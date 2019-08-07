<?php


namespace ViewDNS;

use http\Exception\InvalidArgumentException;

/**
 * Class API
 * @package ViewDNS
 * use:
 * $api = new API('<KEY>');
 * $api->reverseIPLookup('172.217.4.100');
 */
class API
{
  /**
   * VERSION
   *
   * Stores the version of API
   * use $api->version()
   */
  const VERSION = '1.0.0';
  
  const VIEWDNS_URL = 'https://api.viewdns.info';
  const DNSRECORD = 'dnsrecord';
  const REVERSEIP = 'reverseip';
  
  protected $output = 'json';
  protected $apiKey;
  
  /**
   * API constructor.
   *
   * @param $apiKey
   *
   * @param string $output
   *
   * @throws InvalidArgumentException
   *
   */
  public function __construct($apiKey, $output = 'json')
  {
    if (empty($apiKey)) {
      throw new InvalidArgumentException('apiKey is required');
    }
    $this->apiKey = $apiKey;
    
    if(!empty($output)) {
      $this->output = $output;
    }
  }
  
  /**
   * version
   *
   * gets the API version
   *
   * @return VERSION
   */
  public function version()
  {
    return self::VERSION;
  }
  
  /**
   * dnsRecordLookup - returns all configured DNS records (A, MX, CNAME etc.) for a specified domain name.
   *
   * @param $domain
   * @param string $recordType
   *
   * @throws Exception
   *
   * @return string - response returned from ViewDNS server
   */
  public function dnsRecordLookup($domain, $recordType = 'A')
  {
    $dnsRecordLookupUrl =  $this->buildUrl(self::DNSRECORD, [
      'domain' => $domain,
      'recordtype' => $recordType
    ]);
    
    $data = file_get_contents($dnsRecordLookupUrl);
    
    if($data === false) {
      throw new Exception("Can't get data from ViewDNS server");
    }
    
    return $data;
  }
  
  /**
   * dnsRecordLookup - for a domain or IP address and returns all other domains hosted from the same server.
   * By default, the first 10,000 results are returned.
   *
   * @param $host
   *
   * @param string $recordType
   *
   * @return string - response returned from ViewDNS server
   */
  public function reverseIPLookup($host)
  {
    $reverseIPLookupUrl =  $this->buildUrl(self::REVERSEIP, [
      'host' => $host,
    ]);
    
    $data = file_get_contents($reverseIPLookupUrl);
    
    if($data === false) {
      throw new Exception("Can't get data from ViewDNS server");
    }
    
    return $data;
  }
  
  protected function buildUrl($uri, array $params)
  {
    $queryParams = array_merge($params, [
      'apikey' => $this->apiKey,
      'output' => $this->output
    ]);
    
    return self::VIEWDNS_URL . '/' . $uri . '/?' . http_build_query($queryParams);
  }
}