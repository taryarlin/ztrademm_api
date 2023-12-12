<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AboutUs;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Exception;

class AboutUsController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'description' => 'required|string',
            'image' => 'required|image:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $file= $request->file('image');
        $filename= time().$file->getClientOriginalName();
        $file-> move(public_path('storage/aboutus_image'), $filename);
        $aboutus = AboutUs::create([
            'description' => $data['description'],
            'image' => $filename,
        ]);
        return response()->json([
            'status' => 'success',
            'data' =>  $aboutus,
        ], 201);
    }

    public function index()
    {
        $aboutus = AboutUs::all()->first();
        return $aboutus;
    }

    public function show($id)
    {
        $aboutus = AboutUs::find($id);
        if($aboutus){
            return response()->json([
                'status' => 'success',
                'data' =>  $aboutus
            ], 201);
        }
        else{
            return response()->json([
                'status' => 'fail',
                'message' =>  "Not Found"
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
      try{
            // $data = $request->validate([
            //     'description' => 'required|string',
            // ]);
            $aboutus_find_to_update = AboutUs::find($id);

                if($aboutus_find_to_update){
                    if($request->hasFile('image') != null){
                        $file= $request->file('image');
                        $filename= time().$file->getClientOriginalName();
                        $file-> move(public_path('storage/aboutus_image'), $filename);
                        File::delete(public_path('storage/banner_image/'.$aboutus_find_to_update->image));
                        $aboutus_find_to_update->update([
                        'description' => $request->description ?? $aboutus_find_to_update->description,
                        'image' => $filename
                            ]);
                    }
                    else{
                        $aboutus_find_to_update->update([
                            'description' => $request->description ?? $aboutus_find_to_update->description,
                            'image' => $aboutus_find_to_update->image
                        ]);
                        return response()->json([
                            'status' => 'success',
                            'message' =>  "Successfully Updated"
                        ], 201);
                    } 
                }
        }
      catch(Exception $e){
        error_log($e);
        return response()->json([
            'message' => "Too Large"
        ], 500);
      }

    }

    public function delete($id){
        $success = AboutUs::find($id);
        if($success){
            $success->delete();
            return response()->json([
                'status' => 'success',
                'message' =>  "Successfully Deleted"
            ], 201);
        }
        else{
            return response()->json([
                'status' => 'fail',
                'message' =>  "Not Found"
            ], 404);
        }
    }


}
