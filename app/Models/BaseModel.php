<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;
use Schema;
use Carbon\Carbon;
use Image;
use File;
use App\Jobs\OptimizeImage;

class BaseModel extends Model
{
    use SoftDeletes;
    protected $dates    = ['deleted_at', 'created_at', 'updated_at'];
    protected $slugKeys = null;

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (Schema::hasColumn($model->getTable(), 'created_by')) {
                if (Auth::user()) {
                    $model->created_by = Auth::user()->id;
                }
            }
        });
        static::updating(function ($model) {
            if (Schema::hasColumn($model->getTable(), 'updated_by')) {
                if (Auth::user()) {
                    $model->updated_by = Auth::user()->id;
                }
            }
        });
        static::deleting(function ($model) {
            if (Schema::hasColumn($model->getTable(), 'deleted_by')) {
                if (Auth::user()) {
                    $model->deleted_by = Auth::user()->id;
                    $model->save();
                }
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getCreatedAtHumanAttribute()
    {
        return Carbon::createFromTimeStamp(strtotime($this->attributes['created_at']), 'Europe/Istanbul')->diffForHumans();
    }

    public function getUpdatedAtHumanAttribute()
    {
        return Carbon::createFromTimeStamp(strtotime($this->attributes['updated_at']), 'Europe/Istanbul')->diffForHumans();
    }

    public function getCreatedAtLabelAttribute()
    {
        return date("d.m.Y", strtotime($this->attributes['created_at']));
    }

    public function updateSlug()
    {
        return $this->slugKeys ?
      $this->update([
        'slug' => str_slug(remove_turkish(implode('-', array_map(function ($key) {
            return $this->attributes[$key];
        }, $this->slugKeys))))
      ])
      : false ;
    }

    public function uploadImage($file, $attribute, $location, $size = 1000, $quality = 80, $format = "jpg", $resize = true)
    {
        $imageName = $this->attributes['id'] . str_random(5) . "." . $format ;
        $imageLocation = upload_path($location);
        $this->attributes[$attribute] = $imageName;
        if ($resize) {
            Image::make($file)
            ->resize($size, null, function ($constraint) {
                $constraint->aspectRatio();
            })
            ->save($imageLocation . '/' . $imageName, $quality);
        } else {
            Image::make($file)
            ->save($imageLocation . '/' . $imageName, $quality);
        }
        dispatch(new OptimizeImage($imageLocation . '/' . $imageName));
        return $this->save();
    }

    public function deleteImage($attribute, $location, $null = false)
    {
        $imageLocation = upload_path($location) . '/' . $this->$attribute;
        File::delete($imageLocation);
        if ($null) {
            $this->$attribute = null;
        }
        return $this->save();
    }
}
