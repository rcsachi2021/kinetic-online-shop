<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $pages = Page::latest();
        if($request->get('keyword')!=''){
            $pages = $pages->where('name', 'LIKE', '%'.$request->get("keyword").'%');
        }
        $pages = $pages->paginate(10);
        return view('admin.pages.list', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:pages'
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $page = new Page();
        $page->name = $request->name;
        $page->slug = $request->slug;
        $page->content = $request->content;
        $page->save();
        session()->flash('success', 'Page created successfully');
        return response()->json([
            'status' => true,
            'message' => 'Page created successfully'
        ]);
    }

    public function edit($id)
    {
        $page = Page::find($id);
        if($page == null){
            return redirect()->route('pages.index')->with('Page not found');
        }
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, $id)
    {
        $page = Page::find($id);
        if($page == null)
        {
            session()->flash('error', 'Page not found!');
            return response()->json([
                    'status' => true,
                    'message' => 'Page not found!'
            ]);
        }

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:pages,slug,'.$page->id.',id'
        ]);
        if($validator->passes()){
            $page -> name = $request->name;
            $page -> slug = $request->slug;
            $page->content = $request->content;
            $page->save();
            $message = "Page updated successfully";
            session()->flash('success',$message);
            return response()->json([
                'status' => true,
                'success' => $message
            ]);
        }
        return response()->json([
            'status' => false,
            'errors' => $validator->errors()
        ]);
    }

    public function destroy($id)
    {
        $page = Page::find($id);
        if($page == null)
        {
            $message = 'Page not found!';
            session()->flash('error', $message);
            return response()->json([
                'status' => true,
                'message' => $message
            ]);
        }

        $page->delete();
        $message = 'Page deleted successfully';
        session()->flash('success', $message);
        return response()->json([
            'status' => true,
            'message' => $message
        ]);
        
    }
}
