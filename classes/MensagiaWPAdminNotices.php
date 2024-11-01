<?php

class MensagiaWPAdminNotices
{
    private $message;
    private $type;

    public function __construct($type, $message)
    {
        $this->type    = $type;
        $this->message = $message;

        $arrayNotices = get_option('MensagiaWPAdminNotices');

        if (!is_array($arrayNotices)) {
            $arrayNotices = array();
        }

        $new_message = $type."|||".$message;

        array_push($arrayNotices, $new_message);

        // type options: updated:green || error:red
        update_option('MensagiaWPAdminNotices', $arrayNotices);
    }
}
