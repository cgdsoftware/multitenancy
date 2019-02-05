<?php

namespace LaravelEnso\Multitenancy\app\Classes;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use LaravelEnso\Companies\app\Models\Company;

class Tenant
{
    private static $tenantPrefix;

    public static function set(Company $company)
    {
        config([
            'database.connections.'.Connections::Tenant.'.database' => self::tenantPrefix().$company->id,
        ]);

        DB::purge(Connections::Tenant);

        DB::reconnect(Connections::Tenant);
    }

    public static function get()
    {
        return Company::find(self::tenantId());
    }

    private static function tenantId()
    {
        return (int) Str::replaceFirst(
            Connections::Tenant, '', self::tenantDatabase()
        );
    }

    public static function tenantDatabase()
    {
        return config('database.connections.'.Connections::Tenant.'.database');
    }

    private static function tenantPrefix()
    {
        if (! isset(self::$tenantPrefix)) {
            self::$tenantPrefix = self::tenantDatabase();
        }

        return self::$tenantPrefix;
    }
}
