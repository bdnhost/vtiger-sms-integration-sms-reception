<?php
// פרטי החיבור למסד הנתונים
$db_server = 'localhost';
$db_port = ':3306';
$db_username = 'USERNAME'; // שם המשתמש של מסד הנתונים
$db_password = 'PASSWORD'; // הסיסמה של מסד הנתונים
$db_name = 'DATABASE'; // שם מסד הנתונים

$vtiger_form_url = "https://crm.shlomi.online/modules/Webforms/capture.php"; // URL של ה-webform

$conn = new mysqli($db_server . $db_port, $db_username, $db_password, $db_name);

// בדיקת חיבור
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully\n";

// פונקציה לחילוץ שמונת הספרות האחרונות ממספר טלפון
function last_8_digits($phone) {
    return substr($phone, -8);
}

// פרטי הספק - להחלפה לפי הצורך
$supplier_sender_param = 'sender'; // הפרמטר עבור מספר השולח
$supplier_receiver_param = 'receiver'; // הפרמטר עבור מספר המקבל
$supplier_message_param = 'msg'; // הפרמטר עבור תוכן ההודעה

// קבלת והכנסת נתוני ה-SMS לכרטיס
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sender = $_POST[$supplier_sender_param];
    $receiver = $_POST[$supplier_receiver_param];
    $message = $_POST[$supplier_message_param];
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

        // יצירת הכותרת
        $ticket_title = "התקבלה הודעת SMS מאת: $contactName ($sender)";
        $contact_field = "12x$contactId";
    } else {
        echo "No contact found for sender: $last8Sender\n";
        $ticket_title = "התקבלה הודעת SMS מ-$sender";
    }

    // נתוני הכרטיס ל-webform
    $postData = array(
        "__vtrftk" => "sid:15b468d4a14a75dae93d7bf319508db5a7f3482e,1714861257",
        "publicid" => "e94312f9e334f34a2a768946116a52de",
        "urlencodeenable" => "1",
        "name" => "SMS ל כרטיס",
        "ticket_title" => $ticket_title,
        "ticketpriorities" => "Normal",
        "ticketstatus" => "SMS",
        "cf_947" => $sender,
        "cf_949" => $receiver,
        "cf_953" => $received_at,
        "cf_955" => $message,
        "contact_id" => $contact_field
    );

    // שליחת נתונים ל-webform
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
