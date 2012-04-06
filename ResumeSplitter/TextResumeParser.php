<?php

require_once 'TextResumeSplitter.php';

/**
 * Extend TextResumeSplitter to be a TextResumeParser.
 */
class TextResumeParser extends TextResumeSplitter
{
    /**
     * Resume parse result
     */
    protected $result = array();

    /**
     * Parsers to parse specified parts
     */
    protected $parsers = array();

    public function setParsers(array $parsers)
    {
        $this->parsers = $parsers;
        return $this;
    }

    public function addParser($name, $parser)
    {
        $this->parses[$name] = $parser;
        return $this;
    }

    public function doParse()
    {
        foreach ($this->parsers as $key => $parser) {
            $part = $this->getPart($key);
            if ($part) {
                $result = call_user_func($parser, $part);
                if (!is_array($result)) {
                    throw new Exception('parse result should be an array');
                }
                $this->result += $result;
            }
        }
    }

    public function getResult()
    {
        return $this->result;
    }
}
