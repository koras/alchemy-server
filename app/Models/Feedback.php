<?php

namespace App\Models;

use App\Contracts\Models\FeedbackInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Feedback extends Model implements FeedbackInterface
{
    use HasFactory;

    protected $table = 'feedback';

    protected $with = [];

    protected $attributes = [];

    protected $fillable = [
        'level_id',
        'user_id',
        'email',
        'message',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function send(array $params)
    {
        return self::create($params);
    }
}
