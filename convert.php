<?php

require('config.inc.php');

if (isset($_FILES['whatsapp'])) {
    $errors = array();
    $file_name = $_FILES['whatsapp']['name'];
    $file_size = $_FILES['whatsapp']['size'];
    $file_tmp = $_FILES['whatsapp']['tmp_name'];
    $file_type = $_FILES['whatsapp']['type'];
    $file_ext = strtolower(end(explode('.', $_FILES['whatsapp']['name'])));

    $extensions = array("txt");

    if (in_array($file_ext, $extensions) === false) {
        $errors[] = "extension not allowed";
    }

    if ($file_size > 16 * 1048576) {
        $errors[] = 'File size must be smaller than 16 MiB';
    }

    if (empty($errors) == true) {
        move_uploaded_file($file_tmp, "temp.txt");

        $target_file_name = str_replace('.txt', '.html', $file_name);

        if ($_POST["attachment"]) {
            // Define headers
            header("Cache-Control: public");
            header('Content-Type: application/octet-stream');
            header("Content-Disposition: attachment; filename=\"" . $target_file_name . "\"");
        }
    } else {
        exit;
    }

    if (isset($_POST["fname"])) {
        $me = $_POST["fname"];
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Whatsapp</title>
    <style>
        body {
            /*    background-color: #ece5dd;*/
            background-color: #E5DCD5;
            font-family: Verdana, Geneva, sans-serif;
            overflow-wrap: break-word;
        }

        @media only screen and (min-width: 600px) {
            .page {
                width: 600px;
                padding: 10px;
                margin: auto;
            }
        }


        .day {
            /*    background-color: #34b7f1;*/
            background-color: #E1F4FB;
            margin: auto;
            border-radius: 10px;
            text-align: center;
            width: 110px;
            position: -webkit-sticky;
            /* Safari */
            position: sticky;
            top: 0;
            font-size: small;
            color: Gray;
        }


        .message {
            background-color: White;
            border-radius: 10px;
            width: 300px;
            /*border: 1px solid black;*/
            padding: 10px;
            margin: 20px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        }

        .mymessage {
            /*    background-color: #dcf8c6; #DCF8C7*/
            background-color: #DCF8C7;
            border-radius: 10px;
            width: 300px;
            padding: 10px;
            margin: 20px 20px 20px auto;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        }

        .sysmessage {
            /*    background-color: LightBlue; #FFF4C6 end-to-end*/
            background-color: #E1F4FB;
            text-align: center;
            font-size: small;
            border-radius: 10px;
            width: 300px;
            padding: 10px;
            margin: 20px auto;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            color: Gray;
        }

        .text {
            margin: 0;
            padding: 0;
        }


        .sender {
            color: green;
        }


        .time {
            text-align: right;
            font-size: x-small;
            color: Gray;
        }

        .image {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
        }
    </style>

    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <div class="page">
        <?php
        $file = 'temp.txt';
        $namecolors = ["Blue", "BlueViolet", "Brown", "DarkCyan", "Crimson", "DarkBlue", "DarkGreen", "DeepPink", "Olive", "Orange", "OrangeRed", "Red", "Purple", "Tan", "Purple", "Magenta", "Maroon", "LimeGreen"];
        shuffle($namecolors);
        $colorindex = 0;
        $day = 0;

        if (!isset($me)) {
            $me = "";
        }

        $handle = fopen($file, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $filename = "";
                $location = "";

                // Detect attachment
                if (preg_match($ATTACHMENT_PATTERN, $line, $matches)) {
                    $filename = $matches[1];
                }

                if (strpos($line, "https://maps.google.com/?q=") > 0) {
                    $location = substr($line, strpos($line, "https://maps.google.com/?q=") + 27);
                }

                if (preg_match($DATE_TIME_PATTERN, $line, $matches)) {
                    $datecreated = date_create_from_format($DATE_TIME_FORMAT, $matches[0]);

                    // If the date for the current message differs from the
                    // previous one, print out a day indicator.
                    if ($day != intval(date_format($datecreated, 'd'))) {
                        $day = intval(date_format($datecreated, 'd'));
                        echo ("<div class=\"day\">");
                        echo (date_format($datecreated, $DATE_FORMAT));
                        echo ("</div>");
                    }

                    // Remove datetime from line
                    $line = preg_replace($DATE_TIME_PATTERN, "", $line);

                    $pos = strpos($line, $SYS_MESSAGE_SUBSTRING);

                    if ($pos !== false && $location == "") {
                        echo ("<div class=\"sysmessage\">");
                        $line = substr($line, strpos($line, $indicator));
                    } else {
                        $pattern = ": ";
                        $pos = strpos($line, $pattern);

                        if ($pos !== false) {
                            //echo(strpos(substr($line , 0 , $pos), $me)==0);
                            if (substr($line, 0, $pos) == $me) {
                                echo ("<div class=\"mymessage\">");
                                $last = true;
                            } else {
                                echo ("<div class=\"message\">");
                                $last = false;
                                $name = substr($line, 0, $pos);

                                if (!array_key_exists($name, $names)) {
                                    $names["$name"] = $namecolors[$colorindex];
                                    $colorindex++;
                                    if ($colorindex > 17) {
                                        $colorindex = 0;
                                    }
                                }
                                echo ("<div class=\"sender\" style=\"color:" . $names["$name"] . ";\">");
                                echo ($name);
                                echo ("</div>"); //sender
                            }
                            $line = substr($line, $pos + 2);
                        }
                    }

                    if ($filename == "") {
                        echo ("<div class=\"text\">");
                        if ($location != "") {
                            //lat,lon
                            $lat = floatval(substr($location, 0, strpos($location, ",")));
                            $lon = floatval(substr($location, strpos($location, ",") + 1));

                            $latLB = $lat - 0.001;
                            $lonLB = $lon - 0.002;

                            $latRT = $lat + 0.001;
                            $lonRT = $lon + 0.002;

                            echo ("<iframe width=\"300\" height=\"350\" frameborder=\"0\" scrolling=\"no\" marginheight=\"0\" marginwidth=\"0\"
                            src=\"https://www.openstreetmap.org/export/embed.html?bbox=" . $lonLB . "%2C" . $latLB . "%2C" . $lonRT . "%2C" . $latRT . "&amp;layer=mapnik&amp;marker=" . $lat . "%2C" . $lon . "\"style=\"border: 0px solid black\"></iframe>");
                        }

                        $line = htmlspecialchars($line);
                        $reg_exUrl = "/(http|https)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";

                        if (preg_match($reg_exUrl, $line, $url)) {
                            $line = preg_replace($reg_exUrl, "<a href=" . $url[0] . ">" . $url[0] . "</a>", $line);
                        }

                        echo ($line);
                    } else {
                        echo ("<div class=\"text\">");
                        if (strripos(strtolower($filename), ".vcf")) {
                            echo ("<a href=\"" . $filename . "\">" . $filename . "</a><br>");
                            echo ("contact");
                        } else if (strripos(strtolower($filename), ".opus") || strripos(strtolower($filename), ".m4a")) {
                            echo ("<a href=\"" . $filename . "\">" . $filename . "</a><br>");
                            echo ("audio");
                        } else {
                            echo ("<a href=\"" . $filename . "\">");
                            echo ("<img class=\"image\" src=\"" . $filename . "\">");
                            echo ("</a><br>");
                        }
                    }
                    echo ("</div>"); //text
                    echo ("<div class=\"time\">");
                    echo (date_format($datecreated, 'H:i'));
                    echo ("</div>"); //time
                    echo ("</div>"); //message
                } elseif ($line == "\n") {
                    // Empty line. We ignore these in the output to keep it better looking.
                } else {
                    //echo('no matches');
                    if ($last) {
                        echo ("<div class=\"mymessage\">");
                    } else {
                        echo ("<div class=\"message\">");
                    }
                    //echo("<br>");
                    echo ($line);
                    echo ("</div>"); //message
                }
            }

            fclose($handle);
        } else {
            // error opening the file.
        }

        unlink("temp.txt");
        ?>
    </div>
</body>

</html>
