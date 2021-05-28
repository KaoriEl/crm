<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Temp_task
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $title
 * @property string|null $text
 * @property int|null $journalist_id
 * @property string|null $draft_url
 * @property string|null $publication_url
 * @property bool $posting
 * @property bool $posting_to_vk
 * @property bool $posting_to_ok
 * @property bool $posting_to_fb
 * @property bool $posting_to_ig
 * @property bool $posting_to_y_dzen
 * @property bool $posting_to_y_street
 * @property bool $posting_to_yt
 * @property bool $posting_to_tt
 * @property bool $posting_to_tg
 * @property string|null $vk_post_url
 * @property string|null $tt_post_url
 * @property string|null $ok_post_url
 * @property string|null $fb_post_url
 * @property string|null $ig_post_url
 * @property string|null $y_dzen_post_url
 * @property string|null $y_street_post_url
 * @property string|null $yt_post_url
 * @property string|null $tg_post_url
 * @property bool $targeting
 * @property int $targeting_to_vk
 * @property int $targeting_to_ok
 * @property int $targeting_to_fb
 * @property int $targeting_to_ig
 * @property int $targeting_to_y_dzen
 * @property int $targeting_to_y_street
 * @property int $targeting_to_yt
 * @property int $targeting_to_tg
 * @property int $target_launched_in_vk
 * @property int $target_launched_in_ok
 * @property int $target_launched_in_fb
 * @property int $target_launched_in_ig
 * @property int $target_launched_in_tg
 * @property int $target_launched_in_yt
 * @property int $target_launched_in_y_street
 * @property int $target_launched_in_y_dzen
 * @property bool $seeding
 * @property int $seeding_to_vk
 * @property int $seeding_to_ok
 * @property int $seeding_to_fb
 * @property int $seeding_to_insta
 * @property int $seeding_to_y_dzen
 * @property int $seeding_to_y_street
 * @property int $seeding_to_yt
 * @property int $seeding_to_tg
 * @property string|null $seed_list_url
 * @property bool $commenting
 * @property bool $commented
 * @property bool|null $approved
 * @property string|null $comment_after_moderating
 * @property \Illuminate\Support\Carbon|null $archived_at
 * @property mixed|null $project_id
 * @property int|null $status_task
 * @property bool|null $on_moderate
 * @property mixed|null $telegram_id
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property mixed|null $posting_text
 * @property mixed|null $targeting_text
 * @property mixed|null $seeding_text
 * @property mixed|null $commenting_text
 * @property mixed|null $editor_id
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task query()
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereArchivedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereCommentAfterModerating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereCommented($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereCommenting($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereCommentingText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereDraftUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereEditorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereFbPostUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereIgPostUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereJournalistId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereOkPostUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereOnModerate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task wherePosting($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task wherePostingText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task wherePostingToFb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task wherePostingToIg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task wherePostingToOk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task wherePostingToTg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task wherePostingToVk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task wherePostingToYDzen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task wherePostingToYStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task wherePostingToYt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task wherePublicationUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereSeedListUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereSeeding($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereSeedingText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereSeedingToFb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereSeedingToInsta($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereSeedingToOk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereSeedingToTg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereSeedingToVk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereSeedingToYDzen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereSeedingToYStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereSeedingToYt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereStatusTask($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereTargetLaunchedInFb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereTargetLaunchedInIg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereTargetLaunchedInOk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereTargetLaunchedInTg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereTargetLaunchedInVk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereTargetLaunchedInYDzen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereTargetLaunchedInYStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereTargetLaunchedInYt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereTargeting($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereTargetingText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereTargetingToFb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereTargetingToIg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereTargetingToOk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereTargetingToTg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereTargetingToVk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereTargetingToYDzen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereTargetingToYStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereTargetingToYt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereTelegramId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereTgPostUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereVkPostUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereYDzenPostUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereYStreetPostUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Temp_task whereYtPostUrl($value)
 * @mixin \Eloquent
 */
class Temp_task extends Model
{
    protected $dates = ['archived_at', 'expires_at'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'check'      => 'boolean',
        'seeding'    => 'boolean',
        'posting'    => 'boolean',
        'approved'   => 'boolean',
        'commenting' => 'boolean',
        'commented'  => 'boolean',
        'targeting'  => 'boolean',
        'on_moderate' => 'boolean',

        'posting_to_vk' => 'boolean',
        'posting_to_ok' => 'boolean',
        'posting_to_fb' => 'boolean',
        'posting_to_ig' => 'boolean',
        'posting_to_y_dzen' => 'boolean',
        'posting_to_y_street' => 'boolean',
        'posting_to_yt' => 'boolean',
        'posting_to_tt' => 'boolean',
        'posting_to_tg' => 'boolean',

        'posting_text' => '',
        'targeting_text' => '',
        'seeding_text' => '',
        'commenting_text' => '',
        'project_id' => '',
        'project' => '',
        'telegram_id' => '',
        'editor_id' => '',
    ];

    protected $guarded = [];
}
