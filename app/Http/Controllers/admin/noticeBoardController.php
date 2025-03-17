<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\notice_board;
use Illuminate\Support\Facades\Validator;
class noticeBoardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $noticeBoardData = notice_board::orderBy('id','desc')->get();
            $noticeBoard = dEncrypt($noticeBoardData);

            return response()->json([
                'status' => 200,
                'message' => 'Data retrieved successfully!',
                'data' => $noticeBoard
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
                // 'title' => 'required|unique:notice_boards,title',
                // 'order' => 'required',
                // 'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed',
                ], 422);
            }
            
            $decryptedData = json_decode(dDecrypt($request->data), true);

    
            $data = new notice_board;
            $data->title = ucwords($decryptedData['title']);
            $data->date  = $decryptedData['date'];
            $data->publiser  = 'admin' ;
            // $data->link_type  = $decryptedData['link_type'];
            $data->order  =  $decryptedData['order'];
            $data->status  = $decryptedData['status'];

            $path = public_path('uploads');
            if(!empty($decryptedData['pdf']) ) {
               $base64Image = $decryptedData['pdf'];
               if($base64Image != null){
                $imageData = substr($base64Image, strpos($base64Image, ',') + 1);
                $image = base64_decode($imageData);
                $finfo = finfo_open();
                $mimeType = finfo_buffer($finfo, $image, FILEINFO_MIME_TYPE);
                finfo_close($finfo);
                $extension = '';
                if ($mimeType == 'application/pdf') {
                    $extension = 'pdf';
                } else {
                    return response()->json(['error' => 'Unsupported image format'], 400);
                }
                $newname = time() . rand(10, 99) . '.' . $extension;
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                file_put_contents($path . '/' . $newname, $image);
                $data->pdf = $newname;
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
    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try{
            $noticeBoard = notice_board::find($id);
           
             if($noticeBoard != null){

                return response()->json([
                    'status' => 200,
                    'success' => 'notice board Show Successfully',
                    'data' => $noticeBoard
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


    public function update(Request $request, string $id)
    {
        try {
        $validator = Validator::make($request->all(), [
            // 'title' => 'required|unique:notice_boards,title',
            // 'order' => 'required',
            // 'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Validation failed',
            ], 422);
        }

       
        $decryptedData = json_decode(dDecrypt($request->updatedData), true);
 
        $data = notice_board:: find($id);
        $data->title = ucwords($decryptedData['title']);
        $data->date  = $decryptedData['date'];
        $data->publiser  = 'admin' ;
        $data->order  =  $decryptedData['order'];
        $data->status  = $decryptedData['status'];

        $path = public_path('uploads');
        if(!empty($decryptedData['pdf']) ) {
            $base64Image = $decryptedData['pdf'];
           if($base64Image != null){

            $imageData = substr($base64Image, strpos($base64Image, ',') + 1);
            $image = base64_decode($imageData);
    
            $finfo = finfo_open();
            $mimeType = finfo_buffer($finfo, $image, FILEINFO_MIME_TYPE);
            finfo_close($finfo);
    
            $extension = '';
            if ($mimeType == 'application/pdf') {
                $extension = 'pdf';
            } else {
                return response()->json(['error' => 'Unsupported image format'], 400);
            }
            $newname = time() . rand(10, 99) . '.' . $extension;
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            file_put_contents($path . '/' . $newname, $image);
            $data->pdf = $newname;
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {

            $noticeBoard = notice_board::where('id', $id)->first();

            if (!empty($noticeBoard)) {
                notice_board::find($id)->delete();
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