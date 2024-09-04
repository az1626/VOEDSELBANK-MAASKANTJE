<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'];
$sql = "SELECT * FROM gezinnen WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $naam = $_POST['naam'];
    $volwassenen = $_POST['volwassenen'];
    $kinderen = $_POST['kinderen'];
    $postcode = $_POST['postcode'];
    $mail = $_POST['mail'];
    $telefoonnummer = $_POST['telefoonnummer'];
    $wensen = $_POST['wensen'];
    $pakket = $_POST['pakket'];

    $sql = "UPDATE gezinnen SET naam=?, volwassenen=?, kinderen=?, postcode=?, mail=?, telefoonnummer=?, wensen=?, pakket=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siiissssi", $naam, $volwassenen, $kinderen, $postcode, $mail, $telefoonnummer, $wensen, $pakket, $id);

    if ($stmt->execute()) {
        header("Location: manage_data.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Data</title>
</head>
<body>
    <h1>Edit Gezinnen Data</h1>
    <form action="edit_data.php?id=<?php echo $id; ?>" method="post">
        Naam: <input type="text" name="naam" value="<?php echo htmlspecialchars($data['naam']); ?>" required><br>
        Volwassenen: <input type="number" name="volwassenen" value="<?php echo $data['volwassenen']; ?>" required><br>
        Kinderen: <input type="number" name="kinderen" value="<?php echo $data['kinderen']; ?>" required><br>
        Postcode: <input type="text" name="postcode" value="<?php echo htmlspecialchars($data['postcode']); ?>" required><br>
        Email: <input type="email" name="mail" value="<?php echo htmlspecialchars($data['mail']); ?>" required><br>
        Telefoonnummer: <input type="text" name="telefoonnummer" value="<?php echo htmlspecialchars($data['telefoonnummer']); ?>" required><br>
        Wensen: <input type="text" name="wensen" value="<?php echo htmlspecialchars($data['wensen']); ?>" required><br>
        Pakket: <input type="text" name="pakket" value="<?php echo htmlspecialchars($data['pakket']); ?>" required><br>
        <input type="submit" value="Update">
    </form>
</body>
</html>
