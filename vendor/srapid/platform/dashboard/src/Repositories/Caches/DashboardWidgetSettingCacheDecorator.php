<?php

namespace Srapid\Dashboard\Repositories\Caches;

use Srapid\Dashboard\Repositories\Interfaces\DashboardWidgetSettingInterface;
use Srapid\Support\Repositories\Caches\CacheAbstractDecorator;

class DashboardWidgetSettingCacheDecorator extends CacheAbstractDecorator implements DashboardWidgetSettingInterface
{
    /**
     * {@inheritDoc}
     */
    public function getListWidget()
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
