<?php

namespace App\Observers;

use App\Models\Category;
use Illuminate\Support\Str;

class CategoryObserver
{
    public function saving(Category $category): void
    {
        if (empty($category->meta_title)) {
            $category->meta_title = Str::limit($category->name . ' | Colors S.r.l.', 60);
        }

        if (empty($category->meta_description)) {
            $raw = strip_tags($category->description ?? '');
            $category->meta_description = Str::limit($raw, 160);
        }
    }
}
