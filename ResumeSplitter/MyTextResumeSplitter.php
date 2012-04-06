<?php

require_once 'TextResumeSplitter.php';

/**
 * A demo to show how to extend TextResumeSplitter to meet your own needs
 */
class MyTextResumeSplitter extends TextResumeSplitter
{
    /**
     * Step 1: override $seperators to your own, you can also extend it in __construct, just as you like.
     *         I override $seperators in this demo
     */
    protected $seperators = array(
        array('基本信息', '一、基本信息：'),
        '自我评价',
        '求职意向',
        'workExperience'        => array('工作经历', '工作经历：', '实习经历', '三、工作经历：', '工作经验'),
        'educationBackground'   => array('教育背景', '教育背景：', '二、教育背景：', '教育经历'),
        '语言能力',
        array('专业技能', '专业技能：',  '五、专业技能：'),
        array('项目经验', '四、项目经验：'),
        '证书信息',
        '培训经历',
        'IT技能',
        '获奖情况',
        '英语及专业技能',
        '爱好及自我评价',
        '其他信息',
        '六、外语能力与国际交流经验',
        '七、性格特征：'
    );

    /**
     * Step 2: if your seperator structure same as TextResumeSplitter, you don't need write your own
     *         identifySeperator() method. In this demo, $seperators structure changed, so I implement
     *         my own identifySeperator() method to identify lines.
     */
    protected function identifySeperator($line)
    {
        foreach ($this->seperators as $key => $seperator) {
            if (is_array($seperator)) {
                foreach ($seperator as $item) {
                    if ($line == $item) {
                        return $key;
                    }
                }
            } else {
                if ($line == $seperator) {
                    return $key;
                }
            }
        }
    }
}
