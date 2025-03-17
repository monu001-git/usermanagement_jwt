<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use DB;
use Hash;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;

class userController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $user = User::orderBy('id','desc')->get();    
            return response()->json([
                'status' => 200,
                'success', 'User deleted successfully',
                'data' => $user
            ]);
     
        } catch (\PDOException $e) { \Log::error('A PDOException occurred: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Database error occurred.',
                'error' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) { \Log::error('An exception occurred: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while fetching the data.',
                'error' => $e->getMessage()
            ], 500);
        } catch (\Throwable $e) { \Log::error('An unexpected exception occurred: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

   
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                //'name' => 'required',
                //'email' => 'required|email|max:255|unique:users,email|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/i',
                //'password' => 'required|same:confirm-password',
                //'roles' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed',
                ], 422);
            }

            $decryptedData = json_decode(dDecrypt($request->data), true);
          
            $data = new User;
            $data->name = $decryptedData['name'];
            $data->email  = $decryptedData['email'];
            $data->password  = $decryptedData['password'];
          
            $data->save();

            return response()->json([
                'status' => 200,
                'success' => "User Created successfully",
            ]);

        } catch (\PDOException $e) { \Log::error('A PDOException occurred: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Database error occurred.',
                'error' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) { \Log::error('An exception occurred: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while fetching the data.',
                'error' => $e->getMessage()
            ], 500);
        } catch (\Throwable $e) { \Log::error('An unexpected exception occurred: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function show($id)
    {
        try {
       
            $user = User::find($id);
            if($user != null){

                return response()->json([
                    'status' => 200,
                    'success' => 'User Show Successfully',
                    'data' => $user
                ]);

            }else{
                return response()->json([
                    'status' => 200,
                    'success', 'Record Not found',
                ]);
            }


        } catch (\PDOException $e) { \Log::error('A PDOException occurred: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Database error occurred.',
                'error' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) { \Log::error('An exception occurred: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while fetching the data.',
                'error' => $e->getMessage()
            ], 500);
        } catch (\Throwable $e) { \Log::error('An unexpected exception occurred: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    
    public function update(Request $request, $id)
    {
        // try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|max:255|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/i',
               // 'roles' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed',
                ], 422);
            }
            
            $input = $request->all();
           

            if (!empty($input['password'])) {
                $input['password'] = Hash::make($input['password']);
            } else {
                $input = Arr::except($input, array('password'));
            }

            $user = User::find($id);
            $user->update($input);
           // DB::table('model_has_roles')->where('model_id', $id)->delete();

          //  $user->assignRole($request->input('roles'));

        
            return response()->json([
                'status' => 200,
                'success' => 'User Updated successfully',
            ]);

        // } catch (\PDOException $e) { \Log::error('A PDOException occurred: ' . $e->getMessage());
        //     return response()->json([
        //         'status' => 500,
        //         'message' => 'Database error occurred.',
        //         'error' => $e->getMessage()
        //     ], 500);
        // } catch (\Exception $e) { \Log::error('An exception occurred: ' . $e->getMessage());
        //     return response()->json([
        //         'status' => 500,
        //         'message' => 'An error occurred while fetching the data.',
        //         'error' => $e->getMessage()
        //     ], 500);
        // } catch (\Throwable $e) { \Log::error('An unexpected exception occurred: ' . $e->getMessage());
        //     return response()->json([
        //         'status' => 500,
        //         'message' => 'An unexpected error occurred.',
        //         'error' => $e->getMessage()
        //     ], 500);
        // }
    }

  
    public function destroy($id)
    {
        try {

            $user = User::where('id',$id)->first();
            if (!empty($user)) {

                User::find($id)->delete();

            } else {

                return response()->json([
                    'message' => 'You are trying to perform an unethical process. Your request is failed.',
                    'status' => false,
                ], 400); 
        
            }
        
            return response()->json([
                'status' => 200,
                'message'=> 'User deleted successfully',
            ]);
            
        } catch (\PDOException $e) { \Log::error('A PDOException occurred: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Database error occurred.',
                'error' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) { \Log::error('An exception occurred: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while fetching the data.',
                'error' => $e->getMessage()
            ], 500);
        } catch (\Throwable $e) { \Log::error('An unexpected exception occurred: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
 
}