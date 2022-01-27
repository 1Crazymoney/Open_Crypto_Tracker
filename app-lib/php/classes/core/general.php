<?php
/*
 * Copyright 2014-2022 GPLv3, Open Crypto Tracker by Mike Kilday: Mike@DragonFrugal.com
 */


class ct_gen {
	
// Class variables / arrays
var $ct_var1;
var $ct_var2;
var $ct_var3;
var $ct_array1 = array();
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function titles_usort_alpha($a, $b) {
   return strcmp( strtolower($a["title"]) , strtolower($b["title"]) ); // Case-insensitive equivelent comparision via strtolower()
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function test_ipv4($str) {
   return filter_var($str, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
   }
   
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function test_ipv6($str) {
   return filter_var($str, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function telegram_msg($msg, $chat_id) {
   
   // Using 3rd party Telegram class, initiated already as global var $telegram_messaging
   global $telegram_messaging;
   
   return $telegram_messaging->send->chat($chat_id)->text($msg)->send();
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function mob_number($str) {
   	
   global $ct_var;
   
   $str = explode("||",$str);
   
   return $ct_var->strip_non_alpha($str[0]);
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function ordinal($num) {
   
   $ends = array('th','st','nd','rd','th','th','th','th','th','th');
       
       if ( ( ($num % 100) >= 11 ) && ( ($num % 100) <= 13 ) ) {
       return $num. 'th';
       }
       else {
       return $num. $ends[$num % 10];
       }
       
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function is_msie() {
   
      if ( preg_match("/msie/i", $_SERVER['HTTP_USER_AGENT']) || preg_match("/trident/i", $_SERVER['HTTP_USER_AGENT']) ) {
      return true;
      }
      else {
      return false;
      }
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function dir_size($dir) {
   
   $size = 0;
   
      foreach ( glob(rtrim($dir, '/').'/*', GLOB_NOSORT) as $each ) {
      $size += ( is_file($each) ? filesize($each) : $this->dir_size($each) );
      }
       
   return $size;
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function regex_compat_url($url) {
      
   $regex_url = trim($url);
   $regex_url = preg_replace("/(http|https|ftp|tcp|ssl):\/\//i", "", $regex_url);
   $regex_url = preg_replace("/\//i", "\/", $regex_url);
   
   return $regex_url;
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function split_text_msg($text, $char_length) {
   
   $chunks = explode("||||", wordwrap($msg, $char_length, "||||", false) );
   $total = count($chunks);
   
      foreach($chunks as $page => $chunk) {
      $msg = sprintf("(%d/%d) %s", $page+1, $total, $chunk);
      }
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function digest($str, $max_length=false) {
   
      if ( $max_length > 0 ) {
      $result = substr( hash('ripemd160', $str) , 0, $max_length);
      }
      else {
      $result = hash('ripemd160', $str);
      }
      
   return $result;
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function nonce_digest($data, $custom_nonce=false) {
      
      if ( isset($data) && $custom_nonce != false ) {
      return $this->digest( $data . $custom_nonce );
      }
      elseif ( isset($data) && isset($_SESSION['nonce']) ) {
      return $this->digest( $data . $_SESSION['nonce'] );
      }
      else {
      return false;
      }
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function admin_logged_in() {
      
      // IF REQUIRED DATA NOT SET, REFUSE ADMIN AUTHORIZATION
      if (
      !isset( $_COOKIE['admin_auth_' . $this->id()] )
      || !isset( $_SESSION['nonce'] )
      || !isset( $_SESSION['admin_logged_in']['auth_hash'] ) 
      ) {
      return false;
      }
      // WE SPLIT THE LOGIN AUTH BETWEEN COOKIE AND SESSION DATA (TO BETTER SECURE LOGIN AUTHORIZATION)
      elseif ( $this->nonce_digest( $_COOKIE['admin_auth_' . $this->id()] ) == $_SESSION['admin_logged_in']['auth_hash'] ) {
      return true;
      }
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function del_all_files($dir) {
   
   $files = glob($dir . '/*'); // get all file names
   
      foreach($files as $file) { // iterate files
      
         if( is_file($file) ) {
         unlink($file); // delete file
         }
         
      }
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function conv_bytes($bytes, $round) {
   
   $type = array("", "Kilo", "Mega", "Giga", "Tera", "Peta", "Exa", "Zetta", "Yotta");
   
     $index = 0;
     while( $bytes >= 1000 ) { // new standard (not 1024 anymore)
     $bytes/=1000; // new standard (not 1024 anymore)
     $index++;
     }
     
   return("".round($bytes, $round)." ".$type[$index]."bytes");
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   // To keep admin nonce key a secret, and make CSRF attacks harder with a different key per submission item
   function admin_hashed_nonce($key, $force=false) {
      
      // WE NEED A SEPERATE FUNCTION $this->nonce_digest(), SO WE DON'T #ENDLESSLY LOOP# FROM OUR
      // $this->admin_logged_in() CALL (WHICH ALSO USES $this->nonce_digest() INSTEAD OF $this->admin_hashed_nonce())
      if ( $this->admin_logged_in() || $force ) {
      return $this->nonce_digest($key);
      }
      else {
      return false;
      }
      
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function get_lines($file) {
   
   $f = fopen($file, 'rb');
   $lines = 0;
   
      while (!feof($f)) {
      $lines += substr_count(fread($f, 8192), "\n");
      }
   
   fclose($f); // Close file
   
   gc_collect_cycles(); // Clean memory cache
   
   return $lines;
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function timestamps_usort_newest($a, $b) {
      
      if ( $a->pubDate != '' ) {
      $a = $a->pubDate;
      $b = $b->pubDate;
      }
      elseif ( $a->published != '' ) {
      $a = $a->published;
      $b = $b->published;
      }
      elseif ( $a->updated != '' ) {
      $a = $a->updated;
      $b = $b->updated;
      }
   
   return strtotime($b) - strtotime($a);
      
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function hardy_sess_clear() {
   
   // Deleting all session data can fail on occasion, and wreak havoc.
   // This helps according to one programmer on php.net
   session_start();
   session_name( $this->id() );
   $_SESSION = array();
   session_unset();
   session_destroy();
   session_write_close();
   setcookie(session_name( $this->id() ),'',0,'/');
   session_regenerate_id(true);
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function create_csv($file, $save_as, $array) {
   
      if ( $file == 'temp' ) {
      $file = tempnam(sys_get_temp_dir(), 'temp');
      }
   
   $fp = fopen($file, 'w');
   
      foreach($array as $fields) {
      fputcsv($fp, $fields);
      }
   
   $this->file_download($file, $save_as); // Download file (by default deletes after download, then exits)
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function list_files($files_dir) {
      
   $scan_array = scandir($files_dir);
   $files = array();
     
     foreach($scan_array as $filename) {
       
       if ( is_file($files_dir.'/'.$filename) ) {
       $files[] = $filename;
       }
       
     }
   
   return $files;
     
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function text_email($str) {
   
   global $ct_conf, $ct_var;
   
   $str = explode("||",$str);
   
   $phone_number = $ct_var->strip_non_alpha($str[0]);
   
   $network_name = trim( strtolower($str[1]) ); // Force lowercase lookups for reliability / consistency
   
      // Set text domain
      if ( trim($phone_number) != '' && isset($ct_conf['mob_net_txt_gateways'][$network_name]) ) {
      return trim($phone_number) . '@' . trim($ct_conf['mob_net_txt_gateways'][$network_name]); // Return formatted texting email address
      }
      else {
      return false;
      }
   
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function start_page($page, $href_link=false) {
   
      // We want to force a page reload for href links, so technically we change the URL but location remains the same
      if ( $href_link != false ) {
      $index = './';
      }
      else {
      $index = 'index.php';
      }
      
      if ( $page != '' ) {
      $url = $index . ( $page != '' ? '?start_page=' . $page . '#' . $page : '' );
      }
      else {
      $url = $index;
      }
      
   return $url;
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function rand_hash($num_bytes) {
   
   global $base_dir;
   
      // Upgrade required
      if ( PHP_VERSION_ID < 70000 ) {
      	
      $this->log(
      			'security_error',
      			'Upgrade to PHP v7 or later to support cryptographically secure pseudo-random bytes in this application, or your application may not function properly'
      			);
      
      }
      // >= PHP 7
      elseif ( PHP_VERSION_ID >= 70000 ) {
      $hash = random_bytes($num_bytes);
      }
   
      if ( strlen($hash) == $num_bytes ) {
      return bin2hex($hash);
      }
      else {
      return false;
      }
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function valid_email($email) {
   
   global $ct_var;
   
   // Trim whitespace off ends, since we do this before attempting to send anyways in our safe_mail function
   $email = trim($email);
   
   $address = explode("@",$email);
      
   $domain = $address[1];
      
      // Validate "To" address
      if ( !$email || !preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)+$/", $email) ) {
      return "Please enter a valid email address.";
      }
      elseif ( function_exists("getmxrr") && !getmxrr($domain, $mxrecords) ) {
      return "No mail server records found for domain '" . $ct_var->obfusc_str($domain) . "' [obfuscated]";
      }
      else {
      return "valid";
      }
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function unicode_to_utf8($char, $format) {
      
       if ( $format == 'decimal' ) {
       $pre = '';
       }
       elseif ( $format == 'hexadecimal' ) {
       $pre = 'x';
       }
   
   $char = trim($char);
   $char = 'PREFIX' . $char;
   $char = preg_replace('/PREFIXx/', 'PREFIX', $char);
   $char = preg_replace('/PREFIXu/', 'PREFIX', $char);
   $char = preg_replace('/PREFIX/', '', $char);
   $char = '&#' . $pre . $char . ';';
   
   return html_entity_decode($char, ENT_COMPAT, 'UTF-8');
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   // Install id (10 character hash, based off base url)
   function id() {
      
   global $base_url, $base_dir, $ct_app_id;
   
      // ALREADY SET
      if ( isset($ct_app_id) ) {
      return $ct_app_id;
      }
      // NOT CRON
      elseif ( $runtime_mode != 'cron' && trim($base_url) != '' ) {
      return substr( md5($base_url) , 0, 10); // First 10 characters
      }
      // CRON
      elseif ( $runtime_mode == 'cron' && trim($base_dir) != '' ) {
      return substr( md5($base_dir) , 0, 10); // First 10 characters
      }
      // SET FAILED
      else {
      return false;
      }
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function sort_files($files_dir, $extension, $sort) {
      
   $scan_array = scandir($files_dir);
   $files = array();
     
     
     foreach($scan_array as $filename) {
       
       if ( pathinfo($filename, PATHINFO_EXTENSION) == $extension ) {
         $mod_time = filemtime($files_dir.'/'.$filename);
         $files[$mod_time . '-' . $filename] = $filename;
       }
       
     }
   
   
     if ( $sort == 'asc' ) {
     ksort($files);
     }
     elseif ( $sort == 'desc' ) {
     krsort($files);
     }
   
   
   return $files;
     
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function dir_struct($path) {
   
   global $ct_conf, $possible_http_users, $http_runtime_user;
   
      if ( !is_dir($path) ) {
      
         // Run cache compatibility on certain PHP setups
         if ( !$http_runtime_user || in_array($http_runtime_user, $possible_http_users) ) {
         $oldmask = umask(0);
         $result = mkdir($path, octdec($ct_conf['dev']['chmod_cache_dir']), true); // Recursively create whatever path depth desired if non-existent
         umask($oldmask);
         return $result;
         }
         else {
         return  mkdir($path, octdec($ct_conf['dev']['chmod_cache_dir']), true); // Recursively create whatever path depth desired if non-existent
         }
      
      }
      else {
      return true;
      }
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function pepper_hashed_pass($password) {
   
   global $password_pepper;
   
      if ( !$password_pepper ) {
      $this->log('conf_error', '$password_pepper not set properly');
      return false;
      }
      else {
         
      $password_pepper_hashed = hash_hmac("sha256", $password, $password_pepper);
      
         if ( $password_pepper_hashed == false ) {
         $this->log('conf_error', 'hash_hmac() returned false in the ct_gen->pepper_hashed_pass() function');
         return false;
         }
         else {
         return password_hash($password_pepper_hashed, PASSWORD_DEFAULT);
         }
      
      }
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function delete_all_cookies() {
   
     
     // Portfolio
     unset($_COOKIE['coin_amounts']); 
     unset($_COOKIE['coin_pairings']); 
     unset($_COOKIE['coin_markets']); 
     unset($_COOKIE['coin_paid']); 
     unset($_COOKIE['coin_leverage']); 
     unset($_COOKIE['coin_margintype']); 
     
     
     // Settings
     unset($_COOKIE['coin_reload']);  
     unset($_COOKIE['notes']);
     unset($_COOKIE['show_charts']);  
     unset($_COOKIE['show_crypto_val']);  
     unset($_COOKIE['show_secondary_trade_val']);  
     unset($_COOKIE['show_feeds']);  
     unset($_COOKIE['theme_selected']);  
     unset($_COOKIE['sort_by']);  
     unset($_COOKIE['alert_percent']);  
     unset($_COOKIE['prim_currency_market_standalone']);  
    
    
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function obfusc_path_data($path) {
      
   global $ct_var;
   
      // Secured cache data
      if ( preg_match("/cache\/secured/i", $path) ) {
         
      $subpath = preg_replace("/(.*)cache\/secured\//i", "", $path);
      
      $subpath_array = explode("/", $subpath);
         
         // Subdirectories of /secured/
         if ( is_array($subpath_array) && sizeof($subpath_array) > 1 ) {
         $path = str_replace($subpath_array[0], $ct_var->obfusc_str($subpath_array[0], 1), $path);
         $path = str_replace($subpath_array[1], $ct_var->obfusc_str($subpath_array[1], 5), $path);
         }
         // Files directly in /secured/
         else {
         $path = str_replace($subpath, $ct_var->obfusc_str($subpath, 5), $path);
         }
            
      //$path = str_replace('cache/secured', $ct_var->obfusc_str('cache', 0) . '/' . $ct_var->obfusc_str('secured', 0), $path);
      
      }
   
   return $path;
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function obfusc_url_data($url) {
      
   global $ct_conf, $ct_var;
   
   // Keep our color-coded logs in the admin UI pretty, remove '//' and put in parenthesis
   $url = preg_replace("/:\/\//i", ") ", $url);
   
      // Etherscan
      if ( preg_match("/etherscan/i", $url) ) {
      $url = str_replace($ct_conf['gen']['etherscan_key'], $ct_var->obfusc_str($ct_conf['gen']['etherscan_key'], 2), $url);
      }
      // Telegram
      elseif ( preg_match("/telegram/i", $url) ) {
      $url = str_replace($ct_conf['comms']['telegram_bot_token'], $ct_var->obfusc_str($ct_conf['comms']['telegram_bot_token'], 2), $url); 
      }
      // Defipulse
      elseif ( preg_match("/defipulse/i", $url) ) {
      $url = str_replace($ct_conf['gen']['defipulse_key'], $ct_var->obfusc_str($ct_conf['gen']['defipulse_key'], 2), $url); 
      }
   
   // Keep our color-coded logs in the admin UI pretty, remove '//' and put in parenthesis
   return '('.$url;
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   // Return the TLD only (no subdomain)
   function get_tld_or_ip($url) {
   
   global $ct_conf;
   
   $urlData = parse_url($url);
      
      // If this is an ip address, then we can return that as the result now
      if ( $this->test_ipv4($urlData['host']) != false || $this->test_ipv6($urlData['host']) != false ) {
      return $urlData['host'];
      }
   
   $hostData = explode('.', $urlData['host']);
   $hostData = array_reverse($hostData);
   
   
      if ( array_search($hostData[1] . '.' . $hostData[0], $ct_conf['dev']['top_level_domain_map']) !== false ) {
      $host = $hostData[2] . '.' . $hostData[1] . '.' . $hostData[0];
      } 
      elseif ( array_search($hostData[0], $ct_conf['dev']['top_level_domain_map']) !== false ) {
      $host = $hostData[1] . '.' . $hostData[0];
      }
   
   
   return strtolower( trim($host) );
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function check_pepper_hashed_pass($input_password, $stored_hashed_password) {
   
   global $password_pepper, $stored_admin_login;
   
      if ( !$password_pepper ) {
      $this->log('conf_error', '$password_pepper not set properly');
      return false;
      }
      elseif ( !is_array($stored_admin_login) ) {
      $this->log('conf_error', 'No admin login set yet to check against');
      return false;
      }
      else {
         
      $input_password_pepper_hashed = hash_hmac("sha256", $input_password, $password_pepper);
      
         if ( $input_password_pepper_hashed == false ) {
         $this->log('conf_error', 'hash_hmac() returned false in the ct_gen->check_pepper_hashed_pass() function');
         return false;
         }
         else {
         return password_verify($input_password_pepper_hashed, $stored_hashed_password);
         }
         
      }
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   // Pretty decimals calculation (ONLY returns num of decimals to use)
   // (NO DECIMALS OVER 100 IN UNIT VALUE, MAX 2 DECIMALS OVER 1, #AND MIN 2 DECIMALS# UNDER, FOR INTERFACE UX)
   function thres_dec($num, $mode) {
       
   global $ct_conf;
   
   $result = array();
   
      // Unit
      if ( $mode == 'u' ) {
   
   		  if ( abs($num) >= 100 ) {
          $result['max_dec'] = 0;
          $result['min_dec'] = 0;
          }
		  elseif ( abs($num) >= 1 ) {
          $result['max_dec'] = 2;
          $result['min_dec'] = 0;
          }
          else {
          $result['max_dec'] = $ct_conf['gen']['prim_currency_dec_max'];
          $result['min_dec'] = 2;
          }
          
      }
      // Percent 
      elseif ( $mode == 'p' ) {
          
          if ( abs($num) >= 100 ) {
          $result['max_dec'] = 0;
          $result['min_dec'] = 0;
          }
		  else {
          $result['max_dec'] = 2;
          $result['min_dec'] = 2;
          }
      
      }
      
   return $result;
      
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function store_cookie($name, $val, $time) {
      
      if ( PHP_VERSION_ID >= 70300 ) {
         
      $result = setcookie(
              			  $name,
              			  $val,
              			     [
         	                 'samesite' => 'Strict', // Strict for high privacy
            	             'expires' => $time,
                             ]
                          );
      
      }
      else {
      $result = setcookie($name, $val, $time);
      }
   
      
      
      // Android / Safari maximum cookie size is 4093 bytes, Chrome / Firefox max is 4096
      if ( strlen($val) > 4093 ) {
      	
      $this->log(
      		'other_error',
      		'Cookie size is greater than 4093 bytes (' . strlen($val) . ' bytes). If saving portfolio as cookie data fails on your browser, try using CSV file import / export instead for large portfolios.'
      		);
      
      }
      
      if ( $result == false ) {
      $this->log('system_error', 'Cookie creation failed for cookie "' . $name . '"');
      }
      
      
   return $result;
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function valid_username($username) {
   
   global $ct_conf;
   
       if ( mb_strlen($username, $ct_conf['dev']['charset_default']) < 4 ) {
       $error .= "requires 4 minimum characters; ";
       }
       
       if ( mb_strlen($username, $ct_conf['dev']['charset_default']) > 30 ) {
       $error .= "requires 30 maximum characters; ";
       }
       
       if ( !preg_match("/^[a-z]([a-z0-9]+)$/", $username) ) {
       $error .= "lowercase letters and numbers only (lowercase letters first, then optionally numbers, no spaces); ";
       }
       
       if ( preg_match('/\s/',$username) ) {
       $error .= "no spaces allowed; ";
       }
   
   
       if( $error ){
       return 'valid_username_error: ' . $error;
       }
       else {
       return 'valid';
       }
   
   }
    
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
    
    // For captcha image
    // Credit to: https://code.tutsplus.com/tutorials/build-your-own-captcha-and-contact-form-in-php--net-5362
   function captcha_str($input, $strength=10) {
      
   $input_length = strlen($input);
   $random_str = '';
           
            $count = 0;
            while ( $count < $strength ) {
                  
            $rand_case = rand(1, 2);
                  
               if( $rand_case % 2 == 0 ){ 
               // Even number  
               $random_char = strtoupper( $input[mt_rand(0, $input_length - 1)] );
               } 
               else { 
               // Odd number
               $random_char = strtolower( $input[mt_rand(0, $input_length - 1)] );
               } 
            
            
               if ( stristr($random_str, $random_char) == false ) {
               $random_str .= $random_char;
               $count = $count + 1;
               }

            
            }
           
   return $random_str;
   
   }
       
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function subarray_ct_conf_upgrade($cat_key, $conf_key, $skip_upgrading) {
   
   global $upgraded_ct_conf, $cached_ct_conf, $check_default_ct_conf, $default_ct_conf;
   
      // Check for new variables, and add them
      foreach ( $default_ct_conf[$cat_key][$conf_key] as $setting_key => $setting_val ) {
      
         if ( is_array($setting_val) ) {
         $this->log('conf_error', 'Sub-array depth to deep for app config upgrade parser');
         }
         elseif ( !in_array($setting_key, $skip_upgrading) && !isset($upgraded_ct_conf[$cat_key][$conf_key][$setting_key]) ) {
         	
         $upgraded_ct_conf[$cat_key][$conf_key][$setting_key] = $default_ct_conf[$cat_key][$conf_key][$setting_key];
         
         $this->log(
         			'conf_error',
         			'Outdated app config, upgraded parameter ct_conf[' . $cat_key . '][' . $conf_key . '][' . $setting_key . '] imported (default value: ' . $default_ct_conf[$cat_key][$conf_key][$setting_key] . ')'
         			);
         
         $conf_upgraded = 1;
         
         }
            
      }
      
      // Check for depreciated variables, and remove them
      foreach ( $cached_ct_conf[$cat_key][$conf_key] as $setting_key => $setting_val ) {
      
         if ( is_array($setting_val) ) {
         $this->log('conf_error', 'Sub-array depth to deep for app config upgrade parser');
         }
         elseif ( !in_array($setting_key, $skip_upgrading) && !isset($default_ct_conf[$cat_key][$conf_key][$setting_key]) ) {
         	
         unset($upgraded_ct_conf[$cat_key][$conf_key][$setting_key]);
         
         $this->log(
         			'conf_error',
         			'Depreciated app config, parameter ct_conf[' . $cat_key . '][' . $conf_key . '][' . $setting_key . '] removed'
         			);
         
         $conf_upgraded = 1;
         
         }
            
      }
      
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function smtp_mail($to, $subj, $msg, $content_type='text/plain', $charset=null) {
   
   // Using 3rd party SMTP class, initiated already as global var $smtp
   global $ct_conf, $smtp;
   
      if ( $charset == null ) {
      $charset = $ct_conf['dev']['charset_default'];
      }
      
      
      // Fallback, if no From email set in app config
      if ( $this->valid_email($ct_conf['comms']['from_email']) == 'valid' ) {
      $from_email = $ct_conf['comms']['from_email'];
      }
      else {
      $temp_data = explode("||", $ct_conf['comms']['smtp_login']);
      $from_email = $temp_data[0];
      }
   
   
   $smtp->From('Open Crypto Tracker <' . $from_email . '>'); 
   $smtp->singleTo($to); 
   $smtp->Subject($subj);
   $smtp->Charset($charset);
   
   
      if ( $content_type == 'text/plain' ) {
      $smtp->Text($msg);
      $smtp->Body(null);
      }
      elseif ( $content_type == 'text/html' ) {
      $smtp->Body($msg);
      $smtp->Text(null);
      }
   
   
   return $smtp->Send();
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function log($log_type, $log_msg, $verbose_tracing=false, $hashcheck=false, $overwrite=false) {
   
   global $runtime_mode, $ct_conf, $log_array;
   
   
   // Less verbose log category
   $category = $log_type;
   $category = preg_replace("/_error/i", "", $category);
   $category = preg_replace("/_debug/i", "", $category);
   
   
      // Disable logging any included verbose tracing, if log verbosity level config is set to normal
      if ( $ct_conf['dev']['log_verb'] == 'normal' ) {
      $verbose_tracing = false;
      }
   
   
      if ( $hashcheck != false ) {
      $log_array[$log_type][$hashcheck] = '[' . date('Y-m-d H:i:s') . '] ' . $runtime_mode . ' => ' . $category . ': ' . $log_msg . ( $verbose_tracing != false ? '; [ '  . $verbose_tracing . ' ]' : ';' ) . " <br /> \n";
      }
      // We parse cache errors as array entries (like when hashcheck is included, BUT NO ARRAY KEY)
      elseif ( $category == 'cache' ) {
      $log_array[$log_type][] = '[' . date('Y-m-d H:i:s') . '] ' . $runtime_mode . ' => ' . $category . ': ' . $log_msg . ( $verbose_tracing != false ? '; [ '  . $verbose_tracing . ' ]' : ';' ) . " <br /> \n";
      }
      elseif ( $overwrite != false ) {
      $log_array[$log_type] = '[' . date('Y-m-d H:i:s') . '] ' . $runtime_mode . ' => ' . $category . ': ' . $log_msg . ( $verbose_tracing != false ? '; [ '  . $verbose_tracing . ' ]' : ';' ) . " <br /> \n";
      }
      else {
      $log_array[$log_type] .= '[' . date('Y-m-d H:i:s') . '] ' . $runtime_mode . ' => ' . $category . ': ' . $log_msg . ( $verbose_tracing != false ? '; [ '  . $verbose_tracing . ' ]' : ';' ) . " <br /> \n";
      }
   
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function key_to_name($str) {
   
   global $ct_conf;
   
   // Uppercase every word, and remove underscore between them
   $str = ucwords(preg_replace("/_/i", " ", $str));
   
   
      // Pretty up the individual words as needed
      $words = explode(" ",$str);
      foreach($words as $key => $val) {
      
         if ( $val == 'Us' ) {
         $words[$key] = strtoupper($val); // All uppercase US
         }
      
      $pretty_str .= $words[$key] . ' ';
      
      }
   
      
      // Pretty up all secondary asset market symbols
      foreach($ct_conf['power']['crypto_pairing_pref_markets'] as $key => $unused) {
      $pretty_str = preg_replace("/".strtolower($key)."/i", strtoupper($key), $pretty_str);
      }
   
      foreach($ct_conf['power']['btc_currency_markets'] as $key => $unused) {
      $pretty_str = preg_replace("/".strtolower($key)."/i", strtoupper($key), $pretty_str);
      }
   
   $pretty_str = preg_replace("/btc/i", 'BTC', $pretty_str);
   $pretty_str = preg_replace("/nft/i", 'NFT', $pretty_str);
   $pretty_str = preg_replace("/coin/i", 'Coin', $pretty_str);
   $pretty_str = preg_replace("/bitcoin/i", 'Bitcoin', $pretty_str);
   $pretty_str = preg_replace("/exchange/i", 'Exchange', $pretty_str);
   $pretty_str = preg_replace("/market/i", 'Market', $pretty_str);
   $pretty_str = preg_replace("/base/i", 'Base', $pretty_str);
   $pretty_str = preg_replace("/forex/i", 'Forex', $pretty_str);
   $pretty_str = preg_replace("/finex/i", 'Finex', $pretty_str);
   $pretty_str = preg_replace("/stamp/i", 'Stamp', $pretty_str);
   $pretty_str = preg_replace("/flyer/i", 'Flyer', $pretty_str);
   $pretty_str = preg_replace("/panda/i", 'Panda', $pretty_str);
   $pretty_str = preg_replace("/pay/i", 'Pay', $pretty_str);
   $pretty_str = preg_replace("/swap/i", 'Swap', $pretty_str);
   $pretty_str = preg_replace("/iearn/i", 'iEarn', $pretty_str);
   $pretty_str = preg_replace("/pulse/i", 'Pulse', $pretty_str);
   $pretty_str = preg_replace("/defi/i", 'DeFi', $pretty_str);
   $pretty_str = preg_replace("/ring/i", 'Ring', $pretty_str);
   $pretty_str = preg_replace("/amm/i", 'AMM', $pretty_str);
   $pretty_str = preg_replace("/ico/i", 'ICO', $pretty_str);
   $pretty_str = preg_replace("/erc20/i", 'ERC-20', $pretty_str);
   $pretty_str = preg_replace("/okex/i", 'OKex', $pretty_str);
   $pretty_str = preg_replace("/mart/i", 'Mart', $pretty_str);
   $pretty_str = preg_replace("/ftx/i", 'FTX', $pretty_str);
   $pretty_str = preg_replace("/dcx/i", 'DCX', $pretty_str);
   $pretty_str = preg_replace("/gateio/i", 'Gate.io', $pretty_str);
   
   
   return trim($pretty_str);
   
   }
    
    
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
    
   function utf8_to_unicode($char, $format) {
      
       if (ord($char[0]) >=0 && ord($char[0]) <= 127)
           $result = ord($char[0]);
           
       if (ord($char[0]) >= 192 && ord($char[0]) <= 223)
           $result = (ord($char[0])-192)*64 + (ord($char[1])-128);
           
       if (ord($char[0]) >= 224 && ord($char[0]) <= 239)
           $result = (ord($char[0])-224)*4096 + (ord($char[1])-128)*64 + (ord($char[2])-128);
           
       if (ord($char[0]) >= 240 && ord($char[0]) <= 247)
           $result = (ord($char[0])-240)*262144 + (ord($char[1])-128)*4096 + (ord($char[2])-128)*64 + (ord($char[3])-128);
           
       if (ord($char[0]) >= 248 && ord($char[0]) <= 251)
           $result = (ord($char[0])-248)*16777216 + (ord($char[1])-128)*262144 + (ord($char[2])-128)*4096 + (ord($char[3])-128)*64 + (ord($char[4])-128);
           
       if (ord($char[0]) >= 252 && ord($char[0]) <= 253)
           $result = (ord($char[0])-252)*1073741824 + (ord($char[1])-128)*16777216 + (ord($char[2])-128)*262144 + (ord($char[3])-128)*4096 + (ord($char[4])-128)*64 + (ord($char[5])-128);
           
       if (ord($char[0]) >= 254 && ord($char[0]) <= 255)    //  error
           $result = false;
           
           
       if ( $format == 'decimal' ) {
       $result = $result;
       }
       elseif ( $format == 'hexadecimal' ) {
       $result = 'x'.dechex($result);
       }
       

   return $result;
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function base_url($atRoot=false, $atCore=false, $parse=false) {
      
   // WARNING: THIS ONLY WORKS WELL FOR HTTP-BASED RUNTIME, ----NOT CLI---!
   // CACHE IT TO FILE DURING UI RUNTIME FOR CLI TO USE LATER ;-)
   
      if ( isset($_SERVER['HTTP_HOST']) ) {
            
      $http = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
      $hostname = $_SERVER['HTTP_HOST'];
      $dir =  str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
   
      $core = preg_split('@/@', str_replace($_SERVER['DOCUMENT_ROOT'], '', realpath(dirname(__FILE__))), null, PREG_SPLIT_NO_EMPTY);
      $core = $core[0];
   
      $tmplt = $atRoot ? ($atCore ? "%s://%s/%s/" : "%s://%s/") : ($atCore ? "%s://%s/%s/" : "%s://%s%s");
      $end = $atRoot ? ($atCore ? $core : $hostname) : ($atCore ? $core : $dir);
      $base_url = sprintf( $tmplt, $http, $hostname, $end );
               
      }
      else $base_url = 'http://localhost/';
      
   
      if ($parse) {
      	
      $base_url = parse_url($base_url);
      
          if (isset($base_url['path'])) if ($base_url['path'] == '/') $base_url['path'] = '';
              
      }
   
   
   return $base_url;
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function time_date_format($offset=false, $mode=false) {
   
   
      if ( $offset == false ) {
      $time = time();
      }
      else {
      $time = time() + round( $offset * (60 * 60) );  // Offset is in hours (ROUNDED, so it can be decimals)
      }
   
   
      if ( $mode == false ) {
      $date = date("Y-m-d H:i:s", $time); // Format: 2001-03-10 17:16:18 (the MySQL DATETIME format)
      }
      elseif ( $mode == 'standard_date' ) {
      $date = date("Y-m-d", $time); // Format: 2001-03-10
      }
      elseif ( $mode == 'standard_time' ) {
      $date = date("H:i", $time); // Format: 22:45
      }
      elseif ( $mode == 'pretty_date_time' ) {
      $date = date("F jS, @ g:ia", $time); // Format: March 10th, @ 5:16pm
      }
      elseif ( $mode == 'pretty_date' ) {
      $date = date("F jS", $time); // Format: March 10th
      }
      elseif ( $mode == 'pretty_time' ) {
      $date = date("g:ia", $time); // Format: 5:16pm
      }
   
   
   // 'at' is a stubborn word to escape into the date() function, so we cheated a little
   $date = preg_replace("/@/", "at", $date); 
   
   return $date;
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function file_download($file, $save_as, $delete=true) {
      
   global $ct_conf;
   
   $type = pathinfo($save_as, PATHINFO_EXTENSION);
   
      if ( $type == 'csv' ) {
      $content_type = 'Content-type: text/csv; charset=' . $ct_conf['dev']['charset_default'];
      }
      else {
      $content_type = 'Content-type: application/octet-stream';
      }
   
   
      if ( file_exists($file) ) {
         
         header('Content-description: file transfer');
         header($content_type);
         header('Content-disposition: attachment; filename="'.basename($save_as).'"');
         header('Expires: 0');
         header('Cache-control: must-revalidate');
         header('Pragma: public');
         header('Content-length: ' . filesize($file));
         
         $result = readfile($file);
         
            if ( $result != false && $delete == true ) {
            unlink($file); // Delete file
            }
         
         exit;
         
      }
   
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function csv_import_array($file) {
   
   global $ct_conf;
      
      $row = 0;
      if ( ( $handle = fopen($file, "r") ) != false ) {
         
         while ( ( $data = fgetcsv($handle, 0, ",") ) != false ) {
            
         $num = count($data);
         $asset = strtoupper($data[0]);
         
            // ONLY importing if it exists in $ct_conf['assets']
            if ( is_array($ct_conf['assets'][$asset]) ) {
         
               for ($c=0; $c < $num; $c++) {
               $check_csv_rows[$asset][] = $data[$c];
               }
               
               // Validate / auto-correct the import data
               $validated_csv_import_row = $this->valid_csv_import_row($check_csv_rows[$asset]);
               
               if ( $validated_csv_import_row ) {
               $csv_rows[$asset] = $validated_csv_import_row;
               }
            
            }
            
         $row++;
            
         }
         fclose($handle);
   
         gc_collect_cycles(); // Clean memory cache
         
      }
   
   unlink($file); // Delete temp file
   
   return $csv_rows;
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function in_megabytes($str) {
   
   $str_val = preg_replace("/ (.*)/i", "", $str);
   
      // Always in megabytes
      if ( preg_match("/kilo/i", $str) || preg_match("/kb/i", $str) ) {
      $in_megs = $str_val * 0.001;
      $type = 'Kilobytes';
      }
      elseif ( preg_match("/mega/i", $str) || preg_match("/mb/i", $str) ) {
      $in_megs = $str_val * 1;
      $type = 'Megabytes';
      }
      elseif ( preg_match("/giga/i", $str) || preg_match("/gb/i", $str) ) {
      $in_megs = $str_val * 1000;
      $type = 'Gigabytes';
      }
      elseif ( preg_match("/tera/i", $str) || preg_match("/tb/i", $str) ) {
      $in_megs = $str_val * 1000000;
      $type = 'Terabytes';
      }
      elseif ( preg_match("/peta/i", $str) || preg_match("/pb/i", $str) ) {
      $in_megs = $str_val * 1000000000;
      $type = 'Petabytes';
      }
      elseif ( preg_match("/exa/i", $str) || preg_match("/eb/i", $str) ) {
      $in_megs = $str_val * 1000000000000;
      $type = 'Exabytes';
      }
      elseif ( preg_match("/zetta/i", $str) || preg_match("/zb/i", $str) ) {
      $in_megs = $str_val * 1000000000000000;
      $type = 'Zettabytes';
      }
      elseif ( preg_match("/yotta/i", $str) || preg_match("/yb/i", $str) ) {
      $in_megs = $str_val * 1000000000000000000;
      $type = 'Yottabytes';
      }
   
   $result['num_val'] = $str_val;
   $result['type'] = $type;
   $result['in_megs'] = round($in_megs, 3);
   
   return $result;
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   /* Usage: 
   
   // HTML
   $content = $ct_gen->txt_between_tags('a', $html);
   
   foreach( $content as $item ) {
       echo $item.'<br />';
   }
   
   // XML
   $content2 = $ct_gen->txt_between_tags('description', $xml, 1);
   
   foreach( $content2 as $item ) {
       echo $item.'<br />';
   }
   
   */
   
   // Credit: https://phpro.org/examples/Get-Text-Between-Tags.html
   function txt_between_tags($tag, $html, $strict=0) {
   	
       /*** a new dom object ***/
       $dom = new domDocument;
   
       /*** load the html into the object ***/
       if($strict==1) {
       $dom->loadXML($html);
       }
       else {
       $dom->loadHTML($html);
       }
   
       /*** discard white space ***/
       $dom->preserveWhiteSpace = false;
   
       /*** the tag by its tag name ***/
       $content = $dom->getElementsByTagname($tag);
   
       /*** the array to return ***/
       $out = array();
       foreach ($content as $item) {
           /*** add node value to the out array ***/
           $out[] = $item->nodeValue;
       }
   
       
   /*** return the results ***/
   return $out;
       
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function smtp_vars() {
   
   // To preserve SMTPMailer class upgrade structure, by creating a global var to be run in classes/smtp-mailer/conf/config_smtp.php
   
   global $app_version, $base_dir, $ct_conf;
   
   $vars = array();
   
   $log_file = $base_dir . "/cache/logs/smtp_error.log";
   $log_file_debug = $base_dir . "/cache/logs/smtp_debug.log";
   
   // Don't overwrite globals
   $temp_smtp_email_login = explode("||", $ct_conf['comms']['smtp_login'] );
   $temp_smtp_email_server = explode(":", $ct_conf['comms']['smtp_server'] );
   
   // To be safe, don't use trim() on certain strings with arbitrary non-alphanumeric characters here
   $smtp_user = trim($temp_smtp_email_login[0]);
   $smtp_password = $temp_smtp_email_login[1];
   
   $smtp_host = trim($temp_smtp_email_server[0]);
   $smtp_port = trim($temp_smtp_email_server[1]);
   
   
      // Set encryption type based on port number
      if ( $smtp_port == 25 ) {
      $smtp_secure = 'off';
      }
      elseif ( $smtp_port == 465 ) {
      $smtp_secure = 'ssl';
      }
      elseif ( $smtp_port == 587 ) {
      $smtp_secure = 'tls';
      }
   
   
   // Port vars over to class format (so it runs out-of-the-box as much as possible)
   $vars['cfg_log_file']   = $log_file;
   $vars['cfg_log_file_debug']   = $log_file_debug;
   $vars['cfg_server']   = $smtp_host;
   $vars['cfg_port']     =  $smtp_port;
   $vars['cfg_secure']   = $smtp_secure;
   $vars['cfg_username'] = $smtp_user;
   $vars['cfg_password'] = $smtp_password;
   $vars['cfg_debug_mode'] = $ct_conf['dev']['debug']; // Open Crypto Tracker debug mode setting
   $vars['cfg_strict_ssl'] = $ct_conf['dev']['smtp_strict_ssl']; // Open Crypto Tracker strict SSL setting
   $vars['cfg_app_version'] = $app_version; // Open Crypto Tracker version
   
   return $vars;
   
   }
    
    
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function valid_csv_import_row($csv_row) {
      
   global $ct_conf, $ct_var;
   
   // WE AUTO-CORRECT AS MUCH AS IS FEASIBLE, IF THE USER-INPUT IS CORRUPT / INVALID
   
   $csv_row = array_map('trim', $csv_row); // Trim entire array
      
   $csv_row[0] = strtoupper($csv_row[0]); // Asset to uppercase (we already validate it's existance in $this->csv_import_array())
          
   $csv_row[1] = $ct_var->rem_num_format($csv_row[1]); // Remove any number formatting in held amount
   
   // Remove any number formatting in paid amount, default paid amount to null if not a valid positive number
   $csv_row[2] = ( $ct_var->rem_num_format($csv_row[2]) >= 0 ? $ct_var->rem_num_format($csv_row[2]) : null ); 
      
   // If leverage amount input is corrupt, default to 0 (ALSO simple auto-correct if negative)
   $csv_row[3] = ( $ct_var->whole_int($csv_row[3]) != false && $csv_row[3] >= 0 ? $csv_row[3] : 0 ); 
      
   // If leverage is ABOVE 'margin_leverage_max', default to 'margin_leverage_max'
   $csv_row[3] = ( $csv_row[3] <= $ct_conf['power']['margin_leverage_max'] ? $csv_row[3] : $ct_conf['power']['margin_leverage_max'] ); 
   
   // Default to 'long', if not 'short' (set to lowercase...simple auto-correct, if set to anything other than 'short')
   $csv_row[4] = ( strtolower($csv_row[4]) == 'short' ? strtolower($csv_row[4]) : 'long' ); 
   
   // If market ID input is corrupt, default to 1 (it's ALWAYS 1 OR GREATER)
   $csv_row[5] = ( $ct_var->whole_int($csv_row[5]) != false && $csv_row[5] >= 1 ? $csv_row[5] : 1 ); 
      
   $csv_row[6] = strtolower($csv_row[6]); // Pairing to lowercase
      
      
      // Pairing auto-correction (if invalid pairing)
      if ( $csv_row[6] == '' || !is_array($ct_conf['assets'][ $csv_row[0] ]['pairing'][ $csv_row[6] ]) ) {
         
      $csv_row[5] = 1; // We need to reset the market id to 1 (it's ALWAYS 1 OR GREATER), as the pairing was not found
      
      // First key in $ct_conf['assets'][ $csv_row[0] ]['pairing']
      reset($ct_conf['assets'][ $csv_row[0] ]['pairing']);
      $csv_row[6] = key($ct_conf['assets'][ $csv_row[0] ]['pairing']);
      
      }
      // Market ID auto-correction (if invalid market ID)
      elseif ( is_array($ct_conf['assets'][ $csv_row[0] ]['pairing'][ $csv_row[6] ]) && sizeof($ct_conf['assets'][ $csv_row[0] ]['pairing'][ $csv_row[6] ]) < $csv_row[5] ) {
      $csv_row[5] = 1; // We need to reset the market id to 1 (it's ALWAYS 1 OR GREATER), as the ID was higher than available markets count
      }
      
      
      // Return false if there is no valid held amount
      if ( $csv_row[1] >= 0.00000001 )  {
      return $csv_row;
      }
      else {
      return false;
      }
      
   
   }
  
  
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
  
  
   function news_feed_email($interval) {
  
   global $ct_conf, $ct_cache, $ct_api, $base_dir, $base_url;
  
  
	  // 1439 minutes instead (minus 1 minute), to try keeping daily recurrences at same exact runtime (instead of moving up the runtime daily)
      if ( $ct_cache->update_cache($base_dir . '/cache/events/news-feed-email.dat', ($interval * 1439) ) == true ) {
      
      // Reset feed fetch telemetry 
      $_SESSION[$fetched_feeds] = false;
        
        
        	// NEW RSS feed posts
        	$num_posts = 0;
        	foreach($ct_conf['power']['news_feed'] as $feed_item) {
        	    
        		if ( trim($feed_item["url"]) != '' ) {
        		    
        		$result = $ct_api->rss($feed_item["url"], false, $ct_conf['comms']['news_feed_email_entries_show'], false, true);
        		
        		  if ( trim($result) != '<ul></ul>' ) {
        		  $html .= '<div style="padding: 30px;"><fieldset><legend style="font-weight: bold; color: #00b6db;"> ' . $feed_item["title"] . ' </legend>' . "\n\n";
        	 	  $html .= $result . "\n\n";
        		  $html .= '</fieldset></div>' . "\n\n";
        	 	  $num_posts++;  
        		  }
        		  
        	 	}
        	 	
        	}         
               
        	
      $top .= '<h2 style="color: #00b6db;">' . $num_posts . ' Updated RSS Feeds (over ' . $ct_conf['comms']['news_feed_email_freq'] . ' days)</h3>' . "\n\n";
        	
      $top .= '<p><a style="color: #00b6db;" title="View the news feeds page in the Open Crypto Tracker app here." target="_blank" href="' . $base_url . 'index.php?start_page=news#news">View All News Feeds Here</a></p>' . "\n\n";
	
	  $top .= '<p style="color: #dd7c0d;">You can disable receiving news feed emails in the Admin Config "Communications" section.</p>' . "\n\n";
	
	  $top .= '<p style="color: #dd7c0d;">You can edit this list in the Admin Config "Power User" section.</p>' . "\n\n";
	
	  $top .= '<p>To see the date / time an entry was published, hover over it.</p>' . "\n\n";
	
	  $top .= '<p>Entries are sorted newest to oldest.</p>' . "\n\n";
      
      
      $email_body = '<div style="padding: 15px;">' . $top . $html . '</div>';
      
               
      $send_params = array(
                                                    
                           'email' => array(
                                            'content_type' => 'text/html', // Have email sent as HTML content type
                                            'subject' => $num_posts . ' Updated RSS Feeds (over ' . $ct_conf['comms']['news_feed_email_freq'] . ' days)',
                                            'message' => $email_body // Add emoji here, so it's not sent with alexa alerts
                                           )
                                                       
                          );
                    
                    
                    
      // Send notifications
      @$ct_cache->queue_notify($send_params);
                        
      
      $ct_cache->save_file($base_dir . '/cache/events/news-feed-email.dat', $this->time_date_format(false, 'pretty_date_time') );
      
      }
      
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function prune_first_lines($filename, $num, $oldest_allowed_timestamp=false) {
   
   $result = array();
   $file = file($filename);
   
    if ( !is_array($file) ) {
    $result['lines_removed'] = 0;
    $result['data'] = false;
    return $result;
    }
   
   $size = sizeof($file);
   $loop = 0;
   
   
      if ( $oldest_allowed_timestamp == false ) {
      
         while ( $loop < $num && !$stop_loop ) {
            
            if ( isset($file[$loop]) ) {
            unset($file[$loop]);
            }
            else {
            $stop_loop = true;
            }
            
         $loop = $loop + 1;
         }
      
      }
      else {
      
         while( $loop < $size && !$stop_loop ) {
         
            if ( isset($file[$loop]) ) {
               
            $line_array = explode("||", $file[$loop]);
            $line_timestamp = $line_array[0];
            
               // If timestamp is older than allowed, we remove the line
               if ( $line_timestamp < $oldest_allowed_timestamp ) {
               unset($file[$loop]);
               }
               else {
               $stop_loop = true;
               }
            
            }
            else {
            $stop_loop = true;
            }
            
         $loop = $loop + 1;
         }
      
      }
      
      
   $result['lines_removed'] = $size - sizeof($file);
   $result['data'] = implode("", $file); // WITHOUT newline delimiting, since file() maintains those by default
   
   return $result;
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function pass_strength($password, $min_length, $max_length) {
   
   global $ct_conf;
   
   
       if ( $min_length == $max_length && mb_strlen($password, $ct_conf['dev']['charset_default']) != $min_length ) {
       $error .= "MUST BE EXACTLY ".$min_length." characters; ";
       }
       elseif ( mb_strlen($password, $ct_conf['dev']['charset_default']) < $min_length ) {
       $error .= "requires AT LEAST ".$min_length." characters; ";
       }
       elseif ( mb_strlen($password, $ct_conf['dev']['charset_default']) > $max_length ) {
       $error .= "requires NO MORE THAN ".$max_length." characters; ";
       }
       
       
       if ( !preg_match("#[0-9]+#", $password) ) {
       $error .= "include one number; ";
       }
       
       if ( !preg_match("#[a-z]+#", $password) ) {
       $error .= "include one LOWERCASE letter; ";
       }
       
       if ( !preg_match("#[A-Z]+#", $password) ) {
       $error .= "include one UPPERCASE letter; ";
       }
       
       if ( !preg_match("#\W+#", $password) ) {
       $error .= "include one symbol; ";
       }
       
       if ( preg_match('/\s/',$password) ) {
       $error .= "no spaces allowed; ";
       }
       
       if ( preg_match('/\|\|/',$password) ) {
       $error .= "no double pipe (||) allowed; ";
       }
       
       if ( preg_match('/\:/',$password) ) {
       $error .= "no colon (:) allowed; ";
       }
       
       
       if( $error ){
       return 'password_strength_error: ' . $error;
       }
       else {
       return 'valid';
       }
   
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function reset_price_alert_notice() {
   
   global $ct_conf, $ct_cache, $price_alert_fixed_reset_array, $default_btc_prim_currency_pairing;
   
   // Alphabetical asset sort, for message UX 
   ksort($price_alert_fixed_reset_array);
   
   
      $count = 0;
      foreach( $price_alert_fixed_reset_array as $reset_data ) {
      
         foreach( $reset_data as $asset_alert ) {
         
         $reset_list .= $asset_alert . ', ';
         
         $count = $count + 1;
         
         }
      
      }

      
   // Trim results
   $reset_list = trim($reset_list);
   $reset_list = rtrim($reset_list, ',');
   
      
      // Return if no resets occurred
      if ( $count < 1 ) {
      return;
      }
   
   
   $text_msg = $count . ' ' . strtoupper($default_btc_prim_currency_pairing) . ' Price Alert Fixed Resets: ' . $reset_list;
   
   $email_msg = 'The following ' . $count . ' ' . strtoupper($default_btc_prim_currency_pairing) . ' price alert fixed resets (run every ' . $ct_conf['charts_alerts']['price_alert_fixed_reset'] . ' days) have been processed, with the latest spot price data: ' . $reset_list;
   
   $notifyme_msg = $email_msg . ' Timestamp is ' . $this->time_date_format($ct_conf['gen']['loc_time_offset'], 'pretty_time') . '.';
   
   
   // Message parameter added for desired comm methods (leave any comm method blank to skip sending via that method)
                       
   // Minimize function calls
   $encoded_text_msg = $this->charset_encode($text_msg); // Unicode support included for text messages (emojis / asian characters / etc )
                       
   $send_params = array(
   
                        'notifyme' => $notifyme_msg,
                        'telegram' => $email_msg,
                        'text' => array(
                                        'message' => $encoded_text_msg['content_output'],
                                        'charset' => $encoded_text_msg['charset']
                                        ),
                        'email' => array(
                                         'subject' => 'Price Alert Fixed Reset Processed For ' . $count . ' Alert(s)',
                                         'message' => $email_msg 
                                         )
                                         
                          );
                   
                   
   // Send notifications
   @$ct_cache->queue_notify($send_params);
         
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
    // https://thisinterestsme.com/random-rgb-hex-color-php/ (MODIFIED)
    // Human visual perception of different color mixes seems a tad beyond what an algo can distinguish based off AVERAGE range minimums,
    // ESPECIALLY once a list of random-colored items get above a certain size in number (as this decreases your availiable range minimum)
    // That said, auto-adjusting range minimums based off available RGB palette / list size IS feasible AND seems about as good as it can get,
    // AS LONG AS YOU DON'T OVER-MINIMIZE THE RANDOM OPTIONS / EXAUST ALL RANDOM OPTIONS (AND ENDLESSLY LOOP)
   function rand_color($list_size) {
      
   global $rand_color_ranged;
   
   // WE DON'T USE THE ENTIRE 0-255 RANGES, AS SOME COLORS ARE TOO DARK / LIGHT AT FULL RANGES
   $darkest = 79;
   $lightest = 178;
   $thres_min = 0.675; // (X.XXX) Only require X% of threshold, to avoid exhuasting decent amount / ALL of random options
       
   // Minimum range threshold, based on USED RGB pallette AND number of colored items 
   // (range minimum based on list size, AND $thres_min)
   $min_range = round( ( ($lightest - $darkest) / $list_size ) * $thres_min );
   // ABSOLUTE min (max auto-calculated within safe range)
   $min_range = ( $min_range < 1 ? 1 : $min_range );
   
   
      // Generate random colors, WITH minimum (average) range differences
      while ( $result['hex'] == '' ) {
      
      $result = array('rgb' => '', 'hex' => '');
      $hex = null;
      $range_too_close = false;
      
      
         /////////////////////////////////
         // Randomly generate a color
         /////////////////////////////////
         foreach( array('r', 'b', 'g') as $col ) {
         
         $rand = mt_rand($darkest, $lightest); 
         $rgb[$col] = $rand;
         $dechex = dechex($rand);
             
             if( strlen($dechex) < 2 ){
             $dechex = '0' . $dechex;
             }
             
         $hex .= $dechex;
         
         }
       
         
         /////////////////////////////////
         // Check to make sure new random color isn't within range (nearly same color codes) of any colors already generated
         /////////////////////////////////
         if( is_array($rand_color_ranged) && sizeof($rand_color_ranged) > 0 ) {
         
            // Compare new random color's range to any colors already generated
            foreach( $rand_color_ranged as $used_range ) {
               
            $overall_range = abs($rgb['r'] - $used_range['r']) + abs($rgb['g'] - $used_range['g']) + abs($rgb['b'] - $used_range['b']);
               
               // If we are too close to a previously-generated random color's range, flag it
               if ( $overall_range < ($min_range * 3) ) {
               $range_too_close = true;
               }
               
            }
         
            
            // If the new random color is NOT out of range, use it / add it to list of any colors already generated
            if ( !$range_too_close ) {
            $rand_color_ranged[] = $rgb;
            $result['hex'] = $hex;
            $result['rgb'] = $rgb;
            }
         
         }
         /////////////////////////////////
         // If this is the first random color generated
         /////////////////////////////////
         else {
         $rand_color_ranged[] = $rgb;
         $result['hex'] = $hex;
         $result['rgb'] = $rgb;
         }
       
       
      }
      
   
   return $result;
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   // Check to see if we need to upgrade the app config (add new primary vars / remove depreciated primary vars)
   function upgrade_cache_ct_conf() {
   
   global $upgraded_ct_conf, $cached_ct_conf, $check_default_ct_conf, $default_ct_conf;
   
   $upgraded_ct_conf = $cached_ct_conf;
   
   
   // WE LEAVE THE SUB-ARRAYS FOR PROXIES / CHARTS / TEXT GATEWAYS / PORTFOLIO ASSETS / ETC / ETC ALONE
   // (ANY SUB-ARRAY WHERE A USER ADDS / DELETES VARIABLES THEY WANTED DIFFERENT FROM DEFAULT VARS)
   $skip_upgrading = array(
                           'proxy',
                           'tracked_markets',
                           'crypto_pairing',
                           'crypto_pairing_pref_markets',
                           'btc_currency_markets',
                           'btc_pref_currency_markets',
                           'eth_erc20_icos',
                           'mob_net_txt_gateways',
                           'assets',
                           'news_feed',
                           );
   
   
      // If no cached app config or it's corrupt, just use full default app config
      if ( $cached_ct_conf != true ) {
      return $default_ct_conf;
      }
      // If the default app config has changed since last check (from upgrades / end user editing)
      elseif ( $check_default_ct_conf != md5(serialize($default_ct_conf)) ) {
         
         
         // Check for new variables, and add them
         foreach ( $default_ct_conf as $cat_key => $cat_val ) {
            
            foreach ( $cat_val as $conf_key => $conf_val ) {
         
               if ( !in_array($cat_key, $skip_upgrading) && !in_array($conf_key, $skip_upgrading) ) {
                  
                  if ( is_array($conf_val) ) {
                  $this->subarray_ct_conf_upgrade($cat_key, $conf_key, $skip_upgrading);
                  }
                  elseif ( !isset($upgraded_ct_conf[$cat_key][$conf_key]) ) {
                  	
                  $upgraded_ct_conf[$cat_key][$conf_key] = $default_ct_conf[$cat_key][$conf_key];
                  
                  $this->log(
                  			'conf_error',
                  			'Outdated app config, upgraded parameter $ct_conf[' . $cat_key . '][' . $conf_key . '] imported (default value: ' . $default_ct_conf[$cat_key][$conf_key] . ')'
                  			);
                  						
                  $conf_upgraded = 1;
                  
                  }
            
               }
            
            }
         
         }
         
         
         // Check for depreciated variables, and remove them
         foreach ( $cached_ct_conf as $cached_cat_key => $cached_cat_val ) {
            
            foreach ( $cached_cat_val as $cached_conf_key => $cached_conf_val ) {
         
               if ( !in_array($cached_cat_key, $skip_upgrading) && !in_array($cached_conf_key, $skip_upgrading) ) {
               
                  if ( is_array($cached_conf_val) ) {
                  $this->subarray_ct_conf_upgrade($cached_cat_key, $cached_conf_key, $skip_upgrading);
                  }
                  elseif ( !isset($default_ct_conf[$cached_cat_key][$cached_conf_key]) ) {
                  	
                  unset($upgraded_ct_conf[$cached_cat_key][$cached_conf_key]);
                  
                  $this->log(
                  			'conf_error',
                  			'Depreciated app config parameter $ct_conf[' . $cached_cat_key . '][' . $cached_conf_key . '] removed'
                  			);
                  
                  $conf_upgraded = 1;
                  
                  }
                  
               }
               
            }
            
         }
         
      
      return $upgraded_ct_conf;
      
      }
   
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function charset_encode($content) {
      
   global $ct_conf;
   
   
   // Charsets we want to try and detect here
   // (SAVE HERE FOR POSSIBLE FUTURE USE)
   $charset_array = array(
                           'ASCII',
                           'UCS-2',
                           'UCS-2BE',
                           'UTF-16BE',
                           'UTF-16LE',
                           'UTF-16',
                           'UTF-8',
                           );
   
   
   // Changs only if non-UTF-8 / non-ASCII characters are detected further down in this function
   $set_charset = $ct_conf['dev']['charset_default'];
   
   $words = explode(" ", $content);
      
      
      foreach ( $words as $scan_key => $scan_val ) {
         
      $scan_val = trim($scan_val);
      
      $scan_charset = ( mb_detect_encoding($scan_val, 'auto') != false ? mb_detect_encoding($scan_val, 'auto') : null );
      
         if ( isset($scan_charset) && !preg_match("/" . $ct_conf['dev']['charset_default'] . "/i", $scan_charset) && !preg_match("/ASCII/i", $scan_charset) ) {
         $set_charset = $ct_conf['dev']['charset_unicode'];
         }
      
      }
   
      
      foreach ( $words as $word_key => $word_val ) {
         
      $word_val = trim($word_val);
      
      $word_charset = ( mb_detect_encoding($word_val, 'auto') != false ? mb_detect_encoding($word_val, 'auto') : null );
      
      $result['debug_original_charset'] .= ( isset($word_charset) ? $word_charset . ' ' : 'unknown_charset ' );
      
         if ( isset($word_charset) && strtolower($word_charset) == strtolower($set_charset) ) {
         $temp = $word_val . ' ';
         }
         elseif ( isset($word_charset) && strtolower($set_charset) != strtolower($word_charset) ) {
         $temp = mb_convert_encoding($word_val . ' ', $set_charset, $word_charset);
         }
         elseif ( !isset($word_charset) ) {
         $temp = mb_convert_encoding($word_val . ' ', $set_charset);
         }
         
         $temp_converted .= $temp;
         
      }
      
   
   $temp_converted = trim($temp_converted);
      
   $result['debug_original_charset'] = trim($result['debug_original_charset']);
   
   $result['debug_temp_converted'] = $temp_converted;
   
   $result['charset'] = $set_charset;
      
   $result['length'] = mb_strlen($temp_converted, $set_charset); // Get character length AFTER trim() / BEFORE bin2hex() processing
         
      
      if ( $set_charset == $ct_conf['dev']['charset_unicode'] ) {
         
         for($i =0; $i < strlen($temp_converted); $i++) {
         //$content_converted .= ' ' . strtoupper(bin2hex($temp_converted[$i])); // Spacing between characters
         $content_converted .= strtoupper(bin2hex($temp_converted[$i])); // No spacing
         }
      
      $result['content_output'] = trim($content_converted);
      }
      else {
      $result['content_output'] = $temp_converted;
      }
      
   
   return $result;
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function light_chart_time_period($lite_chart_days, $mode) {
      
      
      if ( $mode == 'short' ) {
   
         if ( $lite_chart_days == 'all' ) {
         $time_period_text = strtoupper($lite_chart_days);
         }
         elseif ( $lite_chart_days == 7 ) {
         $time_period_text = '1W';
         }
         elseif ( $lite_chart_days == 14 ) {
         $time_period_text = '2W';
         }
         elseif ( $lite_chart_days == 21 ) {
         $time_period_text = '3W';
         }
         elseif ( $lite_chart_days == 30 ) {
         $time_period_text = '1M';
         }
         elseif ( $lite_chart_days == 60 ) {
         $time_period_text = '2M';
         }
         elseif ( $lite_chart_days == 90 ) {
         $time_period_text = '3M';
         }
         elseif ( $lite_chart_days == 120 ) {
         $time_period_text = '4M';
         }
         elseif ( $lite_chart_days == 150 ) {
         $time_period_text = '5M';
         }
         elseif ( $lite_chart_days == 180 ) {
         $time_period_text = '6M';
         }
         elseif ( $lite_chart_days == 365 ) {
         $time_period_text = '1Y';
         }
         elseif ( $lite_chart_days == 730 ) {
         $time_period_text = '2Y';
         }
         elseif ( $lite_chart_days == 1095 ) {
         $time_period_text = '3Y';
         }
         elseif ( $lite_chart_days == 1460 ) {
         $time_period_text = '4Y';
         }
         elseif ( $lite_chart_days == 1825 ) {
         $time_period_text = '5Y';
         }
         else {
         $time_period_text = $lite_chart_days . 'D';
         }
      
      }
      elseif ( $mode == 'long' ) {
   
         if ( $lite_chart_days == 'all' ) {
         $time_period_text = ucfirst($lite_chart_days);
         }
         elseif ( $lite_chart_days == 7 ) {
         $time_period_text = '1 Week';
         }
         elseif ( $lite_chart_days == 14 ) {
         $time_period_text = '2 Weeks';
         }
         elseif ( $lite_chart_days == 21 ) {
         $time_period_text = '3 Weeks';
         }
         elseif ( $lite_chart_days == 30 ) {
         $time_period_text = '1 Month';
         }
         elseif ( $lite_chart_days == 60 ) {
         $time_period_text = '2 Months';
         }
         elseif ( $lite_chart_days == 90 ) {
         $time_period_text = '3 Months';
         }
         elseif ( $lite_chart_days == 120 ) {
         $time_period_text = '4 Months';
         }
         elseif ( $lite_chart_days == 150 ) {
         $time_period_text = '5 Months';
         }
         elseif ( $lite_chart_days == 180 ) {
         $time_period_text = '6 Months';
         }
         elseif ( $lite_chart_days == 365 ) {
         $time_period_text = '1 Year';
         }
         elseif ( $lite_chart_days == 730 ) {
         $time_period_text = '2 Years';
         }
         elseif ( $lite_chart_days == 1095 ) {
         $time_period_text = '3 Years';
         }
         elseif ( $lite_chart_days == 1460 ) {
         $time_period_text = '4 Years';
         }
         elseif ( $lite_chart_days == 1825 ) {
         $time_period_text = '5 Years';
         }
         else {
         $time_period_text = $lite_chart_days . ' Days';
         }
      
      }
   
   
   return $time_period_text;
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function chart_data($file, $chart_format, $start_timestamp=0) {
   
   global $ct_conf, $ct_var, $default_btc_prim_currency_pairing, $runtime_nonce, $runtime_data;
   
   
      // #FOR CLEAN CODE#, RUN CHECK TO MAKE SURE IT'S NOT A CRYPTO AS WELL...WE HAVE A COUPLE SUPPORTED, BUT WE ONLY WANT DESIGNATED FIAT-EQIV HERE
      if ( array_key_exists($chart_format, $ct_conf['power']['btc_currency_markets']) && !array_key_exists($chart_format, $ct_conf['power']['crypto_pairing']) ) {
      $fiat_formatting = true;
      }
      elseif ( $chart_format == 'system' ) {
      $system_statistics_chart = true;
      }
      elseif ( $chart_format == 'performance' ) {
      $asset_perf_chart = true;
      $asset = $file;
      $asset = preg_replace("/(.*)_days\//i", "", $asset);
      $asset = preg_replace("/\/(.*)/i", "", $asset);
      }
   
   
   $data = array();
   $fn = fopen($file,"r");
     
     while( !feof($fn) )  {
      
      $result = explode("||", fgets($fn) );
      
         if ( trim($result[0]) != '' && trim($result[0]) >= $start_timestamp ) {
            
         $data['time'] .= trim($result[0]) . '000,';  // Zingchart wants 3 more zeros with unix time (milliseconds)
         
         
            if ( $system_statistics_chart ) {
            
            $data['temperature_celsius'] .= trim($result[2]) . ',';
            $data['used_memory_percentage'] .= trim($result[4]) . ',';
            $data['cron_core_runtime_seconds'] .= trim($result[7]) . ',';
            $data['used_memory_gigabytes'] .= trim($result[3]) . ',';
            $data['load_average_15_minutes'] .= trim($result[1]) . ',';
            $data['free_disk_space_terabytes'] .= trim($result[5]) . ',';
            $data['portfolio_cache_size_gigabytes'] .= trim($result[6]) . ',';
            
            }
            elseif ( $asset_perf_chart ) {
      
               if ( !$runtime_data['performance_stats'][$asset]['start_val'] ) {
               $runtime_data['performance_stats'][$asset]['start_val'] = $result[1];
               
               $data['percent'] .= '0.00,';
               $data['combined'] .= '[' . trim($result[0]) . '000, 0.00],';  // Zingchart wants 3 more zeros with unix time (milliseconds)
               }
               else {
                  
               // PRIMARY CURRENCY CONFIG price percent change (CAN BE NEGATIVE OR POSITIVE IN THIS INSTANCE)
               $percent_change = ($result[1] - $runtime_data['performance_stats'][$asset]['start_val']) / abs($runtime_data['performance_stats'][$asset]['start_val']) * 100;
               // Better decimal support
               $percent_change = $ct_var->num_to_str($percent_change); 
               
               $data['percent'] .= round($percent_change, 2) . ',';
               $data['combined'] .= '[' . trim($result[0]) . '000' . ', ' . round($percent_change, 2) . '],';  // Zingchart wants 3 more zeros with unix time (milliseconds)
               
               }
            
            }
            else {
            
               // Format or round primary currency price depending on value (non-stablecoin crypto values are already stored in the format we want for the interface)
               if ( $fiat_formatting ) {
               $data['spot'] .= ( $ct_var->num_to_str($result[1]) >= 1 ? number_format((float)$result[1], 2, '.', '')  :  round($result[1], $ct_conf['gen']['prim_currency_dec_max'])  ) . ',';
               $data['volume'] .= round($result[2]) . ',';
               }
               // Non-stablecoin crypto
               else {
               $data['spot'] .= $result[1] . ',';
               $data['volume'] .= round($result[2], $ct_conf['power']['chart_crypto_vol_dec']) . ',';
               }
            
            }
         
         
         }
      
     }
   
   fclose($fn);
   
   gc_collect_cycles(); // Clean memory cache
   
   // Trim away extra commas
   $data['time'] = rtrim($data['time'],',');
   
   
      if ( $system_statistics_chart ) {
      $data['temperature_celsius'] = rtrim($data['temperature_celsius'],',');
      $data['used_memory_percentage'] = rtrim($data['used_memory_percentage'],',');
      $data['cron_core_runtime_seconds'] = rtrim($data['cron_core_runtime_seconds'],',');
      $data['used_memory_gigabytes'] = rtrim($data['used_memory_gigabytes'],',');
      $data['load_average_15_minutes'] = rtrim($data['load_average_15_minutes'],',');
      $data['free_disk_space_terabytes'] = rtrim($data['free_disk_space_terabytes'],',');
      $data['portfolio_cache_size_gigabytes'] = rtrim($data['portfolio_cache_size_gigabytes'],',');
      }
      elseif ( $asset_perf_chart ) {
      $data['percent'] = rtrim($data['percent'],',');
      $data['combined'] = rtrim($data['combined'],',');
      }
      else {
      $data['spot'] = rtrim($data['spot'],',');
      $data['volume'] = rtrim($data['volume'],',');
      }
   
   
      foreach ( $data as $check_key => $check_value ) {
      
        if ( $check_value == 'NO_DATA' || $check_value == '' ) {
        unset($data[$check_key]);
        }
      
      }
      
   
   return $data;
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function update_all_cookies($cookie_params) {
   
              
   	// Portfolio data
   	// Cookies expire in 1 year (31536000 seconds)
   
   	  foreach ( $cookie_params as $cookie_key => $cookie_val ) {
   	  $this->store_cookie($cookie_key, $cookie_val, time()+31536000);
   	  }
              
   
      // UI settings (not included in any portfolio data)
      if ( $_POST['submit_check'] == 1 ) {
               
                  
            if ( isset($_POST['show_charts']) ) {
            $this->store_cookie("show_charts", $_POST['show_charts'], time()+31536000);
            }
            else {
            unset($_COOKIE['show_charts']);  // Delete any existing cookies
            }
                  
            if ( isset($_POST['show_crypto_val']) ) {
            $this->store_cookie("show_crypto_val", $_POST['show_crypto_val'], time()+31536000);
            }
            else {
            unset($_COOKIE['show_crypto_val']);  // Delete any existing cookies
            }
                  
            if ( isset($_POST['show_secondary_trade_val']) ) {
            $this->store_cookie("show_secondary_trade_val", $_POST['show_secondary_trade_val'], time()+31536000);
            }
            else {
            unset($_COOKIE['show_secondary_trade_val']);  // Delete any existing cookies
            }
                  
            if ( isset($_POST['show_feeds']) ) {
            $this->store_cookie("show_feeds", $_POST['show_feeds'], time()+31536000);
            }
            else {
            unset($_COOKIE['show_feeds']);  // Delete any existing cookies
            }
                 
            if ( isset($_POST['theme_selected']) ) {
            $this->store_cookie("theme_selected", $_POST['theme_selected'], time()+31536000);
            }
            else {
            unset($_COOKIE['theme_selected']);  // Delete any existing cookies
            }
                  
            if ( isset($_POST['sort_by']) ) {
            $this->store_cookie("sort_by", $_POST['sort_by'], time()+31536000);
            }
            else {
            unset($_COOKIE['sort_by']);  // Delete any existing cookies
            }
                 
            if ( isset($_POST['use_alert_percent']) ) {
            $this->store_cookie("alert_percent", $_POST['use_alert_percent'], time()+31536000);
            }
            else {
            unset($_COOKIE['alert_percent']);  // Delete any existing cookies
            }
                 
            if ( isset($_POST['prim_currency_market_standalone']) ) {
            $this->store_cookie("prim_currency_market_standalone", $_POST['prim_currency_market_standalone'], time()+31536000);
            }
            else {
            unset($_COOKIE['prim_currency_market_standalone']);  // Delete any existing cookies
            }
                 
               
            // Notes (only creation / deletion here, update logic is in cookies.php)
            if ( $_POST['use_notes'] == 1 && !$_COOKIE['notes'] ) {
            $this->store_cookie("notes", " ", time()+31536000); // Initialized with some whitespace when blank
            }
            elseif ( $_POST['use_notes'] != 1 ) {
            unset($_COOKIE['notes']);  // Delete any existing cookies
            }
              
              
      }
              
    
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function zip_recursively($source, $destination, $password=false) {
      
         
         if ( !extension_loaded('zip') ) {
         return 'no_extension';
         }
         elseif ( !file_exists($source) ) {
         return 'no_source';
         }
      
      
   $zip = new ZipArchive();
         
         
         if ( !$zip->open($destination, ZIPARCHIVE::CREATE) ) {
         return 'no_open_dest';
         }
         
         
         // If we are password-protecting
         if ( $password != false ) {
         $zip->setPassword($password);
         }
      
      
   $source = str_replace('\\', '/', realpath($source));
      
      
         if ( is_dir($source) === true ) {
            
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
      
            foreach ($files as $file) {
               
               $file = str_replace('\\', '/', $file);
      
               // Ignore "." and ".." folders
               if ( in_array( substr($file, strrpos($file, '/')+1) , array('.', '..') ) )
                  continue;
      
               $file = realpath($file);
      
               if (is_dir($file) === true) {
               $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
               }
               elseif (is_file($file) === true) {
                  
               $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
                  
                  // If we are password-protecting
                  if ( $password != false ) {
                  $zip->setEncryptionName(str_replace($source . '/', '', $file), ZipArchive::EM_AES_256);
                  }
                  
               }
               
            }
            
         }
         elseif ( is_file($source) === true ) {
            
         $zip->addFromString(basename($source), file_get_contents($source));
            
            // If we are password-protecting
            if ( $password != false ) {
            $zip->setEncryptionName(basename($source), ZipArchive::EM_AES_256);
            }
            
         }
      
      
   return $zip->close();      
       
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function start_page_html($page) {
      
      if ( $_GET['start_page'] != '' ) {
      $border_highlight = '_red';
      $text_class = 'red';
      }
      
   ?>
   <span class='start_page_menu<?=$border_highlight?>'> 
      
      <select class='browser-default custom-select' title='Sets alternate start pages, and saves your scroll position on alternate start pages during reloads.' class='<?=$text_class?>' onchange='
      
         if ( this.value == "index.php?start_page=<?=$page?>" ) {
         var anchor = "#<?=$page?>";
         }
         else {
         var anchor = "";
         sessionStorage["scroll_position"] = 0;
         }
      
      // This start page method saves portfolio data during the session, even without cookie data enabled
      var set_action = this.value + anchor;
      set_target_action("coin_amounts", "_self", set_action);
      $("#coin_amounts").submit();
      
      '>
         <option value='index.php'> Show Portfolio Page First </option>
         <?php
         if ( $_GET['start_page'] != '' && $_GET['start_page'] != $page ) {
         $another_set = 1;
         ?>
         <option value='index.php?start_page=<?=$_GET['start_page']?>' selected > Show <?=ucwords( preg_replace("/_/i", " ", $_GET['start_page']) )?> Page First </option>
         <?php
         }
         ?>
         <option value='index.php?start_page=<?=$page?>' <?=( $_GET['start_page'] == $page ? 'selected' : '' )?> > Show <?=ucwords( preg_replace("/_/i", " ", $page) )?> Page First </option>
      </select> 
      
   </span>
   
      <?php
      if ( $another_set == 1 ) {
      ?>
      <span class='red'>&nbsp;(this other secondary page is currently the start page)</span>
       <br class='clear_both' />
      <?php
      }
      elseif ( $_GET['start_page'] == $page ) {
      ?>
      <span class='red'>&nbsp;(this page is currently the start page)</span>
       <br class='clear_both' />
      <?php
      }
      
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function safe_mail($to, $subj, $msg, $content_type='text/plain', $charset=null) {
      
   global $app_version, $ct_conf;
   
      if ( $charset == null ) {
      $charset = $ct_conf['dev']['charset_default'];
      }
   
   // Stop injection vulnerability
   $ct_conf['comms']['from_email'] = str_replace("\r\n", "", $ct_conf['comms']['from_email']); // windows -> unix
   $ct_conf['comms']['from_email'] = str_replace("\r", "", $ct_conf['comms']['from_email']);   // remaining -> unix
   
   // Trim any (remaining) whitespace off ends
   $ct_conf['comms']['from_email'] = trim($ct_conf['comms']['from_email']);
   $to = trim($to);
         
         
      // Validate TO email
      $email_check = $this->valid_email($to);
      if ( $email_check != 'valid' ) {
      return $email_check;
      }
      
      
      // SMTP mailing, or PHP's built-in mail() function
      if ( $ct_conf['comms']['smtp_login'] != '' && $ct_conf['comms']['smtp_server'] != '' ) {
      return @$this->smtp_mail($to, $subj, $msg, $content_type, $charset); 
      }
      else {
         
         // Use array for safety from header injection >= PHP 7.2 
         if ( PHP_VERSION_ID >= 70200 ) {
            
            // Fallback, if no From email set in app config
            if ( $this->valid_email($ct_conf['comms']['from_email']) == 'valid' ) {
            
            $headers = array(
                        'From' => 'From: Open Crypto Tracker <' . $ct_conf['comms']['from_email'] . '>',
                        'X-Mailer' => 'Open_Crypto_Tracker/' . $app_version . ' - PHP/' . phpversion(),
                        'Content-Type' => $content_type . '; charset=' . $charset
                           );
            
            }
            else {
            
            $headers = array(
                        'X-Mailer' => 'Open_Crypto_Tracker/' . $app_version . ' - PHP/' . phpversion(),
                        'Content-Type' => $content_type . '; charset=' . $charset
                           );
            
            }
      
         }
         else {
            
            // Fallback, if no From email set in app config
            if ( $this->valid_email($ct_conf['comms']['from_email']) == 'valid' ) {
            
            $headers = 'From: Open Crypto Tracker <' . $ct_conf['comms']['from_email'] . ">\r\n" .
            'X-Mailer: Open_Crypto_Tracker/' . $app_version . ' - PHP/' . phpversion() . "\r\n" .
            'Content-Type: ' . $content_type . '; charset=' . $charset;
         
            }
            else {
            
            $headers = 'X-Mailer: Open_Crypto_Tracker/' . $app_version . ' - PHP/' . phpversion() . "\r\n" .
            'Content-Type: ' . $content_type . '; charset=' . $charset;
         
            }
         
         }
         
      
      return @mail($to, $subj, $msg, $headers);
      
      }
   
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function get_last_lines($file, $linecount, $length) {
      
   $linecount = $linecount + 2; // Offset including blank data on ends
   
   $length = $length * 1.5; // Offset to assure we get enough data
   
   //we double the offset factor on each iteration
   //if our first guess at the file offset doesn't
   //yield $linecount lines
   $offset_factor = 1;
   
   $bytes = filesize($file);
   
   $fp = fopen($file, "r");
   
      if ( !$fp ) {
      return false;
      }
   
   
      $complete = false;
      while ( !$complete ) {
         
      //seek to a position close to end of file
      $offset = $linecount * $length * $offset_factor;
      fseek($fp, -$offset, SEEK_END);
      
      
         //we might seek mid-line, so read partial line
         //if our offset means we're reading the whole file, 
         //we don't skip...
         if ( $offset < $bytes ) {
         fgets($fp);
         }
      
      
         //read all following lines, store last x
         $lines = array();
         while( !feof($fp) ) {
            
            $line = fgets($fp);
            array_push($lines, $line);
            
            if ( count($lines) > $linecount ) {
            array_shift($lines);
            $complete = true;
            }
            
         }
      
      
         //if we read the whole file, we're done, even if we
         //don't have enough lines
         if ( $offset >= $bytes ) {
         $complete = true;
         }
         else {
         $offset_factor *= 2; //otherwise let's seek even further back
         }
          
          
      }
   
   fclose($fp);
   
   gc_collect_cycles(); // Clean memory cache
   
   
      if ( !$lines ) {
      return false;
      }
      else {
      return array_slice( $lines, (0 - $linecount) );
      }
   
   
   }
  
  
  ////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////
  
  
  function test_proxy($problem_proxy_array) {
  
  global $base_dir, $ct_conf, $ct_cache, $runtime_mode, $proxies_checked;
  
  // Endpoint to test proxy connectivity: https://www.myip.com/api-docs/
  $proxy_test_url = 'https://api.myip.com/';
  
  $problem_endpoint = $problem_proxy_array['endpoint'];
  
  $obfusc_url_data = $this->obfusc_url_data($problem_endpoint); // Automatically removes sensitive URL data
  
  $problem_proxy = $problem_proxy_array['proxy'];
  
  $ip_port = explode(':', $problem_proxy);
  
  $ip = $ip_port[0];
  $port = $ip_port[1];
  
  
      // If no ip/port detected in data string, cancel and continue runtime
      if ( !$ip || !$port ) {
      $this->log('ext_data_error', 'proxy '.$problem_proxy.' is not a valid format');
      return false;
      }
  
  
  // Create cache filename / session var
  $cache_filename = $problem_proxy;
  $cache_filename = preg_replace("/\./", "-", $cache_filename);
  $cache_filename = preg_replace("/:/", "_", $cache_filename);
  
  
      if ( $ct_conf['comms']['proxy_alert_runtime'] == 'all' ) {
      $run_alerts = 1;
      }
      elseif ( $ct_conf['comms']['proxy_alert_runtime'] == 'cron' && $runtime_mode == 'cron' ) {
      $run_alerts = 1;
      }
      elseif ( $ct_conf['comms']['proxy_alert_runtime'] == 'ui' && $runtime_mode == 'ui' ) {
      $run_alerts = 1;
      }
      else {
      $run_alerts = null;
      }
  
  
      if ( $run_alerts == 1 && $ct_cache->update_cache('cache/alerts/proxy-check-'.$cache_filename.'.dat', ( $ct_conf['comms']['proxy_alert_freq_max'] * 60 ) ) == true
      && in_array($cache_filename, $proxies_checked) == false ) {
      
       
      // SESSION VAR first, to avoid duplicate alerts at runtime (and longer term cache file locked for writing further down, after logs creation)
      $proxies_checked[] = $cache_filename;
       
      $response = @$this->ext_data('proxy-check', $proxy_test_url, 0, '', '', $problem_proxy);
      
      $data = json_decode($response, true);
      
      
         if ( is_array($data) && sizeof($data) > 0 ) {
          
            // Look for the IP in the response
            if ( strstr($data['ip'], $ip) == false ) {
             
            $misconfigured = 1;
            
            $notifyme_alert = 'A checkup on proxy ' . $ip . ', port ' . $port . ' detected a misconfiguration. Remote address ' . $data['ip'] . ' does not match the proxy address. Runtime mode is ' . $runtime_mode . '.';
            
            $text_alert = 'Proxy ' . $problem_proxy . ' remote address mismatch (detected as: ' . $data['ip'] . '). runtime: ' . $runtime_mode;
           
            }
          
         $cached_logs = ( $misconfigured == 1 ? 'runtime: ' . $runtime_mode . "; \n " . 'Proxy ' . $problem_proxy . ' checkup status = MISCONFIGURED (test endpoint ' . $proxy_test_url . ' detected the incoming ip as: ' . $data['ip'] . ')' . "; \n " . 'Remote address DOES NOT match proxy address;' : 'runtime: ' . $runtime_mode . "; \n " . 'Proxy ' . $problem_proxy . ' checkup status = OK (test endpoint ' . $proxy_test_url . ' detected the incoming ip as: ' . $data['ip'] . ');' );
         
         }
         else {
          
         $misconfigured = 1;
         
         $notifyme_alert = 'A checkup on proxy ' . $ip . ', port ' . $port . ' resulted in a failed data request. No endpoint connection could be established. Runtime mode is ' . $runtime_mode . '.';
          
         $text_alert = 'Proxy ' . $problem_proxy . ' failed, no endpoint connection. runtime: ' . $runtime_mode;
         
         $cached_logs = 'runtime: ' . $runtime_mode . "; \n " . 'Proxy ' . $problem_proxy . ' checkup status = DATA REQUEST FAILED' . "; \n " . 'No connection established at test endpoint ' . $proxy_test_url . ';';
       
         }
       
       
         // Log to error logs
         if ( $misconfigured == 1 ) {
         	
         $this->log(
         			'ext_data_error',
         			'proxy '.$problem_proxy.' connection failed',
         			$cached_logs
         			);
         
         }
      
     
      // Update alerts cache for this proxy (to prevent running alerts for this proxy too often)
      $this->save_file($base_dir . '/cache/alerts/proxy-check-'.$cache_filename.'.dat', $cached_logs);
        
           
      $email_alert = " The proxy " . $problem_proxy . " recently did not receive data when accessing this endpoint: \n " . $obfusc_url_data . " \n \n A check on this proxy was performed at " . $proxy_test_url . ", and results logged: \n ============================================================== \n " . $cached_logs . " \n ============================================================== \n \n ";
                         
       
         // Send out alerts
         if ( $misconfigured == 1 || $ct_conf['comms']['proxy_alert_checkup_ok'] == 'include' ) {
                           
                           
             // Message parameter added for desired comm methods (leave any comm method blank to skip sending via that method)
             if ( $ct_conf['comms']['proxy_alert'] == 'all' ) {
             
             // Minimize function calls
             $encoded_text_alert = $this->charset_encode($text_alert); // Unicode support included for text messages (emojis / asian characters / etc )
              
                  $send_params = array(
                         'notifyme' => $notifyme_alert,
                         'telegram' => $email_alert,
                         'text' => array(
                               'message' => $encoded_text_alert['content_output'],
                               'charset' => $encoded_text_alert['charset']
                               ),
                         'email' => array(
                               'subject' => 'A Proxy Was Unresponsive',
                               'message' => $email_alert
                               )
                         );
                  
             }
             elseif ( $ct_conf['comms']['proxy_alert'] == 'email' ) {
              
                  $send_params['email'] = array(
                            'subject' => 'A Proxy Was Unresponsive',
                            'message' => $email_alert
                            );
                  
             }
             elseif ( $ct_conf['comms']['proxy_alert'] == 'text' ) {
             
             // Minimize function calls
             $encoded_text_alert = $this->charset_encode($text_alert); // Unicode support included for text messages (emojis / asian characters / etc )
             
                  $send_params['text'] = array(
                            'message' => $encoded_text_alert['content_output'],
                            'charset' => $encoded_text_alert['charset']
                            );
                  
             }
             elseif ( $ct_conf['comms']['proxy_alert'] == 'notifyme' ) {
                  $send_params['notifyme'] = $notifyme_alert;
             }
             elseif ( $ct_conf['comms']['proxy_alert'] == 'telegram' ) {
                  $send_params['telegram'] = $email_alert;
             }
                  
                  
         // Send notifications
         @$this->queue_notify($send_params);
                  
         }
               
       
      }
  
  
  }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
   function system_info() {
   
   global $runtime_mode, $app_version, $base_dir, $ct_var;
   
   // OS
   $system['operating_system'] = php_uname();
      
      
      // CPU stats
      if ( is_readable('/proc/cpuinfo') ) {
      $cpu_info = @file_get_contents('/proc/cpuinfo');
      
      $raw_cpu_info_array = explode("\n", $cpu_info);
      
         foreach ( $raw_cpu_info_array as $cpu_info_field ) {
         
            if ( trim($cpu_info_field) != '' ) {
               
            $temp_array = explode(":", $cpu_info_field);
            
               $loop = 0;
               foreach ( $temp_array as $key => $val ) {
               $trimmed_val = ( $loop < 1 ? strtolower(trim($val)) : trim($val) );
               $trimmed_val = ( $loop < 1 ? preg_replace('/\s/', '_', $trimmed_val) : $trimmed_val );
               $temp_array_cleaned[$key] = $trimmed_val;
               $loop = $loop + 1;
               }
            
            $cpu_info_array[ $temp_array_cleaned[0] ] = $temp_array_cleaned[1];
            }
         
         }
      
      
      $cpu['cpu_info'] = $cpu_info_array;
      
      
         if ( $cpu['cpu_info']['model'] ) {
         $system['model'] = $cpu['cpu_info']['model'];
         }
         
         if ( $cpu['cpu_info']['hardware'] ) {
         $system['hardware'] = $cpu['cpu_info']['hardware'];
         }
         
         if ( $cpu['cpu_info']['model_name'] ) {
         $system['model_name'] = $cpu['cpu_info']['model_name'];
         }
      
         if ( $cpu['cpu_info']['processor'] ) {
         $system['cpu_threads'] = $cpu['cpu_info']['processor'] + 1; // (overwritten until last in loop, starts at 0)
         }
         elseif ( $cpu['cpu_info']['siblings'] ) {
         $system['cpu_threads'] = $cpu['cpu_info']['siblings'];
         }
         else {
         $system['cpu_threads'] = 1; // Presume only one, if nothing parsed
         }
         
      
      }
   
   
      
      // Uptime stats
      if ( is_readable('/proc/uptime') ) {
         
      $uptime_info = @file_get_contents('/proc/uptime');
      
      $num   = floatval($uptime_info);
      $secs  = fmod($num, 60); $num = (int)($num / 60);
      $mins  = $num % 60;      $num = (int)($num / 60);
      $hours = $num % 24;      $num = (int)($num / 24);
      $days  = $num;
      
      $system['uptime'] = $days . ' days, ' . $hours . ' hours, ' . $mins . ' minutes, ' . round($secs) . ' seconds';
      
      }
      
      
      
      // System loads
      if ( function_exists('sys_getloadavg') ) {
          
          $loop = 1;
          foreach ( sys_getloadavg() as $load ) {
             
             if ( $loop == 1 ) {
             $time = 1;
             }
             elseif ( $loop == 2 ) {
             $time = 5;
             }
             elseif ( $loop == 3 ) {
             $time = 15;
             }
             
          $system['system_load'] .= $load . ' (' . $time . ' min avg) ';
          $loop = $loop + 1;
          }
      
      $system['system_load'] = trim($system['system_load']);
      
      }
      
     
      // Temperature stats
      if ( is_readable('/sys/class/thermal/thermal_zone0/temp') ) {
      $temp_info = @file_get_contents('/sys/class/thermal/thermal_zone0/temp');
      $system['system_temp'] = round($temp_info/1000) . '° Celsius';
      }
      elseif ( is_readable('/sys/class/thermal/thermal_zone1/temp') ) {
      $temp_info = @file_get_contents('/sys/class/thermal/thermal_zone1/temp');
      $system['system_temp'] = round($temp_info/1000) . '° Celsius';
      }
      elseif ( is_readable('/sys/class/thermal/thermal_zone2/temp') ) {
      $temp_info = @file_get_contents('/sys/class/thermal/thermal_zone2/temp');
      $system['system_temp'] = round($temp_info/1000) . '° Celsius';
      }
      
   
   
      // Memory stats
      if ( is_readable('/proc/meminfo') ) {
         
      $data = explode("\n", file_get_contents("/proc/meminfo"));
       
       
         foreach ($data as $line) {
           list($key, $val) = explode(":", $line);
           $ram['ram_'.strtolower($key)] = trim($val);
         }
         
      
      $memory_applications_mb = $this->in_megabytes($ram['ram_memtotal'])['in_megs'] - $this->in_megabytes($ram['ram_memfree'])['in_megs'] - $this->in_megabytes($ram['ram_buffers'])['in_megs'] - $this->in_megabytes($ram['ram_cached'])['in_megs'];
      
      $system_memory_total_mb = $this->in_megabytes($ram['ram_memtotal'])['in_megs'];
      
      $memory_applications_percent = abs( ( $memory_applications_mb - $system_memory_total_mb ) / abs($system_memory_total_mb) * 100 );
      $memory_applications_percent = round( 100 - $memory_applications_percent, 2);
      
         
      $system['memory_total'] = $ram['ram_memtotal'];
      
      $system['memory_buffers'] = $ram['ram_buffers'];
      
      $system['memory_cached'] = $ram['ram_cached'];
      
      $system['memory_free'] = $ram['ram_memfree'];
      
      $system['memory_swap'] = $ram['ram_swapcached'];
      
      $system['memory_used_megabytes'] = $memory_applications_mb;
      
      $system['memory_used_percent'] = $memory_applications_percent;
   
      }
   
   
   // Free space on this partition
   $system['free_partition_space'] = $this->conv_bytes( disk_free_space($base_dir) , 3);
   
   
   // Portfolio cache size (cached for efficiency)
   $portfolio_cache = trim( file_get_contents($base_dir . '/cache/vars/cache_size.dat') );
   $system['portfolio_cache'] = ( $ct_var->num_to_str($portfolio_cache) > 0 ? $portfolio_cache : 0 );
   
   
   // Software
   $system['software'] = 'Open_Crypto_Tracker/' . $app_version . ' - PHP/' . phpversion();
   
   
      // Server stats
      if ( is_readable('/proc/stat') ) {
      $server_info = @file_get_contents('/proc/stat');
      
      $raw_server_info_array = explode("\n", $server_info);
      
         foreach ( $raw_server_info_array as $server_info_field ) {
         
            if ( trim($server_info_field) != '' ) {
               
            $server_info_field = preg_replace('/\s/', ':', $server_info_field, 1);
               
            $temp_array = explode(":", $server_info_field);
               
               $loop = 0;
               foreach ( $temp_array as $key => $val ) {
               $trimmed_val = ( $loop < 1 ? strtolower(trim($val)) : trim($val) );
               $trimmed_val = ( $loop < 1 ? preg_replace('/\s/', '_', $trimmed_val) : $trimmed_val );
               $temp_array_cleaned[$key] = $trimmed_val;
               $loop = $loop + 1;
               }
            
            $server_info_array[ $temp_array_cleaned[0] ] = $temp_array_cleaned[1];
            }
         
         }
      
      $server['server_info'] = $server_info_array;
      
      }
      
   
   return $system;
   
   }
   
   
   ////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////
   
   
}


?>
