<script type="module" src="https://cdnjs.cloudflare.com/ajax/libs/ionicons/7.1.0/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://cdnjs.cloudflare.com/ajax/libs/ionicons/7.1.0/ionicons/ionicons.js"></script>

<style>
.sidenav {
    height: 100vh;
    width: 280px;
    position: fixed;
    z-index: 1000;
    top: 0;
    left: 0;
    background: linear-gradient(180deg, #73877b 0%, #5a6e62 100%);
    overflow-x: hidden;
    overflow-y: auto;
    padding-top: 0;
    transition: all 0.3s ease;
    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
}

.sidenav.collapsed {
    width: 100px;
}

.sidenav.collapsed .sidenav-header h2,
.sidenav.collapsed .user-name,
.sidenav.collapsed .user-role,
.sidenav.collapsed .nav-links a span {
    display: none;
}

.nav-icon {
    font-size: 1.25rem;
    margin-right: 12px;
    min-width: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.sidenav.collapsed .nav-icon {
    margin-right: 0;
    font-size: 24px;

}
.container.sidebar-collapsed {
    margin-left: 100px;
    width: calc(100% - 100px);
    transition: all 0.3s ease;
}


.container {
    margin-left: 280px;
    padding: 20px;
    transition: all 0.3s ease;
    min-height: 100vh;
    width: calc(100% - 280px);
    max-width: 100%;
}


.sidenav.collapsed .user-avatar {
    width: 40px;
    height: 40px;
    font-size: 16px;
    margin: 10px auto;
}

.sidenav.collapsed .sidenav-footer {
    padding: 10px;
}

.sidenav.collapsed .logout-btn {
    padding: 10px;
}

.sidenav.collapsed .logout-btn span {
    display: none;
}

.sidenav-header {
            padding: 24px 20px;
            color: white;
            border-bottom: 1px solid rgba(255,255,255,0.15);
            margin-bottom: 0;
            background-color: rgba(0,0,0,0.1);
            cursor: pointer;
        }

        .sidenav-header h2 {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }

        .sidenav.collapsed a {
            padding: 16px 10px;
            justify-content: center;
        }

        .sidenav.collapsed .logout-btn span {
            display: none;
        }

        .menu-toggle {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 2;
            cursor: pointer;
            padding: 12px 16px;
            background-color: #73877b;
            border-radius: 8px;
            color: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            transition: 0.2s ease;
        }

        .user-profile {
            text-align: center;
            padding: 24px 15px;
            background-color: rgba(255,255,255,0.05);
            border-bottom: 1px solid rgba(255,255,255,0.15);
        }

        .user-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: rgba(255,255,255,0.9);
            margin: 0 auto 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: #73877b;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }

        .user-name {
            color: white;
            font-size: 1.1rem;
            font-weight: 500;
            margin-bottom: 4px;
        }

        .user-role {
            color: rgba(255,255,255,0.8);
            font-size: 0.9rem;
            font-weight: 400;
        }

        .nav-links {
            padding: 20px 0;
        }

        .sidenav.collapsed .nav-links a {
            padding: 16px 10px;
            justify-content: center;
        }

        .sidenav a {
            padding: 16px 24px;
            text-decoration: none;
            font-size: 1rem;
            color: rgba(255,255,255,0.9);
            display: flex;
            align-items: center;
            transition: all 0.2s ease;
            border-left: 4px solid transparent;
        }

        .sidenav a:hover {
            background-color: rgba(255,255,255,0.1);
            border-left-color: rgba(255,255,255,0.5);
        }

        .sidenav a.active {
            background-color: rgba(255,255,255,0.15);
            border-left-color: white;
            font-weight: 500;
        }

        .sidenav-footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            padding: 20px;
            background-color: rgba(0,0,0,0.1);
            border-top: 1px solid rgba(255,255,255,0.15);
        }

        .logout-btn {
            background-color: rgba(255,255,255,0.15);
            color: white;
            padding: 12px;
            border-radius: 6px;
            text-align: center;
            transition: 0.2s ease;
            display: block;
            text-decoration: none;
            font-weight: 500;
        }

        .logout-btn:hover {
            background-color: rgba(255,255,255,0.25);
            transform: translateY(-1px);
        }



    </style>


    
    <nav class="sidenav" id="mySidenav">
        <div class="sidenav-header" onclick="toggleSidebar(event)">
            <h2 style="color: #f5f5f5;">Assessment Tool</h2>
            <div class="menu-toggle" onclick="toggleNav()">☰</div>
        </div>
        
        <div class="user-profile">
            <div class="user-avatar">
                <?php echo strtoupper(substr($user['first_name'], 0, 1)); ?>
            </div>
            <div class="user-name">
                <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
            </div>
            <div clas   s="user-role" style="color: #f5f5f5;">Engineer</div>
        </div>

        <div class="nav-links">
        <a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'Questionnaire.php' ? 'active' : ''; ?>">
            <ion-icon name="home-outline" class="nav-icon"></ion-icon>
                <span>Dashboard</span>
            </a>
            <a href="Questionnaire.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'Questionnaire.php' ? 'active' : ''; ?>">
            <ion-icon name="create-outline" class="nav-icon"></ion-icon>
                <span>New Assessment</span>
            </a>
            <a href="view-assessments.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'view-assessments.php' ? 'active' : ''; ?>">
            <ion-icon name="list-outline" class="nav-icon"></ion-icon>
                <span>Assessment History</span>
            </a>
        </div>
        
        <div class="sidenav-footer">
            <a href="back/logout.php" class="logout-btn">
            <ion-icon name="log-out-outline" class="nav-icon"></ion-icon>
                <span>Logout</span>
            </a>
        </div>
    </nav>

<!-- 
    <script>
const sidenav = document.getElementById("mySidenav");
const container = document.querySelector(".container");

function toggleSidebar(event) {
    if (window.innerWidth <= 808) {
        sidenav.classList.toggle("active");
        container.classList.toggle("sidebar-active");
    } else {
        sidenav.classList.toggle("collapsed");
        container.classList.toggle("sidebar-collapsed");
    }
    if (event.target === sidenav || event.target.classList.contains('sidenav-header')) {
        event.preventDefault();
    }
}

sidenav.addEventListener('click', toggleSidebar);


window.addEventListener('resize', () => {
    if (window.innerWidth > 798) {
        sidenav.classList.remove("active");
        container.classList.remove("sidebar-active");
    } else {
        sidenav.classList.remove("collapsed");
        container.classList.remove("sidebar-collapsed");
    }
});
</script> -->