<?php
declare(strict_types=1);

namespace App\Domain\Exceptions;

class MovementNotFoundException extends \Exception
{
    public $message = 'The movement requested does not exist.';
}