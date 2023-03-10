<?php

use Illuminate\Validation\ValidationException;

if (! function_exists('mlPictureValidation')) {
    /**
     * @throws ValidationException
     */
    function mlPictureValidation($file, $name = 'picture', $size = 2097152)
    {
        $types = array("png", "jpg", "jpeg", "gif", 'webp', 'svg');
        if (!in_array(mlGetFileType($file->getClientOriginalName()), $types))
            throw ValidationException::withMessages([$name => 'فایل از نوع قابل قبولی نمیباشد']);

        if ($file->getSize() > $size)
            throw ValidationException::withMessages([$name => 'حجم فایل از ' . $size / 1024 / 1024 . ' مگابایت بیشتر است']);
    }
}


if (! function_exists('mlGetFileType')) {
    function mlGetFileType($name)
    {
        $pieces = explode(".", $name);
        return $pieces[count($pieces) - 1];
    }
}