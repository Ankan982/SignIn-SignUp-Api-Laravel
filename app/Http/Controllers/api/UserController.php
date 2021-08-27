<?php

namespace App\Http\Controllers\api;


use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Http\Controllers\API\BaseController as BaseController;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends BaseController
{
    protected  $userService;

    public function __construct(UserService $userService)
    {

        $this->userService = $userService;
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'email |required',
            'password' => 'required'
        ]);

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);

        try {

            $user = $this->userService->create($input);
            //dd($isSuccess);
            $accessToken = $user->createToken($input['email'])->accessToken;
            $user->access_token = $accessToken;

            $response = [
                'status' => 200,
                'message' => 'User is created successfully.',
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

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

       
    
        try {

            $data = $request->all();

            if (Auth::attempt(['email' => $data['email'],  'password' => $data['password'] ])) {
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


    
}
