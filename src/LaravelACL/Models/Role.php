<?php

namespace AM2Studio\LaravelACL\Models;

use AM2Studio\LaravelACL\Traits\Slugable;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Role
 * @package AM2Studio\LaravelACL\Models
 */
class Role extends Model
{
    use Slugable;

    /**
     * @var string
     */
    protected $table    = 'am2_acl_roles';
    /**
     * @var array
     */
    public $fillable    = ['name', 'slug', 'description'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(\AM2Studio\LaravelACL\Models\Permission::class, 'am2_acl_role_permission')->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(config('acl.models.user'), 'am2_acl_user_role')->withTimestamps();
    }

    /**
     * @param $permission
     * @return bool|void
     */
    public function attachPermission($permission)
    {
        return (!$this->permissions()->get()->contains($permission)) ? $this->permissions()->attach($permission) : true;
    }

    /**
     * @param $permission
     * @return int
     */
    public function detachPermission($permission)
    {
        return $this->permissions()->detach($permission);
    }

    /**
     * @return int
     */
    public function detachAllPermissions()
    {
        return $this->permissions()->detach();
    }
}
