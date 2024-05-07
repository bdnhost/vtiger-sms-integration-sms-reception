<?php
// Database connection details
$db_server = 'localhost';
$db_port = ':3306';
$db_username = 'your_db_username';
$db_password = 'your_db_password';
$db_name = 'your_db_name';

$vtiger_form_url = "https://your-vtiger-domain.com/modules/Webforms/capture.php"; // Webform URL

$conn = new mysqli($db_server . $db_port, $db_username, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully\n";

// Function to extract the last eight digits of a phone number
function last_8_digits($phone) {
    return substr($phone, -8);
}

// Receive and insert SMS data into a ticket
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sender = $_POST['sender'];
    $receiver = $_POST['receiver'];
    $message = $_POST['msg'];
    $received_at = date('Y-m-d H:i:s');

    // Normalize sender phone number
    $last8Sender = last_8_digits($sender);
    echo "Last 8 digits of sender: $last8Sender\n";

    // Check for matching contact in VTiger
    $contactSql = "SELECT contactid, firstname, lastname FROM vtiger_contactdetails WHERE SUBSTRING(REPLACE(phone, '-', ''), -8) = '$last8Sender' OR SUBSTRING(REPLACE(mobile, '-', ''), -8) = '$last8Sender' OR SUBSTRING(REPLACE(fax, '-', ''), -8) = '$last8Sender'";
    $contactResult = $conn->query($contactSql);

    if ($contactResult === FALSE) {
        die("Database query error: " . $conn->error);
    }

    $contactId = NULL;
    $contactName = NULL;
    $contact_field = "";

    if ($contactResult->num_rows > 0) {
        $contactRow = $contactResult->fetch_assoc();
        $contactId = $contactRow['contactid'];
        $contactName = $contactRow['firstname'] . ' ' . $contactRow['lastname'];
        echo "Found contact ID $contactId for sender $sender, last 8 digits: $last8Sender\n";

        // Create ticket title
        $ticket_title = "Received SMS from: $contactName ($sender)";
        $contact_field = "12x$contactId";
    } else {
        echo "No contact found for sender: $last8Sender\n";
        $ticket_title = "Received SMS from $sender";
    }

    // Ticket data for the webform
    $postData = array(
        "__vtrftk" => "sid:your-session-id,1234567890",
        "publicid" => "your-public-id",
        "urlencodeenable" => "1",
        "name" => "SMS to Ticket",
        "ticket_title" => $ticket_title,
        "ticketpriorities" => "Normal",
        "ticketstatus" => "SMS",
        "cf_sender" => $sender,
        "cf_receiver" => $receiver,
        "cf_received_at" => $received_at,
        "cf_message" => $message,
        "contact_id" => $contact_field
    );

    // Send data to the webform
    $ch = curl_init($vtiger_form_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);

    if ($response === FALSE) {
        echo "cURL error: " . curl_error($ch) . "\n";
    } else {
        echo "cURL response: " . $response . "\n";
    }

    curl_close($ch);
}

$conn->close();
?>
