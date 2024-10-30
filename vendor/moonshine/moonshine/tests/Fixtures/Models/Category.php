<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use MoonShine\Tests\Fixtures\Factories\CategoryFactory;
use MoonShine\Tests\Fixtures\Models\Traits\MorphRelationTrait;
use MoonShine\Tests\Fixtures\Models\Traits\UserBelongsToTrait;

class Category extends Model
{
    use UserBelongsToTrait;

    use MorphRelationTrait;

    use HasFactory;

    protected $fillable = [
        'name',
        'content',
        'category_id',
        'moonshine_user_id',
        'public_at',
        'created_at',
        'updated_at',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'category_id');
    }

    public function cover(): HasOne
    {
        return $this->hasOne(Cover::class, 'category_id');
    }

    protected static function newFactory()
    {
        return new CategoryFactory();
    }
}
