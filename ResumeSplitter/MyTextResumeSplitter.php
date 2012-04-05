<?php

require_once 'TextResumeSplitter.php';

class MyTextResumeSplitter extends TextResumeSplitter
{
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
