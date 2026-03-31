<?php

namespace App\Services;

class StoreContext
{
    private const SESSION_KEY = 'active_store_id';

    public static function set(int $storeId): void
    {
        session([self::SESSION_KEY => $storeId]);
    }

    public static function getStoreId(): ?int
    {
        return session(self::SESSION_KEY);
    }

    public static function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public static function isSuperAdmin(): bool
    {
        return self::getStoreId() === null;
    }
}
