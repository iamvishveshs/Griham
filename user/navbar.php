<div class="navbar">
    <a href="../" class="logo">Griham</a>
    <input type="checkbox" id="menu-toggle">
    <label for="menu-toggle" class="hamburger">☰</label>
    <div class="nav-container">
        <label for="menu-toggle" class="close-menu">×</label>
        <ul class="nav-links">
            <li><a href="./home.php">Dashboard</a></li>
            <li class="dropdown">
                <input type="checkbox" id="dropdown1" class="dropdown-toggle">
                <label for="dropdown1" class="dropdown-label">
                    Services<span class="icon-nav"><i class="fa-solid fa-angle-down"></i></span>
                </label>
                <div class="dropdown-content">
                    <a href="./location.php?refferer=accomodation">PG/Room/Apartment</a>
                    <a href="roommate-choice.php">Roomate</a>
                    <a href="./location.php?refferer=meal">Dhaba/Tiffin/Cafe</a>
                    <a href="./location.php?refferer=laundry">Laundry </a>
                    <a href="./location.php?refferer=emergency">Emergency</a>
                    <a href="./location.php?refferer=gym">Gym & Fitness</a>
                </div>
            </li>
            <li><a href="./account.php">Your Account</a></li>
        </ul>
        <!-- Auth buttons for mobile only (hidden by default, shown when menu opens) -->
        <div class="mobile-auth">
            <button class="login-btn" onclick="window.location.href = './logout.php';">Logout</button>
        </div>
        <!-- Auth buttons for desktop only -->
        <div class="auth-container">
            <label for="profile2" class="profile-dropdown">
                <input type="checkbox" id="profile2">
                <img
                    src="../assets<?php echo !empty($_SESSION['nav_profile_pic']) ? "/uploads/profile/" . htmlspecialchars($_SESSION['nav_profile_pic']) : "/images/user.png"; ?>" />
                <ul>
                    <li>
                        <p><?php echo $_SESSION['name']; ?></p>
                    </li>
                    <hr>
                    <li><a href="./account.php">Your Account</a></li>
                    <li><a href="./logout.php">Logout</a></li>
                </ul>
            </label>
        </div>
    </div>
</div>