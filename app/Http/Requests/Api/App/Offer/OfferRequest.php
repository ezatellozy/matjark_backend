<?php

namespace App\Http\Requests\Api\App\Offer;

use App\Http\Requests\Api\ApiMasterRequest;
use App\Models\Offer;

class OfferRequest extends ApiMasterRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $offer = Offer::findOrFail($this->route('id'));
// dd($offer);
        $data =  [
            // 'type'  => 'nullable|required_if:' . $offer->type  . ',buy_x_get_y|in:buy_x,get_y',
            'type'  => 'nullable|required_if:buy_x_get_y,==,' . $offer->type  . ',buy_x_get_y|in:buy_x,get_y',

        ];
        // dd($data);
        return $data;
    }
}
