<?php
  // Database details for connection
  require('config.php');

  // To allow users from other websites (it's a school project, students can host chat app on their own domains)
  header("Access-Control-Allow-Origin: *");

  // Connect to the database
  // -- check if connection is OK
  if (!($mysqli = new mysqli($config['dbhost'], $config['dbuser'], $config['dbpass']))) {
    throw new Exception($mysqli -> error());
  }
  // -- check if database exists
  if (!($mysqli -> select_db($config['dbname']))) {
    throw new Exception($mysqli -> error());
  }

  // Username
  // -- default: xlogin00
  $user = 'xlogin00';
  // Parse username from REFERER
  if (isset($_SERVER['HTTP_REFERER']) && preg_match("/\~(x\S\S\S\S\S\d\S)/i", $_SERVER['HTTP_REFERER'], $ldata)) {
    $user = $ldata[1];
  }
  // Get username from REQUEST
  if (isset($_REQUEST['user'])) {
    $user = $_REQUEST['user'];
  }

  // Save data received by POST to database
  if (isset($_REQUEST['data'])) {
    $query = sprintf("INSERT INTO `itu-ajax` (`login`, `cnt`, `dts`) VALUES ('%s', '%s', CURRENT_TIMESTAMP )", 
    $mysqli -> real_escape_string(strip_tags($user)), $mysqli -> real_escape_string(strip_tags($_REQUEST['data'])));
    // Error during SQL query
    if (!$mysqli -> query($query)) {
      throw new Exception($mysqli -> error());
    }
    echo 'Your message was stored';
  } else { // Get data from database when api is called without params
    $query = "SELECT * FROM `itu-ajax` ORDER BY `id` DESC LIMIT 20";
    // Error during SQL query
    if (!($result = $mysqli -> query($query))) {
      throw new Exception($mysqli -> error());
    }
    // Parse response
    $response = array();
    $i = 0;
    while ($row = $result -> fetch_array(MYSQLI_ASSOC)) {
      $row['cnt'] = htmlentities($row['cnt']);
      $response[$i++] = $row;
    }
    $response = array_reverse($response);
    // Debug option for API GET
    if (isset($_REQUEST['debug'])) {
      echo "<pre>".print_r($response,true)."</pre>";
    } else {
      echo json_encode($response);
    }
  }
?>