<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static static create(array $attributes = [])
 * @method static bool exists()
 * @method static static|null find(int $id)
 * @method static static findOrFail(int $id)
 * @method static static|null first()
 * @method static static firstOrFail()
 * @method static static[]|Collection get($columns = ['*'])
 * @method static static|Builder join($table, $first, $operator = null, $second = null, $type = 'inner', $where = false)
 * @method static mixed max($column)
 * @method static static orderBy(string $sort, $order = null)
 * @method static static select(...$columns)
 * @method static static|Builder where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static static|Builder whereExists($callback, $boolean = 'and', $not = false)
 * @method static static|Builder whereId(int $id)
 * @method static static|Builder whereIn($column, $values, $boolean = 'and', $not = false)
 * @method static static|Builder whereNotNull($column)
 * @method static static|Builder whereNull($column)
 * @method static static|Builder with($relations)
 * @method static static|Builder setEagerLoads(array $array)
 *
 * @property int $id
 *
 * @property Carbon|string $created_at
 * @property Carbon|string $updated_at
 */
class BriskLimbs extends Model
{
    /**
     * Indicates whether attributes are snake cased on arrays.
     *
     * @var bool
     */
    public static $snakeAttributes = false;
}
