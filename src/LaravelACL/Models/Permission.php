<?php

namespace AM2Studio\LaravelACL\Models;

use AM2Studio\LaravelACL\Exceptions\ACLException;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Permission
 * @package AM2Studio\LaravelACL\Models
 */
class Permission extends Model
{
    /**
     * @var string
     */
    protected $table    = 'am2_acl_permissions';
    /**
     * @var array
     */
    public $fillable    = ['name', 'slug', 'description', 'model', 'resource_id'];

    /**
     * @return mixed
     */
    public function roles()
    {
        return $this->belongsToMan('App\Models\Role', 'am2_acl_role_permission');
    }

    /**
     * @param $value
     * @throws ACLException
     */
    public function setModelAttribute($value)
    {
        if (!(config('acl.models')[$value])) {
            throw new ACLException('Model does not exist in acl config file.');
        }

        $this->attributes['model'] = $value;
    }
}
