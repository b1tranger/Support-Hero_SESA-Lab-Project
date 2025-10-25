<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <style>
        body {
            margin: 0;
        }

        .section {
            min-height: 100vh;
            background-color: #202020ff;
            color: white;
            /* margin: 0; */
        }

        .sub-section {
            margin: auto;

            max-width: 500px;
            padding: 20px;
            background-color: #333;
            border-radius: 20px;
            margin-top: 20px;
        }

        .form {
            padding: 10px;
        }

        input,
        textarea {
            width: 100%;
            padding: 5px;
        }

        a {
            color: white;
        }

        a:hover {
            transition: .3s ease;
            color: #0096FF;
        }

        
    </style>
    <?php

    include("../connection.php");



    if (isset($_POST['submit'])) {

        $service_name = trim($_POST['service_name']);
        $service_type = $_POST['service_type'];
        $username = trim($_POST['input_name']);
        $email = trim($_POST['input_email']);
        $deadline = trim($_POST['deadline']);
        $details = $_POST['details'];
        $compensation = trim($_POST['compensation']);
    }
    // $sql2 = "select email from table_1 where email='$email'";
    
    if (empty($service_name) || empty($service_type) || empty($username) || empty($email) || empty($deadline) || empty($details)) {
        echo 'Please fill the form';
        // } else if ($password != $confirm) {
        // echo '<script>alert("Passwords do not match")</script>';
        // } else if ($sql2) {
        // echo '<script>alert("Email already exists")</script>';
        $flag = 1;
    } else {

        // passing to the Database
    
        // $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "insert into service(service_name,service_type, username, email, deadline, details,compensation) values('$service_name','$service_type','$username','$email','$deadline','$details', '$compensation')";
        $result = mysqli_query($conn, $sql);
        // if ($result) {
        // echo '<script>alert("Signup successful")</script>';
        // header("location:login.php");
        // }
        $flag = 2;
    }
    ?>


</head>

<body>

    <div class="section">
        <br>
        <br>
        <div class="sub-section">
            <form method="POST" class="form">
                <h2 style="text-align: center;">Request a Service</h2>
                <label for="service_name">Service Name: </label>
                <input name="service_name" type="text" placeholder="give a short name for the request"><br><br>
                <label for="service_type">Service Type: </label>
                <!-- <input name="input_id" type="number"> -->
                <select name="service_type">
                    <option value="">--Select--</option>
                    <option value="request">Request</option>
                    <option value="offer">Offer</option>
                    <!-- <option value="donor">Donor</option> -->
                </select>
                <br><br>
                <label for="input_name">Username: </label>
                <input name="input_name" type="text"><br><br>
                <label for="input_email">Email: </label>
                <input name="input_email" type="email"><br><br>
                <label for="deadline">Deadline/Uptime: </label>
                <input name="deadline" type="date"><br><br>
                <label for="details">Details: </label>
                <!-- <input name="details" type="message"> -->
                <textarea name="details" rows="5" cols="40" placeholder="Enter your message here..."></textarea>
                <br><br>
                <label for="compensation">Compensation/Demand: </label>
                <input name="compensation" type="number">&nbsp;(will be deducted from Balance in BDT)<br><br>
                <div style="display:flex;justify-content:center;max-width:80px;"><input type="submit" name="submit">
                </div>

            </form>
            <?php
            if (isset($flag)) {
                if ($flag == 1) {
                    // echo '<script>alert("Login Successful")</script>';
                    echo '
                        
                        <div style="height:220px;padding-top:50px;text-align:center;">
                        <hr>
                        <br>
                        <p style="font-size:2rem;">...</p>
                        <p><a href="../Home Page/index.php#services">Go back</a></p>
                        </div>';
                    // header("location:Welcome.php");
                } else if ($flag == 2) {
                    // echo '<script>alert("Login Failed")</script>';
                    echo '
                        <div style="height:220px;padding-top:50px;text-align:center;">
                        <hr>
                        <br>
                        <p style="font-size:2rem;">Entry Successful</p>
                        <p><a href="../Home Page/index.php#services">Go back</a></p>
                        </div>';
                }
            }
            ?>
        </div>

    </div>

</body>

</html>