<?php

namespace App\Http\Requests\Backend\Games;

use App\Http\Requests\Request;

/**
 * Class ManageBlogsRequest.
 */
class ManageGamesRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return access()->allow('view-blog');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
