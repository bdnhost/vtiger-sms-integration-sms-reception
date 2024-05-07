# vtiger-sms-integration-sms-reception
A solution for integrating incoming SMS with VTiger 7,8.X  CRM, facilitating SMS reception as support tickets

VTiger SMS Integration - SMS Reception
Author: Jacob Bidani
Company: BDNHOST
Position: Owner
Contact: info@bdnhost.net
SMS: +972532062346
Website: https://bdnhost.net

Overview
This repository contains a solution for integrating incoming SMS messages with VTiger CRM. The solution enables SMS reception as support tickets within the system. It's a creative and innovative approach that ensures seamless communication and client management.

Features
SMS to Ticket: Converts incoming SMS messages into support tickets in VTiger CRM.
Contact Linking: Automatically links SMS messages to existing contacts based on phone numbers.
Customizable: Can be adapted to work with various SMS providers.
Solution Details
The solution comprises the following features:

SMS Reception:
Receives incoming SMS messages and stores them in a designated table.
Normalizes phone numbers to match the VTiger format.
Checks for matching contacts based on phone numbers.
Creates a support ticket or comment based on the SMS message.
Webform Submission:
Uses VTiger webform for creating tickets.
Includes essential ticket information such as title, priority, and status.
Installation
Database Setup:
Ensure that the necessary tables (sms_messages, vtiger_troubletickets, etc.) are present in the VTiger database.
File Deployment:
Deploy the provided PHP files (sms-receiver.php and process-sms.php) to your server.
Configure SMS Provider:
Set up the SMS provider to forward incoming messages to the appropriate endpoint.
Usage
Receive SMS:
The system listens for incoming SMS messages and processes them accordingly.
Process SMS:
The system checks for unprocessed SMS messages and creates support tickets in VTiger CRM.
Assistance & Support
If you need assistance with implementing this solution in your business or require guidance and support, please contact BDNHOST – Open Source Internet Solutions.
Send an SMS to: +972532062346
Quick response guaranteed.

Donations
If this solution has been helpful to you or your business, please consider making a donation. Your support helps maintain and improve this project.
Donate here: PayPal -yaaqovb@gmail.com

Contact
For inquiries or support, please contact Jacob Bidani.
