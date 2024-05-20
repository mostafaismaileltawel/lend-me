<?php
namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use GuzzleHttp\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Token;
use Validator;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['verify', 'register', 'check_token']], );
    }
    // Check if the token is invalid
    public function check_token(Request $request)
    {

        try {
            JWTAuth::parseToken()->authenticate();
            return response()->json(['message' => 'valid']);

        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['error' => 'expiard']);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['error' => 'inavalid']);

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['error' => 'not found']);
        }
    }

    /**     * @OA\post(
     *      path="/login",
     *      operationId="login",
     *      tags={"Auth"},
     *      summary="login",
     *      description="login",
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

    /**     * @OA\post(
     *      path="/register",
     *      operationId="register",
     *      tags={"Auth"},
     *      summary="register",
     *      description="register",
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
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'phone_number' => 'required',
            "country_code" => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error_message' => $validator->errors()], 422);
        }

        $verificationCode = rand(100000, 999999);

        $user = User::where('phone_number',$request->phone_number)->first();
      $DELETED_USER=User::where('phone_number',$request->phone_number)->onlyTrashed()->first();
      if($DELETED_USER)
      {
        $DELETED_USER->restore();
        $DELETED_USER->update([
            'phone_number' => $request->phone_number,
            'country_code' => $request->country_code,
            'verification_code' => $verificationCode,
            'expire_at' => Carbon::now()->addMinutes(5),
        ]);
        return response()->json(['message' => 'verfication code send  successfully', 'verificationCode' => $verificationCode]);

      }
        if ($user) {
            // $account_sid = getenv("TWILIO_ACCOUNT_SID");
            // $auth_token = getenv("TWILIO_AUTH_TOKEN");
            // $twilio_number = getenv("TWILIO_PHONE_NUMBER");

            // $client = new Client($account_sid, $auth_token);
            // $client->messages->create(
            //     $request->country_code.$request->phone_number,
            //     [
            //         "from" => $twilio_number,
            //         "body" => "your verification code is $verificationCode",
            //     ]
            // );

            $user->update([
                'phone_number' => $request->phone_number,
                'country_code' => $request->country_code,
                'verification_code' => $verificationCode,
                'expire_at' => Carbon::now()->addMinutes(5),

            ]);
            return response()->json(['message' => 'verfication code send  successfully', 'verificationCode' => $verificationCode]);

        } else {
            try {
                // $account_sid = getenv("TWILIO_ACCOUNT_SID");
                // $auth_token = getenv("TWILIO_AUTH_TOKEN");
                // $twilio_number = getenv("TWILIO_PHONE_NUMBER");

                // $client = new Client($account_sid, $auth_token);
                // $client->messages->create(
                //     $request->country_code.$request->phone_number,
                //     [
                //         "from" => $twilio_number,
                //         "body" => "your verification code is $verificationCode",
                //     ]
                // );

                $user = User::create([
                    'phone_number' => $request->phone_number,
                    'country_code' => $request->country_code,
                    'verification_code' => $verificationCode,
                    'expire_at' => Carbon::now()->addMinutes(5),
                ]);

                return response()->json(['message' => 'verfication code send  successfully', 'verificationCode' => $verificationCode]);

            } catch (\Throwable $e) {
                return response()->json(['error' => $e->getMessage(), 'message' => 'verfication code not send  '], 500);
            }
        }

    }

    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            //    'token_device' => 'required',
            'verification_code' => 'required',
        ]);
        $deviceIdentifier = Str::uuid()->toString();

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $now = Carbon::now();
        $user = User::where('verification_code', $request->verification_code)->first();
        $currentToken = $user->token;

        if (!$user || $user->verification_code != $request->verification_code) {
            return response()->json(['error' => 'Invalid verification code'], 401);
        }
        if ($user && $now->isAfter($user->expire_at)) {
            return response()->json(['error' => 'verification code expire'], 401);

        }

        if ($currentToken) {
            $jwtToken = new Token($currentToken);
            JWTAuth::setToken($jwtToken)->invalidate();
        }
        $user->update([

            'token_device' => $request->token_device ?? null,
            'phone_verified' => 1,
            'current_device_id' => $deviceIdentifier,

        ]);

        $token = Auth::login($user);
        $user->update([

            'token_device' => $request->token_device ?? null,
            'phone_verified' => 1,
            'current_device_id' => $deviceIdentifier,
            'token' => $token,

        ]);
        return response()->json(['token' => $token, 'user' => $user]);
    }

    /**     * @OA\Get(
     *      path="/user-profile",
     *        operationId="user-profile",
     *      tags={"Auth"},
     *        summary=" show user profile",
     *      description="show user profile",
     *          @OA\Response(
     *          response=200,
     *            description="Successful operation",
     *       ),
     *        @OA\Response(
     *          response=401,
     *            description="Unauthenticated",
     *      ),
     *        @OA\Response(
     *          response=403,
     *            description="Forbidden"
     *      )
     *     )
     */
    public function userProfile()
    {
        return response()->json(auth()->user());
    }
    /**     * @OA\post(
     *      path="/update",
     *      operationId="update",
     *      tags={"Auth"},
     *      summary="update user profilr",
     *      description="update user profilr",
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

    public function upload_iamge(Request $request)
    {
        if (auth()->check()) {
            $user = Auth::user();
            $old_image = $user->image;
            $data = $request->except('image');
            $new_image = $this->uploadImage($request);
            if ($new_image) {
                $data['image'] = 'http://lendme.prizm-energy.com/public/storage/' . $new_image;
            }
            $user->update($data);

            if ($old_image && $new_image) {
                Storage::disk('public')->delete($old_image);
            }

            return response()->json($user);
        }

    }

    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => now()->addDays(365),
            //  auth()->factory()->getTTL() * 60,
            'user' => auth()->user(),
        ]);
    }

    public function uploadImage(Request $request)
    {
        if (!$request->hasfile('image')) {
            return;
        }

        $file = $request->file('image');
        $name = $file->getClientOriginalName();
        $fileNameWithoutSpaces = str_replace(' ', '_', $name);
        $path = $file->storeAs('upload/profile_image', $fileNameWithoutSpaces, 'public');
        return $path;
    }
}
