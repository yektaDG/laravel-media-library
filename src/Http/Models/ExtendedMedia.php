<?php

namespace YektaDG\Medialibrary\Http\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Plank\Mediable\Media;

class ExtendedMedia extends Media
{
    use HasFactory;

    const TYPE_IMAGE = 'image';
    const TYPE_IMAGE_VECTOR = 'vector';
    const TYPE_PDF = 'pdf';
    const TYPE_VIDEO = 'video';
    const TYPE_AUDIO = 'audio';
    const TYPE_ARCHIVE = 'archive';
    const TYPE_DOCUMENT = 'document';
    const TYPE_SPREADSHEET = 'spreadsheet';
    const TYPE_PRESENTATION = 'presentation';
    const TYPE_OTHER = 'other';
    const TYPE_ALL = 'all';
    const VARIANT_NAME_ORIGINAL = 'original';

    protected $table = 'media';

    protected $guarded = [
        'id',
        'disk',
        'directory',
        'filename',
        'extension',
        'size',
        'mime_type',
        'aggregate_type',
        'variant_name',
        'original_media_id',
        'alt',
        'width',
        'height',
    ];

    protected $casts = [
        'size' => 'int',
    ];

}
