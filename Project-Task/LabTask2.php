<?php
    
    $length = null;
    $width = null;
    $area = null;
    $perimeter = null;
    $error = "";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (isset($_POST["length"]) && isset($_POST["width"])) {
            $length = (float) $_POST["length"];
            $width  = (float) $_POST["width"];

            if ($length < 0 || $width < 0) {
                $error = "Length and width must be non-negative numbers.";
            } else {
                $area = $length * $width;
                $perimeter = 2 * ($length + $width);
            }
        } else {
            $error = "Please enter both length and width.";
        }
    }
?>


// task: 2

<?php
$amount = null;
$vat = null;
$total = null;
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["amount"])) {
        $amount = (float) $_POST["amount"];

        if ($amount < 0) {
            $error = "Amount must be a non-negative number.";
        } else {
            $vat = $amount * 0.15;
            $total = $amount + $vat;
        }
    } else {
        $error = "Please enter an amount.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>VAT Result</title>
</head>
<body>
    <h2>VAT Calculation Result</h2>

    <?php if ($error !== ""): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php elseif ($vat !== null): ?>
        <p>Original Amount: <?php echo htmlspecialchars($amount); ?></p>
        <p>VAT @ 15%: <?php echo number_format($vat, 2); ?></p>
        <p>Total Amount (with VAT): <?php echo number_format($total, 2); ?></p>
    <?php else: ?>
        <p>No amount received.</p>
    <?php endif; ?>

    <a href="vat_form.html">Back to form</a>

 
    
</body>
</html>



// task:3

<?php
$number = null;
$result = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["number"])) {
        $number = (int) $_POST["number"];

        if ($number % 2 == 0) {
            $result = $number . " is an Even number.";
        } else {
            $result = $number . " is an Odd number.";
        }
    } else {
        $error = "Please enter a number.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Odd or Even Result</title>
</head>
<body>
    <h2>Result</h2>

    <?php if ($error !== ""): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php elseif ($result !== ""): ?>
        <p><?php echo htmlspecialchars($result); ?></p>
    <?php else: ?>
        <p>No number received.</p>
    <?php endif; ?>

    <a href="odd_even_form.html">Back to form</a>
</body>
</html>


// task: 4

<?php
$num1 = null;
$num2 = null;
$num3 = null;
$result = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["num1"]) && isset($_POST["num2"]) && isset($_POST["num3"])) {
        $num1 = (float) $_POST["num1"];
        $num2 = (float) $_POST["num2"];
        $num3 = (float) $_POST["num3"];

        if ($num1 == $num2 && $num2 == $num3) {
            $result = "All three numbers are equal: " . $num1;
        } else {
            if ($num1 >= $num2 && $num1 >= $num3) {
                $largest = $num1;
            } elseif ($num2 >= $num1 && $num2 >= $num3) {
                $largest = $num2;
            } else {
                $largest = $num3;
            }

            $result = "The largest number is: " . $largest;
        }
    } else {
        $error = "Please enter all three numbers.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Largest Number Result</title>
</head>
<body>
    <h2>Result</h2>

    <?php if ($error !== ""): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php elseif ($result !== ""): ?>
        <p><?php echo htmlspecialchars($result); ?></p>
    <?php else: ?>
        <p>No numbers received.</p>
    <?php endif; ?>

    <a href="largest_number_form.html">Back to form</a>
</body>
</html>