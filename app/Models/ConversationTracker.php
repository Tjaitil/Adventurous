<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ConversationTracker
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $current_index
 * @property array<array-key, mixed>|null $selected_option_values
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $created_at
 * @method static \Database\Factories\ConversationTrackerFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationTracker newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationTracker newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationTracker query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationTracker whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationTracker whereCurrentIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationTracker whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationTracker whereSelectedOptionValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationTracker whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationTracker whereUserId($value)
 * @mixin \Eloquent
 */
class ConversationTracker extends Model
{
    /**
     * @use HasFactory<\Database\Factories\ConversationTrackerFactory>
     */
    use HasFactory;

    protected $guarded = [
        'user_id',
        'id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'selected_option_values' => 'array',
        ];
    }
}
