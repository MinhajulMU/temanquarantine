<?php

namespace Telegram\Bot\Objects;

/**
 * Class Video.
 *
 *
 * @property string       $fileId     Unique identifier for this file.
 * @property int          $width      Video width as defined by sender.
 * @property int          $height     Video height as defined by sender.
 * @property int          $duration   Duration of the video in seconds as defined by sender.
 * @property PhotoSize    $thumb      (Optional). Video thumbnail.
 * @property string       $mimeType   (Optional). Mime type of a file as defined by sender.
 * @property int          $fileSize   (Optional). File size.
 */
class Video extends BaseObject
{
    /**
     * {@inheritdoc}
     */
    public function relations()
    {
        return [
            'thumb' => PhotoSize::class,
        ];
    }
}
