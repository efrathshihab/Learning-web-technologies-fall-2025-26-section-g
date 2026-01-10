<?php
// contact.php

session_start();

// -------- Config --------
$MAX_FILE_SIZE = 2 * 1024 * 1024; // 2 MB
$ALLOWED_EXTENSIONS = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
$ALLOWED_MIME = [
  'application/pdf',
  'application/msword',
  'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
  'image/jpeg',
  'image/png'
];

$UPLOAD_DIR = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;

// Create uploads dir if not exists
if (!is_dir($UPLOAD_DIR)) {
  @mkdir($UPLOAD_DIR, 0755, true);
}

// -------- Helpers --------
function e($value) {
  return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function sanitize_text($value) {
  $value = trim((string)$value);
  $value = str_replace("\0", '', $value);
  return $value;
}

function get_post($key) {
  return isset($_POST[$key]) ? sanitize_text($_POST[$key]) : '';
}

function is_valid_subject($subject) {
  $allowed = ['General', 'Support', 'Feedback'];
  return in_array($subject, $allowed, true);
}

// -------- State --------
$errors = [];
$old = [
  'name'    => '',
  'email'   => '',
  'subject' => 'General',
  'message' => ''
];

// Show success + data after redirect (PRG pattern)
$sent = isset($_GET['sent']) && $_GET['sent'] === '1';
$sentData = null;
if ($sent && isset($_SESSION['sent_data'])) {
  $sentData = $_SESSION['sent_data'];
  // Clear it so refresh doesn't re-show old data forever
  unset($_SESSION['sent_data']);
}

// -------- Handle POST --------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $old['name']    = get_post('name');
  $old['email']   = get_post('email');
  $old['subject'] = get_post('subject') ?: 'General';
  $old['message'] = get_post('message');

  // Required validations
  if ($old['name'] === '') {
    $errors['name'] = 'Name is required.';
  }

  if ($old['email'] === '') {
    $errors['email'] = 'Email is required.';
  } elseif (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Invalid email format.';
  }

  if (!is_valid_subject($old['subject'])) {
    $errors['subject'] = 'Invalid subject selected.';
  }

  if ($old['message'] === '') {
    $errors['message'] = 'Message is required.';
  } elseif (mb_strlen($old['message']) < 10) {
    $errors['message'] = 'Message must be at least 10 characters.';
  }

  // Attachment validation (optional)
  $uploadedFileName = null;
  $uploadedFilePath = null;

  if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] !== UPLOAD_ERR_NO_FILE) {
    $file = $_FILES['attachment'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
      $errors['attachment'] = 'File upload error.';
    } else {
      if ($file['size'] > $MAX_FILE_SIZE) {
        $errors['attachment'] = 'File is too large. Max size is 2 MB.';
      } else {
        $originalName = $file['name'];
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (!in_array($ext, $ALLOWED_EXTENSIONS, true)) {
          $errors['attachment'] = 'Invalid file type. Allowed: ' . implode(', ', $ALLOWED_EXTENSIONS);
        } else {
          // MIME type check (stronger than extension)
          $finfo = new finfo(FILEINFO_MIME_TYPE);
          $mime = $finfo->file($file['tmp_name']);

          if ($mime === false || !in_array($mime, $ALLOWED_MIME, true)) {
            $errors['attachment'] = 'Invalid file MIME type.';
          } else {
            // Move file (optional, but useful for simulation)
            $safeBase = preg_replace('/[^a-zA-Z0-9_\-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
            $newName = $safeBase . '_' . date('Ymd_His') . '.' . $ext;
            $dest = $UPLOAD_DIR . $newName;

            if (!move_uploaded_file($file['tmp_name'], $dest)) {
              $errors['attachment'] = 'Could not save uploaded file.';
            } else {
              $uploadedFileName = $newName;
              $uploadedFilePath = 'uploads/' . $newName; // relative path for display
            }
          }
        }
      }
    }
  }

  // If valid, "simulate sending email"
  if (empty($errors)) {
    // Simulated email send (no mail() call required)
    $sentPayload = [
      'name'       => $old['name'],
      'email'      => $old['email'],
      'subject'    => $old['subject'],
      'message'    => $old['message'],
      'attachment' => $uploadedFileName ? $uploadedFilePath : null,
      'sent_at'    => date('Y-m-d H:i:s')
    ];

    $_SESSION['sent_data'] = $sentPayload;

    // Clear form by redirect (PRG)
    header('Location: contact.php?sent=1');
    exit;
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Contact Form</title>
  <style>
    body { font-family: Arial, sans-serif; max-width: 820px; margin: 30px auto; padding: 0 16px; }
    .card { border: 1px solid #ddd; border-radius: 10px; padding: 18px; margin-bottom: 18px; }
    label { display: block; font-weight: 600; margin-top: 12px; }
    input[type="text"], input[type="email"], select, textarea {
      width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px; margin-top: 6px;
    }
    textarea { min-height: 120px; resize: vertical; }
    .error { color: #b00020; font-size: 0.92rem; margin-top: 6px; }
    .success { background: #e9f8ef; border: 1px solid #b7ebc6; }
    .btn { margin-top: 16px; padding: 10px 14px; border: 0; border-radius: 8px; cursor: pointer; }
    .btn-primary { background: #1f6feb; color: #fff; }
    .muted { color: #666; font-size: 0.92rem; }
    table { width: 100%; border-collapse: collapse; }
    td { padding: 8px; border-bottom: 1px solid #eee; vertical-align: top; }
    td:first-child { width: 170px; font-weight: 600; }
    a { color: #1f6feb; }
  </style>
</head>
<body>

  <?php if ($sent && $sentData): ?>
    <div class="card success">
      <h2 style="margin-top:0;">✅ Email Sent (Simulated)</h2>
      <p class="muted" style="margin-top:0;">Submitted data:</p>

      <table>
        <tr><td>Name</td><td><?= e($sentData['name']) ?></td></tr>
        <tr><td>Email</td><td><?= e($sentData['email']) ?></td></tr>
        <tr><td>Subject</td><td><?= e($sentData['subject']) ?></td></tr>
        <tr><td>Message</td><td><?= nl2br(e($sentData['message'])) ?></td></tr>
        <tr>
          <td>Attachment</td>
          <td>
            <?php if ($sentData['attachment']): ?>
              <a href="<?= e($sentData['attachment']) ?>" target="_blank">View uploaded file</a>
            <?php else: ?>
              <span class="muted">None</span>
            <?php endif; ?>
          </td>
        </tr>
        <tr><td>Sent At</td><td><?= e($sentData['sent_at']) ?></td></tr>
      </table>

      <p class="muted">Form cleared via redirect. You can submit another message below.</p>
    </div>
  <?php endif; ?>

  <div class="card">
    <h2 style="margin-top:0;">Contact Form</h2>
    <form method="post" action="contact.php" enctype="multipart/form-data" novalidate>
      <label for="name">Name <span class="muted">(required)</span></label>
      <input type="text" id="name" name="name" value="<?= e($old['name']) ?>">
      <?php if (isset($errors['name'])): ?><div class="error"><?= e($errors['name']) ?></div><?php endif; ?>

      <label for="email">Email <span class="muted">(required)</span></label>
      <input type="email" id="email" name="email" value="<?= e($old['email']) ?>">
      <?php if (isset($errors['email'])): ?><div class="error"><?= e($errors['email']) ?></div><?php endif; ?>

      <label for="subject">Subject</label>
      <select id="subject" name="subject">
        <?php foreach (['General','Support','Feedback'] as $opt): ?>
          <option value="<?= e($opt) ?>" <?= $old['subject'] === $opt ? 'selected' : '' ?>>
            <?= e($opt) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <?php if (isset($errors['subject'])): ?><div class="error"><?= e($errors['subject']) ?></div><?php endif; ?>

      <label for="message">Message <span class="muted">(min 10 characters)</span></label>
      <textarea id="message" name="message"><?= e($old['message']) ?></textarea>
      <?php if (isset($errors['message'])): ?><div class="error"><?= e($errors['message']) ?></div><?php endif; ?>

      <label for="attachment">Attachment <span class="muted">(optional: pdf/doc/docx/jpg/png, max 2MB)</span></label>
      <input type="file" id="attachment" name="attachment" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
      <?php if (isset($errors['attachment'])): ?><div class="error"><?= e($errors['attachment']) ?></div><?php endif; ?>

      <button class="btn btn-primary" type="submit">Send</button>
    </form>
  </div>

  <p class="muted">
    After finishing, commit and push your <code>php-contact-form</code> folder to your GitHub repository.
  </p>

</body>
</html>
