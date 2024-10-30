<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests\Resources;

use MoonShine\Exceptions\ResourceException;
use MoonShine\Http\Requests\MoonShineFormRequest;
use Throwable;

final class UpdateFormRequest extends MoonShineFormRequest
{
    /**
     * @throws Throwable
     * @throws ResourceException
     */
    public function authorize(): bool
    {
        $this->beforeResourceAuthorization();

        if (! in_array(
            'update',
            $this->getResource()?->getActiveActions() ?? [],
            true
        )) {
            return false;
        }

        return $this->getResource()?->can('update') ?? false;
    }

    public function rules(): array
    {
        return $this->getResource()?->rules(
            $this->getResource()?->getItemOrFail()
        ) ?? [];
    }
}
