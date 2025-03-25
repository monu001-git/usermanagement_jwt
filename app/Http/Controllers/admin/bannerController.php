<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\banner;
use DB;
use Hash;
use Illuminate\Support\Facades\Validator;

class bannerController extends Controller
{
    public function index(Request $request)
    {
        try {

            $banner = banner::orderBy('id','asc')->get();
            return response()->json([
                'status' => 200,
                'message' => 'Data retrieved successfully!',
                'data' => $banner
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
              //  'title' => 'required|unique:banners,title',
               // 'order' => 'required',
               // 'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed',
                ], 422);
            }
            

            $data = new banner;
            $data->title = ucwords($request->title);
            $data->description  = $request->description;
            $data->url  = $request->url;
            $data->link_type  = $request->link_type;
            $data->order  = $request->order;
            $data->status  = $request->status;

            $path = public_path('uploads/banner');
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $newname = time() . rand(10, 99) . '.' . $file->getClientOriginalExtension();
                $file->move($path, $newname);
                $data->image = $newname;
            }

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

            $banner = banner::find(dDecrypt($id));
            if($banner != null){
                return response()->json([
                    'status' => 200,
                    'success', 'banner Show Successfully',
                    'data' => $banner
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
           
            $banner = banner::where('id',dDecrypt($id))->first();

            if (!empty($banner)) {

                $validator = Validator::make($request->all(), [
                  //  'title' => 'required',
                  //  'order' => 'required',
                   // 'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'errors' => $validator->errors(),
                        'message' => 'Validation failed',
                    ], 422);
                }           

                $data = banner::find(dDecrypt($id));
                $data->title = ucwords($request->title);
                $data->description  = $request->description;
                $data->url  = $request->url;
                $data->link_type  = $request->link_type;
                $data->order  = $request->order;
                $data->status  = $request->status;

                $path = public_path('uploads/banner');
                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    $newname = time() . rand(10, 99) . '.' . $file->getClientOriginalExtension();
                    $file->move($path, $newname);
                    $data->image = $newname;
                }
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
        
            $banner = banner::where('id',dDecrypt($id))->first();
            if (!empty($banner)) {
                banner::find($id)->delete();
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