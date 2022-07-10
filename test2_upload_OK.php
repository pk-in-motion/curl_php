<?php
error_reporting(E_ALL);
ini_set("display_errors",1);
//set_time_limit(0);

// to run this script, access token must be set to a new one, otherwise the following error will appear:
    /*

    $ php test2_upload_ERROR_invalid-filename-extension.php
    PHP Notice:  Undefined index: upload_url in /cygdrive/c/Users/p.kertonugroho/OneDrive/OneDrive - DAILYMOTION/KIB/API/curl_php/test2_upload_ERROR_invalid-filename-extension.php on line 38

    MEANING: upload_url is EMPTY -> because the access token is INVALID so token can not be generated
    */
    // ---- END ---- to run this script, access token must be set to a new one,
$access_token = "cHRQTgEHX0RYDQURDk5XGhdbRg9dQ0UAQV4VAgUEDlsF";


// API1: Get an upload URL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.dailymotion.com/file/upload");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$headers = [
    'Authorization: Bearer '.$access_token,
    'Content-Type: video/mp4'
];

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$server_output = curl_exec($ch);
//echo $server_output.PHP_EOL;

/*
$error_msg = curl_error($ch);
if(!empty($error_msg)){
    echo $error_msg;
    exit;
}
*/


curl_close($ch);


$firstApi = json_decode($server_output, true);
echo 'upload_url:'.$firstApi["upload_url"].PHP_EOL;

/*
if(isset($firstApi["error"])){
    echo $firstApi["error"]["message"];
    exit;
}
*/


if (isset($firstApi["upload_url"])) {

    //$boundary = 'PK-' . md5(time());
    //echo $boundary;

    // API2: Upload the video
    $filepath = "star.mp4";

    $filename = realpath($filepath);
    //echo $filename;
    $finfo = new \finfo(FILEINFO_MIME_TYPE);
    $mimetype = $finfo->file($filename);

    $cfile = curl_file_create($filename, $mimetype, basename($filename));

    //$data = ['file' => $cfile, 'public' => $public];
    $data = ['file' => $cfile, 'public' => 0];

    //--will cause empty file uploaded
    //$post = array('file' => curl_file_create($cFile,"video/*","test.MOV"));
    //---will cause 'invalid filename extenstion'
    //$post = $cFile;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $firstApi["upload_url"]);
    curl_setopt($ch, CURLOPT_POST, 1);

    //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          "Authorization: Bearer " . $access_token,
          "cache-control: no-cache",
          "content-type: multipart/form-data",
          ));


    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $result = curl_exec($ch);
    //echo $result;

    $secondApi = json_decode($result, true);

    $error_msg = curl_error($ch);

    curl_close($ch);

    if(isset($secondApi["error"])){
        print_r($secondApi);
        exit;
    }
    if(!empty($error_msg)){
        echo $error_msg;
        exit;
    }

    if(isset($secondApi["url"])){
      echo "----------------------------------".PHP_EOL;
		//echo "adding details to vid ....";
    //echo $secondApi["url"].PHP_EOL;



        // API3: Create the video
        $post = ['url' => $secondApi["url"],
                'title' => 'test 1', 'channel' => 'tech', 'tags' => 'technology',
                'published' => true
                ];
        echo $post['url'].PHP_EOL;
        echo $post['title'].PHP_EOL;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.dailymotion.com/me/videos");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $headers = [
            'Authorization: Bearer '.$access_token,
            //'Content-Type: video/mp4'
            'Content-Type: application/x-www-form-urlencoded'
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $server_output = curl_exec($ch);
            if(isset($server_output["error"])){
            print_r($server_output["error"]);
            exit;
        }
        curl_close($ch);
        print_r($server_output);
    }

    // Output is {"id":"x6p8jg4","title":"Untitled","channel":null,"owner":"x25vuib"}

}
?>
