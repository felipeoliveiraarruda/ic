<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use App\Traits\Auditavel;

class MenuGroup extends Model
{
    use \Spatie\Permission\Traits\HasRoles;
    use HasFactory, Notifiable, SoftDeletes, Auditavel;

    protected $fillable = ['key', 'label', 'order', 'is_active'];

    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class)->orderBy('order', 'asc');
    }
}