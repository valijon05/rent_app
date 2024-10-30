<?php

declare(strict_types=1);

namespace MoonShine\Buttons;

use Illuminate\Database\Eloquent\Model;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Fields\Relationships\HasMany;
use MoonShine\Resources\ModelResource;
use Throwable;

final class HasManyButton
{
    /**
     * @throws Throwable
     */
    public static function for(
        HasMany $field,
        bool $update = false,
        ?ActionButton $button = null,
    ): ActionButton {
        /** @var ModelResource $resource */
        $resource = $field->getResource();
        $parentResource = moonshineRequest()->getResource();
        $parentPage = moonshineRequest()->getPage();

        if (! $resource->formPage()) {
            return ActionButton::emptyHidden();
        }

        $action = static fn (?Model $data) => $parentResource->route(
            'relation.has-many-form',
            moonshineRequest()->getItemID(),
            [
                'pageUri' => $parentPage?->uriKey(),
                '_relation' => $field->getRelationName(),
                '_key' => $data?->getKey(),
            ]
        );

        if ($field->isWithoutModals()) {
            $action = static fn (?Model $data) => $resource->formPageUrl($data);
        }

        $authorize = $update
            ? fn (?Model $item): bool => ! is_null($item) && in_array('update', $resource->getActiveActions())
                && $resource->setItem($item)->can('update')
            : fn (?Model $item): bool => in_array('create', $resource->getActiveActions())
                && $resource->can('create');

        $actionButton = $button
            ? $button->setUrl($action)
            : ActionButton::make($update ? '' : __('moonshine::ui.add'), url: $action);

        $actionButton = $actionButton
            ->canSee($authorize)
            ->primary()
            ->icon($update ? 'heroicons.outline.pencil' : 'heroicons.outline.plus');

        if (! $field->isWithoutModals()) {
            $actionButton = $actionButton->inModal(
                title: fn (): array|string|null => __($update ? 'moonshine::ui.edit' : 'moonshine::ui.create'),
                content: '',
                async: true,
                wide: true,
            );
        }

        return $actionButton;
    }
}
