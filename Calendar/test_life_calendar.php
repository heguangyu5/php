<?php
require_once 'LifeCalendar.php';
require_once 'Smtp.php';

function generateSubject($subject)
{
    $preferences = array(
        'input-charset'     => 'UTF-8',
        'output-charset'    => 'UTF-8',
        'line-length'       => 76,
        'line-break-chars'  => '\n',
        'scheme'            => 'B'
    );

    return str_replace('\n', "\n", iconv_mime_encode('Subject', $subject, $preferences));
}

$event = array(
    'startTime'     => '2012-10-05 11:00:00',
    'endTime'       => '2012-10-05 13:00:00',
    'summary'       => '去全聚德吃烤鸭',
    'location'      => '全聚德烤鸭店王府井店, 北京市东城区帅府园胡同9号',
    'description'   => '中午11点准时到,如果忙的话提前说.',
    'organizer'     => array('cn' => 'Employee001(Employee001@126.com)', 'email' => 'Employee001@126.com'),
    'attendee'      => array('cn' => '何广宇(heguangyu5@gmail.com)', 'email' => 'heguangyu5@gmail.com')
);

$calendar = new LifeCalendar($event);

$from = 'fake@fakedomain.com';
$transport = new Smtp(
    'smtp.fakedomain.com',
    array(
        'auth'     => 'login',
        'username' => $from,
        'password' => 'password',
    )
);

$to        = $event['attendee']['email'];
$subject   = generateSubject($event['summary']);
$rawHeader = "From: $from
To: $to
$subject
MIME-Version: 1.0
Content-Type: text/calendar; method=REQUEST; charset=\"utf-8\"
Content-Transfer-Encoding: 8bit\n";
$rawContent = $calendar->__toString();

echo "\n========================\n";
echo $rawHeader . Zend_Mime::LINEEND . $rawContent;
echo "\n========================\n\n";

readline("send out ?");

$transport->setRawHeader($rawHeader);
$transport->setRawContent($rawContent);
$transport->setReturnPath($from);
$transport->setRecipients(array($to));
$transport->sendMail();

echo "scheduled\n";

readline('update ?');

$update = array(
    'startTime'     => '2012-10-05 17:00:00',
    'endTime'       => '2012-10-05 19:00:00',
    'summary'       => '去东来顺吃火锅',
    'location'      => '北京市东城区王府井大街198号',
    'description'   => '不想吃烤鸭了,去吃火锅吧'
);
$calendar->update($update);

$subject   = generateSubject($update['summary']);
$rawHeader = "From: $from
To: $to
$subject
MIME-Version: 1.0
Content-Type: text/calendar; method=REQUEST; charset=\"utf-8\"
Content-Transfer-Encoding: 8bit\n";
$rawContent = $calendar->__toString();

echo "\n========================\n";
echo $rawHeader . Zend_Mime::LINEEND . $rawContent;
echo "\n========================\n\n";

readline("send out ?");

$transport->setRawHeader($rawHeader);
$transport->setRawContent($rawContent);
$transport->setReturnPath($from);
$transport->setRecipients(array($to));
$transport->sendMail();

echo "updated\n";

readline("cancel ?");

$calendar->cancel();

$subject   = generateSubject('活动取消,还是各自在家吃吧');
$rawHeader = "From: $from
To: $to
$subject
MIME-Version: 1.0
Content-Type: text/calendar; method=CANCEL; charset=\"utf-8\"
Content-Transfer-Encoding: 8bit\n";
$rawContent = $calendar->__toString();

echo "\n========================\n";
echo $rawHeader . Zend_Mime::LINEEND . $rawContent;
echo "\n========================\n\n";

readline("send out ?");

$transport->setRawHeader($rawHeader);
$transport->setRawContent($rawContent);
$transport->setReturnPath($from);
$transport->setRecipients(array($to));
$transport->sendMail();

echo "cancelled\n";
