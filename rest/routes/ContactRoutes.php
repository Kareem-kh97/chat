<?php
/**
 * @OA\Get(path="/contacts", tags={"contacts"}, security={{"ApiKeyAuth": {}}},
 *         summary="Return all contacts from the API. ",
 *         @OA\Response( response=200, description="List of contacts.")
 * )
 */
Flight::route('GET /contacts', function(){
  // who is the user who calls this method?
  $user = Flight::get('user');
  // Flight::json(["message" => "Hi from service ".implode(" ",$user)], 404);
  // $search = Flight::query('search');
  // Flight::json(["message" => "Hi "], 404);

  Flight::json(Flight::contactService()->get_user_contacts($user));
});
/**
 * @OA\Get(path="/contacts/{id}", tags={"contacts"}, security={{"ApiKeyAuth": {}}},
 *     @OA\Parameter(in="path", name="id", example=1, description="Id of contact"),
 *     @OA\Response(response="200", description="Fetch individual contact")
 * )
 */
Flight::route('GET /contacts/@id', function($id){
  // who is the user who calls this method?
  $user = Flight::get('user');
  // Flight::json(["message" => "Hi from service ".implode(" ",$user)], 404);
  // $search = Flight::query('search');
  // Flight::json(["message" => "Hi "], 404);

  Flight::json(Flight::contactService()->get_user_contact_messages($user, $id));
});
/**
 * @OA\Get(path="/contacts", tags={"contacts"}, security={{"ApiKeyAuth": {}}},
 *         summary="Post contacts to the API. ",
 *         @OA\Response( response=200, description="contacts posting.")
 * )
 */
Flight::route('POST /contacts', function(){
  // who is the user who calls this method?
  $user = Flight::get('user');
  $entity = Flight::request()->data->getData();
  // Flight::json(["message" => "Hi "], 404);

  Flight::json(Flight::contactService()->add_friend($user, $entity));
});

?>
