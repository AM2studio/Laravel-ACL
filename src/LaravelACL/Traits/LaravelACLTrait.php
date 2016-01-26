<?php

namespace AM2Studio\LaravelACL\Traits;

use AM2Studio\LaravelACL\Exceptions\ACLException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Class LaravelACLTrait
 * @package AM2Studio\LaravelACL\Traits
 */
trait LaravelACLTrait
{
    /**
     * @var
     */
    protected $roles;
    /**
     * @var
     */
    protected $permissions;

    /**
     * @return mixed
     */
    public function roles()
    {
        return $this->belongsToMany(\AM2Studio\LaravelACL\Models\Role::class, 'am2_acl_user_role')->withTimestamps();
    }

    /**
     * @return mixed
     */
    public function permissions()
    {
        return $this->belongsToMany(\AM2Studio\LaravelACL\Models\Permission::class, 'am2_acl_user_permission')->withTimestamps();
    }

    /**
     * @return mixed
     */
    public function getRoles()
    {
        return (!$this->roles) ? $this->roles = $this->roles()->get() : $this->roles;
    }

    /**
     * @param $role
     * @return bool
     */
    public function attachRole($role)
    {
        return (!$this->getRoles()->contains($role)) ? $this->roles()->attach($role) : true;
    }

    /**
     * @param $role
     * @return mixed
     */
    public function detachRole($role)
    {
        $this->roles = null;

        return $this->roles()->detach($role);
    }

    /**
     * @return mixed
     */
    public function detachAllRoles()
    {
        $this->roles = null;

        return $this->roles()->detach();
    }

    /**
     * @param $role
     * @param bool $all
     * @return mixed
     */
    public function is($role, $all = false)
    {
        return $this->{$this->getMethodName('is', $all)}($role);
    }

    /**
     * @param $role
     * @return bool
     */
    public function isOne($role)
    {
        foreach ($this->getArrayFrom($role) as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $role
     * @return bool
     */
    public function isAll($role)
    {
        foreach ($this->getArrayFrom($role) as $role) {
            if (!$this->hasRole($role)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $role
     * @return mixed
     */
    public function hasRole($role)
    {
        return $this->getRoles()->contains(function ($key, $value) use ($role) {
            return $role == $value->id || Str::is($role, $value->slug);
        });
    }

    /**
     * @return mixed
     */
    public function rolePermissions()
    {
        return \AM2Studio\LaravelACL\Models\Permission::select([
            'am2_acl_permissions.*',
            'am2_acl_role_permission.created_at as pivot_created_at',
            'am2_acl_role_permission.updated_at as pivot_updated_at'
        ])
            ->join('am2_acl_role_permission', 'am2_acl_role_permission.permission_id', '=', 'am2_acl_permissions.id')
            ->join('am2_acl_roles', 'am2_acl_roles.id', '=', 'am2_acl_role_permission.role_id')
            ->whereIn('am2_acl_roles.id', $this->getRoles()->lists('id')->toArray())
            ->groupBy(['am2_acl_permissions.id', 'pivot_created_at', 'pivot_updated_at']);
    }

    /**
     * @return mixed
     */
    public function userPermissions()
    {
        return $this->belongsToMany(\AM2Studio\LaravelACL\Models\Permission::class, 'am2_acl_user_permission')->withTimestamps();
    }

    /**
     * @return mixed
     */
    public function getPermissions()
    {
        return (!$this->permissions)
            ? $this->permissions = $this->rolePermissions()->get()->merge($this->userPermissions()->get())
            : $this->permissions;
    }

    /**
     * @param $permission
     * @param bool $all
     * @return mixed
     */
    public function can($permission, $all = false)
    {
        return $this->{$this->getMethodName('can', $all)}($permission);
    }

    /**
     * @param $permission
     * @return bool
     */
    public function canOne($permission)
    {
        foreach ($this->getArrayFrom($permission) as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $permission
     * @return bool
     */
    public function canAll($permission)
    {
        foreach ($this->getArrayFrom($permission) as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param $permission
     * @return mixed
     */
    public function hasPermission($permission)
    {
        return $this->getPermissions()->contains(function ($key, $value) use ($permission) {
            return $permission == $value->id || Str::is($permission, $value->slug);
        });
    }

    /**
     * @param $permission
     * @param Model $entity
     * @param null $resourceId
     * @return bool
     * @throws ACLException
     */
    public function allowed($permission, Model $entity, $resourceId = null)
    {
        $model = array_search(get_class($entity), config('acl.models'), true);
        if (!$model) {
            throw new ACLException('Model does not exist in acl config file.');
        }

        foreach ($this->getPermissions() as $value) {
            if (
                null != $value->model
                && $model == $value->model
                && ($value->id == $permission || $value->slug == $permission)
                && (null == $resourceId || $resourceId == $value->resource_id)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $permission
     * @return bool
     */
    public function attachPermission($permission)
    {
        return (!$this->getPermissions()->contains($permission)) ? $this->userPermissions()->attach($permission) : true;
    }

    /**
     * @param $permission
     * @return mixed
     */
    public function detachPermission($permission)
    {
        $this->permissions = null;

        return $this->userPermissions()->detach($permission);
    }

    /**
     * @return mixed
     */
    public function detachAllPermissions()
    {
        $this->permissions = null;

        return $this->userPermissions()->detach();
    }

    /**
     * @param $methodName
     * @param $all
     * @return string
     */
    private function getMethodName($methodName, $all)
    {
        return ((bool) $all) ? $methodName.'All' : $methodName.'One';
    }

    /**
     * @param $argument
     * @return array
     */
    private function getArrayFrom($argument)
    {
        return (!is_array($argument)) ? preg_split('/ ?[,|] ?/', $argument) : $argument;
    }

    /**
     * @param $method
     * @param $parameters
     * @return bool|mixed
     * @throws ACLException
     */
    public function __call($method, $parameters)
    {
        if (starts_with($method, 'is')) {
            return $this->is(
                snake_case(substr($method, 2), config('acl.separator'))
            );
        } elseif (starts_with($method, 'can')) {
            return $this->can(
                snake_case(substr($method, 3), config('acl.separator'))
            );
        } elseif (starts_with($method, 'allowed')) {
            return $this->allowed(
                snake_case(substr($method, 7), config('acl.separator')),
                $parameters[0],
                (isset($parameters[1])) ? $parameters[1] : null
            );
        }

        return parent::__call($method, $parameters);
    }
}
