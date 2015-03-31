<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the interface for console responses to implement
 */
namespace RDev\Console\Responses;
use RuntimeException;

interface IResponse
{
    /**
     * Clears the response from view
     */
    public function clear();

    /**
     * Sets whether or not messages should be styled
     *
     * @param bool $isStyled Whether or not messages should be styled
     */
    public function setStyled($isStyled);

    /**
     * Writes to output
     *
     * @param string|array $messages The message or messages to display
     * @throws RuntimeException Thrown if there was an issue writing the messages
     */
    public function write($messages);

    /**
     * Writes to output with a newline character at the end
     *
     * @param string|array $messages The message or messages to display
     * @throws RuntimeException Thrown if there was an issue writing the messages
     */
    public function writeln($messages);
}