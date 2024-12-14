<?php

include_once 'util.php';

// Database connection settings
$host = "localhost";
$dbname = "public_transport";
$username = "root";
$password = "";

// Database connection
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Get variables sent via POST from the USSD gateway
$sessionId = isset($_POST["sessionId"]) ? $_POST["sessionId"] : "";
$phoneNumber = isset($_POST["phoneNumber"]) ? $_POST["phoneNumber"] : "";
$text = isset($_POST["text"]) ? $_POST["text"] : "";

// Split input text for multi-level handling
$textArray = explode("*", $text);
$level = count($textArray);

// Handle navigation commands: "98" (back) and "99" (main menu)
if (in_array(Util::$GoBack, $textArray)) {
    array_pop($textArray);
    $text = implode("*", $textArray);
    $level = count($textArray);
} elseif (in_array(Util::$BackToMainMenu, $textArray)) {
    $textArray = [];
    $text = "";
    $level = 0;
}

// Main menu
if ($text == "") {
    $response = "CON Welcome to Bus Ticket Booking:\n";
    $response .= "1. Register as a Passenger\n";
    $response .= "2. View Available Routes\n";
    $response .= "3. Book a Ticket\n";
    $response .= "4. View Booking Status\n";
    $response .= "5. Pay for Ticket\n";
} elseif ($textArray[0] == "1") {
    // Option 1: Register as a Passenger
    if ($level == 1) {
        $response = "CON Enter your full name:\n" . Util::$GoBack . ". Back\n" . Util::$BackToMainMenu . ". Main Menu";
    } elseif ($level == 2) {
        $response = "CON Enter your ID number:\n" . Util::$GoBack . ". Back\n" . Util::$BackToMainMenu . ". Main Menu";
    } elseif ($level == 3) {
        $fullName = $textArray[1];
        $idNumber = $textArray[2];

        // Check if the phone number already exists
        $stmt = $conn->prepare("SELECT phone FROM passengers WHERE phone = :phone");
        $stmt->execute(["phone" => $phoneNumber]);

        if ($stmt->rowCount() > 0) {
            // Update passenger details
            $stmt = $conn->prepare("UPDATE passengers SET full_name = :full_name, id_number = :id_number WHERE phone = :phone");
            $stmt->execute([
                "phone" => $phoneNumber,
                "full_name" => $fullName,
                "id_number" => $idNumber
            ]);
            Util::sendSMS($phoneNumber, "Your details have been updated successfully!");
            $response = "END Your details have been updated successfully!";
        } else {
            // Insert new passenger details
            $stmt = $conn->prepare("INSERT INTO passengers (phone, full_name, id_number) VALUES (:phone, :full_name, :id_number)");
            $stmt->execute([
                "phone" => $phoneNumber,
                "full_name" => $fullName,
                "id_number" => $idNumber
            ]);
            Util::sendSMS($phoneNumber, "Registration successful! Welcome, $fullName.");
            $response = "END Registration successful! Welcome, $fullName.";
        }
    }
} elseif ($textArray[0] == "2") {
    // Option 2: View Available Routes
    $response = "CON Available routes:\n";
    $stmt = $conn->query("SELECT id, route, FRW FROM routes");
    while ($row = $stmt->fetch()) {
        $response .= $row["id"] . ". " . $row["route"] . " - " . number_format($row["FRW"], 2) . " RWF\n";
    }
    $response .= "\n" . Util::$GoBack . ". Back\n";
} elseif ($textArray[0] == "3") {
    // Option 3: Book a Ticket
    if ($level == 1) {
        $response = "CON Enter the route number:\n" . Util::$GoBack . ". Back\n" . Util::$BackToMainMenu . ". Main Menu";
    } elseif ($level == 2) {
        $response = "CON Enter the number of tickets:\n" . Util::$GoBack . ". Back\n" . Util::$BackToMainMenu . ". Main Menu";
    } elseif ($level == 3) {
        $routeId = intval($textArray[1]);
        $tickets = intval($textArray[2]);

        // Fetch route details
        $stmt = $conn->prepare("SELECT route, FRW FROM routes WHERE id = :id");
        $stmt->execute(["id" => $routeId]);
        $route = $stmt->fetch();

        if ($route) {
            $total = $route["FRW"] * $tickets;
            $ticketCode = "TCK" . str_pad(mt_rand(1000, 9999), 4, "0", STR_PAD_LEFT);

            // Save the booking
            $stmt = $conn->prepare("INSERT INTO bookings (phone, route, tickets, ticket_code, total, status) VALUES (:phone, :route, :tickets, :ticket_code, :total, 'Pending Payment')");
            $stmt->execute([
                "phone" => $phoneNumber,
                "route" => $route["route"],
                "tickets" => $tickets,
                "ticket_code" => $ticketCode,
                "total" => $total
            ]);
            $response = "END Ticket(s) booked successfully! Total: " . number_format($total, 2) . " RWF. Ticket Code: $ticketCode. Please proceed to payment.";
        } else {
            $response = "END Invalid route. Please try again.";
        }
    }
} elseif ($textArray[0] == "4") {
    // Option 4: View Booking Status
    $stmt = $conn->prepare("SELECT ticket_code, route, tickets, total, status FROM bookings WHERE phone = :phone");
    $stmt->execute(["phone" => $phoneNumber]);
    if ($stmt->rowCount() > 0) {
        $response = "CON Your bookings:\n";
        while ($row = $stmt->fetch()) {
            $response .= "Ticket: " . $row["ticket_code"] . ", Route: " . $row["route"] . ", Tickets: " . $row["tickets"] . ", Total: " . number_format($row["total"], 2) . " RWF, Status: " . $row["status"] . "\n";
        }
    } else {
        $response = "END No bookings found.";
    }
} elseif ($textArray[0] == "5") {
    // Option 5: Pay for Ticket
    if ($level == 1) {
        $response = "CON Enter your ticket code:\n" . Util::$GoBack . ". Back\n" . Util::$BackToMainMenu . ". Main Menu";
    } elseif ($level == 2) {
        $ticketCode = $textArray[1];

        // Fetch booking details
        $stmt = $conn->prepare("SELECT total FROM bookings WHERE ticket_code = :ticket_code AND phone = :phone");
        $stmt->execute(["ticket_code" => $ticketCode, "phone" => $phoneNumber]);
        $booking = $stmt->fetch();

        if ($booking) {
            $response = "END Payment of " . number_format($booking["total"], 2) . " RWF for ticket $ticketCode is successful! Thank you.";

            // Update booking status
            $stmt = $conn->prepare("UPDATE bookings SET status = 'Paid' WHERE ticket_code = :ticket_code");
            $stmt->execute(["ticket_code" => $ticketCode]);
        } else {
            $response = "END Invalid ticket code or no booking found.";
        }
    }
} else {
    $response = "END Invalid option. Please try again.";
}

// Echo the response to the USSD gateway
header("Content-type: text/plain");
echo $response;

?>
