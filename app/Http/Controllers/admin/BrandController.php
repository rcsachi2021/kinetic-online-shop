<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $brands = Brand::orderBY('id','DESC');
        if($keyword = $request->get('keyword'))
        {
            $brands = $brands->where('name', 'LIKE', '%'.$keyword.'%');
        }
        $brands = $brands->paginate(10);
        return view('admin.brand.list', compact('brands'));
    }
    public function create()
    {
        return view('admin.brand.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:brands',
            'status' => 'required'
        ]);

        if($validator->passes()){

            $brand = new Brand();
            $brand->name = $request->name;
            $brand->slug = $request->slug;
            $brand->status = $request->status;
            $brand->save();

            $request->session()->flash('success', 'Brand added successfully');
            return response()->json([
                'status' => true,
                'message' => 'Brand added successfully'
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
        $brand = Brand::find($id);
        if(empty($brand)){
            $request->session()->flash('error', 'Record Not Found');
            return redirect()->route('brands.index');
        }
        return view('admin.brand.edit', compact('brand'));
    }

    public function update($id, Request $request)
    {
        $brand = Brand::find($id);
        if(empty($brand)){
            $request->session()->flash('error', 'Record Not Found');
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Record Not Found'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,'.$brand->id,
            'status' => 'required'
        ]);

        if($validator->passes()){
            $brand->name = $request->name;
            $brand->slug = $request->slug;
            $brand->status = $request->status;
            $brand->save();
            $request->session()->flash('success', 'Brand updated successfully');
            return response()->json([
                'status' => true,
                'message' => 'Brand updated successfully'
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function delete($id, Request $request)
    {
        $brand = Brand::find($id);
        if(empty($brand))
        {
            $request->session()->flash('error', 'NO record found!');
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Record not found!'
            ]);
        }
        $brand->delete();
        $request->session()->flash('success', 'Brand deleted successfully!');
        return response()->json([
            'status' => true,
            'message' => 'Brand deleted successfully!'
        ]);
    }
}
