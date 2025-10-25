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

        a {
            color: white;
        }

        a:hover {
            transition: .3s ease;
            color: #0096FF;
        }

        .form {}
    </style>
    <?php

    include("../connection.php");

    if (isset($_POST['submit'])) {


        $email = trim($_POST['input_email']);
        $password = trim($_POST['input_password']);
        $type = $_POST['input_type'];
        $sql = "select * from account where email='$email' and password = '$password' and type='$type' ";
        $result = mysqli_query($conn, $sql);
        $count = mysqli_num_rows($result);
        // echo $count;
        // if ($count) {
        //     echo '<script>alert("Login Successful")</script>';
        //     // header("location:Welcome.php");
        // } else {
        //     echo '<script>alert("Login Failed")</script>';
        // }
    }
    ?>


</head>

<body>

    <div class="section">
        <br>
        <br>
        <div class="sub-section">
            <form method="POST" class="form" style="font-size: 1.2rem;">
                <h2 style="text-align: center;">Login Form</h2>
                <label for="input_type">User Type: </label>
                <select name="input_type">
                    <option value=""></option>
                    <option value="provider">Provider</option>
                    <option value="consumer">Consumer</option>
                    <option value="donor">Donor</option>
                </select>
                <br><br>
                <label for="input_email">Email: </label>
                <input name="input_email" type="email"><br><br>
                <label for="input_password">Password: </label>
                <input name="input_password" type="password"> &nbsp;(numbers only)<br><br>
                <div style="display:flex;justify-content:center;"><input type="submit" name="submit"></div>

            </form>
            <!-- <div style="height:220px;padding-top:50px;text-align:center;"> -->
                <?php
                if (isset($count)) {
                    if ($count) {
                        // echo '<script>alert("Login Successful")</script>';
                        echo '
                        
                        <div style="height:220px;padding-top:50px;text-align:center;">
                        <hr>
                        <br>
                        <p style="font-size:2rem;">Login Successful</p>
                        <p><a href="../Home Page/index.php">Browse Website</a></p>
                        </div>';
                        // header("location:Welcome.php");
                    } else {
                        // echo '<script>alert("Login Failed")</script>';
                        echo '
                        <div style="height:220px;padding-top:50px;text-align:center;">
                        <hr>
                        <br>
                        <p style="font-size:2rem;">Login Failed</p>';
                        echo '<p>Don&apos;t have an account? <a href="../Registration_Login/registration_form.php">Create Account?</a></p>';
                        echo '<a href="#">Forgot Password?</a>
                        </div>';
                    }
                }
                ?>
            <!-- </div> -->
        </div>

    </div>

</body>

</html>