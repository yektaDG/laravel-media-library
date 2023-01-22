<?php


namespace YektaDG\Medialibrary\Facades;

use Illuminate\Support\Facades\Facade;

class ExtendedMediaFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'extendedmediable.uploader';
    }

    public static function getFacadeRoot()
    {
        // prevent the facade from behaving like a singleton
        if (!self::isMock()) {
            self::clearResolvedInstance('extendedmediable.uploader');
        }
        return parent::getFacadeRoot();
    }

}
