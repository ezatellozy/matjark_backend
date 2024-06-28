<?php

namespace App\Http\Controllers\Api\Dashboard\CommonQuestion;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Requests\Api\Dashboard\CommonQuestion\CommonQuestionRequest;
use App\Models\CommonQuestion;
use App\Http\Resources\Api\Dashboard\CommonQuestion\CommonQuestionResource;

class CommonQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $commonQuestions = CommonQuestion::when($request->keyword, function ($query) use($request) {
            $query->whereTranslationLike('question', '%'.$request->keyword.'%')
            ->orWhereTranslationLike('answer', '%'.$request->keyword.'%');
        })
        ->latest()->paginate();

        return CommonQuestionResource::collection($commonQuestions)->additional(['status' => 'success', 'message' => null]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CommonQuestionRequest $request)
    {
        $commonQuestion = CommonQuestion::create($request->validated());
        return CommonQuestionResource::make($commonQuestion)->additional(['status' => 'success', 'message' => trans('dashboard.create.commonQuestion')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(CommonQuestion $commonQuestion)
    {
        return (new CommonQuestionResource($commonQuestion))->additional(['status' => 'success', 'message' => '']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CommonQuestionRequest $request, CommonQuestion $commonQuestion)
    {
        $commonQuestion->update($request->validated());
        return CommonQuestionResource::make($commonQuestion)->additional(['status' => 'success', 'message' => trans('dashboard.update.commonQuestion')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(CommonQuestion $commonQuestion)
    {
        if ($commonQuestion->delete()) {
            return response()->json(['status' => 'success', 'data' => null, 'messages' => trans('dashboard.delete.commonQuestion')]);
        }

        return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('dashboard.delete.fail')], 422);
    }

}