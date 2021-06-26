<?php

$DEFAULT_NAME = 'John Doe';

// sv-SE, YYYY-mm-dd HH:ii:ss (tested with iOS)
//$DATE_TIME_PATTERN = "/\[\d\d\d\d\-\d\d\-\d\d\, \d\d\:\d\d\:\d\d\]\ /";
//$DATE_TIME_FORMAT = '[Y-m-d, H:i:s] ';
//$SYS_MESSAGE_SUBSTRING = 'Meddelanden och samtal är komplett krypterade';
//$ATTACHMENT_PATTERN = '<attached: .+>' // TODO: retest

// sv-FI, dd-mm-YYYY HH.ii (tested with Android)
$DATE_TIME_PATTERN = '/\d{2}\-\d{2}\-\d{4}\ \d{2}\.\d{2} - /';
$DATE_TIME_FORMAT = 'd-m-Y H.i - ';
$SYS_MESSAGE_SUBSTRING = 'Meddelanden och samtal är komplett krypterade';
$ATTACHMENT_PATTERN = "/\u{200E}(.+) \(bifogad fil\)/";

// Output formats
$DATE_FORMAT = 'Y-m-d';
