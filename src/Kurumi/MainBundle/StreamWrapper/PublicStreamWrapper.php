<?php

namespace Kurumi\MainBundle\StreamWrapper;

/**
 * Drupal public (public://) stream wrapper class.
 *
 * Provides support for storing publicly accessible files with the Drupal file
 * interface.
 */
class PublicStreamWrapper extends AbstractLocalStreamWrapper
{
    /**
     * Overrides getExternalUrl().
     *
     * Return the HTML URI of a public file.
     */
    function getExternalUrl()
    {
        $path = str_replace('\\', '/', $this->getTarget());

        return $this->getDirectoryPath() . '/' . $path;
    }
}