<?php

include("includes/login.php");
include("includes/signup.php");

if (isset($_SESSION['email'])) {
    header('location: dashboard.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager Tool</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/font-awesome/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
</head>

<body>
    <header>
        <nav>
            <div class="logo"><a href="index.php#home">Tasklify</a></div>
            <div id="navbar" class="nav-container">
                <div class="nav-links">
                    <ul>
                        <li><a href="index.php#home">Home</a></li>
                        <li><a href="index.php#about">About Us</a></li>
                        <li><a href="#help">Help</a></li>
                    </ul>
                    <ul class="navbar-right">
                        <li onclick="signupToggle()" class="signup-btn">Signup</li>
                        <li onclick="loginToggle()" class="login-btn">Login</li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <div class="wrapper">
        <div class="signup-form">
            <div class="form">
                <div class="close" onclick="signupToggle()">&times;</div>
                <div class="text">
                    <span class="error" id="error_message"></span>
                    <h1>Sign Up</h1>
                    <p>Fill out the form below to get started.</p>
                </div>
                <form action="includes/signup.php" method="POST">
                    <div class="row">
                        <input type="text" placeholder="First Name" id="first_name" name="first_name" required />
                        <input type="text" placeholder="Last Name" id="last_name" name="last_name" required />
                    </div>
                    <input type="email" placeholder="Email" id="email" name="email" required />
                    <input type="password" placeholder="Password" id="password" name="password" required />
                    <input type="password" placeholder="Confirm Password" name="confirm_password" required />
                    <input type="submit" value="Register" />
                </form>
            </div>
        </div>
        <div class="login-form">
            <div class="form">
                <div class="close" onclick="loginToggle()">&times;</div>
                <div class="text">
                    <h1>Login</h1>
                    <p>Fill out the form below to get started.</p>
                </div>
                <form action="includes/login.php" method="POST">
                    <input type="email" name="email" placeholder="Email" required />
                    <input type="password" name="password" placeholder="Password" required />
                    <input type="submit" value="Login" />
                </form>
            </div>
        </div>
    </div>
    <section class="section home" id="home">
        <img src="assets/images/background.jpg" alt="background-image">
        <div class="content">
            <h1 class="section-title">Welcome to Task Manager Tool</h1>
            <p>Your go-to task management solution.</p>
            <button class="action-button" onclick="loginToggle()">Create Task</button>
        </div>
    </section>

    <section class="section about" id="about">
        <div class="content">
            <div class="upper-left">
                <h3 class="paragraph-title">Our Mission</h3>
                <p>At Task Manager Tool, our mission is to simplify task management for individuals and teams. We are dedicated to providing a user-friendly platform that helps you achieve your goals efficiently and effectively. With a commitment to excellence, we strive to make productivity accessible to everyone.</p>
            </div>
            <div class="upper-right">
                <img src="assets/images/image-right.jpg" alt="Image 1">
            </div>
            <div class="bottom-left">
                <img src="assets/images/image-left.jpg" alt="Image 2">
            </div>
            <div class="bottom-right">
                <h3 class="paragraph-title">What Sets Us Apart</h3>
                <p>What sets Task Manager Tool apart is our focus on user experience. We have designed our platform to be intuitive, powerful, and adaptable to your unique needs. We believe that task management should be a seamless and enjoyable experience, and that's what we deliver.</p>
            </div>
        </div>
    </section>

    <section class="section help" id="help">
        <div class="content">
            <h2 class="section-title">Get Started</h2>
            <p>Learn how to use Task Manager Tool with our step-by-step tutorial.</p>
            <a href="#tutorial" class="action-button">Tutorial</a>
        </div>
    </section>

    <footer class="company-footer">
        <p class="copyright">&copy; 2023 Group Name</p>
    </footer>
    <script src="assets/js/index.js"></script>
</body>

</html>