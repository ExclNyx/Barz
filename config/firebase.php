<?php
/**
 * Firebase project settings for the PHP server.
 * Never put the service-account JSON file inside the public project folder.
 */
define('FIREBASE_PROJECT_ID', getenv('FIREBASE_PROJECT_ID') ?: 'barz-28363');

// Set this environment variable to the path of your service-account JSON file.
// Example (PowerShell): $env:GOOGLE_APPLICATION_CREDENTIALS = 'C:\\BarzSecrets\\barz-28363-service-account.json'
$firebaseCredentialsPath = getenv('GOOGLE_APPLICATION_CREDENTIALS') ?: '';
if ($firebaseCredentialsPath !== '' && is_file($firebaseCredentialsPath)) {
    putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $firebaseCredentialsPath);
}
