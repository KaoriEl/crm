<?php

namespace App\Models;

use App\Enums\PostTargetStatusesEnum;
use App\Models\User;
use App\Models\Project;
use App\Traits\Mediable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Symfony\Component\Mime\Message;

/**
 * App\Models\Post
 *
 * @property int $id * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $title
 * @property string $text
 * @property int|null $journalist_id
 * @property string|null $draft_url
 * @property string|null $publication_url
 * @property bool $posting
 * @property bool $posting_to_vk
 * @property bool $posting_to_ok
 * @property bool $posting_to_fb
 * @property bool $posting_to_ig
 * @property bool $posting_to_tg
 * @property bool $posting_to_tt
 * @property bool $posting_to_yt
 * @property bool $posting_to_y_street
 * @property bool $posting_to_y_dzen
 * @property string|null $vk_post_url
 * @property string|null $ok_post_url
 * @property string|null $fb_post_url
 * @property string|null $ig_post_url
 * @property string|null $tg_post_url
 * @property string|null $tt_post_url
 * @property string|null $yt_post_url
 * @property string|null $y_street_post_url
 * @property string|null $y_dzen_post_url
 * @property bool $targeting
 * @property int $targeting_to_vk
 * @property int $targeting_to_ok
 * @property int $targeting_to_fb
 * @property int $targeting_to_ig
 * @property int $targeting_to_tg
 * @property int $targeting_to_yt
 * @property int $targeting_to_y_street
 * @property int $targeting_to_y_dzen
 * @property int $targeted_to_vk
 * @property int $targeted_to_ok
 * @property int $targeted_to_fb
 * @property int $targeted_to_ig
 * @property int $targeted_to_tg
 * @property int $targeted_to_yt
 * @property int $targeted_to_y_street
 * @property int $targeted_to_y_dzen
 * @property bool $seeding
 * @property int $seeding_to_vk
 * @property int $seeding_to_ok
 * @property int $seeding_to_fb
 * @property int $seeding_to_insta
 * @property int $seeding_to_tg
 * @property int $seeding_to_yt
 * @property int $seeding_to_y_street
 * @property int $seeding_to_y_dzen
 * @property string|null $seed_list_url
 * @property bool $commenting
 * @property bool $commented
 * @property bool|null $approved
 * @property string|null $comment_after_moderating
 * @property \Illuminate\Support\Carbon|null $archived_at
 * @property \Illuminate\Support\Carbon $expires_at
 * @property int $target_launched_in_vk
 * @property int $target_launched_in_ok
 * @property int $target_launched_in_fb
 * @property int $target_launched_in_ig
 * @property int $target_launched_in_y_dzen
 * @property int $target_launched_in_y_street
 * @property int $target_launched_in_yt
 * @property int $target_launched_in_tg
 * @property mixed|null $posting_text
 * @property mixed|null $targeting_text
 * @property mixed|null $seeding_text
 * @property mixed|null $commenting_text
 * @property mixed|null $project_id
 * @property int|null $status_task
 * @property bool|null $on_moderate
 * @property mixed|null $editor_id
 * @property mixed|null $smm_id
 * @property mixed|null $target_id
 * @property mixed|null $seeder_id
 * @property mixed|null $commentator_id
 * @property int $cron_has_update
 * @property int|null $status_id
 * @property mixed|null $default_screenshot_url
 * @property mixed|null $vk_screenshot
 * @property mixed|null $ok_screenshot
 * @property mixed|null $fb_screenshot
 * @property mixed|null $ig_screenshot
 * @property mixed|null $y_dzen_screenshot
 * @property mixed|null $y_street_screenshot
 * @property mixed|null $yt_screenshot
 * @property mixed|null $tg_screenshot
 * @property int $target_not_pass_moderation_in_vk
 * @property int $target_not_pass_moderation_in_ok
 * @property int $target_not_pass_moderation_in_fb
 * @property int $target_not_pass_moderation_in_ig
 * @property int $target_not_pass_moderation_in_yt
 * @property int $target_not_pass_moderation_in_tg
 * @property int $target_not_pass_moderation_in_y_dzen
 * @property int $target_not_pass_moderation_in_y_street
 * @property-read User|null $commentator
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read User|null $editor
 * @property-read mixed $attachments
 * @property-read string $date_offset
 * @property-read User|null $journalist
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Media[] $media
 * @property-read int|null $media_count
 * @property-read Project|null $project
 * @property-read User|null $seeder
 * @property-read User|null $smm
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SocialNetwork[] $socialNetworks
 * @property-read int|null $social_networks_count
 * @property-read \App\Models\StatusTask|null $status
 * @property-read User|null $target
 * @method static \Plank\Mediable\MediableCollection|static[] all($columns = ['*'])
 * @method static \Plank\Mediable\MediableCollection|static[] get($columns = ['*'])
 * @method static Builder|Post newModelQuery()
 * @method static Builder|Post newQuery()
 * @method static Builder|Post onlyArchived()
 * @method static Builder|Post PublishedStat()
 * @method static Builder|Post query()
 * @method static Builder|Post skipArchived()
 * @method static Builder|Post skipPublished()
 * @method static Builder|Post whereApproved($value)
 * @method static Builder|Post whereArchivedAt($value)
 * @method static Builder|Post whereCommentAfterModerating($value)
 * @method static Builder|Post whereCommentatorId($value)
 * @method static Builder|Post whereCommented($value)
 * @method static Builder|Post whereCommenting($value)
 * @method static Builder|Post whereCommentingText($value)
 * @method static Builder|Post whereCreatedAt($value)
 * @method static Builder|Post whereCronHasUpdate($value)
 * @method static Builder|Post whereDefaultScreenshotUrl($value)
 * @method static Builder|Post whereDraftUrl($value)
 * @method static Builder|Post whereEditorId($value)
 * @method static Builder|Post whereExpiresAt($value)
 * @method static Builder|Post whereFbPostUrl($value)
 * @method static Builder|Post whereFbScreenshot($value)
 * @method static Builder|Post whereHasMedia($tags, $matchAll = false)
 * @method static Builder|Post whereHasMediaMatchAll($tags)
 * @method static Builder|Post whereId($value)
 * @method static Builder|Post whereIgPostUrl($value)
 * @method static Builder|Post whereIgScreenshot($value)
 * @method static Builder|Post whereJournalistId($value)
 * @method static Builder|Post whereOkPostUrl($value)
 * @method static Builder|Post whereOkScreenshot($value)
 * @method static Builder|Post whereOnModerate($value)
 * @method static Builder|Post wherePosting($value)
 * @method static Builder|Post wherePostingText($value)
 * @method static Builder|Post wherePostingToFb($value)
 * @method static Builder|Post wherePostingToIg($value)
 * @method static Builder|Post wherePostingToOk($value)
 * @method static Builder|Post wherePostingToTg($value)
 * @method static Builder|Post wherePostingToVk($value)
 * @method static Builder|Post wherePostingToYDzen($value)
 * @method static Builder|Post wherePostingToYStreet($value)
 * @method static Builder|Post wherePostingToYt($value)
 * @method static Builder|Post whereProjectId($value)
 * @method static Builder|Post wherePublicationUrl($value)
 * @method static Builder|Post whereSeedListUrl($value)
 * @method static Builder|Post whereSeederId($value)
 * @method static Builder|Post whereSeeding($value)
 * @method static Builder|Post whereSeedingText($value)
 * @method static Builder|Post whereSeedingToFb($value)
 * @method static Builder|Post whereSeedingToInsta($value)
 * @method static Builder|Post whereSeedingToOk($value)
 * @method static Builder|Post whereSeedingToTg($value)
 * @method static Builder|Post whereSeedingToVk($value)
 * @method static Builder|Post whereSeedingToYDzen($value)
 * @method static Builder|Post whereSeedingToYStreet($value)
 * @method static Builder|Post whereSeedingToYt($value)
 * @method static Builder|Post whereSmmId($value)
 * @method static Builder|Post whereStatusId($value)
 * @method static Builder|Post whereStatusTask($value)
 * @method static Builder|Post whereTargetId($value)
 * @method static Builder|Post whereTargetLaunchedInFb($value)
 * @method static Builder|Post whereTargetLaunchedInIg($value)
 * @method static Builder|Post whereTargetLaunchedInOk($value)
 * @method static Builder|Post whereTargetLaunchedInTg($value)
 * @method static Builder|Post whereTargetLaunchedInVk($value)
 * @method static Builder|Post whereTargetLaunchedInYDzen($value)
 * @method static Builder|Post whereTargetLaunchedInYStreet($value)
 * @method static Builder|Post whereTargetLaunchedInYt($value)
 * @method static Builder|Post whereTargetNotPassModerationInFb($value)
 * @method static Builder|Post whereTargetNotPassModerationInIg($value)
 * @method static Builder|Post whereTargetNotPassModerationInOk($value)
 * @method static Builder|Post whereTargetNotPassModerationInTg($value)
 * @method static Builder|Post whereTargetNotPassModerationInVk($value)
 * @method static Builder|Post whereTargetNotPassModerationInYDzen($value)
 * @method static Builder|Post whereTargetNotPassModerationInYStreet($value)
 * @method static Builder|Post whereTargetNotPassModerationInYt($value)
 * @method static Builder|Post whereTargetedToFb($value)
 * @method static Builder|Post whereTargetedToIg($value)
 * @method static Builder|Post whereTargetedToOk($value)
 * @method static Builder|Post whereTargetedToTg($value)
 * @method static Builder|Post whereTargetedToVk($value)
 * @method static Builder|Post whereTargetedToYDzen($value)
 * @method static Builder|Post whereTargetedToYStreet($value)
 * @method static Builder|Post whereTargetedToYt($value)
 * @method static Builder|Post whereTargeting($value)
 * @method static Builder|Post whereTargetingText($value)
 * @method static Builder|Post whereTargetingToFb($value)
 * @method static Builder|Post whereTargetingToIg($value)
 * @method static Builder|Post whereTargetingToOk($value)
 * @method static Builder|Post whereTargetingToTg($value)
 * @method static Builder|Post whereTargetingToVk($value)
 * @method static Builder|Post whereTargetingToYDzen($value)
 * @method static Builder|Post whereTargetingToYStreet($value)
 * @method static Builder|Post whereTargetingToYt($value)
 * @method static Builder|Post whereText($value)
 * @method static Builder|Post whereTgPostUrl($value)
 * @method static Builder|Post whereTgScreenshot($value)
 * @method static Builder|Post whereTitle($value)
 * @method static Builder|Post whereUpdatedAt($value)
 * @method static Builder|Post whereVkPostUrl($value)
 * @method static Builder|Post whereVkScreenshot($value)
 * @method static Builder|Post whereYDzenPostUrl($value)
 * @method static Builder|Post whereYDzenScreenshot($value)
 * @method static Builder|Post whereYStreetPostUrl($value)
 * @method static Builder|Post whereYStreetScreenshot($value)
 * @method static Builder|Post whereYtPostUrl($value)
 * @method static Builder|Post whereYtScreenshot($value)
 * @method static Builder|Post withMedia($tags = [], $matchAll = false)
 * @method static Builder|Post withMediaMatchAll($tags = [])
 * @mixin \Eloquent
 * @property \Illuminate\Support\Carbon|null $publication_url_updated_at
 * @method static Builder|Post wherePublicationUrlUpdatedAt($value)
 */
