<?php
class UPS{
  private $api = '3D508DDD58A1030C';
  private $username = 'msafetyp';
  private $password = 'Redwings#1';
  private $account = 'V7V895';

  public $from = array(
    'address_1' => '8640 Commerce Ct',
    'city'      => 'Harbor Springs',
    'state'     => 'MI',
    'postal'    => '49740',
    'country'   => 'US',
  );

  public $access_request;

  public $time_in_transit_path = array(
    'test' => 'https://wwwcie.ups.com/ups.app/xml/TimeInTransit',
    'production' => 'https://onlinetools.ups.com/ups.app/xml/TimeInTransit'
  );

  public function __construct(){
    $this->access_request = $this->get_access_request();
  }

  public function time_in_transit( $ship_to ){
    $time_in_transit_request = new SimpleXMLElement('<TimeInTransitRequest></TimeInTransitRequest>');
    $time_in_transit_request->addChild( 'Request' );
    $time_in_transit_request->Request->addChild( 'TransactionReference', 'greg' );
    $time_in_transit_request->Request->addChild( 'RequestAction', 'TimeInTransit' );

    $from = new SimpleXMLElement('<TransitFrom></TransitFrom>');
    $from->addChild( 'AddressArtifactFormat' );
    $from->AddressArtifactFormat->addChild( 'StreetName', $this->from['address_1'] );
    $from->AddressArtifactFormat->addChild( 'PostcodePrimaryLow', $this->from['postal'] );
    $from->AddressArtifactFormat->addChild( 'CountryCode', $this->from['country'] );

    $to = new SimpleXMLElement('<TransitTo></TransitTo>');
  	$to->addChild( 'AddressArtifactFormat' );
  	$to->AddressArtifactFormat->addChild( 'StreetName', $ship_to['street'] );
  	$to->AddressArtifactFormat->addChild( 'PostcodePrimaryLow', $ship_to['postal'] );
  	$to->AddressArtifactFormat->addChild( 'CountryCode', $ship_to['country'] );

    $this->append( $time_in_transit_request, $from );
    $this->append( $time_in_transit_request, $to );

    $time_in_transit_request->addChild( 'PickupDate', date('Ymd') );
    $requestXML = $this->access_request->asXML() . $time_in_transit_request->asXML();
    $response = $this->send( $this->time_in_transit_path['test'], $requestXML );
    return $response;
  }

  public function get_access_request(){
    $accessRequest = new SimpleXMLElement('<AccessRequest></AccessRequest>');
    $accessRequest->addChild( 'AccessLicenseNumber', $this->api );
    $accessRequest->addChild( 'UserId', $this->username );
    $accessRequest->addChild( 'Password', $this->password );

    return $accessRequest;
  }

  function append(SimpleXMLElement $to, SimpleXMLElement $from) {
  	// https://stackoverflow.com/questions/4778865/php-simplexml-addchild-with-another-simplexmlelement
  	// LIFESAVER ^^^
      $toDom = dom_import_simplexml($to);
      $fromDom = dom_import_simplexml($from);
      $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
  }

  public function send( $url, $xml = '', $convert = true ){
    try{
        $ch = curl_init();
        if ($ch === false) {
          throw new Exception('failed to initialize');
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        // uncomment the next line if you get curl error 60: error setting certificate verify locations
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        // uncommenting the next line is most likely not necessary in case of error 60
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3600);
        $content = curl_exec($ch);
        // Check the return value of curl_exec(), too
        if ($content === false) {
            throw new Exception(curl_error($ch), curl_errno($ch));
        }
        if( $convert == true ){
          /* Process $content here */
          $xml = simplexml_load_string($content, "SimpleXMLElement", LIBXML_NOCDATA);
          $json = json_encode($xml);
          $content = json_decode($json,TRUE);
        }
        return $content;
        // Close curl handle
        curl_close($ch);
      } catch(Exception $e) {
      trigger_error(sprintf(
          'Curl failed with error #%d: %s',
          $e->getCode(), $e->getMessage()),
          E_USER_ERROR);
    }
  }
}
