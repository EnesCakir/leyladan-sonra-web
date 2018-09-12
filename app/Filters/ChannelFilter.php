<?php

namespace App\Filters;

use App\Models\Channel;

class ChannelFilter extends Filter
{
    protected $filters = ['category', 'search', 'download'];

    protected function download()
    {
        $mapper = function ($item) {
            $item->logo = $item->logo_url;
            return $item;
        };

        Channel::download($this->builder, $mapper);
    }
}