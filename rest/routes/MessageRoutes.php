<?php

/**
 * @OA\Get(path="/messages", tags={"messages"}, security={{"ApiKeyAuth": {}}},
 *         summary="Return all user messages from the API. ",
 *         @OA\Response( response=200, description="List of messages.")
 * )
 */

Flight::route('GET /messages', function () {
 
  $user = Flight::get('user');
  // Flight::json(["message" => "Hi from service ".implode(" ",$user)], 404);
  // $search = Flight::query('search');
  // Flight::json(["message" => "Hi "], 404);

  Flight::json(Flight::messageService()->get_all($user));
});

/**
 * @OA\Get(path="/messages/{id}", tags={"messages"}, security={{"ApiKeyAuth": {}}},summary="Return all user messages from the API by contact id. ",
 *         @OA\Parameter(
 *             name="id",
 *             in="path",
 *             example=1,
 *             description="ID of the contact to retrieve messages for."),
 *         @OA\Response( response=200, description="fetch indivisual messages.")
 * ),
 */
Flight::route('GET /messages/@id', function ($id) {
  $user = Flight::get('user');
  // Flight::json(["message" => "Hi from service ".implode(" ",$user)], 404);
  // $search = Flight::query('search');
  // Flight::json(["message" => "Hi "], 404);

  Flight::json(Flight::messageService()->get_user_contact_messages($user, $id));
});

/**
 * @OA\Post(
 *     path="/sendmessage",
 *     tags={"Message"},
 *     summary="Send a message",
 *     description="Creates and sends a message from the authenticated user",
 *     security={{"ApiKeyAuth": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="Message sent successfully"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized - authentication failed"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error"
 *     )
 * )
 */

Flight::route('POST /sendmessage', function () {
  // Flight::json(["message" => "send message called"], 404);

  $message = Flight::request()->data->getData();
  // Flight::json(["message" => $message['sender_id']." ".$message['text']], 404);
  // Flight::json(["message" => implode(" ", $message)], 404);

  $user = Flight::get('user');

  // $message = Flight::request()->data->getData();
  // setup the senderID as the current user
  // or it is going to assign the userID value to the sender_id which is the property of message
  $message['sender_id'] = $user['id'];
// invoks sendmessage() on instance of messageService
  Flight::messageService()->sendmessage($message);
});

/**
 * @OA\Delete(
 *     path="/softdelete/{id}",
 *     description="deletes a single message",
 *     tags={"softdelete"}, security={{"ApiKeyAuth": {}}},
 *     @OA\Parameter(
 *         description="Message ID to delete",
 *         in="path",
 *         name="id",
 *         example=1,
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Message deleted"
 *     ),
 *     @OA\Response(
 *         response="default",
 *         description="unexpected error",
 *     )
 * )
 */
Flight::route('DELETE /softdelete/@id', function ($id) {

  // $user = Flight::get('user');
  // Flight::json(["message" => "Deleting the message " . $id . " (route)"], 404);

  Flight::messageService()->softdelete($id);
});
/**
 * @OA\PUT(path="/updatetext", tags={"messages"}, security={{"ApiKeyAuth": {}}},summary="Update all user messages from the API. ",
 *         @OA\Parameter(
 *             name="id",
 *             in="path",
 *             example=1,
 *             description="updating sent messages for sender."),
 *         @OA\Response( response=200, description="fetch indivisual messages.")
 * ),
 */
Flight::route('PUT /updatetext', function () {
  // Flight::json(["message" => "It works (route)"], 404);
  $entity = Flight::request()->data->getData();
  $user = Flight::get('user');
  Flight::messageService()->updatetext($entity, $user);
})
?>