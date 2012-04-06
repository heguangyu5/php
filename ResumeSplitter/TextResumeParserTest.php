<?php

require_once 'TextResumeParser.php';
require_once 'PHPUnit/Framework/TestCase.php';

class SampleResumeParser
{
    public function parseBasicInfo($basicInfo)
    {
        // .... 50 lines code ignore
        return array(
            'name'      => '张大胆',
            'gender'    => 'm',
            'birthYear' => 1988
        );
    }

    public function parseEducationBackground($educationBackground)
    {
        // ... 40 lines code ignore
        return array(
            'lastSchool' => '清华大学',
            'lastDegree' => '本科',
            'educationBackground' => $educationBackground
        );
    }
}

class TextResumeParserTest extends PHPUnit_Framework_TestCase
{
    public function testDoParse()
    {
        $textResume =<<<TEXT_RESUME
男 |40岁|北京朝阳区| 本科 | 十九年以上工作经验
基本信息
姓名： 张大胆 性别： 男 出生日期： 1988年4月1日
自我评价
我乐于助人，积级向上，还有胆子比较大。
求职意向
现任职业：建筑师 期望工作性质：不限 期望税前薪资：面谈
工作经历
2009年11月― 至今 张大胆建筑工程设计有限公司
教育背景
2001年09月 ― 2005年07月 清华大学 环境艺术设计 本科
语言能力
英语【掌握水平】：良好
TEXT_RESUME;

        $sampleResumeParser = new SampleResumeParser();
        $parsers = array(
            'basicInfo'           => array($sampleResumeParser, 'parseBasicInfo'),
            'educationBackground' => array($sampleResumeParser, 'parseEducationBackground')
        );

        $parser = new TextResumeParser();
        $parser->setTextResume($textResume)
               ->doSplit()
               ->setParsers($parsers)
               ->doParse();

        $expectedResult = array(
            'name'      => '张大胆',
            'gender'    => 'm',
            'birthYear' => 1988,
            'lastSchool' => '清华大学',
            'lastDegree' => '本科',
            'educationBackground' => '2001年09月 ― 2005年07月 清华大学 环境艺术设计 本科'
        );

        $this->assertEquals($expectedResult, $parser->getResult());
    }
}
