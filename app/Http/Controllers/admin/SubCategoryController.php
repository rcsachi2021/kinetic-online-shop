<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubCategoryController extends Controller
{

    public function index(Request $request)
    {
        $subCategories = SubCategory::select('sub_categories.*', 'categories.name as categoryName')->latest('sub_categories.id')->leftjoin('categories', 'categories.id', 'sub_categories.category_id');
        if(!empty($keyword = $request->get('keyword')))
        {
            $subCategories = $subCategories->where('sub_categories.name', 'LIKE', '%'.$keyword.'%');
            $subCategories = $subCategories->orWhere('categories.name', 'LIKE', '%'.$keyword.'%');
        }
        $subCategories = $subCategories->paginate(10);
        //dd($subCategories);
        return view('admin.sub-category.list', compact('subCategories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $data['categories'] = $categories;
        return view('admin.sub-category.create',$data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:sub_categories',
            'category_id' => 'required',
            'status' => 'required'
        ]);

        if($validator->passes()){
            $subCategory = new SubCategory();
            $subCategory->name = $request->name;
            $subCategory->slug = $request->slug;
            $subCategory->category_id = $request->category_id;
            $subCategory->status = $request->status;
            $subCategory->showhome = $request->showhome;
            $subCategory->save();

            $request->session()->flash('success', 'Subcategory added successfully');

            return response()->json([
                'status' => true,
                'message' => 'Subcategory added successfully'
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
        
        
    }
    public function edit($id, Request $request)
    {
        $subCategory = SubCategory::find($id);
        if(empty($subCategory))
        {
            $request->session()->flash('error', 'Record not found');
            return redirect()->route('sub-categories.index');
        }
        $categories = Category::orderBy('name','ASC')->get();
        $data['categories'] = $categories;
        $data['subCategory'] = $subCategory;
        return view('admin.sub-category.edit', $data);
    }

    public function update($id, Request $request)
    {
        $subCategory = SubCategory::find($id);
        if(empty($subCategory)){
            $request->session()->flash('error', 'No record found');
            response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'No record found'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' =>'required|unique:sub_categories,slug,'.$id,
            'category_id' => 'required',
            'status' => 'required'
        ]);

        if($validator->passes()){
            $subCategory->name = $request->name;
            $subCategory->slug = $request->slug;
            $subCategory->category_id = $request->category_id;
            $subCategory->status = $request->status;
            $subCategory->showhome = $request->showhome;
            $subCategory -> save();
            $request->session()->flash('success', 'Subcategory updated successfully');
            return response()->json([
                'status' => true,
                'message' => 'Subcategory updated successfully'
            ]);
        }
        else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function destroy($id, Request $request)
    {
        $subCategory = SubCategory::find($id);
        if(empty( $subCategory ))
        {
            $request->session()->flash('error', 'Record not found');
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Record not found'
            ]);
        }
        else{
            $subCategory -> delete();
            $request->session()->flash('success', 'Record deleted successfully');
            return response()->json([
                'status' => true,
                'message' => 'Record deleted successfully'
            ]);
        }
    }
}
