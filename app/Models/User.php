<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $guarded = [];
    protected $hidden = ['password'];

    /**
     * URL foto profil yang aman dipakai di seluruh tampilan.
     *
     * Selain membaca path dari database, accessor ini memulihkan foto lama
     * berdasarkan ID user apabila nama file di database sudah tidak sama
     * dengan file yang tersimpan di public/uploads/profiles.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        $avatar = trim((string) ($this->attributes['avatar'] ?? ''));

        if ($avatar !== '') {
            if (
                str_starts_with($avatar, 'http://') ||
                str_starts_with($avatar, 'https://') ||
                str_starts_with($avatar, '//')
            ) {
                return $avatar;
            }

            $relativePath = ltrim($avatar, '/');

            // Gunakan path database hanya jika file lokalnya benar-benar ada.
            if (is_file(public_path($relativePath))) {
                return asset($relativePath);
            }
        }

        // Recovery untuk data lama: cari foto user berdasarkan pola profile_{id}_*.
        // Ini mengatasi database yang masih menyimpan nama file lama/berbeda.
        $userId = (int) ($this->attributes['id'] ?? 0);

        if ($userId > 0) {
            $matches = glob(public_path("uploads/profiles/profile_{$userId}_*")) ?: [];
            $matches = array_values(array_filter($matches, 'is_file'));

            if ($matches !== []) {
                usort($matches, static function (string $a, string $b): int {
                    return filemtime($b) <=> filemtime($a);
                });

                return asset('uploads/profiles/' . basename($matches[0]));
            }
        }

        // Kalau memang belum ada file foto, tampilkan avatar kosong.
        return null;
    }
}
