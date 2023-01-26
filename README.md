# laravel-media-library

MediaLibrary is a laravel library for handling images in laravel projects . You can upload your images , modify their alt , categorize them into the folders and etc.
It also compress your images to 4 different sizes and keeps the original size , so you can access them in client side based on user screen or use it with lazy loading .

[![Packagist Version](https://img.shields.io/packagist/v/optix/media.svg)](https://packagist.org/packages/yektadg/medialibrary)
[![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)](https://github.com/yektaDG/laravel-media-library/blob/main/LICENSE.md)

## Installation

You can install the package via composer:

```
composer require yektadg/medialibrary
```

Once installed, you should publish the provided assets to create the necessary migration and config files.

```
php artisan vendor:publish --provider="YektaDG\Medialibrary\Providers\MediaLibraryServiceProvider" 
```

## Requirements

[jQuery](https://jquery.com/)
and
[Axios Js](https://axios-http.com/docs/intro) .

## Screenshots

....

## Usage

Just add the following line at the end of your laravel blade after all javascript codes .

```
<x-mediaLibrary::ml-init :id="your prefered id(withoutspace)" ></x-mediaLibrary::ml-init>
```

If you want to just access the media library view for using alongside your customized view you can use the following line .

```
<x-mediaLibrary::media-library :libraryId="your prefered id(withoutspace)" ></x-mediaLibrary::media-library>
```

If you want to use both on the same page beware of id conflict between them . 
