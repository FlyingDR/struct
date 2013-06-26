<?php

namespace Flying\Struct\Storage;

/**
 * Interface for classes that can be stored into storage
 */
interface StorableInterface
{

    /**
     * Get key to use in structures storage
     *
     * @return string
     */
    public function getStorageKey();

    /**
     * Get object representation suitable to put into storage
     *
     * @return mixed
     */
    public function toStorage();

}
