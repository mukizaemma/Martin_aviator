<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Slide;

class SlidesController extends Controller
{
    public function index()
    {

        $slides = Slide::latest()->get();
        return view('admin.slides', ['slides'=>$slides]);
    }


    public function store(Request $request)
    {
        $data = new Slide();
        $data ->heading = $request->heading;
        // $data ->category = $request->category;

        // Uploading image
        if ($request->hasFile('image')) {
            $fileName = $this->storeOptimizedImage($request->file('image'), 'public/images/slides', 'slide');
            if ($fileName) {
                $data->image = $fileName;
            }
        }

        $stored = $data->save();

        if($stored){
            return redirect('slides')->with('success', 'New Image has been added successfuly');
        }

        return redirect()->back()->with('error','Failed to add new Image');
    }

    public function edit($id)
    {
        $data = Slide::find($id);
        return view('admin.slideUpdate', ['data'=>$data]);
    }

    public function update(Request $request, $id)
    {
        $data = Slide::find($id);
        $data->heading = $request->input('heading');
        // $data->category = $request->input('category');

        if(!$data){
            return back()->with('Error','Image Not Found');
        }

        if ($request->hasFile('image') && request('image') != '') {
            $fileName = $this->storeOptimizedImage($request->file('image'), 'public/images/slides', 'slide');
            if ($fileName) {
                if ($data->image) {
                    Storage::delete('public/images/slides/'.$data->image);
                }
                $data->image = $fileName;
            }
        }

        $data->update();

        return redirect('slides')->with('success','Image has been updated');
    }

    public function destroy($id)
    {
        $image = Slide::findOrFail($id);
        // delete the image file
        if ($image->image) {
            Storage::delete('public/images/slides/'.$image->image);
        }
        $image->delete();
        return redirect()->back()->with('warning', 'Item has been deleted');
    }
}
