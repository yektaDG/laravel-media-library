<link rel="stylesheet" href="{{asset('/vendor/yektadg/medialibrary/medialibrary.min.css')}}">

<x-mediaLibrary::media-library-modal :id="$id"></x-mediaLibrary::media-library-modal>
<script src="{{ asset('/vendor/yektadg/medialibrary/mlLang.min.js') }}"></script>
<script src="{{ asset('/vendor/yektadg/medialibrary/sweetalert2.min.js') }}"></script>
<script src="{{ asset('/vendor/yektadg/medialibrary/medialibrary.min.js') }}"></script>

<script>
    const mlLang = new Multilingual('{{app()->getLocale()}}', '{{asset('/vendor/yektadg/medialibrary/')}}');
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
        libraryId: "{{$id}}",
        userId: "{{auth()->id()}}",
        checkImageExists: {!! config('medialibrary.generate_sizes') !!},
        imageExistRoute: '{{route('medialibrary.image-exists')}}',
    });
</script>
