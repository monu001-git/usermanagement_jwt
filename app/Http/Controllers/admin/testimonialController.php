<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\testimonial;
use DB;
use Hash;
use Illuminate\Support\Facades\Validator;
use Str;

class testimonialController extends Controller
{
    public function index(Request $request)
    {
        try {

            $testimonialData = testimonial::orderBy('id','desc')->get();
            $testimonial = dEncrypt($testimonialData);

            return response()->json([
                'status' => 200,
                'message' => 'Data retrieved successfully!',
                'data' => $testimonial
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
                'title' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed',
                ], 422);
            }

            $data = new testimonial;
            $data->title = ucwords($decryptedData['title']);
            $data->testimonial  = $decryptedData['testimonial'];
            $data->giver_name  =  $decryptedData['giver_name'];
            $data->giver_role  = $decryptedData['giver_role'];
            $data->status  = $decryptedData['status'];
            $data->order  = $decryptedData['order'];

            $path = public_path('uploads/testimonial');
            if(!empty($decryptedData['image']) ) {
               $base64Image =  $decryptedData['image'];
               if($base64Image != null){
   
                $imageData = substr($base64Image, strpos($base64Image, ',') + 1);
                $image = base64_decode($imageData);
        
                $finfo = finfo_open();
                $mimeType = finfo_buffer($finfo, $image, FILEINFO_MIME_TYPE);
                finfo_close($finfo);
        
                $extension = '';
                if ($mimeType == 'image/jpeg') {
                    $extension = 'jpg';
                } elseif ($mimeType == 'image/png') {
                    $extension = 'png';
                } elseif ($mimeType == 'image/gif') {
                    $extension = 'gif';
                } else {
                    return response()->json(['error' => 'Unsupported image format'], 400);
                }
                $newname = time() . rand(10, 99) . '.' . $extension;
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                file_put_contents($path . '/' . $newname, $image);
                $data->image = $newname;
               }
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

            $testimonialData = testimonial::find(dDecrypt($id));
            $testimonial = dEncrypt($testimonialData);
            if($testimonial != null){
                return response()->json([
                    'status' => 200,
                    'success' => 'testimonial Show Successfully',
                    'data' => $testimonial
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
                $testimonial = testimonial::where('id',dDecrypt($id))->first();
                if (!empty($testimonial)) {
                $decryptedData = json_decode(dDecrypt($request->data), true);
                $validator = Validator::make($decryptedData, [
                    'title' => 'required',
                ]);
    
                if ($validator->fails()) {
                    return response()->json([
                        'errors' => $validator->errors(),
                        'message' => 'Validation failed',
                    ], 422);
                }

                $data = testimonial::find(dDecrypt($id));
                $data->title = ucwords($decryptedData['title']);
                $data->testimonial  = $decryptedData['testimonial'];
                $data->giver_name  =  $decryptedData['giver_name'];
                $data->giver_role  = $decryptedData['giver_role'];
                $data->status  = $decryptedData['status'];
                $data->order  = $decryptedData['order'];
    
                $path = public_path('uploads/testimonial');
                if(!empty($decryptedData['image']) ) {
                   $base64Image = $decryptedData['image'];
                   if($base64Image != null){
       
                    $imageData = substr($base64Image, strpos($base64Image, ',') + 1);
                    $image = base64_decode($imageData);
            
                    $finfo = finfo_open();
                    $mimeType = finfo_buffer($finfo, $image, FILEINFO_MIME_TYPE);
                    finfo_close($finfo);
            
                    $extension = '';
                    if ($mimeType == 'image/jpeg') {
                        $extension = 'jpg';
                    } elseif ($mimeType == 'image/png') {
                        $extension = 'png';
                    } elseif ($mimeType == 'image/gif') {
                        $extension = 'gif';
                    } else {
                        return response()->json(['error' => 'Unsupported image format'], 400);
                    }
                    $newname = time() . rand(10, 99) . '.' . $extension;
                    if (!file_exists($path)) {
                        mkdir($path, 0777, true);
                    }
                    file_put_contents($path . '/' . $newname, $image);
                    $data->image = $newname;
                   }
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
           
            $testimonial = testimonial::where('id',dDecrypt($id))->first();
            if (!empty($testimonial)) {
                testimonial::find(dDecrypt($id))->delete();
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
