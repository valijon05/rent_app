<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Closure;
use MoonShine\Support\Condition;

trait ShowOrHide
{
    public bool $showOnCreateForm = true;

    public bool $showOnUpdateForm = true;

    public bool $useOnImport = false;

    protected bool $showOnIndex = true;

    protected bool $showOnExport = false;

    protected bool $showOnForm = true;

    protected bool $showOnDetail = true;

    /**
     * Set field as visible on index page, based on condition
     *
     * @return $this
     */
    public function showOnIndex(mixed $condition = null): static
    {
        $this->showOnIndex = Condition::boolean($condition, true);

        return $this;
    }

    /**
     * Set field as hidden on index page, based on condition
     *
     * @return $this
     */
    public function hideOnIndex(mixed $condition = true): static
    {
        $this->showOnIndex = ! value($condition);

        return $this;
    }

    /**
     * Set field as visible on create/edit page, based on condition
     *
     * @return $this
     */
    public function showOnForm(mixed $condition = null): static
    {
        $this->showOnForm = Condition::boolean($condition, true);

        return $this;
    }

    /**
     * Set field as hidden on create/edit page, based on condition
     *
     * @return $this
     */
    public function hideOnForm(mixed $condition = true): static
    {
        $this->showOnForm = ! value($condition);

        return $this;
    }

    /**
     * Set field as visible on show page, based on condition
     *
     * @return $this
     */
    public function showOnDetail(mixed $condition = null): static
    {
        $this->showOnDetail = Condition::boolean($condition, true);

        return $this;
    }

    /**
     * Set field as hidden on show page, based on condition
     *
     * @return $this
     */
    public function hideOnDetail(mixed $condition = true): static
    {
        $this->showOnDetail = ! value($condition);

        return $this;
    }

    /**
     * Set field as visible in export report, based on condition
     *
     * @return $this
     */
    public function showOnExport(mixed $condition = null, ?Closure $modifyRawValue = null): static
    {
        $this->showOnExport = Condition::boolean($condition, true);

        if (! is_null($modifyRawValue)) {
            $this->modifyRawValue($modifyRawValue);
        }

        return $this;
    }

    /**
     * Set field as hidden in export report, based on condition
     *
     * @return $this
     */
    public function hideOnExport(mixed $condition = true): static
    {
        $this->showOnExport = ! value($condition);

        return $this;
    }

    /**
     * Set field as show on create page, based on condition
     *
     * @return $this
     */
    public function showOnCreate(mixed $condition = null): static
    {
        $this->showOnCreateForm = Condition::boolean($condition, true);
        $this->showOnForm = $this->isNowOnCreateForm() && $this->showOnCreateForm;

        return $this;
    }

    /**
     * Set field as hidden on create page, based on condition
     *
     * @return $this
     */
    public function hideOnCreate(mixed $condition = true): static
    {
        $this->showOnCreateForm = ! value($condition);
        $this->showOnForm = $this->isNowOnUpdateForm() || $this->showOnCreateForm;

        return $this;
    }

    /**
     * Set field as show on update page, based on condition
     *
     * @return $this
     */
    public function showOnUpdate(mixed $condition = null): static
    {
        $this->showOnUpdateForm = Condition::boolean($condition, true);
        $this->showOnForm = $this->isNowOnUpdateForm() && $this->showOnUpdateForm;

        return $this;
    }

    /**
     * Set field as hidden on update page, based on condition
     *
     * @return $this
     */
    public function hideOnUpdate(mixed $condition = true): static
    {
        $this->showOnUpdateForm = ! value($condition);
        $this->showOnForm = $this->isNowOnCreateForm() || $this->showOnUpdateForm;

        return $this;
    }

    /**
     * Hide entity from all pages/forms
     *
     * You can use Field::make()->hideOnAll()->showOnIndex() for show field only on index page
     */
    public function hideOnAll(mixed $condition = true): static
    {
        $condition = ! value($condition);

        $this->showOnCreateForm = $condition;
        $this->showOnUpdateForm = $condition;
        $this->showOnIndex = $condition;
        $this->showOnExport = $condition;
        $this->showOnForm = $condition;
        $this->showOnDetail = $condition;

        return $this;
    }

    /**
     * Set field as used on import, based on condition
     *
     * @return $this
     */
    public function useOnImport(mixed $condition = null, ?Closure $fromRaw = null): static
    {
        $this->useOnImport = Condition::boolean($condition, true);

        if (! is_null($fromRaw)) {
            $this->fromRaw($fromRaw);
        }

        return $this;
    }

    public function isOnImport(): bool
    {
        return $this->useOnImport;
    }

    public function isOnIndex(): bool
    {
        return $this->showOnIndex;
    }

    public function isOnForm(): bool
    {
        return $this->showOnForm;
    }

    public function isOnExport(): bool
    {
        return $this->showOnExport;
    }

    public function isOnDetail(): bool
    {
        return $this->showOnDetail;
    }
}
