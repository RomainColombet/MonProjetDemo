<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All users</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
    </style>
</head>
<body>

<?php

$host = 'localhost';
$port = '3306';
$db = 'my_activities';
$user = 'root';
$pass = 'root';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    echo $e->getMessage() ;
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}

?>

<h1>All Users</h1>

<form action="all_users.php" method="post">
    <label>Start with letter </label>
    <input type="text" name="letter_start" 
    <?php
        if (isset($_POST['letter_start'])) {
            echo "value=\"";
            echo $_POST['letter_start'];
            echo "\"";
        }
    ?>
    >
    <label>and status is</label>
    <select name="acc_status">
        <option>Active account</option>
        <option
        <?php
            if (isset($_POST['acc_status']) && $_POST['acc_status'] == "Waiting for account validation") {
                echo " selected";
            }
        ?>
        >Waiting for account validation</option>
    </select>
    <button type="submit">Ok</button>
</form>

<?php

if (isset($_POST['letter_start']) && isset($_POST['acc_status'])) {

    $status = $_POST['acc_status'] == "Waiting for account validation" ? 1 :2;
    $username_like = $_POST['letter_start'];

    $stmt = $pdo->prepare("SELECT users.id AS user_id, username, email, s.name AS status
        FROM users JOIN status s ON users.status_id = s.id
        WHERE users.status_id = :status
        AND username LIKE :username_like");
    $stmt->execute(['status' => $status, 'username_like' => $username_like."%"]);

?>
    <table>
        <tr>
            <th>Id</th>
            <th>Username</th>
            <th>Email</th>
            <th>Status</th>
        </tr>
        <?php while ($row = $stmt->fetch()) { ?>
        <tr>
            <td><?php echo $row['user_id']?></td>
            <td><?php echo $row['username']?></td>
            <td><?php echo $row['email']?></td>
            <td><?php echo $row['status']?></td>
        </tr>
        <?php } ?>
    </table>

<?php } ?>

</body>
</html>