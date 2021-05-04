<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $connection ='sqlite';
    protected $fillable = ['first_name', 'last_name', 'email_address','job_role'];
}
