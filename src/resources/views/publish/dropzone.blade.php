<div class="col-xl-12">
    <form class="form" action="none" id="dropzone-form-{{$id ?? ''}}">
        <form class="form" action="
        {{route('media.store')}}
        " method="post" enctype="multipart/form-data">
            <div class="fv-row ">
                <div class="dropzone ml-dropzone d-flex justify-content-center  dz-clickable"
                     id="dropzone-js-{{$id ?? ''}}">
                    <div class="dz-message needsclick">
                        <i class="bi bi-file-earmark-arrow-up text-primary fs-3x"></i>
                        <div class=" ms-4">
                            <h5 class="fs-6  text-gray-900 "> برای بارگذاری فایل‌ها را بکشید . یا کلیک کنید </h5>
                            <span
                                    class="form-text fs-6 text-muted">حداکثر اندازه پرونده برای بارگذاری: 2 مگابایت .</span>

                            <div class="text-muted fs-7 mt-1"> فرمت‌های قابل قبول
                                png, jpg, jpeg, gif, webp, svg
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </form>
</div>