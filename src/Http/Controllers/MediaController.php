<?php

namespace YektaDG\Medialibrary\Http\Controllers;


use App\Jobs\ImageProcess;
use YektaDG\Medialibrary\Http\Models\ExtendedMedia as Media;
use YektaDG\Medialibrary\Facades\ExtendedMediaFacade as MediaUploader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Plank\Mediable\Exceptions\MediaUpload\ConfigurationException;
use Plank\Mediable\Exceptions\MediaUpload\FileExistsException;
use Plank\Mediable\Exceptions\MediaUpload\FileNotFoundException;
use Plank\Mediable\Exceptions\MediaUpload\FileNotSupportedException;
use Plank\Mediable\Exceptions\MediaUpload\FileSizeException;
use Plank\Mediable\Exceptions\MediaUpload\ForbiddenException;
use YektaDG\Medialibrary\Jobs\DeleteMedia;

class MediaController extends Controller
{

    /**
     * this method fetch the images that current user uploaded then returns
     * @return array
     */
    public function getAllImagesUploadedByCurrentUser(Request $request)
    {

        function getAllImagesInLibrary($folder)
        {
            $imgs = DB::table('media as m')->join('mediables as me', function ($join) use ($folder) {
                $join->on('m.id', '=', 'me.media_id');
                $join->on('me.tag', '=', DB::raw("'{$folder}'"));
            })->select('m.*')->groupBy('m.id', 'm.disk', 'm.directory', 'm.filename', 'm.extension', 'm.mime_type'
                , 'm.aggregate_type', 'm.height', 'm.width', 'm.alt', 'm.size', 'm.variant_name', 'm.original_media_id'
                , 'm.created_at', 'm.updated_at')->get();
            return $imgs;
        }

        $currentUser = Auth::user();
        if (!$request->accessAllMedia) {
            $images = $currentUser->getMedia($request->folder);
        } else {
            $images = getAllImagesInLibrary($request->folder);
        }
        $total = ($request->limit * $request->offset) + $request->limit;
        $hasMore = count($images) > $total;
        $images = $images->reverse()->skip($request->limit * $request->offset)->take($request->limit)->toArray();
        return [
            'images' => $images,
            'hasMore' => $hasMore,
        ];
    }

    /**
     * this method fetch the images that current user uploaded and saved in a folder in a array with keys of tags and values of images then returns
     * like ([gallery=>[image1,image2] ,gallery-folder=>[image3,image2] ])
     * @return Media|array|\never
     */
    public function getImageByGalleryFolder()
    {
        $currentUser = Auth::user();
        $images = [];
        foreach ($currentUser->getAllMediaByTag() as $key => $value) {
            if (str_contains($key, 'gallery')) {
                $images[$key] = $value;
            }
        }

        return $images;
    }


    public function getSingleImage(Request $request)
    {
        $id = $request->get('id');
        $image = Media::findOrFail($id);
        if (isset($image)) {
            $image->imageUrl = '/storage/' . $image->getDiskPath();
            $image->created_date = getJalali($image->created_at);
            return $image;
        }
    }

    /**
     *   this method first check if the user has permission to store media then store the image
     * in folders separated by years and month
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $image = $request->file('media-library-image');
        try {
            mlPictureValidation($image, 'picture', 15097152);
            $extension = $image->extension();
            $name = str_replace(array($extension, '.'), '', $image->getClientOriginalName());
            $name .= Str::random(6);
            $media = MediaUploader::fromSource($image)
                ->toDestination('public', 'uploads/images/' . now()->year . '/' . now()->month)
                ->useFilename($name)
                ->upload();
            $extension = $media->extension;
            $user->attachMedia($media, 'gallery');
            if ($request->has('folder'))
                $user->attachMedia($media, $request->folder);
            $name = $media->filename;
            $notChange = ['svg', 'gif'];
            $path = $media->getDiskPath();
            if (!in_array(strtolower($extension), $notChange))
                ImageProcess::dispatchSync($path, $name, $extension);    //TODO : fix here
            return response()->json(['status' => 'success', 'folder' => $request->folder, 'media' => $media]);

        } catch (ValidationException|ConfigurationException|FileExistsException|FileNotFoundException|FileNotSupportedException|FileSizeException|ForbiddenException $e) {
            return response()->json(['failed']);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response|\never
     */
    public function remove(Request $request)
    {


        $user = auth()->user();


        $ids = $request->get('media_ids');
        foreach ($ids as $id) {
            $id = preg_replace("/\D/", "", $id);
            $images = $user->getMedia('gallery');
            foreach ($images as $image) {
                if ($image->id == $id) {
                    $user->detachMedia($image);
                    DeleteMedia::dispatchSync($image->directory, $image->filename, $image->extension); // TODO: fix here
                    $image->delete();
                }
            }
        }


    }

    /**
     * sets the alt for an image
     * @param Request $request
     */
    public function setAlt(Request $request)
    {
        $user = auth()->user();
        if ($request->accessAllMedia) {
            $image = Media::findOrFail($request->id);
        } else {
            $image = $user->getMedia('gallery')->where('id', $request->id)->firstOrFail();
        }
        $image->alt = $request->alt_value;
        $image->save();
    }


    public function removeFolder(Request $request)
    {
        $folderName = $request->folder_name;
        $currentUser = Auth::user();
        $currentUser->detachMediaTags($folderName);
    }

    public function addToFolder(Request $request)
    {
        $currentUser = Auth::user();
        $image_ids = $request->image_ids;
        foreach ($image_ids as $image_id) {
            $image = $currentUser->getMedia('gallery')->where('id', $image_id)->first();
            if (isset($image) && !in_array($request->folder_name, $currentUser->getTagsForMedia($image)))
                $currentUser->attachMedia($image, $request->folder_name);
        }
    }

    public function getAllFolders(Request $request)
    {

        $currentUser = auth()->user();
        $folders = [];

        if ($request->accessAllMedia) {
            $folders = DB::table('mediables as m')
                ->select('tag as folder', 'mediable_id as uid')
                ->where('tag', 'like', 'gallery-%')
                ->groupBy('tag', 'mediable_id')->get()->toArray();
        } else {
            $tags = $currentUser->getAllMediaByTag();
            foreach ($tags as $key => $value) {
                if (str_contains($key, 'gallery-')) {
                    $folders[] = ['folder' => $key,
                        'uid' => $currentUser->id];
                }
            }
        }
        return $folders;
    }

    public function getMediaByFolder($folder)
    {
        $currentUser = Auth::user();

        return $currentUser->getMedia($folder);
    }

    public function removeMediaFromFolder(Request $request)
    {
        $currentUser = Auth::user();
        $image_ids = $request->image_ids;
        foreach ($image_ids as $image_id) {
            $image = $currentUser->getMedia('gallery')->where('id', $image_id)->first();
            if (isset($image) && in_array($request->folder_name, $currentUser->getTagsForMedia($image)))
                $currentUser->detachMedia($image, $request->folder_name);
        }
    }


    public function imageExists(Request $request)
    {
        try {
            $images = $request['images'];
            foreach ($images as $key => $image) {
                $image['element'] = file_exists(public_path($image['url']));
                $images[$key] = $image;
            }
            return $images;
        } catch (\Exception $e) {
            return $images;
        }
    }
}
