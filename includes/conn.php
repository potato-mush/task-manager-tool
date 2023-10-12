<?php
    $con = mysqli_connect("localhost", "root", "", "task_db","3306")
    or die(mysqli_error($con));
    if(!isset($_SESSION)){
      session_start();
    }
