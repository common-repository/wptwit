<?php

require_once(dirname(__FILE__) . '/mytwit.inc.php');

class lineMyTwit extends myTwit
{
  protected $show_follow_us_as = false;
  protected $no_response_file = 'twitnoresponse.txt';
  protected $allowed_option_keys = array('user', 'cacheFile', 'cachExpire', 'postLimit', 'debug', 'targetBlank', 'postClass', 'base_dir', 'myTwitHeader', 'show_follow_us_as', 'email');

  /**
   * Override the default printError function and send the error to the
   * specified contact address
   * @param string $message
   */
  public function printError($message) {
    if($this->email) {
      mail($this->email, 'myTwit Issue', htmlspecialchars($message), "From: " . $this->email . "\r\n");
    }
  }

  /**
   * Create a new lineMyTwit object and merge the default options with the user
   * defined ones
   * @param mixed array $options
   */
  public function __construct($options = array())
  {
    // REMOVE ANY NON-VALID OPTION KEYS
    $options = array_intersect_key($options, array_flip($this->allowed_option_keys));
    
    // SET THE NECESSARY DEFAULTS
    $defaults = array(
        'base_dir'      => './',
        'myTwitHeader'  => true,
        'postLimit'     => 5
       );

    $options = $options + $defaults;

    // ASSIGN THE OPTIONS TO THEIR ASSOCIATED $this->
    foreach($options as $key => $value) {
      $this->$key = $value;
    }

    $this->initMyTwit();
  }

  /**
   * Override the default initMyTwit to have more control over header and entries
   */
  public function initMyTwit(){
    if (!is_string($this->user))
      $this->printError('Please set a user.');

    $this->targetAppend = ($this->targetBlank) ? ' target="_blank"' : '';
    $this->postClassAppend = ($this->postClass) ? ' class="' . $this->postClass . '"' : '';

    $this->checkCacheFile();
    $this->readCache();

    $this->jsonArray = json_decode($this->jsonData, true);

    $output = '<ul class="twitbox">';
    $output .= $this->getTwitterHeader();
    $output .= $this->getTwitterEntries();
    $output .= $this->getTwitterFooter();
    $output .= '</ul>';
    
    $this->myTwitData = $output;
  }

  /**
   * Get the HTML markup for the twitter header
   * @return string
   */
  public function getTwitterHeader()
  {
    if ($this->myTwitHeader && isset($this->jsonArray[0])) {
      return '<div class="mytwitHead"><a href="http://twitter.com/' . $this->user . '"' . $this->targetAppend . '><img src="' . $this->jsonArray[0]['user']['profile_image_url'] . '" style="border:0" alt="' . $this->user . '" /></a>' .
             '<div class="mytwitSummary"><a href="http://twitter.com/' . $this->user . '"' . $this->targetAppend . '>' . $this->user . '</a><br />' .
              $this->formatPlural($this->jsonArray[0]['user']['followers_count'], 'follower') . '</div></div>';
    }
  }

  /**
   * Get the HTML markup for the twitter entries
   * @return string
   */
  public function getTwitterEntries()
  {
    $output = '';
    for ($x = 0; $x < count($this->jsonArray) && $x < $this->postLimit; $x++) {
      $seconds_ago = mktime() - strtotime($this->jsonArray[$x]['created_at']);
      $ts = strtotime($this->jsonArray[$x]['created_at']) + $this->jsonArray[$x]['user']['utc_offset'];
      $cur_ts = mktime();
      $output .= '<li' . $this->postClassAppend . '>' . $this->linkURLs($this->jsonArray[$x]['text']) .
              ' <span class="twhen">by <a href="http://twitter.com/' . $this->jsonArray[$x]['user']['screen_name'] . '"' . $this->targetAppend . '>@' . $this->jsonArray[$x]['user']['screen_name'] . '</a> ' .
              $this->intoRelativeTime($seconds_ago) . "</span></li>\n";
    }

    return $output;
  }

  public function getTwitterFooter()
  {
    if($this->show_follow_us_as) {
      $link_text = str_replace('[USERNAME]', $this->user, $this->show_follow_us_as);
      
      return '<p class="mytwitFooter"><a class="twitterBtn" href="http://twitter.com/'. $this->user. '"' . $this->targetAppend . '>' . $link_text . '</a></p>';
    }
  }

