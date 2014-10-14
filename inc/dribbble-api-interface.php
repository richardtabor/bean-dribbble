<?php 
/**
 * Define wrapper functions and their global aliases for interfacing with the
 * Dribbble API
 *
 */

class Bean_Dribbble_API_Interface {

  private static $apiBaseEndpoint = "https://api.dribbble.com/v1";
  private static $usersSlug = "/users";
  private static $shotsSlug = "/shots";

  private static $cacheKey = "bean-dribbble-requests-cache";
  private static $cacheTime = 3600; // seconds




  /**
   * Cache the response in the database
   * 
   * @param  string $endpointSlug The endpoint the response was received from
   * @param  string $accessToken  The access token used for the request
   * @param  string $response     The raw response string
   * @return boolean              True upon successful caching; false otherwise
   */
  private static function cacheRequestResponse( $endpointSlug,
                                                $accessToken,
                                                $response ) {
    // only cache successful request responses
    if ( 0 != $response['errorNo'] ) {
      if ( 200 != $response['responseCode'] ) {
        return false;
      }
    }

    $object = array(
      "accessToken"   => $accessToken,
      "response"      => $response
    );

    // retrieve the saved cache object
    $cacheObject = get_transient( self::$cacheKey );
    $cacheObject = $cacheObject === false ? array() : $cacheObject;

    // update the cache object
    $cacheObject[ $endpointSlug ] = $object;

    // save the updated cache object
    return set_transient( self::$cacheKey, $cacheObject, self::$cacheTime );
  }




  /**
   * Retrieve the possibly cached response from the database
   * 
   * @param  string $endpointSlug The endpoint to retrieve the cache of
   * @param  string $accessToken  The access token that belongs to the cache
   * @return mixed                Raw response data string; false if not found
   */
  private static function retrieveCachedRequest( $endpointSlug,
                                                 $accessToken ) {
    // retrieve the saved cache object
    $cacheObject = get_transient( self::$cacheKey );

    // check if the cache object was not returned successfully
    if ( $cacheObject === false ) {
      return false;
    }

    // check if the current request is available in the cache object
    if ( isset( $cacheObject[ $endpointSlug ] ) ) {
      $cachedResponse = $cacheObject[ $endpointSlug ];

      // assert the $cachedResponse object
      if ( !isset( $cachedResponse['accessToken'] ) || 
           !isset( $cachedResponse['response'] )
      ) {
        return false;
      }

      if ( $accessToken === $cachedResponse['accessToken'] ) {
        return $cachedResponse['response'];
      }
    }

    // cache could not be retrieved
    return false;
  }




  /**
   * Send the request to a dribbble API endpoint with the provided access token
   * Automatically retrieve and return the cached response if found
   * 
   * @param  string  $endpointSlug The endpoint slug to send the request to
   * @param  string  $accessToken  The access token to use for the request
   * @param  boolean $forceNoCache Set to true to not use cache
   * @return array                 The response metadata and payload
   */
  private static function sendRequest($endpointSlug,
                                      $accessToken,
                                      $forceNoCache = false,
                                      $nobody = false) {

    if ( !$forceNoCache ) {
      // try to retrieve from cache and return that if available and valid
      $cachedRes = self::retrieveCachedRequest( $endpointSlug, $accessToken );
      if ( $cachedRes ) {
        return $cachedRes;
      }
    }

    // prepare the request
    $ch = curl_init();

    // set the request options
    $options = array(
      CURLOPT_URL             => self::$apiBaseEndpoint . $endpointSlug,
      CURLOPT_RETURNTRANSFER  => true,
      CURLOPT_NOBODY          => $nobody,
      CURLOPT_HTTPHEADER      => array(
                                  'Authorization: Bearer ' . 
                                    $accessToken
                                 )
    );
    curl_setopt_array($ch, $options);

    // send the request
    $result = curl_exec($ch);

    $returnObject = array();

    // check for any errors
    if ( !curl_errno($ch) ) {
      $info = curl_getinfo($ch);

      // prepare the return value
      $returnObject = array( "result"       => $result,
                             "responseCode" => $info["http_code"],
                             "errorNo"        => 0
                      );
    } else {
      // prepare the return value
      $returnObject = array( "errorNo" => curl_errno($ch),
                             "error"   => curl_error($ch)
                      );
    }

    // close the session
    curl_close($ch);

    // save in cache
    self::cacheRequestResponse( $endpointSlug,
                                $accessToken,
                                $returnObject ); // updating expired cache

    return $returnObject;
  }




  /**
   * Check to see if the access token is formatted correctly
   * 
   * @param  string $accessToken The access token
   * @return boolean             true or false
   */
  private static function validateAccessToken($accessToken) {
    /* We could also check for the length of access token but there doesn't
     * seem to be any concensus on that.
     */
    return is_string($accessToken);
  }




  /**
   * Ping the shots endpoint with the provided access token to check
   * to see if the response code is 200
   * 
   * @param  string $accessToken The access token
   * @return boolean             true or false
   */
  public static function verifyAccessToken($accessToken) {
    if ( empty($accessToken) ) return false;

    if ( self::validateAccessToken( $accessToken ) ) {
      $req_response = self::sendRequest( self::$shotsSlug,
                                         $accessToken,
                                         true,
                                         true );

      // check to see if the request was successfull
      if ( 0 == $req_response['errorNo']) {
        if ( 200 == $req_response['responseCode'] ) {
          return true;
        }
      }
    }

    return false;
  }




  /**
   * Hit the user's shots endpoint to retrieve the shots and return them
   * wrapped in a PHP array
   * 
   * @param  string $username    The username to retrieve the shots of
   * @param  string $accessToken The access token to use for the request
   * @param  int    $shotsCount  Number of shots to retrieve
   * @return mixed               Array in case the request was successful;
   *                             false, otherwise
   */
  public static function retrieveShots($username,
                                       $accessToken,
                                       $shotsCount = 12) {

    if ( empty($username) || empty($accessToken) ) return false;

    $requestAddress = self::$usersSlug .
                      '/' . $username .
                      '/' . self::$shotsSlug .
                      '?per_page=' . $shotsCount;

    $reqResponse = self::sendRequest( $requestAddress, $accessToken );

    // check to see if the request was successfull
    if ( 0 == $reqResponse['errorNo']) {
      if ( 200 == $reqResponse['responseCode'] ) {
        try {
          $result = $reqResponse['result'];
          $responseJson = json_decode( $result );

          return $responseJson;
        } catch(Exception $e) {
          return $e;
        }
      }
    }

    return false;
  }
}

?>