<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverDetails extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $appends = [
        'first_id_card_image_url',
        'last_id_card_image_url',
        'first_residence_card_image_url',
        'last_residence_card_image_url',
        'first_license_image_url',
        'last_license_image_url',
        'record_url',
        'pdf_url',
    ];
    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function driverCarImage()
    {
        return $this->belongsTo(DriverCarImage::class , 'driver_details_id');
    }
    public function getFirstIdCardImageUrlAttribute()
    {
        return $this->first_id_card_image ? asset('assets/images/driver/' . $this->first_id_card_image) : null;
    }
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('assets/images/driver/' . $this->image) : null;
    }
    public function getLastIdCardImageUrlAttribute()
    {
        return $this->last_id_card_image ? asset('assets/images/driver/' . $this->last_id_card_image) : null;
    }
    public function getFirstResidenceCardImageUrlAttribute()
    {
        return $this->first_residence_card_image ? asset('assets/images/driver/' . $this->first_residence_card_image) : null;
    }
    public function getLastResidenceCardImageUrlAttribute()
    {
        return $this->last_residence_card_image ? asset('assets/images/driver/' . $this->last_residence_card_image) : null;
    }
    public function getFirstLicenseImageUrlAttribute()
    {
        return $this->first_license_image ? asset('assets/images/driver/' . $this->first_license_image) : null;
    }
    public function getLastLicenseImageUrlAttribute()
    {
        return $this->last_license_image ? asset('assets/images/driver/' . $this->last_license_image) : null;
    }
    public function getRecordUrlAttribute()
    {
        return $this->record ? asset('assets/images/driver/' . $this->record) : null;
    }
    public function getPdfUrlAttribute()
    {
        return $this->pdf ? asset('assets/images/driver/' . $this->pdf) : null;
    }

}
