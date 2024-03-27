<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Http\Requests\Admin\BannerRequest;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
            $banners = Banner::orderby('id','desc')->paginate(getPaginate());
            $pageTitle = 'All Banners';
            $emptyMessage = 'No banner found';
            return view('admin.banners.index',compact('banners','emptyMessage','pageTitle'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {     $pageTitle = 'Create Banner';
          return view('admin.banners.create',compact('pageTitle'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\BannerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BannerRequest $request)
    {
        $all_request = $request->validated();
        unset($all_request['image']);
        $banner = Banner::create($all_request);
        if ($request->hasFile('image')) {
            try {
                $banner->image = uploadImage($request->image, imagePath()['banner']['path']);
                $banner->save();
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Image could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }
        $notify[] = ['success', 'Banner save successfully.'];
        return back()->withNotify($notify);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Banner
     * @return \Illuminate\Http\Response
     */
    public function show(Banner $banner)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Banner
     * @return \Illuminate\Http\Response
     */
    public function edit(Banner $banner){
       $pageTitle = 'Create Banner';
        return view('admin.banners.edit',compact('banner','pageTitle'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Banner
     * @return \Illuminate\Http\Response
     */
    public function update(BannerRequest $request, $id)
    {
        $banner = Banner::find($id);
        $all_request = $request->validated();
        unset($all_request['image']);
        $banner->update($all_request);
        
        if ($request->hasFile('image')) {
            try {
                $old = $banner->image ?: null;
                $banner->image = uploadImage($request->image, imagePath()['banner']['path'], imagePath()['banner']['size'], $old);
                $banner->save();
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Image could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }
        $notify[] = ['success', 'Banner update successfully.'];
        return back()->withNotify($notify);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request){
        $request->validate(['id' => 'required|integer']);
        Banner::find($request->id)->delete();
        $notify[] = ['success', 'Banner deleted successfully.'];
        return back()->withNotify($notify);
    }





}
