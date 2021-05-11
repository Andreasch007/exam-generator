<?php
header('Access-Control-Allow-Origin: *');
$url = 'https://exam.nocortech.com/api/create-task/';
$data_array =  array(
      "exam_no"     => 'EX-A20210001',
      "uid"         => json_encode(["7789072916b9b62b","c7f660212bb6d5cb","ea4e71d6a3176303"]),
	  "start_time"  => '2021-05-11 13:45:00',
	  "end_time"	=> '2021-05-11 14:30:00',
	  "company_id"  => '14'
);
$ch = curl_init($url);
# Setup request to send json via POST.
$payload = $data_array;
//curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTREDIR, 3);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:multipart/form-data'));
# Return response instead of printing.
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
# Send request.
$result = curl_exec($ch);
curl_close($ch);
# Print response.
echo "<pre>$result</pre>";

?>