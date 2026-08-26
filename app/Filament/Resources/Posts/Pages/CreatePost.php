<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Enums\PostStatus;
use App\Filament\Resources\Posts\PostResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        // Author biasa tidak boleh set status sendiri lewat form tersembunyi/tampering
        if (! auth()->user()->canEditBlog()) {
            $data['status'] = PostStatus::DRAFT->value;
        }

        return $data;
    }
}
