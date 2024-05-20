<?php

namespace App\Http\Controllers\Api;

use App\Events\MessageCreate;
use App\Events\MessageNotify;
use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Message;
use App\Services\FCMService;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{

    public function sendMessage(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:text,image,voice,video,file',
            'body' => 'required',
            'name' => 'nullable',
            'localpath' => 'nullable',
            'size' => 'nullable',
            'date' => 'nullable',
        ]);

        $contacts = Auth::user()->with('contacts')->find($id);
        $user = Contact::find($id);
        $user_token = User::where('id', $id)->pluck('token_device')->first();

        if (!$contacts) {
            return response()->json(['error' => 'Message  not sent there is no contact'], 400);
        }
        if ($request->type == 'image' || $request->type == 'voice' || $request->type == 'video' || $request->type == 'file') {
            try {
                $data = $request->except('body');
                $path = $this->uploadfile($request);
                $data['body'] = 'http://lendme.prizm-energy.com/public/storage/' . $path;
                $data['type'] = $request->type;
                $data['name'] = $request->name ?? null;
                $data['date'] = $request->date ?? null;
                $data['localpath'] = $request->localpath ?? null;
                $data['size'] = $request->size ?? null;
                $data['sender_id'] = auth()->user()->id;
                $data['receiver_id'] = $contacts->id;
                $data['from_phone_number'] = auth()->user()->phone_number;
                $data['to_phone_number'] = $contacts->phone_number;
                $data['file_path'] = 'http://lendme.prizm-energy.com/public/storage/' . $path;
                event(new MessageNotify($user));
                $message = Message::create($data);
                FCMService::send(
                    $token = $user_token,
                  
                [
                  'imageUrl'=>auth()->user()->image,
                  'phone'=>auth()->user()->phone_number,
                  'country_code'=>auth()->user()->country_code,
                 'category'=>"message",
                'senderId'=>strval(auth()->user()->id),
              ],
                  
                  
                    [
                      
                        'title' => 'new message',
                        'body' => "your have new message",
                      

                      
                    ]                  

                );
                // broadcast(new MessageCreate($message));
                return response()->json(['message' => 'Message sent successfully'], 200);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 400);
            }
        }
        try {
            $message = new Message();
            $message->type = $request->type;
            $message->body = $request->body;
             $message->date=$request->date;
            $message->sender_id = auth()->user()->id;
            $message->receiver_id = $contacts->id;
            $message->from_phone_number = auth()->user()->phone_number;
            $message->to_phone_number = $contacts->phone_number;
            event(new MessageNotify($user));

            $message->save();
            $user_token = User::where('id', $id)->pluck('token_device')->first();
            FCMService::send(
                $token = $user_token,
             [
              'imageUrl'=>auth()->user()->image,
              'phone'=>auth()->user()->phone_number,
               'country_code'=>auth()->user()->country_code,
                 'category'=>"message",
                'senderId'=>strval(auth()->user()->id)],
              
                [
                    'title' => 'new message',
                    'body' => "your have new message",
                  
                ]
            );
            broadcast(new MessageCreate($message));

            return response()->json(['message' => 'Message sent successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getMessages($id)
    {
        $contact = Auth::user()->contacts()->find($id);
        $messages = [];
        if (!$contact) {
            $messages = Message::Where(function ($query) {

                $query->where('receiver_id', auth()->user()->id);
            })->orderBy('created_at', 'asc')->get();
            foreach ($messages as $message) {
                $user = User::where('id', $message->sender_id)->get();
            }
            foreach ($user as $us) {
                $name = $us->name;
            }

            return response()->json(['message' => "you need add  $name  to your contact  ", 'data' => $messages], 200);
        } elseif ($contact) {

            $messages = Message::where(function ($query) use ($contact) {
                $query->where('sender_id', auth()->user()->id)
                    ->where('receiver_id', $contact->id);
            })->orWhere(function ($query) use ($contact) {
                // foreach($contact as $con){
                $query->where('sender_id', $contact->id)
                    ->where('receiver_id', auth()->user()->id);
            })->orderBy('created_at', 'DESC')->get();
            $counte = count($messages);
            return response()->json(['messages' => $messages,], 200);
        } else {
            return response()->json(['message' => "no message "], 200);

        }
    }
    public function uploadfile(Request $request)
    {
        if ($request->hasfile('body') == false) {
            return;
        } elseif ($request->hasfile('body')) {

            $file = $request->file('body');
            $name = $file->getClientOriginalName();
            $fileNameWithoutSpaces = str_replace(' ', '_', $name);
            $path = $file->storeAs("upload/chatting/", $fileNameWithoutSpaces, 'public');
            return $path;
        }

    }
}
