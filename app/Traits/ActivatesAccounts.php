<?php

namespace Valda\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\RedirectsUsers;

trait ActivatesAccounts
{
    use TransformsResponses;

    /**
     * The account model.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Activates the given user's account.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function activateAccount(Request $request)
    {
        $data = $request->validate($this->rules());

        $model = $this->model->where('email', $data['email'])->first();

        if ($model && Hash::check($data['token'], $model->activation_token)) {
            $model->activate([
                'password' => Hash::make($data['password']),
                'is_enabled' => true,
            ]);

            return $this->sendActivatedResponse();
        }

        return $this->sendActivationFailedResponse();
    }

    /**
     * Check if the activation token is valid.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkActivationToken(Request $request)
    {
        $data = $request->validate([
            'token' => 'required',
            'email' => 'required|email',
        ]);

        $model = $this->model->where('email', $data['email'])->first();

        return $model && Hash::check($data['token'], $model->activation_token)
            ? $this->okResponse()
            : $this->errorResponse(422, 'Invalid Token');
    }

    /**
     * Get the account activation validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ];
    }

    /**
     * Get the response for a successful password reset.
     *
     * @param  string  $response
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendActivatedResponse()
    {
        return $this->okResponse();
    }

    /**
     * Get the response for a failed password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendActivationFailedResponse(Request $request, $response)
    {
        return $this->errorResponse(422, 'Activation Failed');
    }
}