  /**
   * Update the twitter cache
   */
  public function updateCache(){
    $uri = 'http://twitter.com/statuses/user_timeline/'.$this->user.'.json';
    
    $req = new HTTPRequest($uri);
    $tmpdata = $req->DownloadToString();
    $resp = json_decode($tmpdata, true);
    
    if (isset($resp['error'])) {
      // DISPLAY ERROR
      $this->printError('Error getting information from Twitter ['.$resp['error'].']. Please check the username ('.$this->user.')');
    } elseif (!is_array($resp)) {
      // TWITTER IS NOT AVAILABLE
      // DISPLAY ERROR
      $this->printError('Error getting information from Twitter. File is not JSON.');

      // CREATE NO RESPONSE FILE (WILL MANTAIN EXISTING CACHE FOR A FURTHER
      // 10 MINUTES (OR $cachExpire IF DIFFERENT)
      $handle = @fopen($this->getNoResponseFileLocation(), 'w');
      fwrite($handle, 'Twitter Unavailable - Last attempt ' . date('Y-m-d H:i:s'));
      fclose($handle);
      $this->debugMsg('Twitter currently unavailable... waiting ' . $this->cachExpire . ' seconds before trying again');
    } else {
      // GET THE LOCATION OF THE CACHE FILE
      $handle = @fopen($this->getCacheFileLocation(), 'w');

      // DISPLAY ERROR
      if (!$handle) $this->printError('Could not write to cache file: '.$this->getCacheFileLocation().'. Please check read/write permissions.');

      // UPDATE CACHE
      fwrite($handle, $tmpdata);
      fclose($handle);
      $this->debugMsg('Updated cache file: '.$this->getCacheFileLocation());
    }
  }

  function checkCacheFile(){
    // CHECK TO SEE IF THE CACHE FILE HAS EXPIRED
    if ((@filemtime($this->getCacheFileLocation()) < (mktime() - $this->cachExpire) ) || (!is_file($this->getCacheFileLocation()))){
      // CHECK TO SEE IF THERE IS A NO RESPONSE FILE
      if(file_exists($this->getNoResponseFileLocation())) {
        // IF THE NO RESPONSE FILE HAS EXPIRED THEN REMOVE AND ATTEMPT TO UPDATE THE CACHE AGAIN
        if(@filemtime($this->getNoResponseFileLocation()) < (mktime() - $this->cachExpire)) {
          unlink($this->getNoResponseFileLocation());
          $this->debugMsg('Re-trying Twitter fetch');
          $this->updateCache();
        }
      } else {
        // IF THERE IS NOT A NO-RESPONSE FILE THEN UPDATE THE CASE
        $this->debugMsg('Cache file outdated');
        $this->updateCache();
      }
    } else {
      // CACHE IS STILL VALID - NO NEED TO UPDATE
      $this->debugMsg('Cache file still valid');
    }
  }

  /**
   * Override readCache to allow use of new file location methods
   */
  function readCache(){
    if( false == ($this->jsonData = @file_get_contents( $this->getCacheFileLocation() )))
      $this->printError('Could not read cache file: '.$this->getCacheFileLocation());
  }

  /**
   * Determine the location of the Cache File
   * @return string
   */
  protected function getCacheFileLocation()
  {
    return $this->getFileLocation($this->cacheFile);
  }

  /**
   * Determine the location of the No Response File
   * @return string
   */
  protected function getNoResponseFileLocation()
  {
    return $this->getFileLocation($this->no_response_file);
  }

  /**
   * Find the location of the file and create the directory structure
   * if necessary
   * @param string $file
   * @return string
   */
  protected function getFileLocation($file)
  {
    // MAKE THE BASE DIRECTORY IF IT DOESN'T EXISTS
    if(!is_dir($this->base_dir)) {
      mkdir($this->base_dir,0755, true);
    }

    if(substr($this->base_dir, strlen($this->base_dir) - 1) != '/') {
      $this->base_dir .= '/';
    }

    return $this->base_dir . $this->sanitise($this->user . '_' . $file);
  }

  /**
   * Output the twitter data
   * @return string
   */
  public function show()
  {
    return $this->myTwitData;
  }

  /**
   * Make sure the string only contains alphanumeric characters and a set of allowed characters (-_.)
   * @param string $string
   * @return string
   */
  public function sanitise($string)
  {
    return preg_replace("/[^a-zA-Z0-9\s-_.]/", "_", $string);
  }
}
?>