<?php
if (isset($_SESSION['user'])) {
    ?>
    <nav class="navbar navbar-expand-lg ds-navbar">
        <div class="container">
            <a class="navbar-brand" href="#">DiscHub</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="<?php echo $base_url; ?>/index">Home</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            Categories
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>/category/gaming">Gaming</a></li>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>/category/community">Community</a>
                            </li>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>/category/technology
                            ">Technology</a></li>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>/category/art-design">Art &
                                    Design</a>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>/category/music">Music</a>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>/category/anime">Anime</a>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>/category/books-literature">Books &
                                    Literature</a>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>/category/fitness">Fitness</a>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>/category/sports">Sports</a>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>/category/education">Education</a>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>/category/science">Science</a>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>/category/movies-tv-shows">Movies &
                                    TV
                                    Shows</a>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>/category/cooking">Cooking</a>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>/category/travel">Travel</a>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>/category/politics-debate">Politics &
                                    Debate</a>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>/category/pets-animals">Pets &
                                    Animals</a>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>/category/fashion-beauty">Fashion &
                                    Beauty</a>
                            <li><a class="dropdown-item"
                                    href="<?php echo $base_url; ?>/category/photography">Photography</a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="<?php echo $base_url; ?>/featured">Featured</a>
                    </li>
                </ul>

                <!-- User Profile Dropdown -->
                <div class="nav-item dropdown">
                    <a class="nav-link d-flex align-items-center" href="#" id="navbarDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <span style="margin-right: 7px; text-transform: uppercase;"><?php echo $user['username']; ?></span>
                        <img src="<?php echo htmlspecialchars($user['avatar_url']); ?>" alt="Profile Image"
                            class="rounded-circle ds-profile-nav">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="<?php echo $base_url; ?>/my-servers">My Servers</a></li>
                        <li><a class="dropdown-item" href="<?php echo $base_url; ?>/add-server">Add Server</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="<?php echo $base_url; ?>/logout">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
<?php } else { ?>
    <nav class="navbar navbar-expand-lg ds-navbar">
        <div class="container">
            <a class="navbar-brand" href="#">DiscordServers</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="<?php echo $base_url; ?>/index">Home</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            Categories
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>/category/gaming">Gaming</a></li>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>/category/community">Community</a>
                            </li>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>/category/technology
                            ">Technology</a></li>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>/category/art-design">Art &
                                    Design</a>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>/category/music">Music</a>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>/category/anime">Anime</a>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>/category/books-literature">Books &
                                    Literature</a>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>/category/fitness">Fitness</a>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>/category/sports">Sports</a>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>/category/education">Education</a>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>/category/science">Science</a>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>/category/movies-tv-shows">Movies &
                                    TV
                                    Shows</a>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>/category/cooking">Cooking</a>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>/category/travel">Travel</a>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>/category/politics-debate">Politics &
                                    Debate</a>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>/category/pets-animals">Pets &
                                    Animals</a>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>/category/fashion-beauty">Fashion &
                                    Beauty</a>
                            <li><a class="dropdown-item"
                                    href="<?php echo $base_url; ?>/category/photography">Photography</a>
                            </li>
                        </ul>
                    </li>
                </ul>
                <div class="nav-item dropdown">
                    <a class="nav-link d-flex align-items-center" href="#" id="navbarDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <span>My Account</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="<?php echo $base_url; ?>/discord">Login</a></li>
                        <li><a class="dropdown-item" href="<?php echo $base_url; ?>/discord">Register</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
<?php } ?>