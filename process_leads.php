<?php
session_start(); // Start the session

// Function to send message to Telegram bot
function sendMessageToTelegram($message, $token, $chatId) {
    $url = "https://api.telegram.org/bot{$token}/sendMessage";
    $data = array(
        'chat_id' => $chatId,
        'text' => $message
    );
    $options = array(
        'http' => array(
            'method' => 'POST',
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'content' => http_build_query($data)
        )
    );
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return $result;
}

// Function to send a file to Telegram bot
function sendFileToTelegram($file_path, $botToken, $chatId, $message = null) {
    $url = "https://api.telegram.org/bot{$botToken}/sendDocument";
    $postFields = array(
        'chat_id' => $chatId,
        'document' => new CURLFile(realpath($file_path)),
        'caption' => $message // Add message as caption for the document
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:multipart/form-data"));
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    $output = curl_exec($ch);
    curl_close($ch);

    return $output;
}
if(isset($_POST["submit"])) {
    // Send starting message to the bot
    $startMessage = "---- Emails filtering process has started ----";
    sendMessageToTelegram($startMessage, $_POST['botToken'], $_POST['chatId']);

    // Rest of your processing logic...

}
if(isset($_POST["submit"])) {
    $file = $_FILES['fileToUpload']['tmp_name'];

    // Read file into a string
    $leads_content = file_get_contents($file);

    // Extract email addresses using regex
    preg_match_all("/\b[A-Za-z0-9._%+-]+@([^\s]+)\b/", $leads_content, $matches);

    // Get unique email addresses
    $filteredLeads = array_unique($matches[0]);

    // Check if lead validation is enabled
    $validateLeads = isset($_POST['validateLeads']);

    // Function to validate an email address
    function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    // Group leads by domain (ignoring TLD)
    $leadsByDomain = array();
    foreach ($filteredLeads as $lead) {
        $domain = explode('@', $lead)[1];
        // Get the domain name without the TLD
        $domainWithoutTLD = preg_replace("/\.[A-Za-z]{2,}$/", "", $domain);
        if (!in_array($domainWithoutTLD, array('hotmail', 'live', 'outlook', 'gmail', 'protonmail', 'yahoo', 'aol', 'mail', 'att', 'icloud', 'yandex', 'comcast'))) {
            $domainWithoutTLD = 'others'; // Assign 'others' category for non-specified domains
        }
        $leadsByDomain[$domainWithoutTLD][] = $lead;
    }

    // Send filtered email TXT files to Telegram bot and store success message
    foreach ($leadsByDomain as $domain => $leads) {
        // Add '.com' to the domain name to ensure uniqueness in file names
        $file_path = 'filtered/' . $domain . '_leads.txt';
        $content = implode(PHP_EOL, $leads) . PHP_EOL;
        file_put_contents($file_path, $content);

        // Send the file to Telegram bot with a message
        $message = "Email Domain: {$domain}\nTotal leads: " . count($leads);
        sendFileToTelegram($file_path, $_POST['botToken'], $_POST['chatId'], $message);
    }

    // Send "process complete" message to the bot
    $completeMessage = "---- Process complete. All emails files have been delivered ----";
    $completeMessage = "---- For More Visit https://knyghthax.com  ----";
    sendMessageToTelegram($completeMessage, $_POST['botToken'], $_POST['chatId']);

    // Store success message in session variable
    $_SESSION['success_message'] = "Leads filtered and saved successfully!";
    
    // Redirect back to the form page
    header("Location: index.php");
    exit(); // Ensure script execution stops after redirection
}
?>
