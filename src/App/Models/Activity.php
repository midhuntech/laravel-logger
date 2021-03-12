<?php

namespace midhuntech\LaravelLogger\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table;

    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Fillable fields for a Profile.
     *
     * @var array
     */
    protected $fillable = [
        'description',
        'details',
        'userType',
        'userId',
        'route',
        'ipAddress',
        'userAgent',
        'locale',
        'referer',
        'methodType',
        'input_request',
        'output_data',
    ];

    protected $casts = [
        'description'   => 'string',
        'details'       => 'string',
        'user'          => 'integer',
        'route'         => 'string',
        'ipAddress'     => 'string',
        'userAgent'     => 'string',
        'locale'        => 'string',
        'referer'       => 'string',
        'methodType'    => 'string',
        'input_request'    => 'array',
        'output_data'    => 'array',
    ];

    /**
     * Create a new instance to set the table and connection.
     *
     * @return void
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('LaravelLogger.loggerDatabaseTable');
        $this->connection = config('LaravelLogger.loggerDatabaseConnection');
    }

    /**
     * Get the database connection.
     */
    public function getConnectionName()
    {
        return $this->connection;
    }

    /**
     * Get the database connection.
     */
    public function getTableName()
    {
        return $this->table;
    }

    /**
     * An activity has a user.
     *
     * @var array
     */
    public function user()
    {
        return $this->hasOne(config('LaravelLogger.defaultUserModel'));
    }

    /**
     * Get a validator for an incoming Request.
     *
     * @param array $merge (rules to optionally merge)
     *
     * @return array
     */
    public static function rules($merge = [])
    {
        if (app() instanceof \Illuminate\Foundation\Application) {
            $route_url_check = \Illuminate\Foundation\Application::VERSION < 5.8 ? 'active_url' : 'url';
        } else {
            $route_url_check = 'url';
        }

        return array_merge(
            [
                'description'   => 'required|string',
                'details'       => 'nullable|string',
                'userType'      => 'required|string',
                'userId'        => 'nullable|integer',
                'route'         => 'nullable|'.$route_url_check,
                'ipAddress'     => 'nullable|ip',
                'userAgent'     => 'nullable|string',
                'locale'        => 'nullable|string',
                'referer'       => 'nullable|string',
                'methodType'    => 'nullable|string',
            ],
            $merge
        );
    }

    /**
     * User Agent Parsing Helper.
     *
     * @return string
     */
    public function getUserAgentDetailsAttribute()
    {
        return \midhuntech\LaravelLogger\App\Http\Traits\UserAgentDetails::details($this->userAgent);
    }
}
