<?php

namespace Virtualorz\Tag;

use Illuminate\Support\Facades\Facade;

/**
 * @see Virtualorz\Tag\Tag
 */
class TagFacade extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {
        return 'tag';
    }

}
