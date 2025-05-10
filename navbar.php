
  <div class="navbar">
        <a href="./" class="logo">Griham</a>
        
        <input type="checkbox" id="menu-toggle">
        <label for="menu-toggle" class="hamburger">☰</label>
        
        <div class="nav-container">
            <label for="menu-toggle" class="close-menu">×</label>
            <ul class="nav-links">
                <li><a href="./">Home</a></li>
                
                <li class="dropdown">
                    <input type="checkbox" id="dropdown1" class="dropdown-toggle">
                    <label for="dropdown1" class="dropdown-label">
                        Services<span class="icon-nav"><i class="fa-solid fa-angle-down"></i></span>
                    </label>
                    <div class="dropdown-content">
                        <a href="./browse-listing.php">Accomodation</a>
                        <a href="./browse-meal-services.php">Meal</a>
                        <a href="./browse-gym-services.php">Gym</a>
                    </div>
                </li>
                
                
                
                <li><a href="./about.php">About</a></li>
                <li class="dropdown">
                    <input type="checkbox" id="dropdown2" class="dropdown-toggle">
                    <label for="dropdown2" class="dropdown-label">
                        Sign Up<span class="icon-nav"><i class="fa-solid fa-angle-down"></i></span>
                    </label>
                    <div class="dropdown-content">
                        <a href="./register.php">User</a>
                        <?php $token = base64_encode("owner");
                    echo '<a href="./register.php?token=' . urlencode($token) . '">Property Owner</a>'; ?>
                    </div>
                </li>
            </ul>
            
            <!-- Auth buttons for mobile only (hidden by default, shown when menu opens) -->
            <div class="mobile-auth">
                <button class="login-btn" onclick="window.location.href = './login.php';">Login</button>
            </div>
        </div>
        
        <!-- Auth buttons for desktop only -->
        <div class="auth-container">
            <button class="login-btn" onclick="window.location.href = './login.php';">Login</button>
        </div>
    </div>