<?php

require_once 'TextResumeSplitter.php';
require_once 'PHPUnit/Framework/TestCase.php';

class TextResumeSplitterTest extends PHPUnit_Framework_TestCase
{
    public function testDoSplit()
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
        $splitter = new TextResumeSplitter();
        $splitter->setTextResume($textResume)
                 ->doSplit();

        $this->assertEquals(
            '男 |40岁|北京朝阳区| 本科 | 十九年以上工作经验',
            $splitter->getPart('topLines')
        );
        $this->assertEquals(
            '姓名： 张大胆 性别： 男 出生日期： 1988年4月1日',
            $splitter->getPart('basicInfo')
        );
        $this->assertEquals(
            '我乐于助人，积级向上，还有胆子比较大。',
            $splitter->getPart('selfAssessment')
        );
        $this->assertEquals(
            '现任职业：建筑师 期望工作性质：不限 期望税前薪资：面谈',
            $splitter->getPart('careerObjective')
        );
        $this->assertEquals(
            '2009年11月― 至今 张大胆建筑工程设计有限公司',
            $splitter->getPart('workExperience')
        );
        $this->assertEquals(
            '2001年09月 ― 2005年07月 清华大学 环境艺术设计 本科',
            $splitter->getPart('educationBackground')
        );
        $this->assertEquals(
            '英语【掌握水平】：良好',
            $splitter->getPart('languageSkills')
        );
    }
}
