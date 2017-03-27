<?php

namespace TCG\Voyager\Models;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as AuthUser;
use TCG\Voyager\Traits\VoyagerUser;
use App\Company; 
use DB;

class User extends AuthUser
{
    use VoyagerUser;

    protected $guarded = [];

    /**
     * On save make sure to set the default avatar if image is not set.
     */
    public function save(array $options = [])
    {
        // If no avatar has been set, set it to the default
        $this->avatar = $this->avatar ?: config('voyager.user.default_avatar', 'users/default.png');

        parent::save();
    }


    public function companyId(){
    return $this->belongsTo(Company::class);
    }

    public function roleId(){
    return $this->belongsTo(Role::class);
    }


    public function company()
    {
        return $this->belongsTo(Company::class);
    }


    public function role()
    {
        return $this->belongsTo(Role::class);
    }


    public function setCreatedAtAttribute($value)
    {
        $this->attributes['created_at'] = Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    public static  function getUserRole($id)
    {
        $user_role =    DB::table('users')
                        ->join('roles', 'roles.id', '=', 'users.role_id')->select('display_name')
                        ->where('users.id', '=', $id)->first();

        return $user_role->display_name;
    }

}
