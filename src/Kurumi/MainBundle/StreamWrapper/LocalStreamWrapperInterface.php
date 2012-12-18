<?php

namespace Kurumi\MainBundle\StreamWrapper;

/**
 * Drupal stream wrapper extension.
 *
 * Extend the StreamWrapperInterface with methods expected by Drupal stream
 * wrapper classes.
 */
interface LocalStreamWrapperInterface extends PhpStreamWrapperInterface
{
    /**
     * Set the absolute stream resource URI.
     *
     * This allows you to set the URI. Generally is only called by the factory
     * method.
     *
     * @param $uri
     *   A string containing the URI that should be used for this instance.
     */
    function setUri($uri);

    /**
     * Returns the stream resource URI.
     *
     * @return
     *   Returns the current URI of the instance.
     */
    public function getUri();

    /**
     * Returns the MIME type of the resource.
     *
     * @param $uri
     *   The URI, path, or filename.
     *
     * @return
     *   Returns a string containing the MIME type of the resource.
     */
    public static function getMimeType($uri);

    /**
     * Changes permissions of the resource.
     *
     * PHP lacks this functionality and it is not part of the official stream
     * wrapper interface. This is a custom implementation for Drupal.
     *
     * @param $mode
     *   Integer value for the permissions. Consult PHP chmod() documentation
     *   for more information.
     *
     * @return
     *   Returns TRUE on success or FALSE on failure.
     */
    public function chmod($mode);

    /**
     * Returns canonical, absolute path of the resource.
     *
     * Implementation placeholder. PHP's realpath() does not support stream
     * wrappers. We provide this as a default so that individual wrappers may
     * implement their own solutions.
     *
     * @return
     *   Returns a string with absolute pathname on success (implemented
     *   by core wrappers), or FALSE on failure or if the registered
     *   wrapper does not provide an implementation.
     */
    public function realpath();

    /**
     * Gets the name of the directory from a given path.
     *
     * This method is usually accessed through drupal_dirname(), which wraps
     * around the normal PHP dirname() function, which does not support stream
     * wrappers.
     *
     * @param $uri
     *   An optional URI.
     *
     * @return
     *   A string containing the directory name, or FALSE if not applicable.
     *
     * @see drupal_dirname()
     */
    public function dirname($uri = null);
}