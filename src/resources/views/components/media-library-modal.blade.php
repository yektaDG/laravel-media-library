<div class="modal media-library-modal fade" tabindex="-1" id="media-library" libraryId= {{$id}}>
    @include('vendor.yektadg.medialibrary.toast', ['text' => '', 'id' => 'ml-toast-'.$id])
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-body px-md-3">
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2  fa-pull-left" data-bs-dismiss="modal"
                     aria-label="Close">
                    <span class="svg-icon svg-icon-muted svg-icon-2x"><svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                           height="24"
                                                                           viewBox="0 0 24 24" fill="none">
                    <path opacity="0.3"
                          d="M6 19.7C5.7 19.7 5.5 19.6 5.3 19.4C4.9 19 4.9 18.4 5.3 18L18 5.3C18.4 4.9 19 4.9 19.4 5.3C19.8 5.7 19.8 6.29999 19.4 6.69999L6.7 19.4C6.5 19.6 6.3 19.7 6 19.7Z"
                          fill="black"/>
                    <path
                            d="M18.8 19.7C18.5 19.7 18.3 19.6 18.1 19.4L5.40001 6.69999C5.00001 6.29999 5.00001 5.7 5.40001 5.3C5.80001 4.9 6.40001 4.9 6.80001 5.3L19.5 18C19.9 18.4 19.9 19 19.5 19.4C19.3 19.6 19 19.7 18.8 19.7Z"
                            fill="black"/>
                    </svg></span></div>
                <x-mediaLibrary::media-library :libraryId="$id"></x-mediaLibrary::media-library>
            </div>
            <div class="card-footer">
                @include('vendor.yektadg.medialibrary.dropzone', ['libraryId' => $id])
            </div>
        </div>
    </div>
</div>
