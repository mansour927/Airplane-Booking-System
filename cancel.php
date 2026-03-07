<?php
require_once __DIR__ . '/../config/db.php';

$booking_id = (int)($_GET['booking_id'] ?? 0);

if ($booking_id <= 0) {
    exit("Invalid booking.");
}

try {

    $pdo->beginTransaction();

    // get seat id from booking
    $stmt = $pdo->prepare("SELECT seat_id FROM bookings WHERE booking_id = ?");
    $stmt->execute([$booking_id]);
    $booking = $stmt->fetch();

    if (!$booking) {
        throw new Exception("Booking not found.");
    }

    $seat_id = $booking['seat_id'];

    // change booking status
    $stmt = $pdo->prepare("UPDATE bookings SET booking_status = 'CANCELLED' WHERE booking_id = ?");
    $stmt->execute([$booking_id]);

    // make seat available again
    $stmt = $pdo->prepare("UPDATE flight_seats SET is_available = 1 WHERE seat_id = ?");
    $stmt->execute([$seat_id]);

    $pdo->commit();

    header("Location: my_bookings.php");
    exit;

} catch (Exception $e) {

    $pdo->rollBack();
    echo "Error cancelling booking.";
}
?>

<!doctype html>
<html>
<head></title>
<link rel="stylesheet" href="../assets/styles.css"></head>
<body>
</body>
</html>