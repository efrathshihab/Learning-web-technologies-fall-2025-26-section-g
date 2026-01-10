<?php
// ---------- Helper: sanitize ----------
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    // HTML special characters escape (XSS protection)
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// ---------- Init ----------
$name = $email = "";
$password = $confirm_password = "";
$errors = ["name"=>"", "email"=>"", "password"=>"", "confirm_password"=>""];
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // 1) Read raw inputs
    $name = $_POST["name"] ?? "";
    $email = $_POST["email"] ?? "";
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    // 2) Validation: required fields
    if (trim($name) === "") $errors["name"] = "নাম দেওয়া বাধ্যতামূলক।";
    if (trim($email) === "") $errors["email"] = "ইমেইল দেওয়া বাধ্যতামূলক।";
    if ($password === "") $errors["password"] = "পাসওয়ার্ড দেওয়া বাধ্যতামূলক।";
    if ($confirm_password === "") $errors["confirm_password"] = "কনফার্ম পাসওয়ার্ড দেওয়া বাধ্যতামূলক।";

    // 3) Validation: email format
    if ($errors["email"] === "" && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "সঠিক ইমেইল ফরম্যাট নয়।";
    }

    // 4) Validation: password match
    if ($errors["password"] === "" && $errors["confirm_password"] === "" && $password !== $confirm_password) {
        $errors["confirm_password"] = "পাসওয়ার্ড মিলছে না।";
    }

    // If no errors => sanitize + show data
    $hasError = false;
    foreach ($errors as $e) {
        if ($e !== "") { $hasError = true; break; }
    }

    if (!$hasError) {
        // 5) Sanitize input data
        $safe_name = clean_input($name);
        $safe_email = clean_input($email);

        // Security note: বাস্তবে কখনো password display করা উচিত না।
        // Lab-এর জন্য sanitized/processed value দেখাতে password hash দেখানো হলো:
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $success = true;

        // form values update to sanitized (so it shows clean values)
        $name = $safe_name;
        $email = $safe_email;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>User Registration</title>
  <style>
    body{
      font-family: Arial, sans-serif;
      background:#f4f6f9;
      margin:0;
      padding:0;
    }
    .container{
      max-width: 520px;
      margin: 40px auto;
      background:#fff;
      padding: 22px 24px;
      border-radius: 10px;
      box-shadow: 0 6px 18px rgba(0,0,0,0.08);
    }
    h2{ margin-top:0; }
    .field{ margin-bottom: 14px; }
    label{ display:block; margin-bottom:6px; font-weight:600; }
    input{
      width:100%;
      padding: 10px;
      border:1px solid #cfd6df;
      border-radius: 8px;
      outline:none;
    }
    input:focus{ border-color:#6b8cff; }
    .error{
      color:#d93025;
      margin-top:6px;
      font-size: 0.92rem;
    }
    .btn{
      width:100%;
      padding: 11px;
      border:none;
      border-radius: 8px;
      cursor:pointer;
      font-weight:700;
      background:#2f6fed;
      color:#fff;
    }
    .btn:hover{ opacity:0.95; }
    .success-box{
      background:#e8f5e9;
      border:1px solid #a5d6a7;
      padding: 12px;
      border-radius: 8px;
      margin-bottom: 14px;
    }
    .data-box{
      background:#f7f9ff;
      border:1px solid #dfe6ff;
      padding: 12px;
      border-radius: 8px;
      margin-top: 12px;
    }
    .muted{ color:#546e7a; font-size:0.92rem; }
    code{ word-break: break-all; }
  </style>
</head>
<body>
  <div class="container">
    <h2>Registration Form</h2>

    <?php if ($success): ?>
      <div class="success-box">
        <strong>✅ Registration Successful!</strong><br>
        <span class="muted">নিচে আপনার স্যানিটাইজড/প্রসেসড ডাটা দেখানো হলো:</span>

        <div class="data-box">
          <p><strong>Name:</strong> <?php echo $name; ?></p>
          <p><strong>Email:</strong> <?php echo $email; ?></p>
          <p><strong>Password (hashed):</strong> <code><?php echo $password_hash; ?></code></p>
        </div>
      </div>
    <?php endif; ?>

    <!-- Self submit form (POST to same page) -->
    <form method="POST" action="">
      <div class="field">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>">
        <?php if ($errors["name"]): ?><div class="error"><?php echo $errors["name"]; ?></div><?php endif; ?>
      </div>

      <div class="field">
        <label for="email">Email</label>
        <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>">
        <?php if ($errors["email"]): ?><div class="error"><?php echo $errors["email"]; ?></div><?php endif; ?>
      </div>

      <div class="field">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" value="">
        <?php if ($errors["password"]): ?><div class="error"><?php echo $errors["password"]; ?></div><?php endif; ?>
      </div>

      <div class="field">
        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" value="">
        <?php if ($errors["confirm_password"]): ?><div class="error"><?php echo $errors["confirm_password"]; ?></div><?php endif; ?>
      </div>

      <button class="btn" type="submit">Register</button>
    </form>
  </div>
</body>
</html>