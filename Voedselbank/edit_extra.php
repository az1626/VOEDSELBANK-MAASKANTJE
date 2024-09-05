<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: extra.php");
    exit;
}

$id = $_GET['id'];

// Fetch the current data
$sql = "SELECT * FROM extra WHERE beschikbare_allergieën=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $beschikbare_allergieën = $_POST['beschikbare_allergieën'];
    $beschikbare_categorieën = $_POST['beschikbare_categorieën'];

    $sql = "UPDATE extra SET beschikbare_allergieën=?, beschikbare_categorieën=? WHERE beschikbare_allergieën=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $beschikbare_allergieën, $beschikbare_categorieën, $id);

    if ($stmt->execute()) {
        $success_message = "Extra information updated successfully!";
        header("Location: extra.php");
        exit;
    } else {
        $error_message = "Error: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Extra Information</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Edit Extra Information</h1>
        <?php
        if (isset($success_message)) {
            echo "<div class='message success'>$success_message</div>";
        }
        if (isset($error_message)) {
            echo "<div class='message error'>$error_message</div>";
        }
        ?>
        <form action="edit_extra.php?id=<?php echo urlencode($id); ?>" method="post">
            <label for="beschikbare_allergieën">Available Allergies:</label>
            <input type="text" id="beschikbare_allergieën" name="beschikbare_allergieën" value="<?php echo htmlspecialchars($data['beschikbare_allergieën']); ?>" required>

            <label for="beschikbare_categorieën">Available Categories:</label>
            <input type="text" id="beschikbare_categorieën" name="beschikbare_categorieën" value="<?php echo htmlspecialchars($data['beschikbare_categorieën']); ?>" required>

            <input type="submit" value="Update Extra Info">
        </form>
    </div>
</body>
</html>
