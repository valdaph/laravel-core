<?php

namespace Valda\Traits;

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Valda\Notifications\ActivateAccount;

trait Activatable
{
    /**
     * The token column.
     *
     * @var string
     */
    public $activationToken = 'activation_token';

    /**
     * The timestamp column.
     *
     * @var string
     */
    public $activationTimestamp = 'activated_at';

    /**
     * Boot the trait.
     *
     * @return void
     */
    public static function bootActivatable()
    {
        $token = strtoupper(str_random(32));

        static::creating(function ($model) use ($token) {
            $model->{$this->activationToken} = Hash::make($token);
        });

        static::created(function ($model) use ($token) {
            $model->sendAccountActivationNotification($token);
        });
    }

    /**
     * Add the activation columns to the table.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $table
     */
    public static function columns(Blueprint $table)
    {
        $table->string($this->activationToken)->nullable();
        $table->timestamp($this->activationTimestamp)->nullable();
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
                $this->activationToken => null,
                $this->activationTimestamp => Carbon::now()->toDateTimeString(),
            ] + $credentials);
        };

        if (in_array(SilencesModelEvents::class, class_uses($this))) {
            $this->silentWhen(!$fireEvent, $activate);
        } else {
            $activate($this);
        }
    }

    /**
     * Send the account activation notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendAccountActivationNotification($token)
    {
        $this->notify(new ActivateAccount($this->email, $token));
    }
}
