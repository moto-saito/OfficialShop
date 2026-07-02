<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * 一括代入可能なカラム
     * 注文・配送先・決済情報と連携するフィールドを含む
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'postal_code',
        'prefecture',
        'address',
        'phone_number',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /**
     * 登録済み住所が揃っているか
     * 注文時に配送先として利用できるかの判定に使用
     */
    public function hasAddress(): bool
    {
        return filled($this->postal_code)
            && filled($this->prefecture)
            && filled($this->address);
    }

    /**
     * 表示用の住所文字列（〒 郵便番号 都道府県 住所）
     */
    public function getFormattedAddressAttribute(): ?string
    {
        if (!$this->hasAddress()) {
            return null;
        }

        return "〒{$this->postal_code} {$this->prefecture}{$this->address}";
    }
}
