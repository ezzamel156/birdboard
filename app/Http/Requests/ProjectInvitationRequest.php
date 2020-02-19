<?php

namespace App\Http\Requests;

use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class ProjectInvitationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('update', $this->route('project'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => ['required', 'exists:users,email']
        ];
        /**
         * Another way to create validation
         * More explicit
         * Does not require to override message method
         */

        // return [
        //      'email' => [
        //          'required',
        //          function($attributes, $value, $fail) {
        //              if(! User::whereEmail($value)->exists()) {
        //                  $fail('The user you are inviting must have a Birdboard account.');
        //              }
        //          }
        //      ]
        // ];
    }

    public function messages()
    {
        return [
            'email.exists' => 'The user you are inviting must have a Birdboard account.'
        ];
    }
}
