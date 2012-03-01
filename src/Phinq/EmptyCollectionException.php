<?php

namespace Phinq;

use \Exception;

/**
 * Thrown when trying to access a member of an empty collection
 */
class EmptyCollectionException extends Exception { }