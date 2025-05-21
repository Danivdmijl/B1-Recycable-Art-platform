<?php 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = htmlspecialchars($_POST['remaker-firstname']);
    $lastname = htmlspecialchars($_POST['remaker-lastname']);
    $email = htmlspecialchars($_POST['remaker-email']);
    $phone = htmlspecialchars($_POST['remaker-phone']);
    $country = htmlspecialchars($_POST['remaker-country']);
    $city = htmlspecialchars($_POST['remaker-city']);
    $brandname = htmlspecialchars($_POST['remaker-brandname']);
    $socials = htmlspecialchars($_POST['remaker-socials']);
    $refferal = htmlspecialchars($_POST['remaker-refferal']);
    $materialprocess = htmlspecialchars($_POST['remaker-materialProcess']);

    // images not working yet
    // $materialprocess1 = $_POST['remaker-materialProcess-1'];
    // $materialprocess2 = $_POST['remaker-materialProcess-2'];
    // $materialprocess3 = $_POST['remaker-materialProcess-3'];

    if(
        empty($firstname) ||
        empty($lastname) ||
        empty($email) ||
        empty($country) ||
        empty($city) ||
        empty($refferal) ||
        empty($materialprocess)
    ){
	
       header("Location: https://arat-wp.duckduckdev.nl/arat-remaker-form/?error=missing_fields");
        exit();
    } else {
        $data = [
            "firstname" => $firstname,
            "lastname" => $lastname,
            "email" => $email,
            "phone" => $phone,
            "country" => $country,
            "city" => $city,
            "brandname" => $brandname,
            "socials" => $socials,
            "refferal" => $refferal,
            "materialprocess" => $materialprocess
        ];
       
        // Prepare the curl request
        $ch = curl_init("https://arat-api.duckduckdev.nl/wp/remaker-request");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            // Log the error or show a user-friendly message
            echo "Error sending to API: " . htmlspecialchars($error);
        } elseif ($httpcode >= 200 && $httpcode < 300) {
            // Success!
            header("Location: /Remaker-Form-Demo/success.html");
            exit();
        } else {
            // Failed with API error response
            echo "API Error (HTTP $httpcode): " . htmlspecialchars($response);
        }
    }
        
        
} else {
}




