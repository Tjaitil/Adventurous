<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ConversationTracker
 *
 * @property int $id
 * @property string|null $conversation_option_value
 * @property int|null $user_id
 * @property string|null $current_index
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ConversationTracker newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ConversationTracker newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ConversationTracker query()
 * @method static \Illuminate\Database\Eloquent\Builder|ConversationTracker whereConversationOptionValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConversationTracker whereCurrentIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConversationTracker whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConversationTracker whereUserId($value)
 *
 * @mixin \Eloquent
 */
class ConversationTracker extends Model
{
}
