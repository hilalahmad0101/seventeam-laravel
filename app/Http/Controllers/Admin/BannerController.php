<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::latest()->paginate(10);
        // return view with data
        return view('admin.banner.list',compact('banners'));
    }

    public function create()
    {
        // return view for creating new banner
        return view('admin.banner.create');
    }

    public function store(Request $request)
    {
        // validate the request
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // upload the image
        $imageName = time() . '.' . $request->image->getClientOriginalExtension();
        $request->image->move(public_path('images/banners'), $imageName);

        // create new banner
        $banner = new Banner();
        $banner->title = $request->title;
        $banner->description = $request->description;
        $banner->image = 'images/banners/' . $imageName;
        $banner->save();

        // return to list with success message
        return redirect()->route('admin.banner.list')->with('success', 'Banner Created Successfully');
    }

    public function edit($id)
    {
        // find the banner by id
        $banner = Banner::findOrFail($id);
        // return the edit view with the banner data
        return view('admin.banner.update', compact('banner'));
    }

    public function update(Request $request, $id)
    {
        // validate the request
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // find the banner by id
        $banner = Banner::findOrFail($id);

        // upload the new image if provided
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->getClientOriginalExtension();
            $request->image->move(public_path('images/banners'), $imageName);
            // want to delete image otherwise use previous image
            if (File::exists(public_path($banner->image))) {
                File::delete(public_path($banner->image));
            }
            // update the image path in the database
            $banner->image = 'images/banners/' . $imageName;
        }

        // update the banner data
        $banner->title = $request->title;
        $banner->description = $request->description;
        $banner->save();

        // return to list with success message
        return redirect()->route('admin.banner.list')->with('success', 'Banner Updated Successfully');

    }

    public function destroy($id)
    {
        // find the banner by id
        $banner = Banner::findOrFail($id);

        if (File::exists($banner->image)) {
            File::delete(public_path($banner->image));
        }
        // delete the banner
        $banner->delete();
        // return to list with success message
        return redirect()->route('admin.banner.list')->with('success', 'Banner Deleted Successfully');

    }
}
