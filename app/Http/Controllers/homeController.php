<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;
use App\Models\menu;
use DB;

class homeController extends Controller
{
    public function getMenuTree($menus, $parentId = 0)
    {
        try{
            $branch = array();
            foreach ($menus as $menu) {
                if ($menu->parent_id == $parentId) {
                    $children = $this->getMenuTree($menus, $menu->id);
                    if ($children) {
                        $menu->children = $children;
                    }
                    $branch[] = $menu;
                }
            }
            return $branch;

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


    public function headerMenuSection()
    {
        try{
            $menus = DB::table('menus')->where('menu_place','header')->where('status', 1)->whereNull('deleted_at')->orderBy('order','asc')->get(); 
            $menuTree = $this->getMenuTree($menus, 0);

            return response()->json([
                'status' => 200,
                'message' => 'Data Get Successfully!!!!!!',
                'data' => $menuTree
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

    public function orgDataSection(){
        
        try{

            $orgData = DB::table('org_structures')->orderBy('created_at', 'desc') ->first();
            
            return response()->json([
                'status' => 200,
                'message' => 'Data retrieved successfully!',
                'data' => $orgData,
                
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

    public function noticeBoardSection(){
         
        try{

            $noticeBoardData = DB::table('notice_boards')
            ->where('status', 1)
            ->orderBy('order', 'desc')
            ->get();
        
            $noticeBoard = dEncrypt($noticeBoardData);
        
            return response()->json([
                'status' => 200,
                'message' => 'Data retrieved successfully!',
                'data'=>$noticeBoard
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

    public function footerMenuSection(){
    try{

        $menus = DB::table('menus')->where('menu_place','link')->where('status', 1)->whereNull('deleted_at')->orderBy('order', 'desc')->get(); 
        $menuTree = $this->getMenuTree($menus, 0);

        return response()->json([
            'status' => 200,
            'message' => 'Data Get Successfully!!!!!!',
            'data' => $menuTree
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


    public function eventGallerySection(){
         try{

            $eventData = DB::table('events')->whereNull('deleted_at')->orderBy('order', 'desc')->where('status', 1)->first();

            if ($eventData != null) {
                $eventImage = DB::table('event_images')
                    ->where('event_id', $eventData->id)
                    ->whereNull('deleted_at')
                    ->get();
            
                $eventGallery = [
                    'eventData' => $eventData,
                    'eventImage' => $eventImage,
                ];
            } else {
                // Properly assign null and empty array
                $eventData = null;
                $eventImage = [];
            
                $eventGallery = [
                    'eventData' => $eventData,
                    'eventImage' => $eventImage,
                ];
            }
            
            return response()->json([
                'status' => 200,
                'message' => 'Data Get Successfully!!!!!!',
                'data' => $eventGallery 
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


    public function testimonialSection(){
         
        try{

            $testimonialData = DB::table('testimonials')
            ->where('status', 1)
            ->orderBy('order', 'desc')
            ->whereNull('deleted_at')
            ->get();
        
            $testimonial = dEncrypt($testimonialData);
        
            return response()->json([
                'status' => 200,
                'message' => 'Data retrieved successfully!',
                'data'=>$testimonial
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


    
    public function studentSection(){
         
        try{

            $studentData = DB::table('students')
            ->where('status', 1)
            ->orderBy('order', 'desc')
            ->whereNull('deleted_at')
            ->get();
        
            $student = dEncrypt($studentData);
        
            return response()->json([
                'status' => 200,
                'message' => 'Data retrieved successfully!',
                'data'=>$student
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



    public function achievementSection(){
        try{

            $achievementData = DB::table('achievements')
            ->whereNull('deleted_at')
            ->where('status', 1)
            ->orderBy('order', 'desc')
            ->get(); // Fetch multiple records
        
        if ($achievementData->isNotEmpty()) {
            // Iterate over each achievement and attach its images
            foreach ($achievementData as $achievement) {
                $achievement->achievementImage = DB::table('achievement_images')
                    ->where('achievement_id', $achievement->id)
                    ->whereNull('deleted_at')
                    ->first();
            }
        } 
        
        return response()->json([
            'status' => 200,
            'message' => 'Data retrieved successfully!',
            'data' => $achievementData
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

   public function noticeBoardPdf(Request $request){
    try{

        $decryptedData = json_decode(dDecrypt($request->data), true);


            $noticeBoard = DB::table('notice_boards')
                ->where('id', $decryptedData)
                ->orderBy('order', 'desc')
                ->whereNull('deleted_at')
                ->first();

         //   $noticeBoardData = dEncrypt($noticeBoard);
        
            return response()->json([
                'status' => 200,
                'message' => 'Data retrieved successfully!',
                'data'=>$noticeBoard
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