<?php

namespace App\Rules\Api\Dashboard\Offer;

use App\Models\Category;
use App\Models\ProductDetails;
use Illuminate\Contracts\Validation\Rule;

class ApplyOnRule implements Rule
{
    protected $apply_on; // special_products - special_categories
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($apply_on)
    {
        $this->apply_on = $apply_on;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // $ids = json_decode($value, true);
        // dd($ids);
        if ($this->apply_on == 'special_products')
        {
            foreach ($value as $id)
            {
                $product = ProductDetails::find($id);

                if (! $product)
                {
                    return false;
                }
            }
        }
        elseif ($this->apply_on == 'special_categories')
        {
            foreach ($value as $id)
            {
                $category = Category::where('id', $id)->whereDoesntHave('children')->first();

                if (! $category)
                {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->apply_on == 'special_products' ? 'in :attribute the product details id not exist' : 'in :attribute the category id not exist or not the last category';
    }
}
