<?php
session_start();
header("Content-Type: application/json");

$username = $_SESSION["username"] ?? null;
if (!$username) {
    echo json_encode(["status" => "unauthorized"]);
    exit;
}

$userFile = __DIR__ . "/../users/user.xml";
$msgFile  = __DIR__ . "/messages.xml";   // FIX path

if (!file_exists($userFile) || !file_exists($msgFile)) {
    echo json_encode(["status" => "file_error"]);
    exit;
}

$userXml = simplexml_load_file($userFile);
$chatXml = simplexml_load_file($msgFile);

// ====== CHECK BANNED ======
foreach ($userXml->user as $u) {
    if ((string)$u->username === $username && (string)$u->banned === "yes") {
        echo json_encode(["status" => "banned"]);
        exit;
    }
}

// ====== SEND MESSAGE ======
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST['action'] ?? '') === "send") {
    $text = trim($_POST['message']);
    if ($text !== "") {
        // Lấy IP public thật
        function getUserIP() {
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
            if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
            return $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        }
        $ip = getUserIP();

        // Ghi tin nhắn vào messages.xml
        $m = $chatXml->addChild("message");
        $m->addChild("user", htmlspecialchars($username));
        $m->addChild("time", date("H:i:s d-m-Y"));
        $m->addChild("text", htmlspecialchars($text));
        $m->addChild("deleted", "no");
        $m->addChild("ip", htmlspecialchars($ip));
        $chatXml->asXML($msgFile);

        // Update IP trong user.xml
        foreach ($userXml->user as $u) {
            if ((string)$u->username === $username) {
                if (isset($u->ip)) {
                    $u->ip = $ip;
                } else {
                    $u->addChild("ip", $ip);
                }
                $userXml->asXML($userFile);
                break;
            }
        }

        echo json_encode(["status" => "ok"]);
        exit;
    }
}

// ====== FETCH MESSAGES ======
if (($_GET['action'] ?? '') === "fetch") {
    $result = [];
    foreach ($chatXml->message as $i => $m) {
        $result[] = [
            "user"      => (string)$m->user,
            "text"      => (string)$m->text,
            "time"      => (string)$m->time,
            "deleted"   => (string)$m->deleted,
            "canRevoke" => ((string)$m->user === $username && (string)$m->deleted !== "yes")
        ];
    }
    echo json_encode(["status" => "ok", "messages" => $result]);
    exit;
}

// ====== CHECK BAN (AJAX riêng) ======
if (($_GET['action'] ?? '') === "checkban") {
    foreach ($userXml->user as $u) {
        if ((string)$u->username === $username && (string)$u->banned === "yes") {
            echo json_encode(["status" => "banned"]);
            exit;
        }
    }
    echo json_encode(["status" => "ok"]);
    exit;
}

// ====== REVOKE MESSAGE ======
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST['action'] ?? '') === "revoke") {
    $id = intval($_POST['id']);
    if (isset($chatXml->message[$id])) {
        $msg = $chatXml->message[$id];
        if ((string)$msg->user === $username && (string)$msg->deleted !== "yes") {
            $msg->deleted = "yes";
            $chatXml->asXML($msgFile);
            echo json_encode(["status" => "ok"]);
            exit;
        } else {
            echo json_encode(["status" => "forbidden"]);
            exit;
        }
    }
    echo json_encode(["status" => "not_found"]);
    exit;
}

echo json_encode(["status" => "unknown"]);
