<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'location', 'password', 'slack_id', 'avatar', 'nickname', 'slug'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function scopeSlackAdmin($query)
    {
        return $query->where('slack_id', 'U4FHEPX6J');
    }

    /**
     * A user has many posts
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany;
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * A user has many promotions
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function promotions()
    {
        return $this->hasMany(Promotion::class);
    }

    /**
     * A user has many profile key/pairs
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function profiles()
    {
        return $this->hasMany(Profile::class);
    }

    /**
     * Return user with relations for profile view/edit via User Resource
     * @param $query
     * @return mixed
     */
    public function scopeFullProfile($query)
    {
        return $query->with([
            'profiles.profile_key',
            'promotions',
            'socialLinks',
            'posts' => function ($posts) {
                $posts->orderBy('created_at', 'desc');
            }
        ]);
    }

    /**
     * A user has many socialLinks
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function socialLinks()
    {
        return $this->hasMany(SocialLinks::class);
    }

    /**
     * User is linked to a role
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    /**
     * Check User role
     *
     * @param string $name
     * @return bool
     */
    public function hasRole($name)
    {
        foreach ($this->roles as $role) {
            if ($role->name == $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * Assign user to role
     *
     * @param \App\Models\Role $role
     */
    public function assignRole(Role $role)
    {
        $this->roles()->attach($role);
    }

    /**
     * Remove user role
     *
     * @param \App\Models\Role $role
     * @return int
     */
    public function removeRole($role)
    {
        return $this->roles()->detach($role);
    }

    /**
     * Get the avatar attribute
     *
     * @param string $avatar
     * @return string
     */
    public function getAvatarAttribute($avatar)
    {
        if (is_null($avatar) || empty($avatar)) {
            return asset('/images/default-avatar.png');
        } else {
            return $avatar;
        }
    }

    public function routeNotificationForSlack($notification)
    {
        return config('slack.webhook_url');
    }

    public function routeNotificationForNexmo($notification)
    {
        return config('services.nexmo.sms_to');
    }
}
