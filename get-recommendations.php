<?php
session_start();
require "back/db_configs.php";

if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

if (!isset($_GET['assessment_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Assessment ID is required']);
    exit();
}

$assessment_id = intval($_GET['assessment_id']);
$checkStmt = $pdo->prepare("SELECT id FROM wall_assessments WHERE id = ? AND user_id = ?");
$checkStmt->execute([$assessment_id, $_SESSION['user_id']]);

if (!$checkStmt->fetch()) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Assessment not found or access denied']);
    exit();
}

$stmt = $pdo->prepare("SELECT id, assessment_id, remediation_type, remediation_method, created_at 
                       FROM recommendations 
                       WHERE assessment_id = ?
                       ORDER BY created_at");
$stmt->execute([$assessment_id]);
$recommendations = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode(['status' => 'success', 'data' => $recommendations]);
exit();
?>