<?php

namespace App;

class Testimonial extends BaseModel
{
  // Properties
  protected $table    = 'testimonials';
  protected $fillable = ['name', 'text', 'email', 'via', 'priority', 'approved_at', 'approved_by'];
  protected $dates    = array_merge($this->dates, ['approved_at']);

  // Validation rules
  public static $validationRules = [
    'name'     =>'required|max:255',
    'text'     =>'required',
    'via'      =>'required',
    'priority' =>'required|numeric'
  ];

  // Relations
  public function approver()
  {
    return $this->belongsTo(User::class, 'approved_by');
  }
}