class Post extends Model
{
    use Mediable;

    /**
     * The attributes that should be cast to dates.
     *
     * @var array
     */
    protected $dates = ['archived_at', 'expires_at', 'publication_url_updated_at'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'check' => 'boolean',
        'seeding' => 'boolean',
        'posting' => 'boolean',
        'approved' => 'boolean',
        'commenting' => 'boolean',
        'commented' => 'boolean',
        'targeting' => 'boolean',
        'on_moderate' => 'boolean',

        'posting_to_vk' => 'boolean',
        'posting_to_ok' => 'boolean',
        'posting_to_fb' => 'boolean',
        'posting_to_ig' => 'boolean',
        'posting_to_y_dzen' => 'boolean',
        'posting_to_y_street' => 'boolean',
        'posting_to_yt' => 'boolean',
        'posting_to_tg' => 'boolean',
        'posting_to_tt' => 'boolean',

        'posting_text' => '',
        'targeting_text' => '',
        'seeding_text' => '',
        'commenting_text' => '',
        'project_id' => '',
        'project' => '',
        'editor_id' => '',
        'smm_id' => '',
        'seeder_id' => '',
        'commentator_id' => '',
        'target_id' => '',
        'default_screenshot_url' => '',
        'vk_screenshot' => '',
        'ok_screenshot' => '',
        'fb_screenshot' => '',
        'ig_screenshot' => '',
        'y_dzen_screenshot' => '',
        'y_street_screenshot' => '',
        'yt_screenshot' => '',
        'tg_screenshot' => '',

    ];

