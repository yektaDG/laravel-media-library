class MediaLibrary {


    constructor(params) {
        this.setParameters({
            ...{
                selectedArray: [], // if true  , the modal closes when a media added
                hideOnAdd: true, // if true allows the user to select multiple thumbnail
                multipleSelect: false, useId: 1, libraryId: 2, imagePreviewerId: 2, // can be imagePreview or tinymce or input, or I don't know anymore
                useType: 'imagePreview', limit: 36, offset: 0, isFirst: true,

                // for observer
                responseBuffer: [],            //holds the fetched images like a buffer
                hasMore: true,                 // defines that there is any image left on database after fetching
                requestPending: false,         // defines the  if there is any request pending or not
                loadingButtonEl: null,         // holds the element of loading button
                sentinelEl: null,              // holds the element of sentinel div
            }, // defaultImage, dropzone route, csrf , removeFromFolderRoute , moveToFolderRoute , destroyFolderRoute, allFoldersRoute , getFolderMediaRoute , setAltRoute ,  removeMediaRoute , mediaSingleRoute , allMediaRoute
            ...params
        });

        this.initAllTheMethods();
    }

    setParameters(parameters) {
        Object.entries(parameters)
            .forEach((k) => {
                this[k[0]] = k[1];
            })
    }

    initAllTheMethods() {
        this.initEventForAddAndRemoveButton();
        this.initMlButtonEvents();
        this.initSaveFolderButton();
        this.initRemoveFolderButton()
        this.initMultiSelectModeButton();
        this.initFolderDivEvents();
        this.initModalEvent();
        this.initDropZones();
        this.useCallBack();
    }


    /*
    observers for infinite scroll
 */

    async insertNewItems() {
        await this.mlRefreshLibrary(this.responseBuffer);
        this.sentinelObserver.observe(this.sentinelEl);
        if (this.hasMore === false) {
            this.loadingButtonEl.style = "display: none";
            this.sentinelObserver.unobserve(this.sentinelEl);
            this.listObserver.unobserve(this.loadingButtonEl);
        }
        this.loadingButtonEl.disabled = true
    }

    async handleObserve() {
        if (this.hasMore === false) {
            this.loadingButtonEl.style = "display: none";
            this.sentinelObserver.unobserve(this.sentinelEl);
            this.listObserver.unobserve(this.loadingButtonEl);
        } else {
            this.sentinelObserver.observe(this.sentinelEl);
            this.listObserver.observe(this.loadingButtonEl);
            this.loadingButtonEl.style = `display: ''`;
        }
    }

    requestHandler() {
        if (this.requestPending) return;

        this.requestPending = true;
        const folder = $(`#library-folder-${this.libraryId} .folder-div.selected-folder .folder-hidden`).val(); // finding the selected folder name
        axios.get(this.allMediaRoute, {
            params: {
                limit: this.limit, offset: this.offset, folder: folder
            },
        }).then((response) => {
            this.offset += 1;
            this.requestPending = false;
            this.responseBuffer = Object.values(response.data.images).reverse();
            this.hasMore = response.data.hasMore;
            this.loadingButtonEl.disabled = false;
            if (this.isFirst) {                       // because in modal observer does not recognize first call
                $(`#infinite-scroll-button-${this.libraryId}`).trigger('click');
                this.isFirst = false;
            }
        });
    }

    sentinelObserver = this.generateSentinelObserver();

    generateSentinelObserver() {
        const _self = this;
        return new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.intersectionRatio > 0) {
                    observer.unobserve(_self.sentinelEl);
                    _self.requestHandler();
                }
            });
        });
    }

    listObserver = this.generateListObserver();

    generateListObserver() {
        const _self = this;

        return new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.intersectionRatio > 0 && entry.intersectionRatio < 1) {
                    _self.insertNewItems().then(() => {
                        _self.responseBuffer = [];
                    });
                }
            });
        }, {
            rootMargin: "0px 0px 200px 0px"
        });
    }

    /**
     * this method initiates the every dropzone on the page
     */
    initDropZones() {
        const _self = this;
        $('.ml-dropzone').each((index, element) => {
            const mlDropzone = new Dropzone(`#${$(element).attr('id')}`, {
                url: _self.dropzoneRoute ?? ' ', // Set the url for your upload script location
                headers: {
                    'X-CSRF-TOKEN': _self.csrf,
                }, paramName: "media-library-image", // The name that will be used to transfer the file
                maxFiles: 50, maxFilesize: 10, // MB
                method: 'POST', addRemoveLinks: true, success: (file, response) => {
                    _self.mlAddSingleImage(response.media);
                    setTimeout(() => {        // removes the uploaded image thumbnail in dropzone area after 3 second
                        file._removeLink.click()

                    }, 3000);
                }
            });
            //  adding the folder to the request to force the uploaded image to show in selected folder
            mlDropzone.on("sending", (file, xhr, formData) => {
                const folder = $('.folder-div.selected-folder .folder-hidden').val()
                if (folder !== undefined && folder !== 'gallery' && folder.length > 0) {
                    formData.append("folder", folder);
                }
            });
        })
    }

    /**
     * initiates the event for when the modal completely loads
     */
    initModalEvent() {
        const _self = this;
        const mediaLibraryModal = $('.media-library-modal');
        mediaLibraryModal.off('shown.bs.modal').on('shown.bs.modal', async (e) => {
            _self.responseBuffer = [];
            _self.libraryId = $(e.currentTarget).attr('libraryId');        //sets the global libraryId that currently using
            _self.initSearchFolder();
            await _self.mlGetAllFolders().then(async () => {          // it is important to get the folders first because the observer uses the folder name to fetch images
                _self.loadingButtonEl = document.querySelector(`#infinite-scroll-button-${_self.libraryId}`);
                $(_self.loadingButtonEl).off('click').on('click', () => {
                    _self.insertNewItems();
                });

                _self.sentinelEl = document.querySelector(`#sentinel-${_self.libraryId}`);
                await _self.handleObserve();                  // starts the observers
            })

        })
        mediaLibraryModal.off('hidden.bs.modal').on('hidden.bs.modal', () => {
            $('.library-row .selected').removeClass('selected');
            $(`#add-media-${_self.libraryId}`).addClass('disabled');
            $('#gallery-folder').trigger('click')    // to prevent previous selected folder's images  show on next open
            _self.hasMore = false;
            _self.handleObserve().then(() => {
                _self.hasMore = true;
                _self.isFirst = true;
                _self.offset = 0;
                $('.th-div').remove();
            });    // ends the observers
        })
    }

    /**
     * initiates the event when clicking on a button that has the ml-button or ml-remove-button class
     */
    initMlButtonEvents() {
        const _self = this;
        // for each click  sets the imagePreviewId to use later then
        $('.ml-button').off('click').on('click', (e) => {
            const mlButton = e.currentTarget;

            _self.useType = $(mlButton).attr('useType');              //sets the useType
            // sets the useId
            _self.useId = $(mlButton).attr('useId') ?? $(mlButton).closest('div').attr('id');

            _self.multipleSelect = $(mlButton).attr('multipleSelect') === 'true';

            _self.hideOnAdd = $(mlButton).attr('hideOnAdd') === 'true' || !($(mlButton).attr('hideOnAdd'));
            _self.showModal = $(mlButton).attr('showModal') !== 'false';
            if (_self.showModal === true) $('#media-library').modal('show'); //shows the  media library modal
        });

        //on click removes the selected image  from imagepreview
        $('.ml-remove-button').off('click').on('click', (e) => {
            const mlRemoveButton = e.currentTarget;

            _self.imagePreviewerId = $(mlRemoveButton).closest('div').attr('id');
            const parentDiv = $('#' + _self.imagePreviewerId);
            const lbox = parentDiv.children('.lbox');
            lbox.attr('src', _self.defaultImage);
            lbox.css('background-image', '');
            let hiddenInputQuery = '#' + _self.imagePreviewerId + ' .hidden-image-input';   //gets the hidden input that contains the image url
            $(hiddenInputQuery).val("none").trigger('change');
        });
    }

    /**
     * initiates the multi select mode button that allow user to select multiple image
     */
    initMultiSelectModeButton() {
        const _self = this;
        const multiSelectMod = $(`#multi-select-mode-${_self.libraryId}`);
        multiSelectMod.on('change', () => {
            const addMediaButton = $(`#add-media-${_self.libraryId}`);
            if ($(this).is(':checked') && _self.multipleSelect === 'false') {   // if the button checked and the multiple attribute is false
                addMediaButton.prop('disabled', true);      //sets the tooltip for select button
                addMediaButton.closest('div').attr('data-bs-original-title', 'برای استفاده از عکس ، حالت "انتخاب چندتایی" را غیر فعال کنید')
                new bootstrap.Tooltip(addMediaButton.closest('div'))
            } else {        // if the button unchecked
                addMediaButton.prop('disabled', false);     //  removes the tooltip
                addMediaButton.closest('div').attr('data-bs-original-title', '');
                $(`#library-row-${_self.libraryId} .selected`).each((index, element) => {
                    if (index !== 0) {
                        $(element).removeClass('selected');
                        _self.mlSetStyleToggle($(element).parent('.th-div'), false)
                    }
                });
            }
        })
    }

    /**
     * initiates the folder section of media library  events
     */
    initFolderDivEvents() {
        const _self = this;
        //.off() is for removing previous events on div to avoid duplicate events
        $('.folder-div').off('click').on('click', async (e) => {
            const folderDiv = e.currentTarget;
            if (!$(folderDiv).hasClass('selected-folder')) {    // if the folder is not selected
                // -------- start styling ---------------
                const selectedFolder = $('.selected-folder');
                selectedFolder.removeClass('bg-gray-300').addClass('bg-gray-100')   // make the background of div  lighter
                selectedFolder.find('.remove-folder').removeClass('btn-gray-300').addClass('btn-light')     // these lines change  unlink from folder to add to folder
                selectedFolder.find('.add-to-folder').removeClass('btn-gray-300 btn-active-light-warning').addClass('btn-light  btn-active-light-success')
                    .find('i').removeClass('fa-unlink').addClass('fa-plus')
                selectedFolder.removeClass('selected-folder');

                $(folderDiv).addClass('selected-folder')
                $(folderDiv).find('.remove-folder').removeClass('btn-light').addClass('btn-gray-300')  // these lines change the add to folder to unlink from folder
                $(folderDiv).find('.add-to-folder').removeClass('btn-light btn-active-light-success').addClass('btn-gray-300 btn-active-light-warning')
                    .find('i').removeClass('fa-plus').addClass('fa-unlink')
                $(folderDiv).removeClass('bg-gray-100').addClass('bg-gray-300');     //  make the current selected div  darker

                // -------- ended styling ---------------

                _self.offset = 0;      // to start fetching from start of folder
                _self.responseBuffer = [];        //removes previous images from buffer
                _self.hasMore = true;             // to let observer run
                $('.th-div').remove();      // removes previous added images from library-row
                _self.isFirst = true;
                _self.handleObserve().then(() => {
                    $(`#infinite-scroll-button-${_self.libraryId}`).trigger('click');     // need to be clicked cause the observer does not recognize modal on first show
                })
            }
        })
    }


    /**
     * initiates the save folder button events
     */
    initSaveFolderButton() {
        const _self = this;
        $('.ml-save-folder').off('click').on('click', (e) => {
            const mlSaveFolder = e.currentTarget;
            e.preventDefault()
            const folder = $(mlSaveFolder).closest('div').find('input').val();    //  finds the input saves the value that is the folder name in a variable
            _self.folders.push({'folder': `gallery-${folder}`, 'uid': '_self'})            //adding this folder to array of folders
            $(mlSaveFolder).closest('div').find('input').val('');           //  empties the input value
            // appends the currently added folder to the folders column
            $(mlSaveFolder).closest(`#library-folder-${_self.libraryId}`).find('.folders-list').append(`<div class="folder-div bg-gray-100 rounded mt-2 row "><div class="  p-1  ">
                <input type="hidden" class="folder-hidden" value="gallery-${folder}">
                <div class=""><div class="col-md-6 text-center  mt-2 float-start">
                <span class="ms-2"> ${folder.replace('gallery-', '')}</span></div>
                <div class="mt-2 float-end"><div class="">
                <button class="add-to-folder btn btn-sm btn-icon btn-light btn-active-light-success fa-pull-left d-inline-block">
                <i class="fas fa-plus"></i></button>
                <button id="delete"   class="remove-folder me-1 btn btn-sm btn-icon btn-light btn-active-light-danger fa-pull-left">
                <span class="svg-icon svg-icon-5 m-0"><i class="text-dark-50 fonticon-trash fs-2"></i>
                </span> </button></div></div>  </div></div></div>`);
            this.initRemoveFolderButton();
            this.initFolderDivEvents();
            this.initAddToFolderButton();
            this.filterFolders('');
        })
    }

    initSearchFolder(id = this.libraryId) {
        const libraryFolderName = $(`#library-folder-name-${id}`);
        const _self = this;
        libraryFolderName.off('keyup').on('keyup', () => {
            _self.filterFolders($(libraryFolderName).val())
        })
    }

    /**
     * initiates the remove folder button event
     */
    initRemoveFolderButton() {
        const removeFolderButton = $('.remove-folder');
        const _self = this;
        removeFolderButton.off('click').on('click', (e) => {
            e.preventDefault();
            e.stopPropagation();      // to prevent the parent div to execute (on click) event
            const name = $(removeFolderButton).closest('.folder-div').find('.folder-hidden').val();
            _self.removeFolderFromBackend(name, $(removeFolderButton).closest('.folder-div'))
        })
    }


    /**
     * initiates the add to folder button event
     */
    initAddToFolderButton() {
        const _self = this;
        $('.add-to-folder').off('click').on('click', (e) => {
            const addToFolderButton = e.currentTarget;

            e.preventDefault();
            e.stopPropagation();      // to prevent the parent div to execute on click event
            if ($(addToFolderButton).closest('.folder-div').hasClass('selected-folder'))     // if the folder is currently selected it removes the selected images from folder
            {
                _self.removeFromFolder($(addToFolderButton).closest('.folder-div').find('.folder-hidden').val());
            } else {  // if the folder is not selected , added currently selected images to the folder
                _self.addToFolder($(addToFolderButton).closest('.folder-div').find('.folder-hidden').val());
            }
        })
    }


    /**
     * initiates the select thumbnail event
     */
    initSelectThumbnailEvent() {
        const _self = this;
        $('.th-div').off('click').on('click', (e) => {
            _self.mlSelectThumbnail($(e.currentTarget).find('.img-thumbnail').attr('thumbnailId'))
        })
    }


    /**
     * gets an image as a parameter then unlink the selected images from folder
     * @param folderName
     */
    removeFromFolder(folderName) {
        const images = [];
        const _self = this;
        const selected = $(`#library-row-${this.libraryId} .selected`);   // gets all the selected images
        selected.each((index, element) => {
            const id = $(element).attr('id').split('thumbnail-')[1];
            images.push(id)                             //collect all the images ids  in an array
        })
        axios.post(this.removeFromFolderRoute, {
            "image_ids": images, "folder_name": folderName
        }).then(() => {
            _self.removeImageFromRow(selected)
            this.notifyToast(mlLang.notifyDelete);
        })
    }


    notifyToast(message) {
        const toastElement = document.getElementById(`ml-toast-${this.libraryId}`);
        toastElement.querySelector('.toast-body').innerHTML = message;
        const toast = bootstrap.Toast.getOrCreateInstance(toastElement);
        toast.show();
    }


    /**
     * gets an image as a parameter then links the selected images to the folder
     * @param folderName
     */
    addToFolder(folderName) {
        const images = [];
        const selected = $(`#library-row-${this.libraryId} .selected`);   // gets all the selected images
        selected.each((index, element) => {
            const id = $(element).attr('id').split('thumbnail-')[1];
            images.push(id)                             //collect all the images ids  in an array
        })
        axios.post(this.moveToFolderRoute, {
            "image_ids": images, "folder_name": folderName
        }).then(() => {
            this.notifyToast(mlLang.notifyAddToFolder);
        });
    }


    /**
     *  after confirmation send a request to backend to remove the folder
     * @param name is the name of the folder we want to remove
     * @param folder    is the object of the folder
     */
    removeFolderFromBackend(name, folder) {
        const _self = this;
        Swal.fire({
            text: "آیا از حذف پوشه اطمینان دارید ؟ توجه کنید که عکس‌های درون پوشه حذف نمی شوند !",
            icon: "warning",
            showDenyButton: true,
            buttonsStyling: false,
            confirmButtonText: "حذف",
            denyButtonText: `لغو`,
            customClass: {
                confirmButton: "btn btn-sm btn-danger", denyButton: "btn btn-sm btn-primary",
            }
        }).then((result) => {
            if (result.isConfirmed) {
                axios.post(_self.destroyFolderRoute, {"folder_name": name})
                    .then((response) => {
                        _self.notifyToast(mlLang.notifyRemovedFolder);
                        if (folder.hasClass('selected-folder')) {   // if the currently selected folder is removed move to the gallery (that is the main folder)
                            const gallery = $('#gallery-folder');
                            gallery.addClass('selected-folder')
                            gallery.removeClass('bg-gray-100').addClass('bg-gray-300');
                            gallery.trigger('click');
                            _self.offset = 0;
                            _self.responseBuffer = [];
                            _self.hasMore = true;
                            $('.th-div').remove();
                            _self.isFirst = true;
                            _self.handleObserve().then(() => {
                                $(`#infinite-scroll-button-${_self.libraryId}`).trigger('click');
                            })
                        }
                        _self.folders = _self.folders.filter(f => f !== name)
                        folder.remove();    // removes the folder div
                    })
            }
        });


    }

    /**
     * sends a request to backend and fetch all the folders then calls the refresh folder method
     */
    async mlGetAllFolders() {
        const _self = this;
        await axios.get(_self.allFoldersRoute)
            .then((response) => {
                _self.folders = response.data;
                this.mlRefreshFolders(response.data);
            })
    }


    filterFolders(filter) {
        const filtered = this.folders.filter((data) => {
            return data?.folder?.replace('gallery-', '').includes(filter)
        })
        this.mlRefreshFolders(filtered)
    }


    /**
     * appends the folders to the folder column
     * @param folders is the array of folder names
     */
    mlRefreshFolders(folders) {
        folders = folders.sort();
        const r = $(`#folders-list-div-${this.libraryId}`);
        r.html('');   // empties the folder column
        // first add the gallery folder to be always on top
        r.append(`<div class="folder-div rounded  bg-gray-300 selected-folder row " id="gallery-folder">
                     <div class="col mt-2 p-1  "><input type="hidden" class="folder-hidden" data-user="auth"
                                                                                     value="gallery" >
                         <div class="row">
                             <div class="col-md-7  pb-2  text-center"><span class="ms-2">گالری</span>
                             </div>
                             <div class="col">
                             </div>
                         </div>
                     </div>
                 </div>`);
        // appends the every other folder
        folders.forEach(data => {
            const folder = data.folder;
            const uid = data.uid;
            r.append(`<div class="folder-div bg-gray-100 rounded mt-2 row "><div class="  p-1  "><input type="hidden" class="folder-hidden" data-user="${uid}" value=" ${folder}"><div class=""><div class="col-md-6 text-center  mt-2 float-start"><span class="ms-2">${folder.replace('gallery-', '')}</span> </div>
 <div class="mt-2 float-end"><div class=""> <button class="add-to-folder btn btn-sm btn-icon btn-light btn-active-light-success fa-pull-left d-inline-block"><i class="fas fa-plus"></i></button>
  <button id="delete"
                                                        class="remove-folder me-1 btn btn-sm btn-icon btn-light btn-active-light-danger fa-pull-left">
                                                         <span class="svg-icon svg-icon-5 m-0">
                                                                     <i class="text-dark-50 fonticon-trash fs-2"></i>
                                                                 </span> </button></div></div></div></div></div>`)
        })

        // initiates the buttons
        this.initRemoveFolderButton();
        this.initAddToFolderButton();
        this.initFolderDivEvents();
    }


    //add class and border on select a thumbnail then based on number of selected thumbnails shows an info
    mlSelectThumbnail(id) {
        const btn = $(`#library-row-${this.libraryId} #thumbnail-${id}`);
        const _self = this;
        if (btn.hasClass('selected')) {
            btn.removeClass('selected');
            this.mlSetStyleToggle($(btn).closest('.th-div'), false)

            this.mlCheckSelected();
            this.selectedArray = this.selectedArray.filter(item => item !== btn)
        } else {
            if (!$(`#multi-select-mode-${this.libraryId}`).is(':checked')) {
                const forDelete = []
                $(`#library-row-${this.libraryId} .selected`).each((index, element) => {
                    $(element).removeClass('selected');
                    _self.mlSetStyleToggle($(element).parent('.th-div'), false)
                    forDelete.push(element)
                });
                this.selectedArray = this.selectedArray.filter(item => !forDelete.includes(item))
            }
            btn.addClass('selected');
            this.mlSetStyleToggle($(btn).closest('.th-div'), true)
            this.mlCheckSelected();
            this.selectedArray.push(btn);
        }

    }


    /**
     * this method add or remove selected style to the element
     */

    mlSetStyleToggle(element, isSelected) {
        if (isSelected) {
            element.find('.m-over').addClass('m-over-selected');
            element.find('.th-info').addClass('th-info-selected');
            element.find('.th-info-button').addClass('th-info-button-selected');
        } else {
            element.find('.m-over').removeClass('m-over-selected');
            element.find('.th-info').removeClass('th-info-selected');
            element.find('.th-info-button').removeClass('th-info-button-selected');
        }
    }

    /**
     *
     *shows the selected thumbnail info on sidebar of medialibrary
     *
     */
    mlShowThumbnailInfo(data) {
        let size = parseInt(data['size']);
        size = parseFloat(size / 1024).toFixed(2);
        return `<div class="th-info-details">
        <div class="justify-content-center text-center mb-5"><span class="d-inline-block fw-boldest">جزییات</span> </div>
        <p  class="text-center fw-bold" style="word-wrap: break-word"> ${data['filename']} </p>
        <p id="alt-info-${data['id']}" class="text-center fw-bold" > <i class="fa fa-info-circle ">
        </i> : ${data['alt']}</p><p class="text-center fw-bold"> ${data['created_date']}  در  ${data['created_at'].substring(11, 19)} </p>
         <p class="text-center fw-bold"> کیلو بایت  ${size} </p> <p class="text-center fw-bold"> پیکسل ${data['width']}   *  ${data['height']} </p>  <br>
         <div class="input-group input-group-sm justify-content-center mt-2 px-1" >   <input id="alt-input" type="text"  value="${data['alt']}" class="form-control form-control-sm fs-8"  placeholder="Alt را وارد کنید">
         <button type="button" class="setAlt-btn btn btn-light btn-sm fs-8">ذخیره</button> </div></div>`;
    }

    /**
     * sets an alt for selected image
     */
    setAlt(id) {
        const val = $('#alt-input').val();
        axios.post(this.setAltRoute, {
            "id": id, "alt_value": val
        }).then((response) => {
            if (response.status === 200) {
                this.notifyToast(mlLang.notifyChangeAlt);
                $(`#alt-info-${id}`).html(`
                          <i class="fa fa-info-circle "> </i> :  ${val} `)
            }
        })
    }


    initEventForAddAndRemoveButton() {
        const _self = this;
        $(`.ml-add-button`).off('click').on('click', () => {
            _self.mlAddMediaForUsage()
        });
        $(`.ml-delete-button`).off('click').on('click', () => {
            Swal.fire({
                title: mlLang.confirmText,
                text: mlLang.confirmText2,
                icon: 'warning',
                showCancelButton: true,
                customClass: {
                    confirmButton: 'btn btn-light-danger btn-sm',
                    cancelButton: 'btn btn-light btn-sm',
                },
                buttonsStyling: false,

                cancelButtonText: mlLang.cancelButton,
                confirmButtonText: mlLang.confirmButton
            }).then((result) => {
                if (result.isConfirmed) {
                    _self.mlDeleteMedia()
                }
            })
        })
    }

    /**
     *
     *for enabling or disabling button when select a thumbnail
     *
     */
    mlCheckSelected() {
        const btns = $(`#library-row-${this.libraryId} .selected`);
        const addBtn = $(`#add-media-${this.libraryId} `);
        const removeBtn = $(`#delete-media-${this.libraryId}`);
        if (btns.length > 0) {
            addBtn.removeClass('disabled');
            removeBtn.removeClass('disabled');
        } else {
            addBtn.addClass('disabled');
            removeBtn.addClass('disabled');
        }
    }

    /**
     *add media at the end of  tinymce
     * @returns {Promise<void>}
     */
    async mlAddMediaForUsage() {
        const _self = this;
        const images = $(`#library-row-${this.libraryId} .selected`);
        const addToInput = () => {                         // adds the images to the given <input> tag
            const useInput = document.getElementById(_self.useId);
            for (let i = 0; i < images.length; i++) useInput.value += `${images[i].getAttribute('imageurl')},`
            useInput.dispatchEvent(new Event('change'));    // manually initiates the change event to handle something later if we want
        }
        const addToTinymce = () => {
            let content = '';
            for (let i = 0; i < this.selectedArray.length; i++) {
                const element = _self.selectedArray[i];
                content += `<img alt="${$(element).attr('alt')}" src="${$(element).attr('imageurl')}" style="max-width: 1024px"/>`;
            }
            tinymce.get(_self.useId).execCommand('mceInsertContent', false, content);
            _self.mlUnselectThumbnails();
        }
        const addToImagePreview = () => {
            const lbox = $(`#${_self.useId} .lbox`);
            lbox.css({
                "background-image": ''
            });
            const src = images[images.length - 1].getAttribute('imageurl');
            lbox.attr('src', src);
            $(`#${_self.useId} .hidden-image-input`).val(src).trigger('change');
        }
        if (images.length > 0) {
            switch (this.useType) {
                case 'hidden':
                    addToInput();
                    break;
                case 'tinymce':
                    await addToTinymce();
                    this.selectedArray = [];
                    break;
                case 'imagePreview':
                    addToImagePreview();
                    break;
            }
        }
        if (this.hideOnAdd === true) {
            $('#media-library').modal('hide');
            this.notifyToast(mlLang.notifyAdd)
        } else {
            this.notifyToast(mlLang.notifyAddToTinymce)
        }

    }

    async mlUnselectThumbnails() {
        const _self = this;
        await $(`#library-row-${this.libraryId} .selected`).each((index, element) => {
            $(element).removeClass('selected');
            _self.mlSetStyleToggle($(element).parent('.th-div'), false)
        });
        _self.selectedArray = [];
        await _self.mlCheckSelected();
    }

    /**
     * deletes a media
     */

    mlDeleteMedia() {
        const btns = $('.library-row .selected');  // btn means thumbnails in media library
        const _self = this;
        const ids = [];
        for (let i = 0; i < btns.length; i++) {
            let imageId = btns[i].id;
            $(`#${imageId}`).removeClass('selected');      // for disabling buttons after delete
            imageId = imageId.substring(3);
            ids.push(imageId)
        }
        axios.post(this.removeMediaRoute, {"media_ids": ids}).then(async (response) => {
            if (response.status === 200) {
                _self.selectedArray = [];

                await _self.removeImageFromRow(btns);
                _self.mlCheckSelected();
                this.notifyToast(mlLang.notifyDelete)
            }
        })
    }


    /**
     * removes array of images from library row
     */
    removeImageFromRow(images) {
        images.each((index, image) => {
            $(image).closest('.th-div').remove();
        })
    }


    /**
     * add a single image to the library row
     * @param image
     */
    mlAddSingleImage(image) {
        const row = document.querySelector(`#library-row-${this.libraryId}`);
        const firstChild = document.querySelector(`#library-row-${this.libraryId} .th-div:first-child`);
        const toSend = [];
        const elements = {};
        const url = `storage/${image['directory']}/${image['filename']}-139x-${image['extension']}`
        const id = image['id'];
        const _self = this;
        toSend.push({
            'id': id, 'element': '', 'url': url,
        });
        elements[id] = image;
        axios.post(this.imageExistRoute, {           // sending request to check
            'images': toSend
        }).then(res => {
            res.data.forEach(data => {
                const image = elements[data['id']];
                let url = `/storage/${image['directory']}/${image['filename']}`
                if (data['element'] === true) {
                    url += `-139x-${image['extension']}`;
                } else {
                    url += "." + image['extension'];
                }
                const imageUrl = `/storage/${image['directory']}/${image['filename']}.${image['extension']}`;     // for having the original url for getting it
                const div = document.createElement('div');
                div.classList.add('th-div')
                div.innerHTML = `  <img id="thumbnail-${image['id']}"  thumbnailId="${image['id']}" src="${url}"
                alt="image" class="padding-0 img-thumbnail "  imageUrl="${imageUrl}"><div class="m-over"></div>
                <span type="button" thumbnailId="${image['id']}" class="fw-bold btn th-info-button p-0 "><i class="far fa-edit  fs-5"></i></span>
                <div class="th-info "><span class="fw-bolder th-text">${image['filename']}</span><span class="fw-bold th-text">${image['alt']}</span></div>`

                row.insertBefore(div, firstChild);
                $(div).on('click', () => {                          //initiating select thumbnail
                    _self.mlSelectThumbnail($(div).find('.img-thumbnail').attr('thumbnailId'))
                })
                $(div).find('.th-info-button').on('click', (e) => {          //initiating show info
                    const info = $(`#library-info-${_self.libraryId}`);
                    e.stopPropagation();
                    if ((!$(info).hasClass('show-image-info'))) {
                        _self.mlOpenInfo(info, $(e.currentTarget));
                    } else if ($(div).attr('thumbnailId') !== info.attr('openedBy')) {
                        _self.mlOpenInfo(info, $(div));
                    } else {
                        _self.mlCloseInfo(info)
                    }
                })
            });

        })
    }

    /**
     * refreshes the library
     * @param images
     */
    mlRefreshLibrary(images) {
        const row = document.querySelector(`#library-row-${this.libraryId}`);
        const toSend = [];
        const elements = {};
        const _self = this;
        images.forEach(image => {       // storing all urls to send for checking if exist
            const url = `storage/${image['directory']}/${image['filename']}-139x-${image['extension']}`
            const id = image['id'];
            toSend.push({
                'id': id, 'element': '', 'url': url,
            });
            elements[id] = image;
        })
        axios.post(this.imageExistRoute, {           // sending request to check
            'images': toSend
        }).then(res => {
            res.data.forEach(data => {
                const image = elements[data['id']];
                const url = `/storage/${image['directory']}/${image['filename']}${data['element'] === true ? '-139x-' + image['extension'] : '.' + image['extension']}`;

                const imageUrl = `/storage/${image['directory']}/${image['filename']}.${image['extension']}`;     // for having the original url for getting it
                const div = document.createElement('div');
                div.classList.add('th-div')
                div.innerHTML = `  <img id="thumbnail-${image['id']}"  thumbnailId="${image['id']}" src="${url}"

            alt="${image['alt']}" class="padding-0 img-thumbnail "  imageUrl="${imageUrl}">
                          <div class="m-over"></div>
                                    <span type="button" thumbnailId="${image['id']}" class="fw-bold btn th-info-button p-0 "><i class="far fa-edit  fs-5"></i></span>
                        <div class="th-info ">
                                    <span class="fw-bolder th-text">${image['filename']}</span>
                                    <span class="fw-bold th-text">${image['alt']}</span></div>    `
                row.insertBefore(div, _self.sentinelEl);
            });

        }).then(() => {
            _self.initSelectThumbnailEvent();
            _self.initShowInfoButton();
        }).catch();
    }

    /**
     * initiates the event for showing info
     */
    initShowInfoButton() {
        const info = $(`#library-info-${this.libraryId}`);
        const _self = this;
        $('.th-info-button').off('click').on('click', (e) => {
            const infoButton = e.currentTarget;
            e.stopPropagation();
            if ((!$(info).hasClass('show-image-info'))) {
                _self.mlOpenInfo(info, $(infoButton));
            } else if ($(infoButton).attr('thumbnailId') !== info.attr('openedBy')) {
                _self.mlOpenInfo(info, $(infoButton));
            } else {
                _self.mlCloseInfo(info)
            }
        })
    }


    mlOpenInfo(info, element) {
        const _self = this;
        $(`#thumbnail-${info.attr('openedBy')}`).removeClass('selected-th-info');
        info.attr('openedBy', element.attr('thumbnailId'))
        info.removeClass('close-image-info')
        info.addClass('show-image-info');
        element.closest('.th-div').find('#thumbnail-' + element.attr('thumbnailId')).addClass('selected-th-info')

        let infos = '';
        axios.post(this.mediaSingleRoute, {"id": element.attr('thumbnailId')}).then((response) => {
            if (response.status === 200) {
                infos = _self.mlShowThumbnailInfo(response.data);
                info.html(infos);
                info.find('.th-info-details').removeClass('d-none');
                $(`#details-body-${_self.libraryId}`).collapse('show');
                $('.th-info-details').find('.setAlt-btn').off('click').on('click', () => {
                    _self.setAlt(response.data['id'])
                })
            }
        });
    }

    mlCloseInfo(info) {
        $(`#thumbnail-${info.attr('openedBy')}`).removeClass('selected-th-info');
        info.find('.th-info-details').addClass('d-none');
        info.attr('openedBy', '')
        info.removeClass('show-image-info');
        info.addClass('close-image-info')
    }

    /**
     * for custom events
     */
    useCallBack() {
        if (typeof mediaLibraryCallBack != 'undefined') {
            mediaLibraryCallBack(this);
        }
    }
}
