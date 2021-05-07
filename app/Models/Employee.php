<?php

namespace App\Models;


use App\Classes\Traits\FileStorage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory, FileStorage;

    protected $guarded = [];


}
