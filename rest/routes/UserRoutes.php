<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * @OA\Post(
 *     path="/login",
 *     description="Login to the system",
 *     tags={"chat"},
 *     @OA\RequestBody(description="Basic user info", required=true,
 *       @OA\MediaType(mediaType="application/json",
 *    			@OA\Schema(
 *    				@OA\Property(property="email", type="string", example="kareem@gmail.com",	description="Email"),
 *    				@OA\Property(property="password", type="string", example="12345678",	description="Password" )
 *        )
 *     )),
 *     @OA\Response(
 *         response=200,
 *         description="JWT Token on successful response"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Wrong Password | User doesn't exist"
 *     )
 * )
 */

Flight::route('POST /login', function () {
  $login = Flight::request()->data->getData();
  // Flight::json(["message" => "Data: ".$login['email']], 404);
  $user = Flight::userDao()->get_user_by_email($login['email']);
  if (isset($user['id'])) {
    if ($user['password'] == md5($login['password'])) {
      unset($user['password']); // to delete pass from user obj
      $jwt = JWT::encode($user, Config::JWT_SECRET(), 'HS256');
      Flight::json(['token' => $jwt]);
    } else {
      Flight::json(["message" => "Wrong password"], 404);
    }
  } else {
    Flight::json(["message" => "User doesn't exist"], 404);
  }
});

/**
 * @OA\Post(
 *     path="/signup",
 *     description="Sign up a new user",
 *     tags={"auth"},
 *     @OA\RequestBody(description="User information", required=true,
 *         @OA\MediaType(mediaType="application/json",
 *             @OA\Schema(
 *                 @OA\Property(property="firstName", type="string", example="Kareem", description="User's first name"),
 *                 @OA\Property(property="lastName", type="string", example="Khamis", description="User's last name"),
 *                 @OA\Property(property="email", type="string", example="kareem@gmail.com", description="User's email address"),
 *                 @OA\Property(property="password", type="string", example="Fk6@xjH#9", description="User's password (min 8 characters, including uppercase and lowercase letters, numbers, and symbols)")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=201, description="JWT token on successful sign-up"),
 *     @OA\Response(response=406, description="Invalid password"),
 *     @OA\Response(response=422, description="User already exists")
 * )
 */


Flight::route('POST /signup', function () {
  $entity = Flight::request()->data->getData();
  $user = Flight::userDao()->get_user_by_email($entity['email']);
  if (isset($user['id'])) {
    // If user with same email already exists
    Flight::json(["message" => "User already exist"], 409);
  } else {
    if (strlen($entity['password']) < 8 || strlen($entity['password']) > 20) {
      // If password is too short or too long
      Flight::json(["message" => "Invalid password"], 406);
    } else {
      // add the user to the database
      $entity['password'] = md5($entity['password']);

      $user = Flight::userService()->signup($entity);
      // Flight::json($user);
      unset($user['password']);
      $jwt = JWT::encode($user, Config::JWT_SECRET(), 'HS256');
      Flight::json(['token' => $jwt]);
    }
  }
});