<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Http\Requests\ValidUser;
use App\Http\Requests\ValidLoginUser;
use Carbon\Carbon;
use Exception;
use Laravel\Passport\RefreshToken;
use Laravel\Passport\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected  $userService;

    public function __construct(UserService $userService)
    {

        $this->userService = $userService;
    }

    public function register(ValidUser $request)
    {

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $input['password_confirmation'] = bcrypt($input['password_confirmation']);
        $input['dob'] = Carbon::createFromFormat('d/m/Y', $input['dob'])->format('d-m-Y');


        try {

            $user = $this->userService->create($input);
            $accessToken = $user->createToken($input['email'])->accessToken;
            $user->access_token = $accessToken;

            $response = [
                'status' => 200,
                'message' => 'User has been created successfully.',
                'data' => $user,
                'errors' => [],
                'meta' => new \stdClass(),
                'info' => new \stdClass(),

            ];
            return response()->json($response, $response['status']);
        } catch (Exception $e) {

            $response = [
                'status' => 500,
                'message' => 'Something went wrong.',
                'data' => new \stdClass(),
                'errors' => [
                    [
                        'field' => 'error',
                        'message' => [$e->getMessage()],
                    ],
                ],
                'meta' => new \stdClass(),
                'info' => new \stdClass(),

            ];
            return response()->json($response, $response['status']);
        }
    }

    public function login(ValidLoginUser $request)
    {

        try {

            $data = $request->all();

            if (Auth::attempt(['email' => $data['email'],  'password' => $data['password']])) {
                $user = Auth()->user();
                $accessToken = $user->createToken($data['email'])->accessToken;
                $user->access_token = $accessToken;
                $response = [
                    'status' => 200,
                    'message' => 'You have been logged in...',
                    'data' => $user,
                    'errors' => [],
                    'meta' => new \stdClass(),
                    'info' => new \stdClass(),

                ];
            } else {
                $response = [
                    'status' => 404,
                    'message' => 'Email id or passport incorrect',
                    'data' => new \stdClass(),
                    'errors' => [],
                    'meta' => new \stdClass(),
                    'info' => new \stdClass(),
                ];
            }

            return response()->json($response, $response['status']);
        } catch (Exception $e) {

            $response = [
                'status' => 500,
                'message' => 'Invalid User',
                'data' => new \stdClass(),
                'errors' => [
                    [
                        'field' => 'error',
                        'message' => [$e->getMessage()],
                    ],
                ],
                'meta' => new \stdClass(),
                'info' => new \stdClass(),

            ];
            return response()->json($response, $response['status']);
        }
    }

    public function details()
    {

        $user_details = $this->userService->findAll();
        $response = [
            'status' => 200,
            'message' => 'All User details',
            'data' => $user_details,
            'errors' => [],
            'meta' => new \stdClass(),
            'info' => new \stdClass(),

        ];
        return response()->json($response, $response['status']);
    }

    public function logout()
    {
        $user = Auth::user()->token();
        $user->revoke();
        $message= 'Logged Out';
        $response = [
            'status' => 200,
            'message' => 'User has been logged out successfully.',
            'data' => $message,
            'errors' => [],
            'meta' => new \stdClass(),
            'info' => new \stdClass(),

        ];
        return response()->json($response, $response['status']);
    }
}
