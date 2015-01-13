<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the argv tokenizer
 */
namespace RDev\Console\Requests\Parsers\Tokenizers;

class Argv implements ITokenizer
{
    /**
     * {@inheritdoc}
     */
    public function tokenize($input)
    {
        // Get rid of the application name
        array_shift($input);

        foreach($input as &$token)
        {
            $token = stripslashes($token);
        }

        return $input;
    }
}