<?php

namespace App\Http\Requests\Api\App\Reminder;

use App\Http\Requests\Api\ApiMasterRequest;

class ReminderRequest extends ApiMasterRequest
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
        return [
            'flash_sale_product_id' => 'required|exists:flash_sale_products,id',
        ];
    }

    public function getValidatorInstance()
    {
        $data = $this->all();
        $data['flash_sale_product_id'] = $this->route('flash_sale_product_id');
        $this->getInputSource()->replace($data);
        return parent::getValidatorInstance();
    }
}
