<?php

class Database {
    private static $host = "localhost";
    private static $dbname = "public_transport";
    private static $username = "root";
    private static $password = "";
    private static $conn;

    public static function connect() {
        if (!self::$conn) {
            try {
                self::$conn = new PDO("mysql:host=" . self::$host . ";dbname=" . self::$dbname, self::$username, self::$password);
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
        }
        return self::$conn;
    }

    public static function registerPassenger($phone, $fullName, $idNumber) {
        $conn = self::connect();
        $stmt = $conn->prepare("SELECT phone FROM passengers WHERE phone = :phone");
        $stmt->execute(['phone' => $phone]);

        if ($stmt->rowCount() > 0) {
            $stmt = $conn->prepare("UPDATE passengers SET full_name = :full_name, id_number = :id_number WHERE phone = :phone");
        } else {
            $stmt = $conn->prepare("INSERT INTO passengers (phone, full_name, id_number) VALUES (:phone, :full_name, :id_number)");
        }

        return $stmt->execute(['phone' => $phone, 'full_name' => $fullName, 'id_number' => $idNumber]);
    }
}

?>
