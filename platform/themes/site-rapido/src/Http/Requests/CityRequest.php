<?php

namespace Theme\FlexHome\Http\Requests;

use Srapid\Base\Enums\BaseStatusEnum;
use Srapid\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class CityRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'       => 'required',
            'state_id'   => 'required',
            'country_id' => 'required',
            'slug'       => 'required|unique:cities,slug,' . $this->route('city'),
            'status'     => Rule::in(BaseStatusEnum::values()),
        ];
    }
}