    protected $guarded = ['telegram_id', 'id'];


    /**
     * Редактор, который поставил данную задачу
     *
     * @return BelongsTo
     */
    public function editor()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Статистика по постам
     *
     * @return BelongsTo
     */
    public function statisticSocialNetwork()
    {
        return $this->belongsTo(StatisticSocialNetwork::class);
    }


    /**
     * Статус задачи
     *
     * @return BelongsTo
     */
    public function status()
    {
        return $this->belongsTo(StatusTask::class);
    }


    /**
     * Сммщик, который выполняет задачу
     *
     * @return BelongsTo
     */
    public function smm()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Комментатор, который выполняет задачу
     *
     * @return BelongsTo
     */
    public function commentator()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Таргетщик, который выполняет задачу
     *
     * @return BelongsTo
     */
    public function target()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Посевщик, который выполняет задачу
     *
     * @return BelongsTo
     */
    public function seeder()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Журналист, который ответственен за написание поста.
     *
     * @return BelongsTo
     */
    public function journalist()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Проект.
     *
     * @return BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }


    /**
     * Есть ли у поста черновик.
     *
     * @return boolean
     */
    public function hasDraft()
    {
        return $this->draft_url !== null;
    }


    /**
     * Есть ли у поста исполнитель.
     *
     * @return boolean
     */
    public function hasJournalist()
    {
        return $this->journalist_id !== null;
    }

    /**
     * Нуждается ли пост в модерации.
     *
     * @return boolean
     */
    public function needModeration()
    {
        return $this->hasDraft() && $this->approved === null;
    }

    /**
     * Определяет, опубликован ли пост.
     *
     * @return boolean
     */
    public function published()
    {
        return $this->publication_url !== null;
    }

    public function commented()
    {
        return $this->commented !== null;
    }

    /**
     * Определяет, выполнена ли задача.
     *
     * @return boolean
     */
    public function done()
    {

        if (!$this->hasJournalist()) {
            return false;
        }

        if (!$this->draft_url) {
            return false;
        }

        if (!$this->approved) {
            return false;
        }

        if (!$this->publication_url) {
            return false;
        }

        $smmLinks = SmmLink::where('post_id', $this->id)->get();

        if ($this->posting) {
            if ($this->posting_to_vk && $smmLinks->where('social_network_id', 1)->count() == 0) {
                return false;
            }

            if ($this->posting_to_ok && $smmLinks->where('social_network_id', 2)->count() == 0) {
                return false;
            }

            if ($this->posting_to_fb && $smmLinks->where('social_network_id', 3)->count() == 0) {
                return false;
            }

            if ($this->posting_to_ig && $smmLinks->where('social_network_id', 4)->count() == 0) {
                return false;
            }

            if ($this->posting_to_y_dzen && $smmLinks->where('social_network_id', 5)->count() == 0) {
                return false;
            }

            if ($this->posting_to_y_street && $smmLinks->where('social_network_id', 6)->count() == 0) {
                return false;
            }

            if ($this->posting_to_yt && $smmLinks->where('social_network_id', 7)->count() == 0) {
                return false;
            }

            if ($this->posting_to_tg &&  $smmLinks->where('social_network_id', 8)->count() == 0) {
                return false;
            }
            if ($this->posting_to_tt && $smmLinks->where('social_network_id', 9)->count() == 0) {
                return false;
            }

        }

//        Проверка на то что весь таргет запущен и прошел модерацию
        if ($this->targeting) {
            foreach ($this->socialNetworks as $socialNetworks) {
                if ($socialNetworks->pivot->status !== PostTargetStatusesEnum::SUCCESSFUL_MODERATED_STATUS) {
                    return false;
                }
            }
        }

        if ($this->seeding && !$this->seed_list_url) {
            return false;
        }

        if ($this->commenting && !$this->commented) {
            return false;
        }

        return true;
    }

    /**
     * Перемещает пост в архив.
     */
    public function archive()
    {
        $this->archived_at = now();
        $this->save();
    }

    /**
     * Возвращает пост из архива.
     */
    public function unarchive()
    {
        $this->archived_at = null;
        $this->save();
    }

    /**
     * Опредляет, находится ли пост в архиве.
     *
     * @return bool
     */
    public function archived()
    {
        return $this->archived_at !== null;
    }

    /**
     * Определяет, просрочен ли пост.
     *
     * @return bool
     */
    public function expired()
    {
        return $this->expires_at <= now();
    }


    public function nameJournalist($id)
    {
        $user = User::find($id)->first()->value('name');

        return $user;
    }

    /**
     * Определяет, прикреплен ли к посту скриншот комментария.
     *
     * @return bool
     */
    public function hasCommentScreenshot()
    {
        return !$this->getMedia('comment_screenshot')->isEmpty();
    }

    /**
     * Определяет, прикреплен ли к посту скриншот комментария VK.
     *
     * @return bool
     */
    public function hasCommentScreenshotVK()
    {
        return !$this->getMedia('vk_screenshot')->isEmpty();
    }

    /**
     * Определяет, прикреплен ли к посту скриншот комментария OK.
     *
     * @return bool
     */
    public function hasCommentScreenshotOK()
    {
        return !$this->getMedia('ok_screenshot')->isEmpty();
    }

    /**
     * Определяет, прикреплен ли к посту скриншот комментария FB.
     *
     * @return bool
     */
    public function hasCommentScreenshotFB()
    {
        return !$this->getMedia('fb_screenshot')->isEmpty();
    }

    /**
     * Определяет, прикреплен ли к посту скриншот комментария INSTA.
     *
     * @return bool
     */
    public function hasCommentScreenshotINSTA()
    {
        return !$this->getMedia('insta_screenshot')->isEmpty();
    }

    public function hasCommentScreenshotYdzen()
    {
        return !$this->getMedia('y_dzen_screenshot')->isEmpty();
    }

    public function hasCommentScreenshotYstreet()
    {
        return !$this->getMedia('y_street_screenshot')->isEmpty();
    }

    public function hasCommentScreenshotYoutube()
    {
        return !$this->getMedia('yt_screenshot')->isEmpty();
    }

    public function hasCommentScreenshotTelegram()
    {
        return !$this->getMedia('tg_screenshot')->isEmpty();
    }

    /**
     * Все посты, кроме архивных.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeSkipArchived(Builder $query)
    {
        return $query->whereNull('archived_at');
    }

    /**
     * Только опубликованные посты..
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopePublished(Builder $query)
    {
        return $query->whereNotNull('publication_url');
    }
    /**
     * Только посты которые есть в статистике
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopePublishedStat(Builder $query)
    {
        return $query->whereNotNull('publication_url_updated_at');
    }



    /**
     * Только неопубликованные посты.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeSkipPublished(Builder $query)
    {
        return $query->whereNull('publication_url');
    }

    /**
     * Только архивные посты.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeOnlyArchived(Builder $query)
    {
        return $query->whereNotNull('archived_at');
    }

    /**
     * Оставшееся время или перерасход.
     *
     * @return string
     */
    public function getDateOffsetAttribute()
    {
        $now = now();

        $diffInterval = $this->expires_at->diff($now);
        $diffInHours = $this->expires_at->diffInHours($now);

        return sprintf('%02dч. %sм.', $diffInHours, $diffInterval->format('%I'));
    }

    /**
     * Получить комментарии для задачи.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function addingCopyTime()
    {
        return $this->expires_at + 900;
    }

    public function socialNetworks(): BelongsToMany
    {
        return $this->belongsToMany(SocialNetwork::class, 'post_social_network', 'post_id', 'social_network_id')
            ->withPivot('price', 'status');
    }

    public function seedLinks() : BelongsToMany {
        return $this->belongsToMany(SocialNetwork::class, 'commercial_seed_links', 'post_id', 'social_network_id')->withPivot('link');
    }

    public function smmLinks() : BelongsToMany {
        return $this->belongsToMany(SocialNetwork::class, 'post_smm_links', 'post_id', 'social_network_id')->withPivot('link');
    }

    public function getSMMLinks() {
        return SmmLink::where('post_id', $this->id)->get();
    }

    public function getSeedLinks() {
        return ModelsSeedLinks::where('post_id', $this->id)->get();
    }

    public function smmLinksPost() {
         return SmmLink::where('post_id', $this->id);

    }

}
