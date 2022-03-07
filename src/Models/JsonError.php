<?php

namespace App\Models;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Gestion personnalisées des erreurs Json
 */
class JsonError
{
    private $error;
    private $messages;
    
    public function __construct(int $error = Response::HTTP_NOT_FOUND, string $message = "Not Found")
    {
        $this->error = $error;
        $this->messages[] = $message;
    }
    
    public function setValidationErrors(ConstraintViolationListInterface $violations)
    {
        foreach ($violations as $violation) {
            $this->messages[] = $violation->getPropertyPath() . ': ' .  $violation->getMessage();
        }
    }

    /**
     * Get the value of error
     */ 
    public function getError()
    {
        return $this->error;
    }

    /**
     * Set the value of error
     *
     * @return  self
     */ 
    public function setError($error)
    {
        $this->error = $error;

        return $this;
    }

    /**
     * Get the value of message
     */ 
    public function getMessage()
    {
        return $this->messages;
    }

    /**
     * Set the value of message
     *
     * @return  self
     */ 
    public function setMessage($message)
    {
        $this->messages = $message;

        return $this;
    }
}