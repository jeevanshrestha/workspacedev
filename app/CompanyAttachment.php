<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Traits\Translatable;
use TCG\Voyager\Facades\Voyager;
use Carbon\Carbon;

class CompanyAttachment extends Model
{


	  public function setCreatedAtAttribute($value)
    {
        $this->attributes['created_at'] = Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    public function userId(){
    return $this->belongsTo(User::class);
	}

    public function companyId(){
    return $this->belongsTo(Company::class);
	}

    public function company()
    {
        return $this->belongsTo(Company::class);
    }


    public function User()
    {
        return $this->belongsTo(User::class);
    }

    
}
