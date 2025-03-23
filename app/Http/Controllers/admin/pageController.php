<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\page;
use App\Models\page_content;
use App\Models\page_image;
use DB;
use Hash;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;

class pageController extends Controller
{
  
    public function index(Request $request)
    {
        try {

            $pageData = page::orderBy('id','asc')->get();
            $page = dEncrypt($pageData);

            return response()->json([
                'status' => 200,
                'message' => 'Data retrieved successfully!',
                'data' => $page
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
             // 'name' => 'required',
             // 'email' => 'required|email|max:255|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/i',
            ]);
 
             if ($validator->fails()) {
                 return response()->json([
                     'errors' => $validator->errors(),
                     'message' => 'Validation failed',
                 ], 422);
             }
     
            $data = new page;
            $data->meta_title =  $decryptedData['meta_title'];
            $data->meta_description =  $decryptedData['meta_description'];
            $data->meta_keyword = $decryptedData['meta_keyword'];
            $data->status  = $decryptedData['status'];
            $data->order  =  $decryptedData['order'];
            $path = public_path('uploads/page');
            if(!empty($decryptedData['banner']) ) {
               $base64Image = $decryptedData['banner'];
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
                $data->banner = $newname;
               }
            }
            $data->save();

            //image content
            if($decryptedData['contentSwitch'] != '' && $decryptedData['contentSwitch'] != null && $decryptedData['contentSwitch'] != false ){
                $content = new page_content;
                $content->name = $decryptedData['name'];
                $content->descriptions =  $decryptedData['descriptions'];
                $path = public_path('uploads/page');
                if(!empty($decryptedData['content_image']) ) {
                    $base64Image = $decryptedData['content_image'];
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
                     $content->content_image = $newname;
                    }
                 }
              
                
                $content->page_id = $data->id;
                $content->save();
            } 

            //image section
            if($decryptedData['items'] != '' && $decryptedData['items'] != null){
                foreach ($decryptedData['imageContent'] as $index => $imageContents) {
                    if ($imageContents) {
                    $imageContentss = new page_image();
                    $imageContentss->image_title = $imageContents['image_title'];;
    
                    $path = public_path('uploads/page');
                    if(!empty($imageContents['page_images']) ) {
                       $base64Image = $imageContents['page_images'];
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
                        $imageContentss->page_images = $newname;
                       }
                    }

                    $imageContentss->page_id = $data->id;
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

            $pageData = page::find($id)->first();

            if ($pageData != null) {
                $pageContent = DB::table('page_contents')
                    ->where('page_id', $pageData->id)
                    ->whereNull('deleted_at')
                    ->first();

                $pageImage = DB::table('page_images')
                    ->where('page_id', $pageData->id)
                    ->whereNull('deleted_at')
                    ->get();

                // Prepare the pageComplete array with data
                $pageComplete = [
                    'pageData' => $pageData,
                    'pageContent' => $pageContent,
                    'pageImage' => $pageImage,
                ];
            } else {
                // Prepare an empty pageComplete structure with empty data instead of just an empty array
                $pageComplete = [
                    'pageData' => null,
                    'pageContent' => null,
                    'pageImage' => [],
                ];
            }

            $page = $pageComplete;

            return response()->json([
                'status' => 200,
                'success', 'Page Show Successfully',
                'data' => $page
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

            $decryptedData = json_decode(dDecrypt($request->data), true);
         
            $validator = Validator::make($decryptedData, [
             // 'name' => 'required',
             // 'email' => 'required|email|max:255|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/i',
            ]);
 
             if ($validator->fails()) {
                 return response()->json([
                     'errors' => $validator->errors(),
                     'message' => 'Validation failed',
                 ], 422);
             }
     
            $data = page::find($id);
            $data->meta_title =  $decryptedData['meta_title'];
            $data->meta_description =  $decryptedData['meta_description'];
            $data->meta_keyword = $decryptedData['meta_keyword'];
            $data->status  = $decryptedData['status'];
            $data->order  =  $decryptedData['order'];

            $path = public_path('uploads/page');
            if(!empty($decryptedData['banner']) ) {
               $base64Image = $decryptedData['banner'];
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
                $data->banner = $newname;
               }
            }
            $data->save();


            //content
            if($decryptedData['contentSwitch'] != '' && $decryptedData['contentSwitch'] != null && $decryptedData['contentSwitch'] != false ){
            
                $contentData = page_content::where('page_id', $data->id)->first();
                if($contentData != null){
                    $content = page_content::where('page_id', $data->id)->first();
                    $content->name = $decryptedData['name'];
                    $content->descriptions =  $decryptedData['descriptions'];
                    $path = public_path('uploads/page');
                    if(!empty($decryptedData['content_image']) ) {
                        $base64Image = $decryptedData['content_image'];
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
                        $content->content_image = $newname;
                        }
                    }
                    $content->page_id = $data->id;
                    $content->save();
                }else{
                    $content = new page_content;
                    $content->name = $decryptedData['name'];
                    $content->descriptions =  $decryptedData['descriptions'];
                    $path = public_path('uploads/page');
                    if(!empty($decryptedData['content_image']) ) {
                        $base64Image = $decryptedData['content_image'];
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
                            $content->content_image = $newname;
                        }
                    }
                    $content->page_id = $data->id;
                    $content->save();
                }
            } else{
                $content = page_content::where('page_id', $data->id)->first();
                if ($content) {
                    $content->delete();
                } 

            }

            // //image section
            if($decryptedData['items'] != '' && $decryptedData['items'] != null){
                foreach ($decryptedData['imageContent'] as $index => $imageContents) {
                    if ($imageContents['imageId']) {
                        $imageContentss = page_image::find($imageContents['imageId']);
                    } else {
                        $imageContentss = new page_image();
                    }
                
                    $imageContentss->image_title = $imageContents['image_title'];
    
                    $path = public_path('uploads/page');
                    if(!empty($imageContents['page_images']) ) {
                       $base64Image = $imageContents['page_images'];
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
                        $imageContentss->page_images = $newname;
                       }
                    }

                    $imageContentss->page_id = $data->id;
                    $imageContentss->save();
                    // }
                }
            }else{
                $image = page_image::where('page_id', $id)->get();
                if ($image->isNotEmpty()) {
                    foreach ($image as $img) {
                        $img->delete();
                    }
                }

            }

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Data Update Successfully!',
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

   
    public function destroy($id)
    {
        try {
        
            $page = page::where('id',$id)->first();
            if (!empty($page)) {
                page::find($id)->delete();
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