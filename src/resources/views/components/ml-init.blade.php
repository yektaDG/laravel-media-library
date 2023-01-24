<link rel="stylesheet" href="{{asset('/vendor/yektadg/medialibrary/medialibrary.min.css')}}">

<x-mediaLibrary::media-library-modal :id="$id"></x-mediaLibrary::media-library-modal>
<script src="{{ asset('/vendor/yektadg/medialibrary/medialibrary.js') }}"></script>
<script>
    new MediaLibrary({
        defaultImage: '{{asset('/vendor/yektadg/medialibrary/blank-image.svg')}}',
        dropzoneRoute: '{{route('medialibrary.store')}}',
        csrf: '{{csrf_token()}}',
        removeFromFolderRoute: '{{route('medialibrary.folder.remove-media')}}',
        moveToFolderRoute: '{{route('medialibrary.folder.add-media')}}',
        destroyFolderRoute: '{{route('medialibrary.folder.remove')}}',
        allFoldersRoute: '{{route('medialibrary.folder.all')}}',
        getFolderMediaRoute: '{{route('medialibrary.folder.folder-media',':id')}}',
        setAltRoute: '{{route('medialibrary.set-alt')}}',
        removeMediaRoute: '{{route('medialibrary.remove')}}',
        mediaSingleRoute: '{{route('medialibrary.single')}}',
        allMediaRoute: '{{route('medialibrary.all')}}',
        libraryId: {{$id}},
        imageExistRoute: '{{route('medialibrary.image-exists')}}',
    });
</script>
