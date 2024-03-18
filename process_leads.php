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

if(isset($_POST["submit"])) {
    $file = $_FILES['fileToUpload']['tmp_name'];

    // Read file into a string
    $leads_content = file_get_contents($file);

    // Extract email addresses using regex
    preg_match_all("/\b[A-Za-z0-9._%+-]+@(hotmail|live|outlook|gmail|protonmail|yahoo|aol|mail|att|icloud|yandex|)\.[A-Za-z]{2,}\b/", $leads_content, $matches);

    // Get unique email addresses
    $filteredLeads = array_unique($matches[0]);

    // Check if lead validation is enabled
    $validateLeads = isset($_POST['validateLeads']);

    // Function to validate an email address
    function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    // Validate and filter leads if validation is enabled
    if ($validateLeads) {
        $validLeads = array();
        $invalidLeads = array();

        foreach ($filteredLeads as $lead) {
            if (isValidEmail($lead)) {
                $validLeads[] = $lead;
            } else {
                $invalidLeads[] = $lead;
            }
        }

        $filteredLeads = $validLeads;

        // Save invalid leads to a separate file
        if (!empty($invalidLeads)) {
            $invalidFilePath = 'invalid_leads.txt';
            $invalidContent = implode(PHP_EOL, $invalidLeads) . PHP_EOL;
            file_put_contents($invalidFilePath, $invalidContent, FILE_APPEND);
        }
    }

    // Group leads by domain (ignoring TLD)
    $leadsByDomain = array();
    foreach ($filteredLeads as $lead) {
        $domain = explode('@', $lead)[1];
        // Get the domain name without the TLD
        $domainWithoutTLD = preg_replace("/\.[A-Za-z]{2,}$/", "", $domain);
        $leadsByDomain[$domainWithoutTLD][] = $lead;
    }

    // Save filtered leads to separate files for each domain
    foreach ($leadsByDomain as $domain => $leads) {
        // Add '.com' to the domain name to ensure uniqueness in file names
        $file_path = 'filtered/' . $domain . '.com_leads.txt';
        $content = implode(PHP_EOL, $leads) . PHP_EOL;
        file_put_contents($file_path, $content);

        // Get Telegram bot token and chat ID from form inputs
        $botToken = $_POST['botToken'];
        $chatId = $_POST['chatId'];

        // Send message to Telegram bot
        $message = "New leads for domain: {$domain}\nTotal leads: " . count($leads);
        sendMessageToTelegram($message, $botToken, $chatId);
    }

    // Store success message in session variable
    $_SESSION['success_message'] = "Leads filtered and saved successfully!";
    
    // Redirect back to the form page
    header("Location: index.php");
    exit(); // Ensure script execution stops after redirection
}
?>
