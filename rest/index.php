<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Firebase\JWT\JWT;
use Firebase\JWT\Key;


require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/services/ContactService.class.php';
require_once __DIR__.'/services/MessageService.class.php';
require_once __DIR__.'/services/UserService.class.php';
require_once __DIR__.'/dao/UserDao.class.php';

Flight::register('userDao', 'UserDao');

Flight::register('contactService', 'ContactService');
Flight::register('messageService', 'MessageService');
Flight::register('userService', 'UserService');

Flight::map('error', function(Exception $ex){
    // Handle error
    Flight::json(['message' => $ex->getMessage()], 500);
});

/* utility function for reading query parameters from URL */
// Flight::map('query', function($name, $default_value = NULL){ 
//   $request = Flight::request();
//   $query_param = @$request->query->getData()[$name]; 
//   $query_param = $query_param ? $query_param : $default_value; 
//   return urldecode($query_param); // returns decoded value
// });

// middleware method
Flight::route('/*', function(){
  // return TRUE;
  //perform JWT decode
  $path = Flight::request()->url;
  // exclude these routes from middleware
  if ($path == '/login' || $path == '/signup' || $path == '/docs.json') return TRUE; 

  $headers = getallheaders();
  // does not exist
  if (@!$headers['Authorization']){
    Flight::json(["message" => "Authorization is missing"], 403);
    return FALSE;
  }else{
    try {
      // decode the token, store the decoded payload in the flight framework's shared data,
      // this allows `routes` or `middleware` to access authenticated user's info
      // token exetracted from 'Authorization' header of the request
      $decoded = (array)JWT::decode($headers['Authorization'], new Key(Config::JWT_SECRET(), 'HS256'));
      Flight::set('user', $decoded); 
      return TRUE; // success
    } catch (\Exception $e) {
      Flight::json(["message" => "Authorization token is not valid"], 403);
      return FALSE;
    }
  }
});

/* REST API documentation endpoint */
/* generates documentation */
Flight::route('GET /docs.json', function(){
  $openapi = \OpenApi\scan('routes'); //scan route and generate documentation
  header('Content-Type: application/json'); // indicating the response will be in json
  echo $openapi->toJson(); //convert the result
});

require_once __DIR__.'/routes/ContactRoutes.php';
require_once __DIR__.'/routes/MessageRoutes.php';
require_once __DIR__.'/routes/UserRoutes.php';


Flight::start();
?>
