<?php

namespace App\Models;

use EnesCakir\Helper\Traits\BaseActions;
use EnesCakir\Helper\Traits\Filterable;
use EnesCakir\Helper\Traits\HasMediaTrait;
use EnesCakir\Helper\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\Models\Media;
use Spatie\MediaLibrary\HasMedia\HasMedia;

class Faculty extends Model implements HasMedia
{
    use BaseActions;
    use Filterable;
    use HasMediaTrait;
    use HasSlug;

    // Properties
    protected $table = 'faculties';
    protected $fillable = [
        'name',
        'slug',
        'latitude',
        'longitude',
        'address',
        'city',
        'code',
        'started_at',
        'stopped_at'
    ];
    protected $dates = ['created_at', 'updated_at', 'deleted_at', 'started_at', 'stopped_at'];

    // Relations
    public function feeds()
    {
        return $this->hasMany(Feed::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function chats()
    {
        return $this->hasMany(Chat::class);
    }

    public function managers()
    {
        return $this->hasMany(User::class)->role('manager');
    }

    public function posts()
    {
        return $this->hasManyThrough(Post::class, Child::class);
    }

    public function messages()
    {
        return $this->hasManyThrough(Message::class, Chat::class);
    }

    public function children()
    {
        return $this->hasMany(Child::class);
    }

    // Scopes
    public function scopeStarted($query, $started = true)
    {
        return $started
            ? $query->whereNotNull('started_at')
            : $query->whereNull('started_at');
    }

    public function scopeStopped($query, $started = true)
    {
        return $started
            ? $query->whereNotNull('stopped_at')
            : $query->whereNull('stopped_at');
    }

    public function scopeSearch($query, $search)
    {
        if (is_null($search)) {
            return;
        }

        $query->where(function ($query2) use ($search) {
            $query2->where('faculties.name', 'like', "%{$search}%");
        });
    }

    // Mutators
    public function setStartedAtAttribute($date)
    {
        $this->attributes['started_at'] = $date
            ? Carbon::createFromFormat('d.m.Y', $date)->toDateString()
            : null;
    }

    public function setStoppedAtAttribute($date)
    {
        $this->attributes['stopped_at'] = $date
            ? Carbon::createFromFormat('d.m.Y', $date)->toDateString()
            : null;
    }

    // Accessors
    public function getStartedAtLabelAttribute()
    {
        return $this->attributes['started_at']
            ? Carbon::parse($this->attributes['started_at'])->format('d.m.Y')
            : null;
    }

    public function getStoppedAtLabelAttribute()
    {
        return $this->attributes['stopped_at']
            ? Carbon::parse($this->attributes['stopped_at'])->format('d.m.Y')
            : null;
    }

    public function getFullNameAttribute()
    {
        return "{$this->attributes['name']} Tıp Fakültesi";
    }

    public function getLogoUrlAttribute()
    {
        return $this->getFirstMediaUrl('default', 'optimized');
    }

    public function getThumbUrlAttribute()
    {
        return $this->getFirstMediaUrl('default', 'thumb');
    }

    public function isStarted()
    {
        return !is_null($this->started_at);
    }

    public function isStopped()
    {
        return !is_null($this->stopped_at);
    }

    # Media Library
    public function registerMediaCollections()
    {
        $this->addMediaCollection('default')->singleFile();
    }

    public function registerMediaConversions(Media $media = null)
    {
        $this->addMediaConversion('thumb')->fit(Manipulations::FIT_CROP, 100, 75);
        $this->addMediaConversion('optimized')->fit(Manipulations::FIT_CROP, 320, 240);
    }
}
