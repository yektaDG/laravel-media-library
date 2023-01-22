<div class="card-header">
    <div class="row">
        <div class="col-md-2">
            <div class="card-title">
                <div class="btn-group" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark"
                     data-bs-placement="top">
                    <button id="add-media-{{$libraryId ?? ''}}" type="button"
                            class="media-library-button btn btn-light btn-sm disabled ml-add-button"
                    >انتخاب
                    </button>
                </div>
                <div class="btn-group" style="margin-right: 5px">
                    <button id="delete-media-{{$libraryId ?? ''}}" type="button"
                            class="btn btn-light-danger btn-sm disabled ml-delete-button"
                            >
                        حذف
                    </button>
                </div>

            </div>
        </div>
        <div class="col justify-content-center text-center">
            <div id="multi-select-mode-info-{{$libraryId ?? ''}}"
                 class="card-title d-none d-sm-block justify-content-center text-center">
            </div>
        </div>
        <div class="col-md-2">
            <div class="card-title fa-pull-left">
                <div class="form-check form-check-sm form-check-solid form-switch fv-row">
                    <label class="form-check-label m-2" for="multi-select-mode-{{$libraryId ?? ''}}">انتخاب
                        چندتایی</label>
                    <input class="multi-select-mode form-check-input w-45px h-30px" type="checkbox"
                           id="multi-select-mode-{{$libraryId ?? ''}}">
                </div>
            </div>
        </div>
    </div>
    <div class="card-toolbar">

    </div>
</div>
<div class="card-body px-0 media-library-body">
    <div class="">
        <div class="border colsm12 border-grey border-top-0 border-left-0  border-bottom-0 col-md-2 float-start">
            <div class="" id="folder-details-accordion-{{$libraryId ?? ''}}">
                <div class="">
                    <h4 class="" id="folder-header-{{$libraryId ?? ''}}">
                        <button class="accordion-button btn-sm fs-7 fw-semibold p-4 collapsed" type="button"
                                data-bs-toggle="collapse"
                                href="#folder-body-{{$libraryId ?? ''}}" aria-expanded="false"
                                aria-controls="folder-body-{{$libraryId ?? ''}}">
                            پوشه ها
                        </button>
                    </h4>
                    <div id="folder-body-{{$libraryId ?? ''}}" class="accordion-collapse collapse "
                         aria-labelledby="folder-header" data-bs-parent="#folder-details-accordion-{{$libraryId ?? ''}}">
                        <div class="accordion-body ml-folder-scroll">
                            <div id="library-folder-{{$libraryId ?? ''}}" class="col">
                                <div class="row">
                                    <div class="col input-group input-group-sm">
                                        <input class="form-control form-control-sm"
                                               id="library-folder-name-{{$libraryId ?? ''}}"
                                               type="text"
                                               placeholder="نام پوشه">
                                        <button class="ml-save-folder btn btn-sm btn-light">ذخیره</button>
                                    </div>
                                    <div class="separator separator-dashed my-3"></div>
                                    <div class="col folders-list" id="folders-list-div-{{$libraryId ?? ''}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-5">
                    <h4 class="" id="details-header-{{$libraryId ?? ''}}">
                        <button class="accordion-button btn-sm fs-7 fw-semibold p-4 collapsed" type="button"
                                data-bs-toggle="collapse"
                                href="#details-body-{{$libraryId ?? ''}}" aria-expanded="false"
                                aria-controls="details-body-{{$libraryId ?? ''}}">
                            جزییات
                        </button>
                    </h4>
                    <div id="details-body-{{$libraryId ?? ''}}" class="px-0 accordion-collapse collapse "
                         aria-labelledby="details-header-{{$libraryId ?? ''}}" data-bs-parent="#folder-details-accordion-{{$libraryId ?? ''}}">
                        <div class="accordion-body-{{$libraryId ?? ''}}">
                            <div id="library-info-{{$libraryId ?? ''}}"
                                 class="libraryInfoRow row overflow-scroll col-2">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="library-row-{{$libraryId ?? ''}}"
             class="ics library-row row overflow-scroll border border-grey border-top-0 gy-0 gx-1 border-left-0  border-bottom-0  col pt-1 d-flex">

            <div id="sentinel-{{$libraryId ?? ''}}" class="snti" style="bottom: 150vh;"></div>
            <button type="button" id="infinite-scroll-button-{{$libraryId ?? ''}}" class="icb mt-5" disabled>
                <span class="disabled-text">درحال بارگذاری ...</span>
                <span class="active-text">نمایش بیشتر</span>
            </button>

        </div>
    </div>
</div>
