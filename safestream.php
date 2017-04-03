<?php

require 'vendor/autoload.php';
$m = new \Moment\Moment();

function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

$ip = get_client_ip();

// Enter your user specific session data here.
// This should take place in PHP to prevent users from modifying their credentials in the browser

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty($_POST["name"])) {
        $name = "Sample User";
    } else {
        $name = $_POST["name"];
    }

    if (empty($_POST["email"])) {
        $email = "test@safestream.com";
    } else {
        $email = $_POST["email"];
    }

    if (empty($_POST["company"])) {
        $company = "Acme Studios 22";
    } else {
        $company = $_POST["company"];
    }

    if (empty($_POST["image"])) {
        $image = "";
    } else {
        $image = $_POST["image"];
    }

    $safeStreamClient = new \SafeStream\SafeStreamClient([
        "protocol" => "https",
        "hostName" => "api.safestream.com",
        "clientId" => "549fbca1-4366-4051-accb-74736385b3fe",
        "apiKey" => "afec700d523270ce45a8838d94234aed",
        "version" => "1.0"]);

    // Lower left "Licensed to: First Last Name"
    $watermarkConfiguration1 = new \SafeStream\Watermark\WatermarkConfiguration([
        "content" => "Licensed to " . $name,
        "fontColor" => "FFFFFF",
        "y" => 0.83,
        "x" => 0.03,
        "fontOpacity" => 0.5,
        "fontSize" => 0.03,
        "horizontalAlignment" => "LEFT",
        "verticalAlignment"  => "TOP"
    ]);

    // Upper right company name
    $watermarkConfiguration2 = new \SafeStream\Watermark\WatermarkConfiguration([
        "content" => $company,
        "fontColor" => "FFFFFF",
        "y" =>   0.04,
        "x" =>  0.97,
        "fontOpacity" => 0.5,
        "fontSize" => 0.03,
        "horizontalAlignment" => "RIGHT",
        "verticalAlignment"  => "TOP",
        "shadowColor"  => "000000",
        "shadowOffsetX"  => 0.08,
        "shadowOffsetY" => 0.08,
        "shadowOpacity" => 0.33
    ]);

    // Date time below the company name in lower left
    $watermarkConfiguration4 = new \SafeStream\Watermark\WatermarkConfiguration([
        "content" => $m->format('l, dS F Y / H.i (e)'),
        "fontColor" => "FFFFFF",
        "y" =>   0.88,
        "x" =>  0.03,
        "fontOpacity" => 0.5,
        "fontSize" => 0.02,
        "horizontalAlignment" => "LEFT",
        "verticalAlignment"  => "TOP",
        "shadowColor"  => "000000",
        "shadowOffsetX"  => 0.08,
        "shadowOffsetY" => 0.08,
        "shadowOpacity" => 0.33
        ]);

    // Lower left IP address - Will display 1 if run locally
    $watermarkConfiguration5 = new \SafeStream\Watermark\WatermarkConfiguration([
        "content" => "IP Address " . $ip,
        "fontColor" => "FFFFFF",
        "y" =>   0.92,
        "x" =>  0.03,
        "fontOpacity" => 0.5,
        "fontSize" => 0.02,
        "horizontalAlignment" => "LEFT",
        "verticalAlignment"  => "TOP",
        "shadowColor"  => "000000",
        "shadowOffsetX"  => 0.08,
        "shadowOffsetY" => 0.08,
        "shadowOpacity" => 0.33
    ]);

    // Animation from right to left starting at 0 and lasting 120 seconds
    $watermarkConfiguration6 = new \SafeStream\Watermark\WatermarkConfiguration();
    $watermarkConfiguration6
        ->withType("TEXT")
        ->withContent("This copy is licensed to " . $name)
        ->withFontColor("FFFFFF")
        ->withY(0.5)
        ->withX(1)
        ->withFontOpacity(0.2)
        ->withFontSize(0.02)
        ->withHorizontalAlignment("LEFT")
        ->withVerticalAlignment("TOP")
        ->move(-0.5,0.5,0,120);

    // Animation from right to left starting at 300s and lasting to 420 seconds
    $watermarkConfiguration7 = new \SafeStream\Watermark\WatermarkConfiguration();
    $watermarkConfiguration7
        ->withType("TEXT")
        ->withContent("This copy is licensed to " . $name)
        ->withFontColor("FFFFFF")
        ->withY(0.7)
        ->withX(1)
        ->withFontOpacity(0.2)
        ->withFontSize(0.02)
        ->withHorizontalAlignment("LEFT")
        ->withVerticalAlignment("TOP")
        ->move(-0.5,0.7,300,420);


    $mydata = $safeStreamClient -> watermark()->create("feature-1",array($watermarkConfiguration1,$watermarkConfiguration2,$watermarkConfiguration4,$watermarkConfiguration5,$watermarkConfiguration6,$watermarkConfiguration7),0);

    echo json_encode($mydata);

}

?>
