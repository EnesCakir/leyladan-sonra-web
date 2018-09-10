<?php

namespace App\Models;

use App\Traits\Downloadable;
use App\Traits\Filterable;
use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPassword as ResetPasswordNotification;
use App\Notifications\ActivateEmail as ActivateEmailNotification;
use App\Notifications\NewUser as NewUserNotification;
use App\Notifications\ApprovedUser as ApprovedUserNotification;
use Spatie\Permission\Traits\HasRoles;
use Spatie\MediaLibrary\Models\Media;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\Image\Manipulations;
use App\Scopes\GraduateScope;
use App\Scopes\LeftScope;
use App\Traits\HasBirthday;
use App\Traits\HasMobile;
use App\Traits\BaseActions;
use App\Traits\Approvable;

class User extends Authenticatable implements HasMedia
{
    use BaseActions;
    use HasBirthday;
    use HasMobile;
    use Approvable;
    use Notifiable;
    use HasRoles;
    use HasMediaTrait;
    use Filterable;
    use Downloadable;

    // Properties
    protected $table = 'users';
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'birthday',
        'mobile',
        'year',
        'title',
        'profile_photo',
        'faculty_id',
        'gender',
        'email_token',
        'left_at',
        'graduated_at',
        'approved_at',
        'approved_by'
    ];
    protected $hidden = ['password', 'remember_token'];
    protected $appends = ['full_name'];
    protected $dates = ['created_at', 'updated_at', 'deleted_at', 'birthday', 'left_at', 'graduated_at', 'approved_at'];

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(new GraduateScope);
        static::addGlobalScope(new LeftScope);
    }

    // Relations
    public function children()
    {
        return $this->belongsToMany(Child::class);
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function processes()
    {
        return $this->hasMany(Process::class, 'created_by');
    }

    public function answers()
    {
        return $this->hasMany(Message::class, 'answered_by');
    }

    public function visits()
    {
        return $this->hasMany(Process::class, 'created_by')->where('desc', ProcessType::Visit);
    }

    // Scopes
    public function scopeTitle($query, $title)
    {
        $query->where('title', $title);
    }

    public function scopeSearch($query, $search)
    {
        $query->where(function ($query2) use ($search) {
            $query2->where('id', $search)
                   ->orWhere('last_name', 'like', '%' . $search . '%')
                   ->orWhere('email', 'like', '%' . $search . '%')
                   ->orWhere('mobile', 'like', '%' . $search . '%')
                   ->orWhere(\DB::raw('CONCAT_WS(" ", first_name, last_name)'), 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return $this->attributes['first_name'] . ' ' . $this->attributes['last_name'];
    }

    public function getRoleDisplayAttribute()
    {
        return $this->roles->pluck('display')->implode(', ');
    }

    public function getPhotoSmallUrlAttribute()
    {
        return $this->getFirstMediaUrl('default', 'small')
            ?: admin_asset('img/user-default-small.png');
    }

    public function getPhotolUrlAttribute()
    {
        return $this->getFirstMediaUrl('default', 'medium')
            ?: admin_asset('img/user-default-medium.png');
    }

    public function getPhotoLargeUrlAttribute()
    {
        return $this->getFirstMediaUrl('default', 'large')
            ?: admin_asset('img/user-default-large.png');
    }

    public function getLeftAtLabelAttribute()
    {
        return $this->attributes['left_at']
            ? Carbon::parse($this->attributes['left_at'])->format('d.m.Y')
            : '';
    }

    public function getGraduatedAtLabelAttribute()
    {
        return $this->attributes['graduated_at']
            ? Carbon::parse($this->attributes['graduated_at'])->format('d.m.Y')
            : '';
    }

    public function setLeftAtAttribute($date)
    {
        return $this->attributes['left_at'] = is_null($date)
            ? null
            : Carbon::parse($date)->toDateString();
    }

    public function setGraduatedAtAttribute($date)
    {
        return $this->attributes['graduated_at'] = is_null($date)
            ? null
            : Carbon::parse($date)->toDateString();
    }

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
        return $this;
    }

    // Helpers
    public function changeRole($role)
    {
        if ($role) {
            $this->syncRoles($role);
            if ($role == 'left') {
                $this->left();
            } elseif ($role == 'graduated') {
                $this->graduate();
            }
        }

    }

    public function activateEmail()
    {
        $this->email_token = null;
        return $this->save();
    }

    public function left($left = true)
    {
        $this->left_at = $left
            ? now()
            : null;

        return $this->save();
    }

    public function graduate($graduate = true)
    {
        $this->graduated_at = $graduate
            ? now()
            : null;

        return $this->save();
    }

    public static function toSelect($placeholder = null)
    {
        $res = static::orderBy('id', 'DESC')->get()->pluck('full_name', 'id');
        return $placeholder
            ? collect(['' => $placeholder])->union($res)
            : $res;
    }

    // Notifications
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function sendEmailActivationNotification()
    {
        $token = hash_hmac('sha256', str_random(40), $this->email);
        $this->email_token = $token;
        $this->save();
        $this->notify(new ActivateEmailNotification($token));
    }

    public function sendNewUserNotification($user)
    {
        $this->notify(new NewUserNotification($user));
    }

    public function sendApprovedUserNotification()
    {
        $this->notify(new ApprovedUserNotification());
    }


    // Image conversions
    public function registerMediaConversions(Media $media = null)
    {
        $this->addMediaConversion('small')->fit(Manipulations::FIT_CROP, 64, 64);
        $this->addMediaConversion('medium')->fit(Manipulations::FIT_CROP, 256, 256);
        $this->addMediaConversion('large')->fit(Manipulations::FIT_CROP, 512, 512);
    }
}
