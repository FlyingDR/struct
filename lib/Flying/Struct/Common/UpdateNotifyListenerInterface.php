<?php

namespace Flying\Struct\Common;

/**
 * Interface for classes that can act as listeners to structure items update notifications
 */
interface UpdateNotifyListenerInterface
{

    /**
     * Handle notification about update of given property
     *
     * @param SimplePropertyInterface $property
     * @return void
     */
    public function updateNotify(SimplePropertyInterface $property);

}
