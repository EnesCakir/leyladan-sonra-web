<?php

namespace App\Models;

use App\Enums\PostType;
use Carbon\Carbon;
use EnesCakir\Helper\Traits\BaseActions;
use EnesCakir\Helper\Traits\Filterable;
use EnesCakir\Helper\Traits\HasBirthday;
use EnesCakir\Helper\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ChatStatus;
use App\Enums\GiftStatus;
use DB;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;

class Child extends Model implements HasMedia
{
    use BaseActions;
    use HasBirthday;
    use Filterable;
    use HasSlug;
    use HasMediaTrait;

    // Properties
    protected $table = 'children';
    protected $fillable = [
        'faculty_id', 'department', 'first_name', 'last_name', 'diagnosis', 'diagnosis_desc', 'taken_treatment',
        'child_state', 'child_state_desc', 'gender', 'meeting_day', 'birthday', 'wish', 'g_first_name', 'g_last_name',
        'g_mobile', 'g_email', 'province', 'city', 'address', 'extra_info', 'volunteer_id', 'verification_doc',
        'gift_state', 'on_hospital', 'until', 'slug', 'featured_media_id', 'meeting_post_id', 'delivery_post_id',
        'is_name_public', 'is_diagnosis_public', 'wish_category_id'
    ];
    protected $dates = ['created_at', 'updated_at', 'deleted_at', 'meeting_day', 'birthday', 'until'];
    protected $appends = ['full_name'];
    protected $slugKeys = ['first_name', 'id'];

    // Relations
    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function volunteer()
    {
        return $this->belongsTo(Volunteer::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function wishCategory()
    {
        return $this->belongsTo(WishCategory::class);
    }

    public function meetingPost()
    {
        return $this->belongsTo(Post::class, 'meeting_post_id')->withDefault([
            'child_id' => $this->id,
            'type'     => PostType::Meeting
        ]);
    }

    public function deliveryPost()
    {
        return $this->belongsTo(Post::class, 'delivery_post_id')->withDefault([
            'child_id' => $this->id,
            'type'     => PostType::Delivery
        ]);
    }

    public function chats()
    {
        return $this->hasMany(Chat::class);
    }

    public function activeChats()
    {
        return $this->hasMany(Chat::class)->active();
    }

    public function unansweredMessages()
    {
        return $this->hasManyThrough(Message::class, Chat::class)->answered(false);
    }

    public function processes()
    {
        return $this->hasMany(Process::class)->with(['creator', 'processable'])->latest();
    }

    public function featuredMedia()
    {
        return $this->belongsTo(Media::class);
    }

    public function allMedia()
    {
        $media = collect();
        if ($this->meetingPost) {
            $media = $media->merge($this->meetingPost->media);
        }
        if ($this->deliveryPost) {
            $media = $media->merge($this->deliveryPost->media);
        }
        return $media;
    }

    // Scopes
    public function scopeGift($query, $gift_state)
    {
        $query->where('gift_state', $gift_state);
    }

    public function scopeDepartment($query, $department)
    {
        $query->where('department', $department);
    }

    public function scopeDiagnosis($query, $diagnosis)
    {
        $query->where('diagnosis', $diagnosis);
    }

    public function scopeSearch($query, $search)
    {
        if (is_null($search)) {
            return;
        }

        $query->where(function ($query2) use ($search) {
            $query2->where('children.id', $search)->orWhere('children.first_name', 'like', "%{$search}%")
                ->orWhere('children.last_name', 'like', "%{$search}%")
                ->orWhere(
                    DB::raw('CONCAT_WS(" ", children.first_name, children.last_name)'),
                    'like',
                    "%{$search}%"
                );
        });
    }

    public function scopeWithChatCounts($query)
    {
        $query->withCount([
            'chats as open_count'     => function ($query) {
                return $query->where('status', ChatStatus::Open);
            },
            'chats as answered_count' => function ($query) {
                return $query->where('status', ChatStatus::Answered);
            },
            'chats as closed_count'   => function ($query) {
                return $query->where('status', ChatStatus::Closed);
            }
        ]);
    }

    // Accessors
    public function getUserNameListAttribute()
    {
        return implode(', ', $this->users->pluck('full_name')->toArray());
    }

    public function getUserListAttribute()
    {
        return $this->users->pluck('id');
    }

    public function getGiftStateLabelAttribute()
    {
        $status = $this->attributes['gift_state'];
        switch ($status) {
            case GiftStatus::Waiting:
                return "<span class='label label-danger'>{$status}</span>";
            case GiftStatus::OnRoad:
                return "<span class='label label-warning'>{$status}</span>";
            case GiftStatus::Arrived:
                return "<span class='label label-primary'>{$status}</span>";
            case GiftStatus::Delivered:
                return "<span class='label label-success'>{$status}</span>";
            default:
                return "<span class='label label-default'>{$status}</span>";
        }
    }

    public function getFullNameAttribute()
    {
        return $this->attributes['first_name'] . ' ' . $this->attributes['last_name'];
    }

    public function getMeetingDayLabelAttribute()
    {
        return $this->meeting_day
            ? $this->meeting_day->format('d.m.Y')
            : '';
    }

    public function getUntilLabelAttribute()
    {
        return $this->until
            ? $this->until->format('d.m.Y')
            : '';
    }

    public function getMeetingDayHumanAttribute()
    {
        return date('d.m.Y', strtotime($this->attributes['meeting_day']));
    }

    public function getBirthdayHumanAttribute()
    {
        return date('d.m.Y', strtotime($this->attributes['birthday']));
    }

    public function getUntilHumanAttribute()
    {
        return date('d.m.Y', strtotime($this->attributes['until']));
    }

    public function getSafeNameAttribute()
    {
        return $this->attributes['is_name_public']
            ? $this->attributes['first_name']
            : "Minik {$this->id}";
    }

    // Mutators
    public function setMeetingDayAttribute($date)
    {
        $this->attributes['meeting_day'] = $date
            ? (is_object($date)
                ? $date
                : Carbon::createFromFormat('d.m.Y', $date)->toDateString())
            : null;
    }

    public function setUntilAttribute($date)
    {
        $this->attributes['until'] = $date
            ? (is_object($date)
                ? $date
                : Carbon::createFromFormat('d.m.Y', $date)->toDateString())
            : null;
    }

    public function setSlugAttribute($slug)
    {
        $this->attributes['slug'] = $this->is_name_public
            ? $slug
            : "minik-{$this->id}";
    }

    public function setGMobileAttribute($g_mobile)
    {
        return $this->attributes['g_mobile'] = make_mobile($g_mobile);
    }

    // Helpers
    public function volunteered(Volunteer $volunteer)
    {
        $this->volunteer()->associate($volunteer);
        $this->gift_state = GiftStatus::OnRoad;
        $this->save();
    }

    public function getSlugValues()
    {
        $values = collect();
        foreach ($this->slugKeys as $key) {
            if (array_key_exists($key, $this->attributes) && ($value = $this->attributes[$key])) {
                $values->push($value);
            }
        }

        return $values;
    }

    public function registerMediaCollections()
    {
        $this->addMediaCollection('verification')->useDisk('verification')->singleFile();
    }

    public function registerMediaConversions(Media $media = null)
    {
        $this->addMediaConversion('optimized')
            ->fit(Manipulations::FIT_MAX, 1500, 2000)
            ->performOnCollections('verification');
    }

    public function addVerificationDoc($file)
    {
        return $this->addMedia($file)->sanitizingFileName(function ($name) {
            return $this->id . str_random(5) . '.' . pathinfo($name, PATHINFO_EXTENSION);
        })->toMediaCollection('verification');
    }
}
