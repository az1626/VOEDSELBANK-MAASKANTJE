<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in and has the admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit;
}

function addVoedselpakket($conn, $naam, $samenstellingsdatum, $ophaaldatum) {
  $sql = "INSERT INTO voedselpakket (naam, samenstellingsdatum, ophaaldatum) VALUES (?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sss", $naam, $samenstellingsdatum, $ophaaldatum);
  if ($stmt->execute()) {
      return $conn->insert_id;
  }
  return false;
}

function addVoedselpakketProducts($conn, $voedselpakket_id, $product_id, $quantity) {
  $sql = "INSERT INTO voedselpakket_producten (voedselpakket_id, product_id, quantity) VALUES (?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("iii", $voedselpakket_id, $product_id, $quantity);
  return $stmt->execute();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_voedselpakket'])) {
  $naam = $_POST['naam'];
  $samenstellingsdatum = $_POST['samenstellingsdatum'];
  $ophaaldatum = $_POST['ophaaldatum'];
  
  $voedselpakket_id = addVoedselpakket($conn, $naam, $samenstellingsdatum, $ophaaldatum);
  
  if ($voedselpakket_id) {
      foreach ($_POST['producten'] as $index => $product_id) {
          $quantity = $_POST['quantities'][$index];
          if (!empty($product_id) && !empty($quantity)) {
              addVoedselpakketProducts($conn, $voedselpakket_id, $product_id, $quantity);
          }
      }
      header("Location: voedselpakket.php");
      exit;
  } else {
      echo "<script>alert('Failed to add voedselpakket.'); window.location.href='add_voedselpakket.php';</script>";
  }
}

// Fetch all products for displaying checkboxes
$sql = "SELECT * FROM producten";
$result = $conn->query($sql);
$producten = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $producten[] = $row;
    }
} else {
    echo "No products found.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voeg een nieuw Voedselpakket toe</title>
    <style>
        :root {
            --primary-color: #4CAF50;
            --secondary-color: #45a049;
            --background-color: #f4f4f4;
            --text-color: #333;
            --border-color: #ddd;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            line-height: 1.6;
            margin: 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1, h2 {
            color: var(--primary-color);
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input[type="text"], input[type="date"], input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 16px;
        }

        button {
            background-color: var(--primary-color);
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: var(--secondary-color);
        }

        .checkbox-group {
            margin-bottom: 20px;
        }

        .checkbox-group label {
            display: block;
            margin-bottom: 5px;
        }

        .checkbox-group input[type="checkbox"] {
            margin-right: 10px;
        }

        .quantity {
            width: 60px;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">

        <h2>Voeg een nieuw Voedselpakket toe</h2>
        <form method="POST" action="">
            <label for="naam">Naam:</label>
            <input type="text" id="naam" name="naam" required>

            <label>Producten:</label>
            <div class="checkbox-group">
                <?php foreach ($producten as $index => $product): ?>
                    <label>
                        <input type="checkbox" name="producten[]" value="<?php echo $product['id']; ?>">
                        <?php echo $product['naam'] . " (Voorraad: " . $product['voorraad'] . ")";
                        // Add a quantity input next to each checkbox
                        ?>
                        <input type="number" name="quantities[]" min="1" max="<?php echo $product['voorraad']; ?>" class="quantity" placeholder="Aantal">
                    </label>
                <?php endforeach; ?>
            </div>

            <label for="samenstellingsdatum">Samenstellingsdatum:</label>
            <input type="date" id="samenstellingsdatum" name="samenstellingsdatum" required>

            <label for="ophaaldatum">Ophaaldatum:</label>
            <input type="date" id="ophaaldatum" name="ophaaldatum" required>

            <button type="submit" name="add_voedselpakket">Voeg toe</button>
        </form>
    </div>
</body>
</html>