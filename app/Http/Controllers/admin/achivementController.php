<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\achievement;
use App\Models\achievementImage;
use DB;
use Hash;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;

class achivementController extends Controller
{
    public function index(Request $request)
    {
        try {

            $achievementData = achievement::orderBy('id','desc')->get();
            $achievement = dEncrypt($achievementData);
            return response()->json([
                'status' => 200,
                'message' => 'Data retrieved successfully!',
                'data' => $achievement
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
               'title' => 'required',
               'order' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' =>422,
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed',
                ]);
            }

            
            $data = new achievement;
            $data->title = $decryptedData['title'];
            $data->description  = $decryptedData['description'];
            $data->status  =$decryptedData['status'];
            $data->date  =$decryptedData['date'];
            $data->order  = $decryptedData['order'];
            $data->save();

        
            if($decryptedData['imageContent'] != ''){
                foreach ($decryptedData['imageContent'] as $index => $imageContents) {
                    if ($imageContents) {
                    $imageContentss = new achievementImage();
                    $imageContentss->image_title = $imageContents['image_title'];
    
                    $path = public_path('uploads/achievement');
                    if(!empty($imageContents['image_path']) ) {
                        $base64Image = $imageContents['image_path'];
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
    
                    $imageContentss->achievement_id  = $data->id;
                    $imageContentss->save();
                    }
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

            $achievementData = achievement::find(dDecrypt($id));

            if ($achievementData != null) {
                   $achievementImage = DB::table('achievement_images')
                    ->where('achievement_id', $achievementData->id)
                    ->whereNull('deleted_at')
                    ->get();
               
                $achievementComplete = [
                    'achievementData' => $achievementData,
                    'achievementImage' => $achievementImage,
                ];
            } else {
                $achievementComplete = [
                    'achievementData' => null,
                    'achievementImage' => [],
                ];
            }

            return response()->json([
                'status' => 200,
                'success'=> 'Achievement Show Successfully',
                'data' => $achievementComplete
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
           
            $achievement = achievement::where('id',dDecrypt($id))->first();

            if (!empty($achievement)) {

                $decryptedData = json_decode(dDecrypt($request->data), true);
                
                $validator = Validator::make($decryptedData, [
                    'title' => 'required',
                    'order' => 'required',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'stutus'=>422,
                        'errors' => $validator->errors(),
                        'message' => 'Validation failed',
                    ]);
                } 

                $data = achievement::find(dDecrypt($id));
                $data->title = $decryptedData['title'];
                $data->description  = $decryptedData['description'];
                $data->status  =$decryptedData['status'];
                $data->date  =$decryptedData['date'];
                $data->order  = $decryptedData['order'];
                $data->save();

                if($decryptedData['imageContent'] != ''){
                    foreach ($decryptedData['imageContent'] as $index => $imageContents) {
                      
                      
                        if ($imageContents['imageId']) {
                            $imageContentss = achievementImage::find($imageContents['imageId']);
                        } else {
                            $imageContentss = new achievementImage();
                        }

                        $imageContentss->image_title = $imageContents['image_title'];
        
                        $path = public_path('uploads/achievement');
                        if(!empty($imageContents['image_path']) ) {
                            $base64Image = $imageContents['image_path'];
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
        
                        $imageContentss->achievement_id  = $data->id;
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
        
            $achievement = achievement::where('id',dDecrypt($id))->first();
            if (!empty($achievement)) {
                achievement::find(dDecrypt($id))->delete();
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
