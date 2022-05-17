<?php

namespace App\Http\Controllers;

use App\Models\Users;
use App\Models\Messages;
use App\Models\Developers;
use App\Models\Recruiters;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Mime\Message;

class MessagesController extends Controller
{
    /**
     * get all messages list
     *
     * @return void
     */
    public function list() {
        return Messages::all();
    }

    /**
     * get single message by id
     *
     * @param [int] $id
     * @return void
     */
    public function item($id){
        return Messages::whereId($id)->first();
    }

    /**
     * Create new message
     *
     * @param Request $request
     * @return void
     */
    public function create(Request $request) {

        try {
            $messages =new Messages();
            $messages->receiver_user_id = $request->receiver_user_id;
            $messages->sender_user_id = $request->sender_user_id;
            $messages->message_title = $request->title;
            $messages->message_content = $request->message_content;
            $messages->signature = $request->signature;

            if ($messages->save()) {
                return response()->json(['status' => 'success', 'message' => 'Message created successfully']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Update single message
     *
     * @param Request $request
     * @param [type] $id
     * @return void
     */
    public function update(Request $request, $id) {
        try {
            $messages =Messages::findOrFail($id);
            $messages->receiver_user_id = $request->receiver_user_id;
            $messages->sender_user_id = $request->sender_user_id;
            $messages->message_title = $request->title;
            $messages->message_content = $request->message_content;
            $messages->signature = $request->signature;

            if ($messages->save()) {
                return response()->json(['status' => 'success', 'message' => 'Message updated successfully']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Delete single message with id
     *
     * @param [int] $id
     * @return void
     */
    public function delete($id) {

        try {
            $messages = Messages::findOrFail($id);

            if ($messages->delete()) {
                return response()->json(['status' => 'success', 'message' => 'Message deleted succesfully']);
            }
        } catch(\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Retrieve all messages from One User using id and correspondent profiles details
     *
     * @param [int] $id
     * @return void
     */
    public function getAllMessagesFromOneUser($id) {

        // case where our user is the receiver
        $messagesUserReceiver = Messages::join('users', 'messages.receiver_user_id','=', 'users.id')
        ->where('users.id', '=', $id)
        ->get('messages.*');

        $senders = [];
        $senderUser = $messagesUserReceiver->pluck('sender_user_id')->unique();
        foreach ($senderUser as $se) {
            $senderData = Users::where('users.id', '=', $se)->get();

            if($id !== $se){
                $spec = [];
                foreach ($senderData as $sd){
                    if(!isset($sd->dev_id)) {

                        $senderDev = Developers::where('id', '=', $sd->dev_id);
                        return $senderDev;
                       // $senders['spec'] = $senderDev;
                    }
                    else{
                        $sid =$sd->recrut_id;
                        $senderRec = Recruiters::where('id', '=', $sid);
                        //return $senderRec;
                       //$spec[] = $senderRec;
                    }
                    //return $spec;
                }

                $senders['gen'] = $senderData;
            }
            //return $spec;


            /*$senderId = $senderData->pluck('id');
            if ($id !== $senderId) {
                $senders[] = $senderData;*/
            }

        //return $devsd;

        // case in which our user is the sender
        $messagesUserSender = Messages::join('users', 'messages.sender_user_id','=', 'users.id')
        ->where('users.id', '=', $id)
        ->get('messages.*');

        $receivers = [];
        $senderUser = $messagesUserSender->pluck('receiver_user_id')->unique();
        foreach($senderUser as $su){
            $receiverData = Users::where('users.id', '=', $su)->get();
            $receiverId = $receiverData->pluck('id');
            if ($id !== $receiverId) {
                $receivers[] = $receiverData;
            }



            /*   $recieverUser = $msgSender->receiver_user_id;
            $recieverDetail = Users::where('users.id', '=', $recieverUser)->get();
            $devId = $recieverDetail->pluck('dev_id');
            $recrutId = $recieverDetail->pluck('recrut_id');
            $receivers[] = $recieverDetail;

            if($devId) {
                $receiverDevDetails = Developers::where('dev_id', '=', $devId)->first();
                $receivers[] = $receiverDevDetails;
                //return $receivers;
            } elseif($recrutId){
                $receiverRecrutDetails = Recruiters::where('recrut_id', '=', $recrutId)->get('*');
                $receivers[] = $receiverRecrutDetails;
                //return $receivers;
            }*/

            //return $receivers;
            //return $recieverDetail;
        }

        return response()->json(['status' => 'success', 'receivedMessages' => $messagesUserReceiver, 'sentMessages' => $messagesUserSender ,'recieverUserDetail'=>$receivers, 'senderUsersDetails' => $senders]);
    }

     /**
     * Retrieve one message send of a user profile using id and correspondent profile details
     *
     * @param [int] $id
     * @return void
     */
    public function getOneFromAUser(Request $request) {
        $userId = $request->userId;
        $correspondantId = $request->correspondantId;


        $messageUserReceiver = Messages::join('users', 'messages.receiver_user_id','=', 'users.id')
        ->where('users.id', '=', $userId)
        ->get('messages.*');

        //$senderDetail = Users::where('users.id', '=', $correspondantId)->get();

        $messagesUserSender = Messages::join('users', 'messages.sender_user_id','=', 'users.id')
        ->where('users.id', '=', $userId)
        ->get('messages.*');

        //$recieverUser = $messagesUserSender->pluck('receiver_user_id');
        //$recieverDetail = Users::where('users.id', '=', $recieverUser)->get();

        return response()->json(['status' => 'success', 'receiver' => $messageUserReceiver ]);
    }

    /**
     * Create new message from user
     *
     * @param Request $request
     * @return void
     */
    public function createMessageInDb(Request $request) {

        try {
            $messages = new Messages();
            $messages->sender_user_id = $request->sender_user_id;
            $messages->receiver_user_id = $request->receiver_user_id;
            $messages->message_title = $request->message_title;
            $messages->message_content = $request->message_content;
           // $messages->signature = $request->signature;

            if ($messages->save()) {
                return response()->json(['status' => 'success', 'message' => 'Message created successfully', 'createdMessage' => $messages]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
