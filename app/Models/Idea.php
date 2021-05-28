<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use MediaUploader;
use App\Traits\Mediable;
use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Exceptions\MediaUpload\ConfigurationException;
use Plank\Mediable\Exceptions\MediaUpload\FileExistsException;
use Plank\Mediable\Exceptions\MediaUpload\FileNotFoundException;
use Plank\Mediable\Exceptions\MediaUpload\FileNotSupportedException;
use Plank\Mediable\Exceptions\MediaUpload\FileSizeException;
use Plank\Mediable\Exceptions\MediaUpload\ForbiddenException;
use Webklex\IMAP\Attachment;
use Webklex\IMAP\Message;

/**
 * App\Models\Idea
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $text
 * @property \Illuminate\Support\Carbon|null $archived_at
 * @property string|null $from
 * @property int|null $user_id
 * @property string|null $archive_comment
 * @property int|null $read_now
 * @property-read mixed $attachments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Media[] $media
 * @property-read int|null $media_count
 * @property-read \App\Models\User|null $user
 * @method static \Plank\Mediable\MediableCollection|static[] all($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|Idea archived()
 * @method static \Plank\Mediable\MediableCollection|static[] get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|Idea newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Idea newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Idea notArchived()
 * @method static \Illuminate\Database\Eloquent\Builder|Idea query()
 * @method static \Illuminate\Database\Eloquent\Builder|Idea whereArchiveComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Idea whereArchivedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Idea whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Idea whereFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Idea whereHasMedia($tags, $matchAll = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Idea whereHasMediaMatchAll($tags)
 * @method static \Illuminate\Database\Eloquent\Builder|Idea whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Idea whereReadNow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Idea whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Idea whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Idea whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Idea withMedia($tags = [], $matchAll = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Idea withMediaMatchAll($tags = [])
 * @mixin \Eloquent
 */
class Idea extends Model
{
    use Mediable;

    protected $fillable = ['text', 'from', 'user_id', 'read_now'];

    protected $dates = ['archived_at'];

    /**
     * Журналист, который ответственен за написание идеи.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Создает новую идею из сообщения электронной почты.
     *
     * @param Message $email
     * @return Idea
     * @throws ConfigurationException
     * @throws FileExistsException
     * @throws FileNotFoundException
     * @throws FileNotSupportedException
     * @throws FileSizeException
     * @throws ForbiddenException
     */
    public static function createFromEmail(Message $email)
    {
        $subject = $email->getSubject();

        $body = $email->getHtmlBody();
        $from = $email->getFrom()[0]->mail;

        /** @var Idea $idea */
        $idea = self::create([
            'text' => $subject . PHP_EOL . PHP_EOL . $body,
            'from' => $from,
        ]);

        $attachments = $email->getAttachments();

        /** @var Attachment $attachment */
        foreach ($attachments as $attachment) {
            rescue(function () use ($attachment, $idea) {
                $media = MediaUploader::fromString($attachment->getContent())
                    ->useHashForFilename()
                    ->toDestination('public', 'uploads')
                    ->upload();

                $idea->attachMedia($media, 'attachment');
            });
        }

        return $idea;
    }

    /**
     * Только архивные идеи.
     */
    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }

    /**
     * Только те идеи, которые не находятся в архиве.
     */
    public function scopeNotArchived($query)
    {
        return $query->whereNull('archived_at');
    }

    /**
     * Добавляет идею в архив.
     *
     * @return void
     */
    public function archive()
    {
        $this->archived_at = now();
        $this->archive_comment = request()->get('archive_comment');
        $this->save();

    }

    /**
     * Возвращает идею из архива.
     *
     * @return void
     */
    public function restore()
    {
        $this->archived_at = null;
        $this->save();
    }
}
