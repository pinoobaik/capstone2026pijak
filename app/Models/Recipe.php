<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    // Sesuaikan dengan nama tabel resep asli di phpMyAdmin kalian
    protected $table = 'recipes'; 
    
    // Jika tabel kalian tidak punya kolom created_at & updated_at, set ke false
    public $timestamps = false; 
}