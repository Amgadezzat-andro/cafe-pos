<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    protected $fillable = [
        'name',
        'role',
        'salary',
        'hire_date',
        'system_role',
        'user_id',
    ];

    protected $casts = [
        'salary' => 'decimal:2',
        'hire_date' => 'date',
    ];

    /**
     * Relationship: Employee belongs to a User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Get employees by department/role
    public static function byRole($role)
    {
        return self::where('role', $role)->get();
    }

    // Get employees by system role
    public static function bySystemRole($systemRole)
    {
        return self::where('system_role', $systemRole)->get();
    }

    // Get salary stats
    public static function avgSalary()
    {
        return self::avg('salary');
    }

    public static function totalPayroll()
    {
        return self::sum('salary');
    }
}

