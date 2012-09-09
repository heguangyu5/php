<?php
require_once 'Calendar.php';

class LifeCalendar extends Calendar
{
    /**
     * Constructor: create an event calendar
     *
     * event contains:
     *  - startTime
     *  - endTime
     *  - summary
     *  - location
     *  - description
     *  - organizer array('cn' => 'Common Name', 'email' => 'organizer@example.com')
     *  - attendee  array('cn' => 'Common Name', 'email' => 'attendee@example.com')
     */
    public function __construct(array $event)
    {
        $event['uid'] = time();

        $event['dtstamp'] = gmdate('Ymd\THis\Z', time());
        $event['dtstart'] = gmdate('Ymd\THis\Z', strtotime($event['startTime']));
        $event['dtend']   = gmdate('Ymd\THis\Z', strtotime($event['endTime']));
        unset($event['startTime'], $event['endTime']);

        parent::__construct($event);
    }
    
    /**
     * Update calendar event
     *
     * update contains
     *  - startTime
     *  - endTime
     *  - summary
     *  - location
     *  - description
     */
    public function update(array $update)
    {
        $update['dtstamp'] = gmdate('Ymd\THis\Z', time());
        $update['dtstart'] = gmdate('Ymd\THis\Z', strtotime($update['startTime']));
        $update['dtend']   = gmdate('Ymd\THis\Z', strtotime($update['endTime']));
        
        parent::update($update);
    }
}
