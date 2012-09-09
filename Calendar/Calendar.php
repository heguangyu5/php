<?php
class Calendar implements Serializable
{
    const PRODID = '-//HGY Program//Calendar//EN';

    const DEFAULT_ORGANIZER_CN    = 'Calendar Organizer';
    const DEFAULT_ORGANIZER_EMAIL = 'calendar-organizer@fakedomain.com';

    const CATEGORIES = 'LIFE';

    protected $event;

    /**
     * Constructor: create an event calendar
     *
     * event contains:
     *  - uid
     *  - dtstamp
     *  - dtstart
     *  - dtend
     *  - summary
     *  - location
     *  - description
     *  - organizer array('cn' => 'Common Name', 'email' => 'organizer@example.com')
     *  - attendee  array('cn' => 'Common Name', 'email' => 'attendee@example.com')
     */
    public function __construct(array $event)
    {
        $this->event = $event;
        $this->event['method']   = 'REQUEST';
        $this->event['status']   = 'CONFIRMED';
        $this->event['sequence'] = 0;
    }

    /**
     * Update calendar event
     *
     * update contains
     *  - dtstamp
     *  - dtstart
     *  - dtend
     *  - summary
     *  - location
     *  - description
     */
    public function update(array $update)
    {
        $this->event['sequence']++;
        $this->event = array_merge($this->event, $update);
    }

    public function cancel()
    {
        $this->event['sequence']++;
        $this->event['method'] = 'CANCEL';
        $this->event['status'] = 'CANCELLED';
    }

    public function __toString()
    {
        $calendar = array();

        $calendar[] = 'BEGIN:VCALENDAR';
        $calendar[] = 'CALSCALE:GREGORIAN';
        $calendar[] = 'PRODID:' . self::PRODID;
        $calendar[] = 'VERSION:2.0';
        $calendar[] = 'METHOD:' . $this->event['method'];

        $calendar[] = 'BEGIN:VEVENT';
        $calendar[] = 'UID:' . $this->event['uid'];
        $calendar[] = 'DTSTAMP:' . $this->event['dtstamp'];
        $calendar[] = 'DTSTART:' . $this->event['dtstart'];
        $calendar[] = 'DTEND:' . $this->event['dtend'];
        $calendar[] = 'TRANSP:OPAQUE';
        $calendar[] = 'SEQUENCE:' . $this->event['sequence'];
        $calendar[] = $this->splitLongText('SUMMARY:' . $this->event['summary']);
        $calendar[] = $this->splitLongText('LOCATION:' . $this->event['location']);
        $calendar[] = $this->splitLongText('DESCRIPTION:' . $this->event['description']);

        if ($this->event['organizer']['email'] == $this->event['attendee']['email']) {
            $calendar[] = 'ORGANIZER;CN=' . self::DEFAULT_ORGANIZER_CN . ':MAILTO:' . self::DEFAULT_ORGANIZER_EMAIL;
            $calendar[] = 'ATTENDEE;CUTYPE=INDIVIDUAL;ROLE=REQ-PARTICIPANT;PARTSTAT=ACCEPTED;';
            $calendar[] = ' RSVP=FALSE;CN=' . $this->event['attendee']['cn'] . ';LANGUAGE=en:MAILTO:' . $this->event['attendee']['email'];
        } else {
            $calendar[] = 'ORGANIZER;CN=' . $this->event['organizer']['cn'] . ':MAILTO:' . $this->event['organizer']['email'];
            $calendar[] = 'ATTENDEE;CUTYPE=INDIVIDUAL;ROLE=REQ-PARTICIPANT;PARTSTAT=NEEDS-ACTION;';
            $calendar[] = ' RSVP=TRUE;CN=' . $this->event['attendee']['cn'] . ';LANGUAGE=en:MAILTO:' . $this->event['attendee']['email'];
        }

        $calendar[] = 'CATEGORIES:' . self::CATEGORIES;
        $calendar[] = 'CLASS:PUBLIC';
        $calendar[] = 'STATUS:' . $this->event['status'];
        $calendar[] = 'END:VEVENT';

        $calendar[] = 'END:VCALENDAR';

        return implode("\n", $calendar);
    }

    protected function splitLongText($text)
    {
        if (strlen($text) <= 75) {
            return $text;
        }

        $lines = array();
        for ($i = 0, $l = mb_strlen($text, 'UTF-8'); $i < $l; $i += 24) {
            $lines[] = mb_substr($text, $i, 24, 'UTF-8');
        }

        return implode("\n ", $lines);
    }

    public function serialize()
    {
        return serialize($this->event);
    }

    public function unserialize($serialized)
    {
        $this->event = unserialize($serialized);
    }
}
