<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\event;
use App\Models\event_image;
use DB;
use Hash;
use Str;
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

            DB::beginTransaction();

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
            $data->slug    = Str::slug($decryptedData['name'], "-");
            $data->description  = $decryptedData['description'];
            $data->event_date  = $decryptedData['event_date'];
            $data->status  =$decryptedData['status'];
            $data->order  = $decryptedData['order'];
            $data->save();

            if(!empty($decryptedData['images'])){
                  
                foreach ($decryptedData['images'] as $index => $imageContents) {

                    $imageContentss = new event_image();
                    $imageContentss->image_name =  "image$index";

                    $path = public_path('uploads/event');
                    if(!empty($imageContents) ) {
                        $base64Image = $imageContents;

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
                        $imageContentss->image_path = $newname;
                        }
                    }
                    $imageContentss->event_id  = $data->id;
                    $imageContentss->save();
                }

            }

            DB::commit(); 
    
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

            $eventData = event::find(dDecrypt($id));

            if ($eventData != null) {
                   $eventImages = DB::table('event_images')
                    ->where('event_id', $eventData->id)
                    ->whereNull('deleted_at')
                    ->get();
               
                $eventComplete = [
                    'eventData' => $eventData,
                    'eventImages' => $eventImages,
                ];
            } else {
                $eventComplete = [
                    'eventData' => null,
                    'eventImages' => [],
                ];
            }

            return response()->json([
                'status' => 200,
                'success'=> 'Event Gallery Show Successfully',
                'data' => $eventComplete
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

    public function update(Request $request, $id)
    {
        try {

            DB::beginTransaction();
           
            $event = event::where('id',dDecrypt($id))->first();

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

                $data = event::find(dDecrypt($id));
                $data->name = $decryptedData['name'];
                $data->slug    = Str::slug($decryptedData['name'], "-");
                $data->description  = $decryptedData['description'];
                $data->event_date  = $decryptedData['event_date'];
                $data->status  =$decryptedData['status'];
                $data->order  = $decryptedData['order'];
                $data->save();
           
                if(!empty($decryptedData['images'])){
                  
                    foreach ($decryptedData['images'] as $index => $imageContents) {

                        $imageContentss = new event_image();
                        $imageContentss->image_name =  "image$index";

                        $path = public_path('uploads/event');
                        if(!empty($imageContents) ) {
                            $base64Image = $imageContents;

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
                            $imageContentss->image_path = $newname;
                            }
                        }
                        $imageContentss->event_id  = $data->id;
                        $imageContentss->save();
                    }

                }
    
                DB::commit(); 
  
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
        
            $event = event::where('id',dDecrypt($id))->first();
            if (!empty($event)) {
                event::find(dDecrypt($id))->delete();
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