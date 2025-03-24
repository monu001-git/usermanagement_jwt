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

            $orgData = org_structure::orderBy('id','asc')->get();
            $org = dEncrypt($orgData);

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
           $decryptedData = json_decode(dDecrypt($request->data), true);
        
           $validator = Validator::make($decryptedData, [
            'name' => 'required',
            'email' => 'required|email|max:255|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/i',
           ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed',
                ], 422);
            }
    
            $data = new org_structure;
            $data->meta_title = $decryptedData['meta_title'];
            $data->meta_description = $decryptedData['meta_description'];
            $data->meta_keyword = $decryptedData['meta_keyword'];
            $data->body_script =$decryptedData['body_script'];
            $data->head_script = $decryptedData['head_script'];
            
            $data->name = $decryptedData['name'];
            $data->email = $decryptedData['email'];
            $data->phone = $decryptedData['phone'];
            $data->address = $decryptedData['address'];
            $data->header_video = $decryptedData['header_video'];

            $data->payFee = $decryptedData['payFee'];
            $data->admissionOpenLink = $decryptedData['admissionOpenLink'];
            $data->map = $decryptedData['map'];
            $data->footer_content = $decryptedData['footer_content'];
    
            $data->about_heading = $decryptedData['about_heading'];
            $data->about_content = $decryptedData['about_content'];
            $data->about_video = $decryptedData['about_video'];

            $data->facilitie_heading = $decryptedData['facilitie_heading'];
            $data->facilitie_content = $decryptedData['facilitie_content'];

            $data->url1 = $decryptedData['url1'];
            $data->url2 = $decryptedData['url2'];
            $data->url3 = $decryptedData['url3'];
            $data->url4 = $decryptedData['url4'];

   
            //header logo
            $path = public_path('uploads');
             if(!empty($decryptedData['header_logo']) ) {
               $base64Image = $decryptedData['header_logo'];
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
                $data->header_logo = $newname;
               }
            }
            //menu_logo
            $path = public_path('uploads');
            if(!empty($decryptedData['menu_logo']) ) {
                $base64Image = $decryptedData['menu_logo'];
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
                $data->menu_logo = $newname;
                }
            }
            //footer_logo
            $path = public_path('uploads');
            if(!empty($decryptedData['footer_logo']) ) {
                $base64Image = $decryptedData['footer_logo'];
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
                $data->footer_logo = $newname;
                }
            }
            //favicon
            $path = public_path('uploads');
            if(!empty($decryptedData['favicon']) ) {
                $base64Image = $decryptedData['favicon'];
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
                $data->favicon = $newname;
                }
            }

            //about_image1
            $path = public_path('uploads');
            if(!empty($decryptedData['about_image1']) ) {
                $base64Image = $decryptedData['about_image1'];
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
                $data->about_image1 = $newname;
                }
            }
  
            //about_image2
            $path = public_path('uploads');
            if(!empty($decryptedData['about_image2']) ) {
                $base64Image = $decryptedData['favicon'];
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
                $data->about_image2 = $newname;
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

            $orgData = org_structure::find(dDecrypt($id));
            $org = dEncrypt($orgData);
            if($org != null){
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

                $decryptedData = json_decode(dDecrypt($request->data), true);

                $validator = Validator::make($decryptedData, [
                    'name' => 'required|string',
                    'email' => 'required|email',
                ]);
    
                if ($validator->fails()) {
                    return response()->json([
                        'errors' => $validator->errors(),
                        'message' => 'Validation failed',
                    ], 422);
                }
                
                return $decryptedData;

                $data = org_structure::find($id);
                $data->meta_title = $decryptedData['meta_title'];
                $data->meta_description = $decryptedData['meta_description'];
                $data->meta_keyword = $decryptedData['meta_keyword'];
                $data->body_script =$decryptedData['body_script'];
                $data->head_script = $decryptedData['head_script'];
                
                $data->name = $decryptedData['name'];
                $data->email = $decryptedData['email'];
                $data->phone = $decryptedData['phone'];
                $data->address = $decryptedData['address'];
                $data->header_video = $decryptedData['header_video'];
    
                $data->payFee = $decryptedData['payFee'];
                $data->admissionOpenLink = $decryptedData['admissionOpenLink'];
                $data->map = $decryptedData['map'];
                $data->footer_content = $decryptedData['footer_content'];
                
                $data->about_heading = $decryptedData['about_heading'];
                $data->about_content = $decryptedData['about_content'];
                $data->about_video = $decryptedData['about_video'];
    
                $data->facilitie_heading = $decryptedData['facilitie_heading'];
                $data->facilitie_content = $decryptedData['facilitie_content'];
    
                $data->url1 = $decryptedData['url1'];
                $data->url2 = $decryptedData['url2'];
                $data->url3 = $decryptedData['url3'];
                $data->url4 = $decryptedData['url4'];
    
       
                //header logo
                $path = public_path('uploads');
                 if(!empty($decryptedData['header_logo']) ) {
                   $base64Image = $decryptedData['header_logo'];
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
                    $data->header_logo = $newname;
                   }
                }
                //menu_logo
                $path = public_path('uploads');
                if(!empty($decryptedData['menu_logo']) ) {
                    $base64Image = $decryptedData['menu_logo'];
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
                    $data->menu_logo = $newname;
                    }
                }
                //footer_logo
                $path = public_path('uploads');
                if(!empty($decryptedData['footer_logo']) ) {
                    $base64Image = $decryptedData['footer_logo'];
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
                    $data->footer_logo = $newname;
                    }
                }
                //favicon
                $path = public_path('uploads');
                if(!empty($decryptedData['favicon']) ) {
                    $base64Image = $decryptedData['favicon'];
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
                    $data->favicon = $newname;
                    }
                }
                //about_image1
                $path = public_path('uploads');
                if(!empty($decryptedData['about_image1']) ) {
                    $base64Image = $decryptedData['about_image1'];
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
                    $data->about_image1 = $newname;
                    }
                }
                //about_image2
                $path = public_path('uploads');
                if(!empty($decryptedData['about_image2']) ) {
                    $base64Image = $decryptedData['favicon'];
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
                    $data->about_image2 = $newname;
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

            $org = org_structure::where('id',$id)->first();

            if (!empty($org)) {
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
