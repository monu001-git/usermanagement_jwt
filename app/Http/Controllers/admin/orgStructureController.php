<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\org_structure;
use DB;
use Hash;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;

class orgStructureController extends Controller
{
    public function index(Request $request)
    {
        try {

            $org = org_structure::orderBy('id','asc')->get();
            return response()->json([
                'status' => 200,
                'message' => 'Data retrieved successfully!',
                'data' => $org
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
                'name' => 'required',
               // 'order' => 'required',
               // 'link_type' => 'required',
               // 'menu_place' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed',
                ], 422);
            }
            
            $data = new org_structure;
            //seo
            $data->meta_title = $request->meta_title;
            $data->meta_description = $request->meta_description;
            $data->meta_keyword = $request->meta_keyword;
            $data->body_script = $request->body_script;
            $data->head_script = $request->head_script;

            $data->name = $request->name;
            $data->email = $request->email;
            $data->phone = $request->phone;



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

            $org = org_structure::find($id);
            if($menu != null){
                return response()->json([
                    'status' => 200,
                    'success', 'Org Show Successfully',
                    'data' => $org
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
           
            $org = org_structure::where('id',$id)->first();

            if (!empty($org)) {

                $validator = Validator::make($request->all(), [
                   // 'name' => 'required',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'errors' => $validator->errors(),
                        'message' => 'Validation failed',
                    ], 422);
                }           

                $data = menu::find($id);
                $data->meta_title = $request->meta_title;
                $data->meta_description = $request->meta_description;
                $data->meta_keyword = $request->meta_keyword;
                $data->body_script = $request->body_script;
                $data->head_script = $request->head_script;
      
                $data->name = $request->name;
                $data->email = $request->email;
                $data->phone = $request->phone;

        
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
        
            $menu = org_structure::where('id',$id)->first();
            if (!empty($menu)) {
                org_structure::find($id)->delete();
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
