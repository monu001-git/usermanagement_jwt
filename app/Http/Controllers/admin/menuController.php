<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\menu;
use DB;
use Hash;
use Illuminate\Support\Facades\Validator;
use Str;

class menuController extends Controller
{
    public function index(Request $request)
    {
        try {

            $menuData = menu::orderBy('id','Desc')->get();
            $menu = dEncrypt($menuData);
            return response()->json([
                'status' => 200,
                'message' => 'Data retrieved successfully!',
                'data' => $menu
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

            $decryptedData = json_decode(dDecrypt($request->data), true);

            $validator = Validator::make($decryptedData, [
               'name' => 'required',
                'order' => 'required',
                'link_type' => 'required',
                'menu_place' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed',
                ], 422);
            }

            $data = new menu;
            $data->name = ucwords($decryptedData['name']);
            $data->slug    = Str::slug($decryptedData['name'], "-");

            if ($decryptedData['link_type'] === "external") {
                $data->url  = "/";
            } else {
                $data->url  =  Str::slug($decryptedData['name'], "-");
            }

            $data->order  = $decryptedData['order'];
            $data->link_type = $decryptedData['link_type'];
            $data->menu_place  =$decryptedData['menu_place'];
            $data->status  = $decryptedData['status'];
            $data->content_id =  $decryptedData['content_id'] ? $decryptedData['content_id']:null;
            $data->parent_id = $decryptedData['parent_id'] ? $decryptedData['parent_id']:null ;
            $data->save();

            return response()->json([
                'status' => 200,
                'message' => 'Data Save Successfully!',
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

            $menuData = menu::find($id);
            $menu = dEncrypt($menuData);
            if($menu != null){

                return response()->json([
                    
                    'status' => 200,
                    'success' => 'Menu Show Successfully',
                    'data' => $menu
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
        try {
           
            $menu = menu::where('id',$id)->first();

            if (!empty($menu)) {

                $decryptedData = json_decode(dDecrypt($request->data), true);

                $validator = Validator::make($decryptedData, [
                    'name' => 'required',
                    'order' => 'required',
                    'link_type' => 'required',
                    'menu_place' => 'required',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'errors' => $validator->errors(),
                        'message' => 'Validation failed',
                    ], 422);
                }    

                $data = menu::find($id);
                $data->name = ucwords($decryptedData['name']);
                $data->slug    = Str::slug($decryptedData['name'], "-");
    
                if ($decryptedData['link_type'] === "external") {
                    $data->url  = "/";
                } else {
                    $data->url  =  Str::slug($decryptedData['name'], "-");
                }
    
                $data->order  = $decryptedData['order'];
                $data->link_type = $decryptedData['link_type'];
                $data->menu_place  =$decryptedData['menu_place'];
                $data->status  = $decryptedData['status'];
                $data->content_id =  $decryptedData['content_id'] ? $decryptedData['content_id']:null;
                $data->parent_id = $decryptedData['parent_id'] ? $decryptedData['parent_id']:null ;
                $data->save();
            
                return response()->json([
                    'status' => 200,
                    'message' => 'Data Update Successfully!',
                
                ]);

            }else{

                return response()->json([
                    'message' => 'You are trying to perform an unethical process. Your request is failed.',
                    'status' => false,
                ], 400); 

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

   
    public function destroy($id)
    {
        try {
        
            $menu = menu::where('id',$id)->first();
            if (!empty($menu)) {
                menu::find($id)->delete();
            } else {
               return response()->json([
                'message' => 'You are trying to perform an unethical process. Your request is failed.',
                'status' => false,
               ], 400); 
            }

            return response()->json([
                'status' => 200,
                'message' => 'Data Delete Successfully!',
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
