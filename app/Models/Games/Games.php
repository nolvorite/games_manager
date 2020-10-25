<?php

namespace App\Models\Games;

use App\Models\BaseModel;
use App\Models\ModelTrait;
//use Illuminate\Database\Eloquent\SoftDeletes;


class Games extends BaseModel
{
    use ModelTrait;      

    // protected $fillable = [
    //     'name',
    //     'slug',
    //     'publish_datetime',
    //     'content',
    //     'meta_title',
    //     'cannonical_link',
    //     'meta_keywords',
    //     'meta_description',
    //     'status',
    //     'featured_image',
    //     'created_by',
    // ];

    // protected $dates = [
    //     'publish_datetime',
    //     'created_at',
    //     'updated_at',
    // ];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('module.games.table');
    }
}
