<?php

namespace Valda\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Valda\Notifications\ActivateAccount;

trait Activatable
{
    /**
     * Boot the trait.
     *
     * @return void
     */
    public static function bootActivatable()
    {
        $token = strtoupper(str_random(32));

        static::creating(function ($model) use ($token) {
            $model->activation_token = Hash::make($token);
        });

        static::created(function ($model) use ($token) {
            $model->notify(new ActivateAccount($model->email, $token));
        });
    }

    /**
     * Activates the account.
     *
     * @param  array  $credentials
     * @param  boolean  $fireEvent
     * @return void
     */
    public function activate($credentials = [], $fireEvent = true)
    {
        $activate = function ($model) use ($credentials) {
            $model->update([
                'activation_token' => null,
                'activated_at' => Carbon::now()->toDateTimeString(),
            ] + $credentials);
        };

        if (in_array(SilencesModelEvents::class, class_uses($this))) {
            $this->silentWhen(!$fireEvent, $activate);
        } else {
            $activate($this);
        }
    }
}