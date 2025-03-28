<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\student_corner;
use App\Models\student_corner_image;
use DB;
use Hash;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;

class studentCornerController extends Controller
{
    public function index(Request $request)
    {
        try {

            $studentCornerData = student_corner::orderBy('id','desc')->get();
            $studentCorner = dEncrypt($studentCornerData);
            return response()->json([
                'status' => 200,
                'message' => 'Data retrieved successfully!',
                'data' => $studentCorner
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

            
            $data = new student_corner;
            $data->title = $decryptedData['title'];
            $data->description  = $decryptedData['description'];
            $data->status  =$decryptedData['status'];
            $data->order  = $decryptedData['order'];
            $data->save();

            if(!empty($decryptedData['imageContent'])){
                foreach ($decryptedData['imageContent'] as $index => $imageContents) {
                if ($imageContents) {
                    $imageContentss = new student_corner_image();
                    $imageContentss->image_title = $imageContents['image_title'];
                    $path = public_path('uploads/studentCorner');
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
                    $imageContentss->student_corner_id  = $data->id;
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

            $studentCornerData = student_corner::find(dDecrypt($id));

            if ($studentCornerData != null) {
                   $studentCornerImage = DB::table('student_corner_images')
                    ->where('student_corner_id', $studentCornerData->id)
                    ->whereNull('deleted_at')
                    ->get();
               
                $studentCornerComplete = [
                    'studentCornerData' => $studentCornerData,
                    'studentCornerImage' => $achiestudentCornerImagevementImage,
                ];
            } else {
                $studentCornerComplete = [
                    'achievementData' => null,
                    'achievementImage' => [],
                ];
            }

            return response()->json([
                'status' => 200,
                'success'=> 'Student Corner Show Successfully',
                'data' => $studentCornerComplete
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
           
            $studentCorner = student_corner::where('id',dDecrypt($id))->first();

            if (!empty($studentCorner)) {

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

                $data = student_corner::find(dDecrypt($id));
                $data->title = $decryptedData['title'];
                $data->description  = $decryptedData['description'];
                $data->status  =$decryptedData['status'];
                $data->order  = $decryptedData['order'];
                $data->save();
           
                if(!empty($decryptedData['imageContent'])){
                    foreach ($decryptedData['imageContent'] as $index => $imageContents) {
    
                        if ($imageContents['imageId']) {
                            $imageContentss = student_corner_image::find($imageContents['imageId']);
                        } else {
                            $imageContentss = new student_corner_image();
                        }
                    
                        $imageContentss->image_title = $imageContents['image_title'];

                        $path = public_path('uploads/event');
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
                        $imageContentss->student_corner_id  = $data->id;
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
        
            $studentCorner = student_corner::where('id',dDecrypt($id))->first();
            if (!empty($studentCorner)) {
                student_corner::find(dDecrypt($id))->delete();
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
