<?php

# (C) 2016 Matthias Kuhs, Ireland

namespace App\Http\Requests;

use App\Http\Requests\Request;

use Illuminate\Support\Facades\Auth;


class StoreSongRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // validation rules
            'title' => 'required|min:5',
            'lyrics' => 'required_unless:title_2,"video","slides","training"|min:15',
        ];
    }


}
