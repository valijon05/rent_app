<?php

namespace MoonShine\Buttons;

use MoonShine\ActionButtons\ActionButton;
use MoonShine\QueryTags\QueryTag;
use MoonShine\Resources\ModelResource;

final class QueryTagButton
{
    public static function for(ModelResource $resource, QueryTag $tag): ActionButton
    {
        return ActionButton::make(
            $tag->label(),
            $resource->indexPageUrl(['query-tag' => $tag->uri()])
        )
            ->showInLine()
            ->icon($tag->iconValue())
            ->canSee(fn (): bool => $tag->isSee(moonshineRequest()))
            ->customAttributes([
                'class' => 'query-tag-button',
                'x-data' => 'asyncLink(`btn-primary`, `' . $resource->listEventName() . '`)',
            ])
            ->when(
                $tag->isDefault(),
                fn (ActionButton $btn): ActionButton => $btn
                    ->customAttributes([
                        'class' => 'query-tag-default',
                    ])
            )
            ->when(
                $tag->isActive(),
                fn (ActionButton $btn): ActionButton => $btn
                    ->customAttributes([
                        'class' => 'btn-primary',
                        'href' => $resource->indexPageUrl(),
                    ])
            )
            ->when(
                $resource->isAsync(),
                fn (ActionButton $btn): ActionButton => $btn
                    ->onClick(
                        fn ($action): string => "queryTagRequest(`{$tag->uri()}`)",
                        'prevent'
                    )
            );
    }
}
