<?php

namespace App\Http\Controllers\Api\Dashboard\About;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dashboard\About\AboutRequest;
use App\Http\Resources\Api\Dashboard\About\AboutResource;
use App\Models\About;
use App\Models\AboutMetaData;
use Illuminate\Http\Request;
use Exception;

class AboutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $abouts = About::latest()->paginate();

        return AboutResource::collection($abouts)->additional(['status' => 'success', 'message' => '']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AboutRequest $request)
    {
        try {
              $about_data = $request->safe()->only(['ar.title', 'ar.slug', 'en.title', 'en.slug', 'ar.desc', 'en.desc', 'image','ordering']);
            // $about = About::create($request->safe()->except('image'));
            $about = About::create($about_data);

            $meta_dataArr = [
                'meta_canonical_tag' => $request->meta_canonical_tag,
                'en' => [
                    'meta_tag' => ($request->en)['meta_tag'],
                    'meta_title' => ($request->en)['meta_title'],
                    'meta_description' => ($request->en)['meta_description'],
                ],
                'ar' => [
                    'meta_tag' => ($request->ar)['meta_tag'],
                    'meta_title' => ($request->ar)['meta_title'],
                    'meta_description' => ($request->ar)['meta_description'],
                ]
            ];

            $meta_data = $about->metas()->create($meta_dataArr);
            // $meta_data = $about->metas()->create($request->only(['','en.meta_tag','ar.meta_tag','en.meta_title','ar.meta_title','en.meta_description','ar.meta_description']));
            //$meta_data = AboutMetaData::create($request->safe()->except(['ar.title', 'ar.slug', 'en.title', 'en.slug', 'ar.desc', 'en.desc']));
            return AboutResource::make($about)->additional(['status' => 'success', 'message' => trans('dashboard.create.success')]);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('dashboard.create.fail')], 422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $about = About::findOrFail($id);

        return AboutResource::make($about)->additional(['status' => 'success', 'message' => '']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AboutRequest $request, $id)
    {
        try {
            $about = About::findOrFail($id);
             $about_data = $request->safe()->only(['ar.title', 'ar.slug', 'en.title', 'en.slug', 'ar.desc', 'en.desc','ordering']);
             $about->update($about_data);
            // $about->update($request->safe()->except('image'));
            
            $meta_data = [
                'meta_canonical_tag' => $request->meta_canonical_tag,
                'en' => [
                    'meta_tag' => ($request->en)['meta_tag'],
                    'meta_title' => ($request->en)['meta_title'],
                    'meta_description' => ($request->en)['meta_description'],
                ],
                'ar' => [
                    'meta_tag' => ($request->ar)['meta_tag'],
                    'meta_title' => ($request->ar)['meta_title'],
                    'meta_description' => ($request->ar)['meta_description'],
                ]
            ];

            // $meta_data = ($request->only(['meta_canonical_tag','en.meta_tag','ar.meta_tag','en.meta_title','ar.meta_title','en.meta_description','ar.meta_description']));
            if(!empty($meta_data)){
                if($about->metas) {
                    $about->metas->update($meta_data);
                } else {
                    $about->metas()->create($meta_data);
                }
            } else {
            }

            return AboutResource::make($about)->additional(['status' => 'success', 'message' => trans('dashboard.update.success')]);
        } catch (Exception $e) {
            info($e);
            return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('dashboard.update.fail')], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $about = About::findOrFail($id);

        if ($about->delete()) {
            return response()->json(['status' => 'success', 'data' => null, 'messages' => trans('dashboard.delete.success')]);
        }

        return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('dashboard.delete.fail')], 422);
    }
}
