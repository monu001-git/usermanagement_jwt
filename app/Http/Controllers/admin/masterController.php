<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\menu;
use App\Models\page;

class masterController extends Controller
{
  

    public function parentMaster(){
    
        try{
    
            $parentData = menu::all();
            $parent = dEncrypt($parentData);
            return response()->json([
                'status' => 200,
                'success', 'menu parent list fetch Successfully',
                'data'=>$parent
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
    public function contentMaster(){
    
        try{
    
            $pageData = page::all();
            $page = dEncrypt($pageData);
            return response()->json([
                'status' => 200,
                'success', 'page list fetch Successfully',
                'data'=>$page
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
