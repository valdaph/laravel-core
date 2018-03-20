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
    static $activationToken = 'activation_token';

    /**
     * The timestamp column.
     *
     * @var string
     */
    static $activationTimestamp = 'activated_at';

    /**
     * Boot the trait.
     *
     * @return void
     */
    public static function bootActivatable()
    {
        $token = strtoupper(str_random(32));

        static::creating(function ($model) use ($token) {
            $model->{static::$activationToken} = Hash::make($token);
        });

        static::created(function ($model) use ($token) {
            $model->sendAccountActivationNotification($token);
        });
    }

    /**
     * Get the observable event names.
     *
     * @return array
     */
    public function getObservableEvents()
    {
        return array_merge(parent::getObservableEvents, [
            'activating',
            'activated',
        ]);
    }

    /**
     * Add the activation columns to the table.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $table
     */
    public static function activationColumns(Blueprint $table)
    {
        $table->string(static::$activationToken)->nullable();
        $table->timestamp(static::$activationTimestamp)->nullable();
    }

    /**
     * Activate the account.
     *
     * @param  array  $credentials
     * @return void
     */
    public function activate($credentials = [])
    {
        if ($this->fireModelEvent('activating') === false) {
            return;
        }

        $data = $credentials + [
            static::$activationToken => null,
            static::$activationTimestamp => Carbon::now()->toDateTimeString(),
        ];

        if (in_array(SilencesModelEvents::class, class_uses($this))) {
            $this->silentUpdate($data);
        } else {
            $this->update($data);
        }

        $this->fireModelEvent('activated', false);
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

    /**
     * Resend the account activation notification.
     *
     * @return void
     */
    public function resendAccountActivationNotification()
    {
        $token = strtoupper(str_random(32));

        if (in_array(SilencesModelEvents::class, class_uses($this))) {
            $this->silentUpdate([
                static::$activationToken => Hash::make($token)
            ]);
        } else {
            $this->update([
                static::$activationToken => Hash::make($token)
            ]);
        };

        $this->sendAccountActivationNotification($token);
    }
}
