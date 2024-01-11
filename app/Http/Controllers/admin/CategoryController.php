<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use File;
use Illuminate\Http\Client\ResponseSequence;
use Image;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::latest();
        if(!empty($keyword = $request->get('keyword')))
        {
            $categories = Category::where('name', 'LIKE', '%'.$keyword.'%')->latest();
        }
        $categories = $categories->paginate(10);
        return view('admin.category.list', compact('categories'));
    }

    public function create()
    {
        return view('admin.category.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories'
        ]);

        if($validator->passes()){
            $category = new Category();
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->showhome = $request->showhome;
            $category->save();

            if(!empty($request->image_id))
            {
                $tempImage = TempImage::find($request->image_id);
                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);
                $newImageName = $category->id.'.'.$ext;
                $sPath = public_path().'/temp/'.$tempImage->name;
                $dPath = public_path().'/uploads/category/'.$newImageName;
                File::copy($sPath,$dPath);
                $category->image = $newImageName;
                $category -> save();
                // $dPathThumb = public_path().'/uploads/category/thumb/'.$newImageName;
                // $img = Image::make($sPath);
                // $img->resize(450, 600);
                // $img->save($dPathThumb);
            }

            $request->session()->flash('success', 'Category added successfully');


            return response()->json([
                'status' => true,
                'success' => 'Category added successfully'
            ]);

        }else{
            return response()->json([
                'status' => 'false',
                'errors' => $validator->errors()
            ]);
        }
    }

    public function edit($categoryID, Request $request)
    {
        $category = Category::find($categoryID);
        if(empty($category)){
            return redirect()->route('categories.index');
        }
        return view('admin.category.edit',compact('category'));
    }

    public function update($categoryID, Request $request)
    {
        $category = Category::find($categoryID);
        if(empty($category)){
           $request->session()->flash('error', 'Category doesnot found');
           return response()->json([
                'status' => false,
                'notfound' => true,
                'message' => 'Category doesnot found'
           ]);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,'.$categoryID.'id',
        ]);

        if($validator->passes()){            
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->showhome = $request->showhome;
            $category->save();

            if(!empty($request->image_id))
            {
                $oldImage = $category->image;
                File::delete(public_path().'/uploads/category/'.$oldImage);
                $tempImage = TempImage::find($request->image_id);
                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);
                $newImageName = $category->id.'.'.$ext;
                $sPath = public_path().'/temp/'.$tempImage->name;
                $dPath = public_path().'/uploads/category/'.$newImageName;
                File::copy($sPath,$dPath);
                $category->image = $newImageName;
                $category -> save();
                // $dPathThumb = public_path().'/uploads/category/thumb/'.$newImageName;
                // $img = Image::make($sPath);
                // $img->resize(450, 600);
                // $img->save($dPathThumb);                
            }
            $request->session()->flash('success', 'Category updated successfully');
            return response()->json([
                'status' => true,
                'success' => 'Category updated successfully'
            ]);

        }else{
            return response()->json([
                'status' => 'false',
                'errors' => $validator->errors()
            ]);
        }
    }

    public function destroy($categoryID, Request $request)
    {
        $category = Category::find($categoryID);
        if(empty($category))
        {
            $request->session()->flash('error', 'Category doesnot exist');
            return response()->json([
                'status' => false,
                'message' => 'Category doesnot exist'
            ]);
        }
        File::delete(public_path().'/uploads/category/'.$category->image);
        $category -> delete();
        $request->session()->flash('success', 'Category deleted successfully');
        return response()->json([
            'status' => true,
            'message' => 'Category deleted successfully'
        ]);
    }
}
