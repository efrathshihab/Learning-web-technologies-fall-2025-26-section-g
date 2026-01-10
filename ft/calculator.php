<?php
// calculator.php

$num1_raw = $_POST['num1'] ?? '';
$num2_raw = $_POST['num2'] ?? '';
$op       = $_POST['op']   ?? '+';

$errors = [];
$result = null;
$expression = '';

function format_number($n): string {
    // Show up to 10 decimals, but trim trailing zeros (e.g., 5.0000000000 -> 5)
    $s = number_format($n, 10, '.', '');
    $s = rtrim($s, '0');
    $s = rtrim($s, '.');
    return $s === '' ? '0' : $s;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate numbers
    if ($num1_raw === '' || !is_numeric($num1_raw)) {
        $errors[] = "First number must be a valid numeric value.";
    }
    if ($num2_raw === '' || !is_numeric($num2_raw)) {
        $errors[] = "Second number must be a valid numeric value.";
    }

    // Validate operation
    $allowed_ops = ['+', '-', '*', '/'];
    if (!in_array($op, $allowed_ops, true)) {
        $errors[] = "Invalid operation selected.";
    }

    if (!$errors) {
        $a = (float)$num1_raw;
        $b = (float)$num2_raw;

        switch ($op) {
            case '+':
                $result = $a + $b;
                break;
            case '-':
                $result = $a - $b;
                break;
            case '*':
                $result = $a * $b;
                break;
            case '/':
                if (abs($b) < 1e-12) {
                    $errors[] = "Division by zero is not allowed.";
                } else {
                    $result = $a / $b;
                }
                break;
        }

        if (!$errors) {
            $expression = format_number($a) . " {$op} " . format_number($b) . " = " . format_number($result);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Simple PHP Calculator</title>
    <style>
        :root{
            --bg: #0b1220;
            --panel: #101a2e;
            --panel-2: #0e1628;
            --text: #e8eefc;
            --muted: #a9b4d0;
            --accent: #6ea8fe;
            --danger: #ff6b6b;
            --ok: #2ecc71;
            --shadow: 0 18px 45px rgba(0,0,0,.45);
            --radius: 18px;
        }

        *{ box-sizing: border-box; }
        body{
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: radial-gradient(1200px 600px at 20% 10%, rgba(110,168,254,.20), transparent 60%),
                        radial-gradient(900px 500px at 80% 30%, rgba(46,204,113,.10), transparent 55%),
                        var(--bg);
            font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji","Segoe UI Emoji";
            color: var(--text);
            padding: 24px;
        }

        .calc{
            width: min(420px, 100%);
            background: linear-gradient(180deg, var(--panel), var(--panel-2));
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 18px;
            border: 1px solid rgba(255,255,255,.08);
        }

        .brand{
            display:flex;
            align-items:center;
            justify-content: space-between;
            margin-bottom: 14px;
            gap: 12px;
        }

        .brand h1{
            font-size: 16px;
            margin: 0;
            letter-spacing: .4px;
            color: var(--muted);
            font-weight: 600;
        }

        .badge{
            font-size: 12px;
            color: rgba(255,255,255,.85);
            background: rgba(110,168,254,.16);
            border: 1px solid rgba(110,168,254,.35);
            padding: 6px 10px;
            border-radius: 999px;
        }

        .display{
            background: rgba(0,0,0,.35);
            border: 1px solid rgba(255,255,255,.10);
            border-radius: 14px;
            padding: 14px 14px;
            min-height: 64px;
            display:flex;
            flex-direction: column;
            justify-content:center;
            gap: 6px;
            margin-bottom: 14px;
        }

        .display .label{
            font-size: 12px;
            color: var(--muted);
            letter-spacing: .2px;
        }

        .display .value{
            font-size: 18px;
            font-weight: 700;
            word-break: break-word;
        }

        form{
            display:grid;
            grid-template-columns: 1fr 110px;
            gap: 12px;
        }

        .inputs{
            display:grid;
            gap: 12px;
        }

        .row{
            display:grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        input, select, button{
            width:100%;
            border-radius: 14px;
            border: 1px solid rgba(255,255,255,.12);
            background: rgba(255,255,255,.06);
            color: var(--text);
            padding: 12px 12px;
            outline: none;
            font-size: 14px;
        }

        input::placeholder{ color: rgba(169,180,208,.65); }

        input:focus, select:focus{
            border-color: rgba(110,168,254,.6);
            box-shadow: 0 0 0 3px rgba(110,168,254,.15);
        }

        .ops{
            display:grid;
            grid-template-rows: auto 1fr;
            gap: 12px;
        }

        button{
            cursor: pointer;
            font-weight: 700;
            background: rgba(110,168,254,.16);
            border-color: rgba(110,168,254,.35);
            transition: transform .08s ease, filter .15s ease;
        }

        button:active{ transform: translateY(1px) scale(.99); }
        button:hover{ filter: brightness(1.06); }

        .secondary{
            background: rgba(255,255,255,.06);
            border-color: rgba(255,255,255,.12);
            font-weight: 600;
        }

        .messages{
            margin-top: 12px;
            display:grid;
            gap: 10px;
        }

        .error{
            border: 1px solid rgba(255,107,107,.45);
            background: rgba(255,107,107,.12);
            padding: 10px 12px;
            border-radius: 14px;
            color: rgba(255,255,255,.95);
            font-size: 13px;
            line-height: 1.35;
        }

        .result{
            border: 1px solid rgba(46,204,113,.40);
            background: rgba(46,204,113,.10);
            padding: 10px 12px;
            border-radius: 14px;
            color: rgba(255,255,255,.95);
            font-size: 13px;
            line-height: 1.35;
        }

        .hint{
            margin-top: 10px;
            font-size: 12px;
            color: rgba(169,180,208,.85);
        }

        @media (max-width: 420px){
            form{ grid-template-columns: 1fr; }
            .ops{ grid-template-rows: auto auto; }
        }
    </style>
</head>
<body>
    <div class="calc">
        <div class="brand">
            <h1>Simple Calculator (PHP)</h1>
            <div class="badge">Lab Task 02</div>
        </div>

        <div class="display">
            <div class="label">Display</div>
            <div class="value">
                <?php if ($expression !== ''): ?>
                    <?php echo htmlspecialchars($expression); ?>
                <?php else: ?>
                    Ready.
                <?php endif; ?>
            </div>
        </div>

        <!-- Original form -->
        <form method="POST" action="">
            <div class="inputs">
                <div class="row">
                    <input
                        type="text"
                        name="num1"
                        placeholder="First number"
                        value="<?php echo htmlspecialchars($num1_raw); ?>"
                        inputmode="decimal"
                    />
                    <input
                        type="text"
                        name="num2"
                        placeholder="Second number"
                        value="<?php echo htmlspecialchars($num2_raw); ?>"
                        inputmode="decimal"
                    />
                </div>

                <button type="reset" class="secondary">Clear</button>
            </div>

            <div class="ops">
                <select name="op">
                    <option value="+" <?php echo ($op === '+') ? 'selected' : ''; ?>>+</option>
                    <option value="-" <?php echo ($op === '-') ? 'selected' : ''; ?>>−</option>
                    <option value="*" <?php echo ($op === '*') ? 'selected' : ''; ?>>×</option>
                    <option value="/" <?php echo ($op === '/') ? 'selected' : ''; ?>>÷</option>
                </select>

                <button type="submit">Calculate</button>
            </div>
        </form>

        <!-- Result / errors below form -->
        <div class="messages">
            <?php if (!empty($errors)): ?>
                <?php foreach ($errors as $e): ?>
                    <div class="error"><?php echo htmlspecialchars($e); ?></div>
                <?php endforeach; ?>
            <?php elseif ($result !== null): ?>
                <div class="result">
                    Result: <strong><?php echo htmlspecialchars(format_number($result)); ?></strong>
                </div>
            <?php endif; ?>
        </div>

        <div class="hint">Tip: You can enter decimals or negative numbers.</div>
    </div>
</body>
</html>
