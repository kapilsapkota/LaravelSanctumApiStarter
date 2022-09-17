<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $response['category'] = Category::all();
        $response['status'] = true;

        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:150|string',
            'slug' => 'required|max:150|string',
            'file' => 'nullable|max:2048|file',
            'description' => 'nullable',
        ]);
        if($request->hasFile('file')){
            //check folder
            if (!file_exists(public_path('category')))
            {
                @mkdir(public_path('category'));
            }
            $file = $request->file;
            $fileName = time().'_'.rand(1,2222).'.'. $file->getClientOriginalExtension();
            $file->move(public_path('category'), $fileName);
            $request->merge([
                'image' => $fileName
            ]);
        }
        $response['category'] = Category::create($request->all());
        $response['message'] = " Category Created Successfully";

        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $response['category'] = Category::findOrFail($id);
        if($response['category']){
            return response()->json($response);
        }
        abort(404,'Model Not Found');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $response['category'] = Category::findOrFail($id);
        if($request->hasFile('file')){
            //check folder
            if (!file_exists(public_path('category')))
            {
                @mkdir(public_path('category'));
            }
            $file = $request->file;
            $fileName = time().'_'.rand(1,2222).'.'. $file->getClientOriginalExtension();
            $file->move(public_path('category'), $fileName);
            $request->merge([
                'image' => $fileName
            ]);
        }
        $response['category']->update($request->all());

        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        if(file_exists(public_path('category/'.$category->image))){
            @unlink(public_path('category/'.$category->image));
        }

        $category->delete();

        return response()->json([
            'message' => 'Category Deleted Successfully!'
        ],200);
    }
}
