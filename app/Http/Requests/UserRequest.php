<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule as ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRequest extends FormRequest
{
    /* Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /* Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
          //  $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required','string','email', 'max:255',
                            ValidationRule::unique(table: 'users'),
                            'regex:/(.*)@(gmail)\.com/i',
                            ],
                'password' => ['required', 'string', 'min:8'
                            ,'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x]).*$/',
                            'confirmed'],
                'prof_img' => ['image'],
                'phone' => ['required', 'min:10', 'max:10',ValidationRule::unique(table: 'users')]
        ];
    }
        /*** Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors()->all(), 400));
    }
}
