<?php

namespace App\Models;

use App\Observers\UserObserver;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable, HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
    protected $appends = ['avatar', 'name'];
    protected $hidden  = ['password', 'remember_token'];
    protected $casts   = ['email_verified_at' => 'datetime', 'phone_verified_at' => 'datetime'];
    protected $dates   = ['date_of_birth', 'date_of_birth_hijri'];

    protected static function boot()
    {
        parent::boot();
        User::observe(UserObserver::class);
    }

    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = bcrypt($value);
        }
    }

    public function setAvatarAttribute($value)
    {
        if ($value && $value->isValid()) {
            if (isset($this->attributes['avatar']) && $this->attributes['avatar']) {
                if (file_exists(storage_path('app/public/images/user/' . $this->attributes['avatar']))) {
                    unlink(storage_path('app/public/images/user' . "/" . $this->attributes['avatar']));
                }
            }
            $image = upload_single_file($value, 'app/public/images/user');
            $this->attributes['avatar'] = $image;
        }
    }

    public function getAvatarAttribute()
    {
        $avatar = $this->media()->where('option', 'avatar')->first();
        // $image = isset($this->attributes['avatar']) && $this->attributes['avatar'] ? 'storage/images/user/' . $this->attributes['avatar'] : 'dashboardAssets/images/avatars/6.png';
        // $image = file_exists(storage_path('app/public/images/user' . "/" . $avatar->media)) ? 'storage/images/user/' . $avatar->media : 'dashboardAssets/images/avatars/6.png';
        $image = $this->media()->where('option', 'avatar')->first()  ? 'storage/images/user/' . $avatar->media : 'dashboardAssets/images/backgrounds/avatar.jpg';

        return asset($image);
    }


    public function getIsUserDeactiveAttribute()
    {
        return !$this->attributes['is_active'] || $this->attributes['is_ban'];
    }

    public function getNameAttribute()
    {
        return $this->attributes['fullname'];
    }

    public function getCountryNameAttribute()
    {
        return optional(@$this->profile->country)->name;
    }

    public function getCityNameAttribute()
    {
        return optional(@$this->profile->city)->name;
    }

    // Scopes
    public function scopeActive($query)
    {
        $query->where(['is_active' => 1, 'is_ban' => 0, 'is_admin_active_user' => 1]);
    }

    // Relations
    public function media()
    {
        return $this->morphOne(AppMedia::class, 'app_mediaable');
    }

    //==========================Devices==================
    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function country()
    {
        return $this->hasOneThrough(Country::class, Profile::class, 'user_id', 'id', 'id', 'country_id');
    }

    public function city()
    {
        return $this->hasOneThrough(City::class, Profile::class, 'user_id', 'id', 'id', 'city_id');
    }

    //==========================Profile=====================
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }


    public function cart()
    {
        return $this->hasOne(Cart::class, 'user_id');
    }

    public function favouriteProducts()
    {
        return $this->hasMany(FavouriteProduct::class);
    }

    

    // Roles & Permissions
    // public function role()
    // {
    //     return $this->belongsTo(Role::class);
    // }

    // public function hasPermissions($route, $method = null)
    // {
    //     if ($this->user_type == 'superadmin') {
    //         return true;
    //     }

    //     if (is_null($method)) {
    //         if ($this->role->permissions->contains('route_name', $route . ".index")) {
    //             return true;
    //         } elseif ($this->role->permissions->contains('route_name', $route . ".store")) {
    //             return true;
    //         } elseif ($this->role->permissions->contains('route_name', $route . ".update")) {
    //             return true;
    //         } elseif ($this->role->permissions->contains('route_name', $route . ".destroy")) {
    //             return true;
    //         } elseif ($this->role->permissions->contains('route_name', $route . ".show")) {
    //             return true;
    //         } elseif ($this->role->permissions->contains('route_name', $route . ".wallet")) {
    //             return true;
    //         }
    //     } else {
    //         return $this->role->permissions->contains('route_name', $route . "." . $method);
    //     }

    //     return false;
    // }

    // For Notification Channel
    public function receivesBroadcastNotificationsOn()
    {
        return 'outfit-notification.' . $this->id;
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function walletTransactions()
    {
        return $this->HasMany(WalletTransaction::class, 'user_id');
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class, 'user_id');
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class, 'user_id');
    }

    public function orders(){
        return $this->hasMany(Order::class, 'user_id');
    }

    public function fav_products()
    {
        return $this->belongsToMany(ProductDetails::class, 'favourite_products', 'user_id', 'product_detail_id')->withTimestamps();
    }

    public function address()
    {
        return $this->hasMany(Address::class, 'user_id');
    }


    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function permissions()
    {
        return $this->role ? @$this->role->permissions : [];
    }

    public function back_route_name_permissions()
    {
        return $this->role ? @$this->role->permissions()->pluck('back_route_name')->toArray() : [];
    }

    public function hasRole($role)
    {
        return $this->role ? @$this->role->name == $role : false;
    }

    public function hasPermission($permission)
    {
        if(($this->back_route_name_permissions() != null && ! empty($this->back_route_name_permissions()))) {

            if(in_array($permission,$this->back_route_name_permissions())) {
                return true;
            } else {
                return false;
            }

        } else {
            return false;
        }
    }


    public function hasPermissions($route, $method = null)
    {
        if ($this->user_type == 'supper_admin') {
            return true;
        }
        if (is_null($method)) {
            if ($this->role->permissions->contains('route_name', $route . ".index")) {
                return true;
            } elseif ($this->role->permissions->contains('route_name', $route . ".store")) {
                return true;
            } elseif ($this->role->permissions->contains('route_name', $route . ".update")) {
                return true;
            } elseif ($this->role->permissions->contains('route_name', $route . ".destroy")) {
                return true;
            } elseif ($this->role->permissions->contains('route_name', $route . ".show")) {
                return true;
            } elseif ($this->role->permissions->contains('route_name', $route . ".wallet")) {
                return true;
            }
        } else {
            return $this->role->permissions->contains('route_name', $route . "." . $method);
        }
        return false;
    }


    // public function toArray()
    // {
    //     $array = parent::toArray();

    //     $array['full_phone'] = $this->phone_code.$this->phone;

    //     return $array;

    // }

}
