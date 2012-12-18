<?php

namespace Kurumi\MainBundle\StreamWrapper;

/**
 * Drupal temporary (temporary://) stream wrapper class.
 *
 * Provides support for storing temporarily accessible files with the Drupal
 * file interface.
 *
 * Extends DrupalPublicStreamWrapper.
 */
class TemporaryStreamWrapper extends AbstractLocalStreamWrapper
{
}