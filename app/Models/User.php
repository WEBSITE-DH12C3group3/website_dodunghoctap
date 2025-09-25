<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // nếu bạn dùng Sanctum cho API
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

/**
 * App\Models\User
 *
 * Cấu trúc cột (theo DB hiện có):
 * - user_id (PK, int, AI)
 * - full_name, email, password, phone, address
 * - role_id (FK -> roles.role_id)  // mô hình 1–1
 * - is_active (tinyint/bool)
 * - last_activity (timestamp/datetime, nullable)
 * - google_id, facebook_id (nullable)
 *
 * Lưu ý:
 * - Bảng không dùng timestamps mặc định của Laravel.
 * - Mật khẩu sẽ tự hash qua setPasswordAttribute().
 */
class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /** @var string  */
    protected $table = 'users';

    /** @var string  */
    protected $primaryKey = 'user_id';

    /** @var bool  */
    public $timestamps = false;

    /** @var array<int, string>  */
    protected $fillable = [
        'full_name',
        'email',
        'password',
        'phone',
        'address',
        'role_id',
        'is_active',
        'last_activity',
        'google_id',
        'facebook_id',
    ];

    /**
     * Ẩn mật khẩu/token khi serialize sang JSON/array.
     * (Nếu bảng bạn không có remember_token thì không sao, chỉ đơn giản là không xuất hiện.)
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Ép kiểu cho các cột.
     * @var array<string, string>
     */
    protected $casts = [
        'is_active'     => 'boolean',
        'last_activity' => 'datetime',
    ];

    /* -----------------------------------------------------------------
     |  Relationships
     | ----------------------------------------------------------------- */

    /**
     * Quan hệ 1–1: user thuộc về một role.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    /**
     * Lấy danh sách permissions (qua role).
     * @return \Illuminate\Support\Collection<\App\Models\Permission>
     */
    public function permissions(): Collection
    {
        return $this->role ? $this->role->permissions()->get() : collect();
    }

    /* -----------------------------------------------------------------
     |  Attribute Mutators / Accessors
     | ----------------------------------------------------------------- */

    /**
     * Tự động hash mật khẩu nếu giá trị truyền vào chưa được hash.
     * Cho phép update user mà không đổi mật khẩu (bỏ qua khi null/empty).
     */
    public function setPasswordAttribute($value): void
    {
        if (empty($value)) {
            // Không ghi đè nếu không truyền password
            unset($this->attributes['password']);
            return;
        }

        // Nếu chưa hash thì hash; nếu đã là bcrypt/argon2 thì giữ nguyên
        $needsHash = !is_string($value) || strlen($value) < 55 || !preg_match('/^\$2y\$|^\$argon2id\$/', (string)$value);
        $this->attributes['password'] = $needsHash ? Hash::make($value) : $value;
    }

    /* -----------------------------------------------------------------
     |  Authorization helpers
     | ----------------------------------------------------------------- */

    /**
     * Kiểm tra user có quyền $permissionName không (theo role 1–1).
     */
    public function hasPermission(string $permissionName): bool
    {
        $permissionName = strtolower(trim($permissionName));
        return $this->permissions()->contains(function ($p) use ($permissionName) {
            return strtolower($p->permission_name) === $permissionName;
        });
    }

    /**
     * Kiểm tra user có role nằm trong $roles không.
     * - $roles: string ('admin|employee') hoặc array(['admin','employee'])
     * - So sánh không phân biệt hoa/thường, có trim.
     */
    public function hasRole(string|array $roles): bool
    {
        $current = strtolower($this->role->role_name ?? '');
        $roles   = is_string($roles) ? explode('|', $roles) : $roles;

        foreach ($roles as $r) {
            if ($current === strtolower(trim((string)$r))) {
                return true;
            }
        }
        return false;
    }

    /**
     * Alias thân thiện (trong dự án cũ có thể đang dùng hasAnyRole).
     */
    public function hasAnyRole(string|array $roles): bool
    {
        return $this->hasRole($roles);
    }

    /**
     * Xác định online nếu last_activity trong vòng N phút (mặc định 5).
     */
    public function isOnline(int $withinMinutes = 5): bool
    {
        if (!$this->last_activity) return false;
        return now()->diffInMinutes($this->last_activity) < $withinMinutes;
    }

    /**
     * Cập nhật mốc hoạt động gần nhất về "now()".
     * Gọi tại middleware, listener hoặc nơi bạn login/sau request.
     */
    public function markOnline(): void
    {
        $this->last_activity = now();
        // Chỉ lưu cột này để tránh ảnh hưởng field khác
        $this->save(['timestamps' => false]);
    }

    /* -----------------------------------------------------------------
     |  Query Scopes
     | ----------------------------------------------------------------- */

    /**
     * scopeActive(): lọc user đang hoạt động (is_active = 1).
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * scopeRole(): lọc theo role_name (vd: ->role('customer')).
     */
    public function scopeRole($query, string $roleName)
    {
        return $query->whereHas('role', function ($q) use ($roleName) {
            $q->whereRaw('LOWER(role_name) = ?', [strtolower(trim($roleName))]);
        });
    }

    public function favourites()
    {
        return $this->hasMany(\App\Models\Favourite::class, 'user_id', 'user_id');
    }
}
