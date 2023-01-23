<script>
    let libraryId = 2;
    let imagePreviewerId = 2;
    let useType = "imagePreview";           // can be imagePreview or tinymce or input, or I don't know anymore
    let useId = 1;
    let multipleSelect = false;                  // if true allows the user to select multiple thumbnail
    let hidOnAdd = true      // if true  , the modal closes when a media added
    let parameters = {};      // defaultImage, dropzone route, csrf , removeFromFolderRoute , moveToFolderRoute , destroyFolderRoute, allFoldersRoute , getFolderMediaRoute , setAltRoute ,  removeMediaRoute , mediaSingleRoute , allMediaRoute
    let selectedArray = [];
</script>

<script src="{{ asset('/vendor/yektadg/medialibrary/medialibrary.js') }}"></script>
<script>
    const parameters = {
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
    }
</script>


