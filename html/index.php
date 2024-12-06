<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$temp_args = explode("/", $_SERVER["REQUEST_URI"]);
if(count($temp_args)>=2){
  if($temp_args[1]=='index.php') unset($temp_args[1]);
}

$args = array();
$i = 0;
foreach($temp_args as $arg){
  if($i) $args[] = $arg;
  $i++;
}

// print_r($args);

switch($args[0]){   //args 1 = task, args 2 = variable
    case 'userlogin':
        userLogin();
        break;
    case 'status':
        status();
        break;
    case 'start':
        start();
        break;
    case 'qrcode':
        qrcode();
        break;
    case 'qrimage':
        qrimage();
        break;
    case 'stop':
        stop();
        break;
    case 'logout':
        logout();
        break;
    default:
        if(isset($_COOKIE['login_data'])){
          $login_data = $_COOKIE['login_data'];
          if($login_data==md5(getenv('password').getenv('password'))){
            // success
            showDefault();
          }else{
            //failed
            showLogin();
          }
        }else{
          showLogin();
        }
}

function HEADERS(){
  return ['accept: */*', 'Authorization: Bearer '.getenv('TOKEN')];
}

function API_URL(){
  return "http://localhost:".getenv('API_PORT').'/api/'.getenv('SESSION_NAME')."/";
}

function userLogin(){
  $username = $_REQUEST['username'];
  $password = $_REQUEST['password'];
  if($username==getenv('username') && md5($password)==getenv('password')){
    setcookie("login_data", md5(getenv('password').getenv('password')), time() + (86400 * 30), "/"); //30 days
    unset($_SESSION['userlogin_message']);
    header('Location: /');
  }else{
    $_SESSION['userlogin_message'] = "Login is not valid!";
    showLogin();
  }
}

function showLogin(){
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>AM Simple WA BOT Control</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/font-awesome-4.7.0/css/font-awesome.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="assets/jquery-qrcode/jquery.qrcode.min.js"></script>
</head>
<body>
  
<?php include('contents/login.php'); ?>

</body>
</html>

<?php
}

function status(){
  $resp = http_load(API_URL().'status-session', HEADERS());
  echo $resp;
}

function start(){
  $params = array('webhook' => getenv('WEBHOOK'), 'waitQrCode' => true);
  $resp = http_load(API_URL().'start-session', HEADERS(), true, $params);
  echo $resp;
}

function qrcode(){
  $resp = http_load(API_URL().'status-session', HEADERS());
  echo $resp;
}

function qrimage(){
  $resp = http_load(API_URL().'qrcode-session', HEADERS());
  if(substr($resp,0,strlen('{"status"'))!=='{"status"'){
    header ('Content-Type: image/png');
  }
  echo $resp;
}

function stop(){
  $resp = http_load(API_URL().'close-session', HEADERS(), true);
  echo $resp;
}

function logout(){
  $resp = http_load(API_URL().'logout-session', HEADERS(), true);
  echo $resp;
}

function showDefault(){
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>AM Simple WA BOT Control</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/font-awesome-4.7.0/css/font-awesome.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="assets/jquery-qrcode/jquery.qrcode.min.js"></script>
</head>
<body>
  
<?php include('contents/index.php'); ?>

<?php include('assets/modal.php'); ?>

</body>
</html>

<?php
}

function http_load($url, $headers=array(), $post = false, $params=array()){
  $ch = curl_init();
  $timeout = 0;
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  if($post) curl_setopt($ch, CURLOPT_POST, true);
  if($params){
      $fields_string = '';
      foreach($params as $key=>$value)
      {
          $fields_string .= $key.'='.$value.'&';
      }
      rtrim($fields_string, '&');
      curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
  }
  if($headers){
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  }
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
  $file_contents = curl_exec($ch);
  curl_close($ch);
  return $file_contents;
}

function base_url($uri=''){
	$rootdir = str_replace("\\",'/',$_SERVER['DOCUMENT_ROOT']);
	$currentdir = str_replace("\\",'/',getcwd());
	$server = $_SERVER['HTTP_HOST'];
	$protocol = getenv("FORCE_HTTPS") ? 'https' : (isset($_SERVER['HTTPS']) ? ($_SERVER['HTTPS']=='on' ? 'https' : 'http') : 'http');
	return $protocol.'://'.$server.str_replace($rootdir,'',$currentdir).($uri ? '/' : '').$uri;
}

function ibase_url($uri=''){
	$rootdir = str_replace("\\",'/',$_SERVER['DOCUMENT_ROOT']);
	$currentdir = str_replace("\\",'/',getcwd());
	return 'http://localhost'.str_replace($rootdir,'',$currentdir).($uri ? '/' : '').$uri;
}

?>