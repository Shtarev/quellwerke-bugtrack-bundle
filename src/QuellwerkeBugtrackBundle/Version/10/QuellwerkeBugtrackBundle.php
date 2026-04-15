<?php

namespace Quellwerke\QuellwerkeBugtrackBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;

class QuellwerkeBugtrackBundle extends AbstractPimcoreBundle
{
    public function getJsPaths(): array
    {
        return [
            '/bundles/quellwerkebugtrack/js/bugtrack.js'
        ];
    }
}