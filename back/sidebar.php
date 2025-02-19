<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Inter', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
    }

    body {
        min-height: 100vh;
        background-color: #f5f5f5;
    }

    .dashboard {
        display: grid;
        grid-template-areas:
            "sidebar header"
            "sidebar main";
        grid-template-columns: 250px 1fr;
        grid-template-rows: 60px 1fr;
        min-height: 100vh;
        transition: all 0.3s ease;
    }

    .dashboard.collapsed {
        grid-template-columns: 80px 1fr;
    }
    .sidebar {
        grid-area: sidebar;
        background: white;
        padding: 20px;
        color: #333;
        border-right: 1px solid #333;
        position: relative;
        transition: all 0.3s ease;
    }
    .toggle-btn {
        position: absolute;
        right: -15px;
        top: 20px;
        background: white;
        border: 1px solid #333;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
        z-index: 10;
    }

    .collapsed .toggle-btn {
        transform: rotate(180deg);
    }

    .brand-container {
        margin-bottom: 40px;
        padding: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        height: 60px;
    }

    .brand-logo {
        width: auto;
        height: 100%;
        transition: all 0.3s ease;
        object-fit: contain;
    }

    .collapsed .brand-logo {
        width: 40px;
        height: 40px;
        object-fit: contain;
    }

    .sidebar-menu {
        list-style: none;
    }

    .sidebar-menu li {
        padding: 12px;
        margin-bottom: 10px;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .sidebar-menu li a {
        text-decoration: none;
        color: #333;
    }


    .sidebar-menu li:hover {
        background-color: rgba(115, 135, 123, 0.1);
    }

    .sidebar-menu li.active {
        background-color: rgba(115, 135, 123, 0.2);
    }

    /* Menu item styles */
    .menu-item {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 16px;
    }

    ion-icon {
        font-size: 20px;
        min-width: 20px;
    }

    .collapsed .menu-text {
        display: none;
    }


    /* Responsive Design */
    @media (max-width: 768px) {
     
        .sidebar {
            display: none;
        }
    }
</style>


<aside class="sidebar">
    <div class="toggle-btn">⮜</div>
    <div class="brand-container">
        <img src="/api/placeholder/200/60" alt="Brand Logo" class="brand-logo">
    </div>
    <ul class="sidebar-menu">
        <li>
            <a href="Admin_Dashboard.php" class="menu-item">
                <ion-icon name="home-outline"></ion-icon>
                <span class="menu-text">Home</span>
            </a>
        </li>
        <li>
            <a href="analytics.html" class="menu-item">
                <ion-icon name="bar-chart-outline"></ion-icon>
                <span class="menu-text">Analytics</span>
            </a>
        </li>
        <li>
            <a href="create_questionnaire.php" class="menu-item">
                <ion-icon name="document-text-outline"></ion-icon>
                <span class="menu-text">Reports</span>
            </a>
        </li>
        <li>
            <a href="settings.html" class="menu-item">
                <ion-icon name="settings-outline"></ion-icon>
                <span class="menu-text">Settings</span>
            </a>
        </li>
        <li>
            <a href="New folder/Admin_AddUser.php" class="menu-item">
                <ion-icon name="person-outline"></ion-icon>
                <span class="menu-text">Users</span>
            </a>
        </li>
    
    </ul>
    
</aside>

<script>
     // Toggle sidebar
     const dashboard = document.querySelector('.dashboard');
        const toggleBtn = document.querySelector('.toggle-btn');

        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                dashboard.classList.toggle('collapsed');
            });
        }

        // Active menu item
        document.querySelectorAll('.sidebar-menu li').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('.sidebar-menu li').forEach(i => i.classList.remove('active'));
                this.classList.add('active');
            });
        });
</script>