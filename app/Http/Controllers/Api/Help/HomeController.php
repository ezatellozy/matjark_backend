<?php

namespace App\Http\Controllers\Api\Help;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Contact\ContactRequest;
use App\Http\Requests\Api\Client\UserSearchRequest;
use App\Http\Resources\Api\Help\{CarTypeResource, CategoryResource};
use App\Models\{Contact , SearchHistory, Category , User , AppImage, CarType};
use App\Notifications\Contact\ContactNotification;

class HomeController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAbout()
    {
        $about = app()->getLocale() == 'ar' ? 'meta_ar' : 'meta_en';
        $data = [
            'about' => setting($about) != false ? setting($about) : '',
        ];
        return response()->json(['data' => $data , 'status' => 'success' , 'message' => '']);
    }

    public function getTerms()
    {
        $terms = app()->getLocale() == 'ar' ? 'terms_ar' : 'terms_en';
        $data = [
            'terms' => setting($terms) != false ? setting($terms) : '',
        ];
        return response()->json(['data' => $data , 'status' => 'success' , 'message' => '']);
    }

    public function getTax()
    {
        return response()->json(['data' => ['tax' => (float) setting('tax')] , 'status' => 'success' , 'message' => '']);
    }

    public function getContact()
    {
        $data = [
            'phone' => (string)setting('phone'),
            'email' => (string)setting('email'),
            'whatsapp' => (string)setting('whatsapp'),
            'social' => [
                'facebook' => (string)setting('facebook'),
                'twitter' => (string)setting('twitter'),
                'instagram' => (string)setting('instagram'),
            ],
        ];
        return response()->json(['data' => $data , 'status' => 'success' , 'message' => '']);
    }

    public function getPolicy()
    {
        $policy = app()->getLocale() == 'ar' ? 'policy_ar' : 'policy_en';
        return response()->json(['data' => ['policy' => setting($policy) != false ? setting($policy) : ''] , 'status' => 'success' , 'message' => '']);
    }



    public function contact(ContactRequest $request)
    {
        $contact = Contact::create($request->validated());
        $admins = User::whereIn('user_type',['admin','superadmin'])->get();
        \Notification::send($admins,new ContactNotification($contact));
        return response()->json(['status' => 'success' , 'data'=> null , 'message' => 'شكرا، تم إرسال الرسالة']);
    }

    public function search(UserSearchRequest $request)
    {
        $query = $request->keyword;
        $categories = Category::parent()->latest()->where(function ($q) use($query) {
            $q->whereTranslationLike('name',"%{$query}%",'ar')->orWhereTranslationLike('name',"%{$query}%",'en')->orWhereTranslationLike('desc',"%{$query}%",'ar')->orWhereTranslationLike('desc',"%{$query}%",'en');
        })->get();

        $subcategories = Category::children()->latest()->where(function ($q) use($query) {
            $q->whereTranslationLike('name',"%{$query}%",'ar')->orWhereTranslationLike('name',"%{$query}%",'en')->orWhereTranslationLike('desc',"%{$query}%",'ar')->orWhereTranslationLike('desc',"%{$query}%",'en');
        })->get();

        $consultants = User::where('user_type','consultant')->active()->latest()->where(function ($q) use($query) {
            $q->where('fullname','LIKE',"%{$query}%")->orWhere('phone','LIKE',"%{$query}%")->orWhere('email','LIKE',"%{$query}%");
        })->get();

        $search_histories = [];
        if (auth()->guard('api')->check()) {
            $search_histories = auth()->guard('api')->user()->searchHistories()->pluck('keyword')->toArray();
            auth()->guard('api')->user()->searchHistories()->updateOrCreate(['keyword' => $keyword , 'user_id' => auth('api')->id()],['keyword' => $keyword , 'user_id' => auth('api')->id()]);
        }

        return response()->json([
            'status' => 'success',
            'message'=>'',
            'data' => [
                'search_history'=> $search_histories,
                'categories' => CategoryResource::collection($categories),
                'subcategories' => CategoryResource::collection($subcategories),
            ]
        ]);

    }

    public function deleteAppImage(Request $request , $id)
    {
        $image = AppImage::whereHasMorph('app_imageable',[Ad::class],function($q){
            $q->where('ads.client_id',auth('api')->id())->orWhere('ads.organization_id',auth('api')->id());
        })->findOrFail($id);
        $image->delete();
        if (file_exists(storage_path('app/public/images/ad/'.$image->image))){
            \File::delete(storage_path('app/public/images/ad/'.$image->image));
        }
        return response()->json(['status' => 'success','message'=> trans('dashboard.messages.success_delete'),'data' => null]);
    }

  

}
