<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ConversationTracker
 *
 * @property int $id
 * @property string|null $conversation_option_value
 * @property int $user_id
 * @property string|null $current_index
 * @property \Carbon\CarbonInterface|null $updated_at
 * @property \Carbon\CarbonInterface|null $created_at
 * @method static \Database\Factories\ConversationTrackerFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ConversationTracker newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ConversationTracker newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ConversationTracker query()
 * @method static \Illuminate\Database\Eloquent\Builder|ConversationTracker whereConversationOptionValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConversationTracker whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConversationTracker whereCurrentIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConversationTracker whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConversationTracker whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConversationTracker whereUserId($value)
 * @mixin \Eloquent
 */
class ConversationTracker extends Model
{
    /**
     * @use HasFactory<\Database\Factories\ConversationTrackerFactory>
     */
    use HasFactory;
}
