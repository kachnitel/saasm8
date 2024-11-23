<?php

namespace App\Twig\Components\Traits;

use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

/**
 * This trait listens for new time entries and increments a counter
 * to force the component to re-render.
 *
 * @see https://symfony.com/bundles/ux-live-component/current/index.html#listen-to-events
 */
trait NewEntryListenerTrait
{
    #[LiveProp]
    public int $newEntryCount = 0;

    #[LiveListener('time-entry:saved')]
    public function onTimeEntrySaved(): void
    {
        // This method is called when a new time entry is saved
        ++$this->newEntryCount;
    }
}
