# laravel-media-library

MediaLibrary is a laravel library for handling images in laravel projects . You can upload your images , modify their
alt , categorize them into the folders and etc.
It also compress your images to 4 different sizes and keeps the original size , so you can access them in client side
based on user screen or use it with lazy loading .

[![Packagist Version](https://img.shields.io/packagist/v/optix/media.svg)](https://packagist.org/packages/yektadg/medialibrary)
[![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)](https://github.com/yektaDG/laravel-media-library/blob/main/LICENSE.md)

## Screenshots

....

## Installation

You can install the package via composer:

```shell
 composer require yektadg/medialibrary
```

Once installed, you should publish the provided assets to create the necessary migration and config files.

```shell
 php artisan vendor:publish --provider="YektaDG\Medialibrary\Providers\MediaLibraryServiceProvider" 
```

## Requirements

[BootStrap 5](https://getbootstrap.com/)

[jQuery](https://jquery.com/)

[Axios Js](https://axios-http.com/docs/intro)

## Usage

Just add the following line at the end of your laravel blade after all javascript codes .

```html

<x-mediaLibrary::ml-init :id="your prefered id(withoutspace)"></x-mediaLibrary::ml-init>
```

If you want to just access the media library view for using alongside your customized view you can use the following
line .

```html

<x-mediaLibrary::media-library :libraryId="your prefered id(withoutspace)"></x-mediaLibrary::media-library>
```

If you want to use both on the same page beware of id conflict between them .

Then you must add ```ml-button``` class to your html button to open the library .

You can use MediaLibrary for different use types ( you can view all usage under this section ) , so you should specify
it when defining button .

Sample of defining MediaLibrary for ```hidden``` usage :

```html

<button
        useType="hidden"
        multipleSelect="true"
        useId="image-holder-1"
        class="ml-button"
        type="button">
    Upload Images
</button>
<input id="image-holder-1" type="hidden" name="image-holder" class="image-holder">
```

In above code after clicking on `Upload Images` MediaLibrary pops up and after selecting images , they will be stored in
the hidden input with html id `image-holder-1` .

The three first attributes are MediaLibrary attributes .you can see all of MediaLibrary attributes in the below
section .

### Attributes

MediaLibrary uses different html attributes on html elements (like buttons,anchor and ...) to handle different
situation .

You can see list of attributes below :

| Attribute        | Description                                                                                                   |
|------------------|---------------------------------------------------------------------------------------------------------------|
| `useType`        | Defines the type of element for usage (for now 3 types are supported : `hidden` , `imagePreview` , `tinymce`) |
| `multipleSelect` | Allows MediaLibrary to select multiple images for use (like when you use tinymce)                             | 
| `useId`          | Refers the html element that holds the images                                                                 |

Note : All attributes and their values are case-sensitive

## Features

### Limiting Access To Medias

You can limit access to medias by adding `accessAllMedia` attribute to the `Request` using laravel middleware.

Here is the example how to do it :

1. First make a middleware with laravel command `php artisan make:middleware CheckAccessMedia`
2. Register your middleware in `kernel.php` at `$routeMiddleware` array in `Http` folder of your
   project `'CheckMediaAccess' => CheckMediaAccess::class`
3. Then add the alias of your middleware in `middleware` array in `medialibray.php` at config folder of your project in
   this case `'CheckMediaAccess'`
4. Edit your newly created middleware and add your condition for example :

 ```php
 public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        $condition = false;
        if ($user && $user->id == 1) {
            $condition = true;
        }
        $request->merge(['accessAllMedia' => $condition]);

        return $next($request);
    }
```

In above example user with id of `1` can access all of uploaded medias but all other users can only access their medias.

### Lazy Loading

MediaLibrary generates images in 4 different size , you can use them for lazy loading .
Below code loads images based on user screen size :

```javascript
/**
 * changes the image tag src based on windwos
 */
$(document).ready(() => {
    let images = [];
    let i = 0;
    $('.resizing').each(function () {
        const sizes = [1280, 1500];
        let src = $(this).attr('originalSrc');
        let name = src.slice(0, src.lastIndexOf('.'))
        let size = getClosest(sizes, $(window).width());
        let ext = src.substring(src.lastIndexOf(".") + 1);
        let path = name + `-${size}x-${ext}`;

        images.push({
            'id': ++i,
            'element': this,
            'url': path,
        })
    })
    if (images.length > 0) {
        imageExist(images)
    }
})


/**
 * check if image exists then put it in src else replace it with original Image
 */
function imageExist(images) {
    const result = images.reduce((obj, cur) => ({...obj, [cur.id]: cur}), {})   //converting to map
    axios.post(imageExistRoute, {
        'images': images
    }).then(res => {
        res.data.forEach(el => {
            let element = $(result[el['id']]['element']);
            if (el['element'] == true) {
                element.attr('src', el['url'])       //  setting the new src
            } else {
                element.attr('src', element.attr('originalSrc'))
            }
        })
    }).catch();
}

/**
 * returns the closest number to the window size
 * @param arr
 * @param target
 * @returns {*}
 */
function getClosest(arr, target) {
    if (arr == null) {
        return
    }
    return arr.reduce((prev, current) => Math.abs(current - target) < Math.abs(prev - target) ? current : prev);
}
```

Note : you need to define `imageExistRoute`  before above codes;

```
const imageExistRoute = '{{route('medialibrary.image-exists')}}';
 ```

Then you can use lazy loading by adding `resizing` class and `originalSrc` attribute to your html image tag .

```Html
 <img class="resizing" src="address to your default image"
      originalSrc="address to your real image" alt="">

```

Note : default image should have low disk size and be a constant image for all pages

### Language

You can edit `toast.blade.php` for changing toast header in `resources/views/vendor/yektadg/medialibrary`.

For changing toast messages edit `mlLang.js` in `public/vendor/yektadg/medialibrary`.