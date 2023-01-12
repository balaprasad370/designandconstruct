<?php


session_start();

require_once  __DIR__ . "/database.php";

if (isset($_SESSION['loggedin'])) {
    if ($_SESSION['loggedin'] == "true") {
        header("location:index.php");
    }
}

function validate($data)
{

    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submit-query-login'])) {
        $_SESSION['reload'] = 1;


        $user_email_data = validate($_POST['email-login']);

        $user_password_data = validate($_POST['password-login']);




        if (empty($user_email_data) || empty($user_password_data)) {
            $_SESSION['login-error'] = "Username or Password cannot be empty";
            $_SESSION['loggedin'] = false;
            $_SESSION['userid'] = '';
        } else {


            //fetching email from data and checking for record

            $fetch_user_data_sql = "SELECT email,password,usertype from user where email = ?;";
            $stmt = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt, $fetch_user_data_sql)) {
                echo "There was an error";
            } else {
                // $user_password_data = password_hash($user_password_data,PASSWORD_DEFAULT);
                mysqli_stmt_bind_param($stmt, "s", $user_email_data);
                mysqli_stmt_execute($stmt);
                $fetched_both_data = mysqli_stmt_get_result($stmt);
                mysqli_stmt_fetch($stmt);

                while ($row = mysqli_fetch_array($fetched_both_data, MYSQLI_NUM)) {
                    $fetched_email_data = $row[0];
                    $fetched_password_data = $row[1];
                    $usertype = $row[2];
                }



                if ($user_email_data != $fetched_email_data) {

                    $_SESSION['login-error'] = "Email does not exist <a href='register.php' style='font-weight:700;margin-left:20px;'> Create an account</a>";
                    $_SESSION['loggedin'] = "false";
                    $_SESSION['userid'] = '';
                } else {

                    if (password_verify($user_password_data, $fetched_password_data)) {

                        $_SESSION['loggedin'] = "true";
                        $_SESSION['userid'] = $fetched_email_data;
                        $_SESSION['login-error'] = '';
                        if ($usertype) {
                            $_SESSION['usertype'] = "admin";
                        } else {
                            $_SESSION['usertype'] = "user";
                        }
                        header("location:index.php");
                        exit();
                    } else {

                        $_SESSION['login-error'] = "Wrong username or password";
                        $_SESSION['loggedin'] = "false";
                        $_SESSION['userid'] = '';
                        header("location:login.php");
                        exit();
                    }
                }
            }
        }

        header("location:login.php");
    }
}



?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Design and Construct - Loginpage</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <style>
        .login-center {
            display: flex;
            flex-direction: column;
            justify-content: center;
            place-items: center;

        }

        .login-form {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: #fff000;
            width: 50%;
            border-radius: 20px;
            height: 54vh;
        }

        input {
            padding: 10px;

        }



        @media (max-width:600px) {
            .login-form {
                width: 100%;
            }
        }
    </style>

</head>


<body>

    <main id="main">


        <section class="sect">
            <div class="form">

                <center>
                    <h1 class="heading">LOGIN</h1>
                </center>
                <div class="login-center">



                    <form action='login.php' method="POST" class="login-form">


                        <?php echo !empty($_SESSION['login-error']) ? "{$_SESSION['login-error']} " : ''; ?>

                        <div class="email my-4">
                            <h4><label for="email-id">Email</label></h4>
                            <div class="input-wrapper">

                                <input type="email" id="email-id" name="email-login" class="form-control input-form" placeholder="Email" required>
                            </div>
                        </div>



                        <div class="password my-4">
                            <h4><label for="password-id">Password</label></h4>
                            <div class="input-wrapper">

                                <input type="password" id="password-id" name="password-login" class=" input-form" placeholder="Password" required>
                            </div>
                        </div>

                        <div class="submit-query">

                            <button type="submit" style="width: 100%;padding:8px;margin-top:40px;" id="submit-query-id" name="submit-query-login" class="btn btn-primary submit-query-form my-4">LOGIN
                            </button>

                            <div class="newuser">Don't have an account? &nbsp; <a href="register.php"> Register</a></div>
                        </div>
                    </form>


                </div>
            </div>
        </section>





    </main>

</body>

</html>