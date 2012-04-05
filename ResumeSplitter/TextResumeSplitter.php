<?php
/**
 * TextResumeSplitter - split a text resume into pieces by seperators
 */
class TextResumeSplitter
{
    protected $textResume;
    
    protected $seperators = array(
        'basicInfo'             => '基本信息',
        'selfAssessment'        => '自我评价',
        'careerObjective'       => '求职意向',
        'workExperience'        => '工作经历',
        'educationBackground'   => '教育背景',
        'languageSkills'        => '语言能力'
    );
    
    protected $parts = array(
        'topLines' => array()
    );
    
    public function setTextResume($textResume)
    {
        $this->textResume = (string) $textResume;
        return $this;
    }
    
    protected function identifySeperator($line) 
    {
        return array_search($line, $this->seperators);
    }
    
    public function doSplit()
    {
        if (null === $this->textResume) {
            return;
        }
        
        $lines = explode("\n", $this->textResume);
        $currentSeperator = null;
        while($lines) {
            $line = trim(array_shift($lines));
            $seperator = $this->identifySeperator($line);
            if ($seperator) {
                $currentSeperator = $seperator;
                $this->parts[$currentSeperator] = array();
                continue;
            }
            if ($currentSeperator) {
                $this->parts[$currentSeperator][] = $line;
                continue;
            }
            $this->parts['topLines'][] = $line;
        }
        
        foreach ($this->parts as $key => $lines) {
            $this->parts[$key] = implode("\n", $lines);
        }
    }
    
    public function getPart($key)
    {
        if (isset($this->parts[$key])) {
            return $this->parts[$key];
        }
    }
}
