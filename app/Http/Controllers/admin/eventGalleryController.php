<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\event;
use App\Models\event_image;
use DB;
use Hash;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;

class eventGalleryController extends Controller
{
    public function index(Request $request)
    {
        try {

            $eventData = event::orderBy('id','desc')->get();
            $event = dEncrypt($eventData);
            return response()->json([
                'status' => 200,
                'message' => 'Data retrieved successfully!',
                'data' => $event
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
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed',
                ], 422);
            }

            
            $data = new event;
            $data->name = $decryptedData['name'];
            $data->description  = $decryptedData['description'];
            $data->event_date  = $decryptedData['event_date'];
            $data->status  =$decryptedData['status'];
            $data->order  = $decryptedData['order'];
            $data->save();

            if(!empty($decryptedData['imageContent'])){
                foreach ($decryptedData['imageContent'] as $index => $imageContents) {
                if ($imageContents) {
                  $imageContentss = new event_image();
                    $imageContentss->image_name = $imageContents['image_name'];
                    $path = public_path('uploads/event');
                    if(!empty($imageContents['image_path']) ) {
                    $base64Image = $imageContents['image_path'];
                    if($base64Image != null){

                        $imageData = substr($base64Image, strpos($base64Image, ',') + 1);
                        $image = base64_decode($imageData);
                
                        $finfo = finfo_open();
                        $mimeType = finfo_buffer($finfo, $image, FILEINFO_MIME_TYPE);
                        finfo_close($finfo);

                        return  $mimeType;
                
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
                        $data->image_path = $newname;
                    }
                    }
                    $imageContentss->event_id  = $data->id;
                    $imageContentss->save();
                }
                }
            }
    
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

            $menuData = event::find($id);
            $event = dEncrypt($menuData);
            if($event != null){
                return response()->json([
                    'status' => 200,
                    'success', 'Event Show Successfully',
                    'data' => $event
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
           
            $event = event::where('id',$id)->first();

            if (!empty($event)) {

                $decryptedData = json_decode(dDecrypt($request->data), true);

                $validator = Validator::make($decryptedData, [
                    'name' => 'required',
                    'order' => 'required',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'errors' => $validator->errors(),
                        'message' => 'Validation failed',
                    ], 422);
                } 

                $data = event::find($id);
                $data->title = $request->title;
                $data->status  = $request->status;
                $data->order  = $request->order;
                      

                $path = public_path('uploads/media');
                if ($request->has('image')) {
                $base64Image = $request->input('image');
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
        
            $event = event::where('id',$id)->first();
            if (!empty($event)) {
                event::find($id)->delete();
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
