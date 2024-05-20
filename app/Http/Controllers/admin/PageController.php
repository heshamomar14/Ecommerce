<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $pages=Page::latest();
        //search in pages
        if (!empty($request->get('keyword'))){
            $pages=$pages->where('name','like','%'.$request->get('keyword').'%');
        }
        $pages=$pages->paginate(10);
        return view('admin.pages.list',[
            'pages'=>$pages
        ]);

    }
public function create(Request $request)
{
    return view('admin.pages.create');
}
public function store(Request $request)
{
    $validator = Validator::make($request->all()
        , ['name' => 'required', 'slug' => 'required|unique:pages',]);

    if ($validator->passes()){
        $page= new Page;
        $page->name=$request->name;
        $page->slug=$request->slug;
        $page->content=$request->content;
        $page->save();
        session()->flash('success','page added successfully');
        return response()->json([
            'status'=>true,
            'message'=>'page added successfully'
        ]);
    }
    else{
        return response()->json([
            'status'=>false,
            'errors'=>$validator->errors()
        ]);
    }
}
public function edit(Request $request,$id)
{
    $pages=Page::find($id);
    if (!empty($pages)){
        return view('admin.pages.edit',[
            'pages'=>$pages
        ]);

    }
}
public function update(Request $request,$id)
{
    $page=Page::find($id);
    if (empty($page)){
        return response()->json([
            'status' => false,
            'notfound' => true,
            'message' => 'page not found'
        ]);

    }
    $validator = Validator::make($request->all()
        , ['name' => 'required',
            'slug' => 'required|unique:pages,slug,' . $page->id . ',id',]);

    if ($validator->passes()){
        $page->name=$request->name;
        $page->slug=$request->slug;
        $page->content=$request->content;
        $page->save();

        session()->flash('success','page updated successfully');
        return response()->json([
            'status'=>true,
            'message'=>' page  updated successfully'
        ]);
    }
    else{
        return response()->json([
            'status'=>false,
            'errors'=>$validator->errors()
        ]);
    }
}
    public function destroy($id, Request $request)
    {

        $page=Page::find($id);

        if (empty($page)) {
            $request->session()->flash('error', 'page not found ');
            return response()->json(['status' => true, 'message' => 'page not found']);
        }
        $page->delete();
        session()->flash('success', 'page deleted successfully');
        return response()->json(['status' => true, 'message' => 'page deleted successfully']);

    }
}
