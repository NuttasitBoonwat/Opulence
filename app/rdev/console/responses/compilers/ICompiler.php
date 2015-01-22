<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the interface for response compilers to implement
 */
namespace RDev\Console\Responses\Compilers;
use RDev\Console\Responses\Formatters\Elements;

interface ICompiler 
{
    /**
     * Compiles a message
     *
     * @param string $message The message to compile
     * @param Elements\ElementRegistry $elementRegistry The element registry associated with the response
     * @return string The compiled message
     * @throws \RuntimeException Thrown if there was an issue compiling the message
     */
    public function compile($message, Elements\ElementRegistry $elementRegistry);
}