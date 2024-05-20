<?php

namespace App\Http\Controllers\Api;

use App\Events\ContactAddNtify;
use App\Events\ContactConfirmNotify;
use App\Events\ContactRefusedNotify;
use App\Http\Controllers\Controller;
use App\Models\Amountreq;
use App\Models\Contact;
use App\Models\Notification;
use App\Models\User;
use App\Services\FCMService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class ContactController extends Controller
{




    /**     * @OA\GET(
     *      path="/contact/{id}",
     *      operationId="Store contact",
     *      tags={"Contact"},
     *      summary="Store contact",
     *      description="Store contact",
     *     @OA\Parameter(
     *          name="user_id",
     *          description="user  id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     *
     *  */
    public function create_contac(Request $request)
    {
        $user = User::where('id', $request->id)->first();
        if (!$user) {
            return response()->json(['message' => 'user not found send invetaion to it'], 400);
        }
        if (Auth::user()->id != $user->id) {

            try {
                event(new ContactConfirmNotify($user));

                $contact = Contact::create(['id' => $user->id, 'name' => $user->name ?? null, 'image' => $user->image, 'user_contact_countrycode' => $user->country_code, 'user_contact_mobile' => $user->phone_number, 'owner_contact_countrycode' => auth()->user()->country_code, 'owner_user_mobile' => auth()->user()->phone_number]);
                DB::table('contacts')->insert(['id' => auth()->user()->id, 'name' => auth()->user()->name ?? null, 'image' => auth()->user()->image ?? null, 'user_contact_countrycode' => auth()->user()->country_code, 'user_contact_mobile' => auth()->user()->phone_number, 'owner_contact_countrycode' => $user->country_code, 'owner_user_mobile' => $user->phone_number]);
                $user_token = $user->token_device;
                FCMService::send(
                    $token = $user_token,
                  [
                    'phone'=>auth()->user()->phone_number,
                  'country_code'=>auth()->user()->country_code,
                 'category'=>"responseRequest",
                'senderId'=>strval(auth()->user()->id),
              ],
                  
                    [
                        'title' => 'accepted add request',
                        'body' => "your invitation accepted",
                    ]
                );
                return response()->json(['contact' => $contact], 200);

            } catch (\Exception $e) {
                return response()->json(['error' => 'already exist'], 500);
            }

        } else {
            return response()->json(['message' => 'you can not make you contact with yourself'], 200);

        }

    }
    /**     * @OA\GET(
     *      path="/contact_show",
     *      operationId="show_all_conatcts",
     *      tags={"Contact"},
     *      summary="delete all conatacts",
     *      description="get all contacts",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     *
     *  */
    public function show_contact()
    {
        try {

            $contacts = Contact::where('owner_user_mobile', Auth::user()->phone_number)->get();
            return response()->json(['contact' => $contacts], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }

    }

    /**     * @OA\delete(
     *      path="/delete_conatct/{id}",
     *      operationId="delete_single_conatact",
     *      tags={"Contact"},
     *      summary="delete single conatact",
     *      description="delete single conatactt",
     *     @OA\Parameter(
     *          name="contact id",
     *          description="contact  id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     *
     *  */
    public function delete($id)
    {
        $user = Auth::user();

        $contacts = $user->contacts;

        if (count($contacts)==0) {
            return response()->json(['message' => "you do not have this is contact"], 400);
        } else {
            foreach ($contacts as $contacte) {
                $x = $contacte->where('id', $id)->where('owner_user_mobile', auth()->user()->phone_number)->first();
               

            }
            $all_conac = Contact::all();
            foreach ($all_conac as $conac) {
                $z = $conac->where('user_contact_mobile', auth()->user()->phone_number)->where('owner_user_mobile', $x->user_contact_mobile)->first();
            }
            try {

                Amountreq::where(function ($query) use ($x) {
                    $query->where('from_user_mobile', $x->user_contact_mobile)
                        ->where('to_user_mobile', auth()->user()->phone_number)->where('amount', null)
                        ->where('status', 'confirmed')->orwhere('status', 'refused')->orwhere('status', null);
                })->orWhere(function ($query) use ($x) {
                    $query->where('to_user_mobile', $x->user_contact_mobile)
                        ->where('from_user_mobile', auth()->user()->phone_number)
                        ->where('amount', null)
                        ->where('status', 'confirmed')->orwhere('status', 'refused')->orwhere('status', null);
                    ;
                })->delete();
                $x->delete();
                $z->delete();
                return response()->json(['message' => 'deleted'], 200);

            } catch (\Exception $e) {
                return response()->json(['message' => "no contact to delete"], 400);

            }

        }
    }
    /**     * @OA\delete(
     *      path="/delete_current_acount",
     *      operationId="delete current acount",
     *      tags={"Contact"},
     *      summary="delete current acount",
     *      description="delete current acount",
     *
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *     )
     *
     *  */
    public function delete_current_user()
    {
        $phone_number = Auth::user()->phone_number;

        $user = Auth::user();
        if ($user) {
            JWTAuth::invalidate(JWTAuth::getToken());
            $contacts = Contact::where(function ($query) use ($phone_number) {
                $query->where('owner_user_mobile', $phone_number)
                      ->orWhere('user_contact_mobile', $phone_number);
            })->get();

            if (count($contacts) != 0) {
                foreach ($contacts as $contact) {
                    $contact->delete();
                }
                $user->delete();

                return response()->json(['message' => "account deleted"]);

            } else {
                $user->delete();
                return response()->json(['message' => "account deleted"]);
            }

        } else {
            return response()->json(['message' => "account not deleted"]);

        }

    }

    /**     * @OA\post(
     *      path="/send_notify_addcontact/{id}",
     *      operationId="send notificatio to add contact",
     *      tags={"Contact"},
     *      summary="send notificatio to add contact",
     *      description="send notificatio to add contact",
     *     @OA\Parameter(
     *          name="id",
     *          description="user id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     *
     *  */

    public function send_notify_addcontact($id)
    {
        if (Auth::user()->id == $id) {
            return response()->json(['message' => "you can not send notification to yourself"], 400);
        }
        $user = User::where('id', $id)->first();
        if (!$user) {
            return response()->json(['message' => 'user not found to send invetaion to it'], 400);
        }

        $request = Amountreq::where(function ($query) use ($user) {
            $query->where('from_user_mobile', Auth::user()->phone_number)
                ->where('to_user_mobile', $user->phone_number)->where('amount', null)
                ->where('status', 'confirmed');
        })->orWhere(function ($query) use ($user) {

            $query->where('from_user_mobile', Auth::user()->phone_number)
                ->where('to_user_mobile', $user->phone_number)->where('amount', null)
                ->where('status', null);
        })->get();

        if (count($request) != 0) {
            return response()->json(['message' => 'you  send add request before'], 400);
        }
        try {
            event(new ContactAddNtify($user));
            $user_token = User::where('id', $id)->pluck('token_device')->first();
            FCMService::send(
                $token = $user_token,
              
                 [
                   'phone'=>auth()->user()->phone_number,
                  'country_code'=>auth()->user()->country_code,
                 'category'=>"addRequest",
                'senderId'=>strval(auth()->user()->id),
              ],
                [
                    'title' => ' new add request',
                    'body' => "your have new add request ",
                ]
            );
            return response()->json(['message' => " notification send"]);

        } catch (\Throwable $th) {
            return response()->json(['message' => " notification not send", 'erroe' => $th]);
        }
    }

    /**     * @OA\post(
     *      path="/send_refused_addcontact/{id}",
     *      operationId="send notificatio to refused add contact",
     *      tags={"Contact"},
     *      summary="send notificatio to refused add contact",
     *      description="send notificatio to refused add contact",
     *     @OA\Parameter(
     *          name="id",
     *          description="user id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     *
     *  */
    public function refused_add_contact_notify($id)
    {
        $user = User::where('id', $id)->first();
        try {
            event(new ContactRefusedNotify($user));
            $user_token = User::where('id', $id)->pluck('token_device')->first();
            FCMService::send(
                $token = $user_token,
                [
                  'phone'=>auth()->user()->phone_number,
                  'country_code'=>auth()->user()->country_code,
                 'category'=>"responseRequest",
                'senderId'=>strval(auth()->user()->id),
              ],
                [
                    'title' => 'refused add  request',
                    'body' => "your add refused  ",
                ]
            );
            return response()->json(['message' => " notification send"]);

        } catch (\Throwable $th) {
            return response()->json(['message' => " notification not send"]);
        }
    }
    /**     * @OA\get(
     *      path="/get_add_notification",
     *      operationId="get all notifications for auth user",
     *      tags={"Contact"},
     *      summary="get all notifications for auth user",
     *      description="get all notifications for auth user",
     *
     *
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     *
     *  */
    public function get_add_notification()
    {
        $user = Auth::id();
        $notifications = Notification::where('notifiable_id', $user)->orderBy('created_at', 'desc')->get();
        return response()->json(['notifications' => $notifications]);

    }

    public function check_contacts(Request $request)
    {

        $numbers = $request->numbers;
        $found = [];
        $notFound = [];
        $contacts = [];

        foreach ($numbers as $number) {

            $numberToSearch = substr($number,-9);
            $result = User::where('phone_number', 'like', '%' . $numberToSearch . "%")->first();
            if ($result) {

                $contact = Contact::where('user_contact_mobile', $result->phone_number)->where('owner_user_mobile', auth()->user()->phone_number)->first();
                if ($contact) {
                    $contacts[] = $number;
                } else {
                    if (Auth::user()->phone_number == $result->phone_number) {
                        $found[] = ['auth_user' => $number, 'id' => $result->id];
                    } else {
                        $found[] = ['number' => $number, 'id' => $result->id];
                    }

                }

            } else {
                $notFound[] = $number;
            }

        }

        return response()->json(['not found' => $notFound, 'found' => $found, 'contact' => $contacts]);

    }
    public function delete_notification($id)
    {
        try {
            $notification = Notification::find($id);
            if (!$notification) {
                return response()->json(['message' => 'no notification', 'notifications' => $notification], 200);
            }
            $notification->delete();
            return response()->json(['messages' => 'notification deleted'], 200);
        } catch (\Throwable $th) {
            return response()->json(['erroe' => $th->getMessage()], 500);
        }
    }
    public function delete_all_notifications()
    {
        try {
            $user = Auth::id();
            $notifications = Notification::where('notifiable_id', $user);

            if ($notifications->count() == 0) {
                return response()->json(['message' => 'no notification', 'notifications' => $notifications]);
            }
            $notifications->delete();
            return response()->json(['messages' => ' all notisications deleted']);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    public function invitation()
    {
        return response()->json(['invitation'=>"https://prizm-energy.com/lendme/app-release.apk"]);
    }

}
