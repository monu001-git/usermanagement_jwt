<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;
use Illuminate\Support\Facades\Validator;

class roleController extends Controller
{
   
    
    public function index(Request $request)
    {
        try{

           $roles = Role::orderBy('id','DESC')->get();
            return response()->json([
                'status' => 200,
                'success', 'User deleted successfully',
                'data' => $user
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
        try{

            $validator = Validator::make($request->all(), [
             'name' => 'required|unique:roles,name',
             'permission' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed',
                ], 422);
            }


            $permissionsID = array_map(
                function($value) { return (int)$value; },
                $request->input('permission')
            );
        
            $role = Role::create(['name' => $request->input('name')]);
            $role->syncPermissions($permissionsID);
        
            return response()->json([
                'status' => 200,
                'success', 'User Created successfully',
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
        try{
        $role = Role::find($id);
      
            if($role != null){

                $rolePermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
                ->where("role_has_permissions.role_id",$id)
                ->get();
    
                return response()->json([
                    'status' => 200,
                    'success', 'User Show Successfully',
                    'role' => $role,
                     'rolePermissions'=>$rolePermissions

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
    
 
    public function edit($id)
    {
        try{
        $role = Role::find(dDecrypt($id));
        $permission = Permission::get();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",dDecrypt($id))
            ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
            ->all();
    
        return view('admin.common-page.roles.edit',compact('role','permission','rolePermissions'));

    } catch (\Exception $e) {
        \Log::error('An exception occurred: ' . $e->getMessage());
        return view('admin.common-page.error', ['error' => 'An error occurred: ' . $e->getMessage()]);
    } catch (\PDOException $e) {
        \Log::error('A PDOException occurred: ' . $e->getMessage());
        return view('admin.common-page.error', ['error' => 'A database error occurred: ' . $e->getMessage()]);
    } catch (\Throwable $e) {
        \Log::error('An unexpected exception occurred: ' . $e->getMessage());
        return view('admin.common-page.error', ['error' => 'An unexpected error occurred: ' . $e->getMessage()]);
    }
    }
    
  
    public function update(Request $request, $id)
    {
    try{
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'permission' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Validation failed',
            ], 422);
        }

    
           $role = Role::find($id);
           $role->name = $request->input('name');
           $role->save();

            $permissionsID = array_map(
                function($value) { return (int)$value; },
                $request->input('permission')
            );
    
        $role->syncPermissions($permissionsID);
    
        return redirect()->route('roles.index')->with('success','Role updated successfully');

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
        try{

          DB::table("roles")->where('id',$id)->delete();
        
          return response()->json([
            'status' => 200,
            'success', 'Role deleted successfully',
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
