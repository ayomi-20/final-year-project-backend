<?php
$connected = @fsockopen("smtp.gmail.com", 587, $errno, $errstr, 10);
if ($connected) {
    echo "✅ Can reach smtp.gmail.com:587";
    fclose($connected);
} else {
    echo "❌ Cannot reach smtp.gmail.com:587 — Error: $errstr ($errno)";
}

// Also test port 465
$connected2 = @fsockopen("ssl://smtp.gmail.com", 465, $errno2, $errstr2, 10);
if ($connected2) {
    echo "<br>✅ Can reach smtp.gmail.com:465";
    fclose($connected2);
} else {
    echo "<br>❌ Cannot reach smtp.gmail.com:465 — Error: $errstr2 ($errno2)";
}