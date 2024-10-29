<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

use Illuminate\Database\Eloquent\Relations\Relation;
use MoonShine\Fields\Relationships\HasMany;
use MoonShine\Fields\Text;
use MoonShine\Resources\ModelResource;
use MoonShine\Fields\ID;
use MoonShine\Fields\Field;
use MoonShine\Components\MoonShineComponent;

/**
 * @extends ModelResource<User>
 */
class UserResource extends ModelResource
{
    protected string $model = User::class;

    protected string $title = 'Users';
    public string $column ="name";


    /**
     * @return list<Field>
     */
    public function indexFields(): array
    {
        return [
            ID::make()->sortable(),

            Text::make("name")->sortable(),
              Text::make("email")->sortable(),
             Text::make("position")->sortable(),
             Text::make("gender")->sortable(),
              Text::make("phone")->sortable(),

        ];
    }

    /**
     * @return list<MoonShineComponent|Field>
     */
    public function formFields(): array
    {
        return [
            ID::make()->sortable(),
            Text::make("name")->sortable(),
             Text::make("email")->sortable(),
             Text::make("position")->sortable(),
             Text::make("gender")->sortable(),
              Text::make("phone")->sortable()
        ];
    }

    /**
     * @return list<Field>
     */
    public function detailFields(): array
    {
        return [
            ID::make()->sortable(),
            Text::make('name')->sortable(),
            Text::make("email")->sortable(),
            Text::make("position")->sortable(),
            Text::make("gender")->sortable(),
            Text::make("phone")->sortable()
        ];
    }

    /**
     * @param User $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(Model $item): array
    {
        return [];
    }

}
