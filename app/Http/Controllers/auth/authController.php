<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class authController extends Controller
{

    public function login(Request $request)
    {
        try{


            $decryptedData = json_decode(dDecrypt($request->data), true);

            $validator = Validator::make($decryptedData, [
                'password' => 'required|string',
                'email' => 'required|email|max:255|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/i',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed',
                ], 422);
            }
        
             $credentials = [
                'email' => $decryptedData['email'],
                'password' => $decryptedData['password'],
            ];


            $token = Auth::attempt($credentials);
            if (!$token) {
                return response()->json([
                    'status' =>  401,
                    'message' => 'Unauthorized',
                ]);
            }

            $userData = Auth::user();
            $user = dEncrypt($userData);
            return response()->json([
                    'status'=> 200,
                    'message' => 'Login Successfully!!!',
                    'user' => $user,
                    'authorisation' => [
                        'token' => $token,
                        'type' => 'bearer',
                    ]
                ]);
            } catch (\PDOException $e) {
                \Log::error('A PDOException occurred: ' . $e->getMessage());
                return response()->json([
                    'status' => 500,
                    'message' => 'Database error occurred.',
                    'error' => $e->getMessage()
                ], 500);
            } catch (\Exception $e) {
                \Log::error('An exception occurred: ' . $e->getMessage());
                return response()->json([
                    'status' => 500,
                    'message' => 'An error occurred while fetching the data.',
                    'error' => $e->getMessage()
                ], 500);
            } catch (\Throwable $e) {
                \Log::error('An unexpected exception occurred: ' . $e->getMessage());
                return response()->json([
                    'status' => 500,
                    'message' => 'An unexpected error occurred.',
                    'error' => $e->getMessage()
                ], 500);
            }

    }


    public function logout()
    {
        try{
            
            Auth::logout();
            return response()->json([
                'status' => 200,
                'message' => 'Successfully logged out',
            ]);

        } catch (\PDOException $e) {
            \Log::error('A PDOException occurred: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Database error occurred.',
                'error' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            \Log::error('An exception occurred: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while fetching the data.',
                'error' => $e->getMessage()
            ], 500);
        } catch (\Throwable $e) {
            \Log::error('An unexpected exception occurred: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function refresh()
    {
        try{
           
            return response()->json([
                'status' => 'success',
                'user' => Auth::user(),
                'authorisation' => [
                    'token' => Auth::refresh(),
                    'type' => 'bearer',
                ]
            ]);

        } catch (\PDOException $e) {
            \Log::error('A PDOException occurred: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Database error occurred.',
                'error' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            \Log::error('An exception occurred: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while fetching the data.',
                'error' => $e->getMessage()
            ], 500);
        } catch (\Throwable $e) {
            \Log::error('An unexpected exception occurred: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}