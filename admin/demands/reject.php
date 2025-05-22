<?php
require_once '../../models/Demand.php';
$demand = new Demand();
$id = $_GET['id'] ?? null;
if ($id) {
    $demand->updateStatus($id, 'Rejected');
}
header("Location: index.php");