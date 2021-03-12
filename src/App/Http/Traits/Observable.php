<?php

namespace midhuntech\LaravelLogger\App\Http\Traits;

use Illuminate\Support\Facades\Auth;

use Illuminate\Database\Eloquent\Model;
use midhuntech\LaravelLogger\App\Http\Traits\ActivityLogger;

/**
 * Observable trait
 *
 * @package App\Traits
 */
trait Observable
{
    use ActivityLogger;
    public static function bootObservable()
    {
        static::saved(function (Model $model) {
            // create or update?
            if( $model->wasRecentlyCreated ) {
                static::logChange( $model, 'CREATED' );
            } else {
                if( !$model->getChanges() ) {
                    return;
                }
                static::logChange( $model, 'UPDATED' );
            }
        });
        static::updated(function (Model $model) {
            // create or update?
            if( $model->wasRecentlyCreated ) {
                static::logChange( $model, 'CREATED' );
            } else {
                if( !$model->getChanges() ) {
                    return;
                }
                static::logChange( $model, 'UPDATED' );
            }
        });

        static::deleted(function (Model $model) {
            static::logChange( $model, 'DELETED' );
        });
    }

    /**
     * String to describe the model being updated / deleted / created
     *
     * Override this in your own model to customise - see below for example
     *
     * @return string
     */
    public static function logSubject(Model $model): string {
        return static::logImplodeAssoc($model->attributesToArray());
    }

    /**
     * Format an assoc array as a key/value string for logging
     * @return string
     */
    public static function logImplodeAssoc(array $attrs): string {
        $l = '';
        foreach( $attrs as $k => $v ) {
            $l .= "{ $k => $v } ";
        }
        return $l;
    }

    /**
     * String to describe the model being updated / deleted / created
     * @return string
     */
    public static function logChange( Model $model, string $action ) {
        ActivityLogger::activity(json_encode($model->getAttributes()),null,$model->getOriginal(),$model->getChanges());
    }

}
