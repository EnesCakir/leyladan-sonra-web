<?php

namespace App\Filters;

use App\Models\Child;

class ChildFilter extends Filter
{
    protected $filters = ['faculty_id', 'department', 'diagnosis', 'gift_state', 'search', 'until', 'download'];

    protected function faculty_id($faculty_id)
    {
        return $this->builder->where('faculty_id', $faculty_id);
    }

    protected function until($until)
    {
        return $until
            ? $this->builder->whereDate('until', '>=', now())
            : $this->builder->whereDate('until', '<', now());
    }

    protected function download()
    {
        $mapper = function ($item, $key) {
            return [
                'ID'           => $item->id,
                'Ad'           => $item->first_name,
                'Soyad'        => $item->last_name,
                'E-posta'      => $item->email,
                'Telefon'      => $item->mobile,
                'Fakülte'      => $item->faculty->name,
                'Kayıt Tarihi' => $item->created_at,
            ];
        };
        Child::download($this->builder, $mapper);
    }
}
