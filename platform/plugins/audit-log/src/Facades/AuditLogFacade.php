<?php

namespace Srapid\AuditLog\Facades;

use Srapid\AuditLog\AuditLog;
use Illuminate\Support\Facades\Facade;

class AuditLogFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return AuditLog::class;
    }
}
