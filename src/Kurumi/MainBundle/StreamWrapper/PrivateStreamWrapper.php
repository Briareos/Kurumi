<?php

namespace Kurumi\MainBundle\StreamWrapper;

/**
 * Drupal private (private://) stream wrapper class.
 *
 * Provides support for storing privately accessible files with the Drupal file
 * interface.
 *
 * Extends DrupalPublicStreamWrapper.
 */
class PrivateStreamWrapper extends AbstractLocalStreamWrapper
{
    /**
     * Overrides getExternalUrl().
     *
     * Return the HTML URI of a private file.
     */
    function getExternalUrl()
    {
        $path = str_replace('\\', '/', $this->getTarget());

        return url('system/files/' . $path, array('absolute' => true));
    }
}