<?php

namespace App\Models;

use App\Models\Scopes\OrderByScope;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Lacodix\LaravelModelFilter\Traits\IsSearchable;
use Lacodix\LaravelModelFilter\Traits\IsSortable;
use Laravel\Sanctum\HasApiTokens;
use LaravelArchivable\Archivable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements CanResetPasswordContract
{
    use Archivable, CanResetPassword, HasApiTokens, HasFactory, HasRoles, IsSearchable, IsSortable, Notifiable;

    protected static function booted(): void
    {
        static::addGlobalScope(new OrderByScope('name'));
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'job_title',
        'avatar',
        'phone',
        'rate',
        'google_id',
    ];

    protected $searchable = [
        'name',
        'email',
        'job_title',
    ];

    protected $sortable = [
        'name',
        'email',
        'rate',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getFirstName(): string
    {
        return Str::beforeLast($this->name, ' ');
    }

    public function clientCompanies(): BelongsToMany
    {
        return $this->belongsToMany(ClientCompany::class, 'client_company', 'client_id', 'client_company_id');
    }

    public static function clientDropdownValues(): array
    {
        return self::orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($i) => ['value' => (string) $i->id, 'label' => $i->name])
            ->toArray();
    }
}
