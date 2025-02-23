<?php
$stmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<style>
    .header {
    background-color: #73877b;
    color: white;
    padding: 20px;
    margin-bottom: 30px;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.header-content {
    flex-grow: 1;
}

.user-info {
    float: right;
    font-size: 0.9em;
}
</style>  

<div class="header">
            <div class="header-content">
                <h1>Assessment Records</h1>
                <p>View and manage your retaining wall assessments</p>
            </div>
            <div class="user-info">
                Welcome, <?php echo safe_value($user['first_name'] . ' ' . $user['last_name']); ?>
                <div id="clock" style="color: #f5f5f5; font-size: 18px; font-weight: bold;"></div>
                <script> function updateClock() {
                    const now = new Date();
                    const hours = now.getHours().toString().padStart(2, '0');
                    const minutes = now.getMinutes().toString().padStart(2, '0');
                    const seconds = now.getSeconds().toString().padStart(2, '0');
                    const formattedTime = `${hours}:${minutes}:${seconds}`;
                    document.getElementById('clock').textContent = formattedTime;
                }
                
                setInterval(updateClock, 1000);
                updateClock();
                </script>
                <br>
             
            </div>
        </div>