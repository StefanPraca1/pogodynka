<?php
$pdo = new PDO('sqlite:var/data_dev.db');
$hash = '$2y$13$jzxc5p4TK5ZI.A08w7pzV.kcLedo0l6Pv0p0qy0KeDU5gPOgRaOmq';

$stmt = $pdo->prepare('UPDATE user SET password = :hash WHERE username = "admin"');
$stmt->execute(['hash' => $hash]);

echo "✅
 Hasło zostało zaktualizowane!\n";

$check = $pdo->query('SELECT username, password FROM user')->fetchAll(PDO::FETCH_ASSOC);
print_r($check);
