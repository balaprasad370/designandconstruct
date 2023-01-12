<?php



session_start();

require_once __DIR__ . "/database.php";

function validate($data)
{

    $data = trim($data);

    $data = stripslashes($data);

    $data = htmlspecialchars($data);

    return $data;
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (isset($_POST['submit-query-signup'])) {

        $user_email_data = validate($_POST['email-signup']);
        $user_name_data = validate($_POST['name-signup']);
        $user_password_data = validate($_POST['password-signup']);
        $user_type = validate($_POST['usertype']);

        $user_confirm_password_data = validate($_POST['confirm-password-signup']);

        $fetch_user_data_sql = "SELECT email from user where email = ? ;";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $fetch_user_data_sql)) {
            echo "There was an error";
        } else {
            mysqli_stmt_bind_param($stmt, "s", $user_email_data);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $fetched_email_data);
            mysqli_stmt_fetch($stmt);

            $user_email_data = strtolower($user_email_data);

            if (empty($user_email_data)) {
                header("location:register.php?empty=true");
                die();
            } else if ($fetched_email_data == $user_email_data) {
                header("location:register.php?exists=true");
                die();
            } else if (!filter_var($user_email_data, FILTER_VALIDATE_EMAIL)) {
                header("location:register.php?invalid=true");
                die();
            } else {
                if (strlen($user_password_data) < 5 && strlen($user_confirm_password_data) < 5) {
                    header("location:register.php?passlen=true");
                    die();
                } else if (empty($user_password_data) || empty($user_confirm_password_data)) {
                    header("location:register.php?passempty=true");
                    die();
                } else if ($user_password_data != $user_confirm_password_data) {
                    header("location:register.php?notequal=true");
                    die();
                } else {
                    $user_signup_data_sql =  "INSERT into user(name,email,password,usertype) values(?,?,?,?);";

                    $stmt = mysqli_stmt_init($conn);

                    if (!mysqli_stmt_prepare($stmt, $user_signup_data_sql)) {
                        echo "There was an error";
                    } else {
                        $user_password_data = password_hash($user_password_data, PASSWORD_DEFAULT);
                        if ($user_type == "admin") {
                            $user_type_data = 1;
                        } else {
                            $user_type_data = 0;
                        }
                        mysqli_stmt_bind_param($stmt, "ssss", $user_name_data, $user_email_data, $user_password_data, $user_type_data);

                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_close($stmt);



                        header("location:register.php?status=success");
                    }
                }
            }
        }
    }
}


if (isset($_SESSION['loggedin'])) {
    if ($_SESSION['loggedin'] == "true") {
        header("location:index.php");
        exit();
    }
}


$error_data = '';
$show_green = "danger";
$icon = "<i class='bi bi-exclamation-triangle-fill'></i>";

//duplicate entry checking
if (isset($_GET['exists'])) {
    $exist = $_GET['exists'];
    if ($exist == "true") {
        $error_data =  "Email already exists";
    }
}

//empty checking
else if (isset($_GET['empty'])) {
    $empty = $_GET['empty'];
    if ($empty == "true") {
        $error_data =  "Email cannot be empty";
    }
}

//invalid email checking
else if (isset($_GET['invalid'])) {
    $invalid = $_GET['invalid'];
    if ($invalid == "true") {
        $error_data =  "Invalid email";
    }
} else if (isset($_GET['status'])) {
    $status = $_GET['status'];
    if ($status == "success") {
        $error_data =  "Signup successful";
        $show_green = "success";
        $icon = "<i class='bi bi-check-circle-fill'></i>";
    }
} else if (isset($_GET['passempty'])) {
    $passempty = $_GET['passempty'];
    if ($passempty == "true") {
        $error_data =  "Password cannot be empty";
    }
} else if (isset($_GET['notequal'])) {
    $notequal = $_GET['notequal'];
    if ($notequal == "true") {
        $error_data =  " Both passwords should be same";
    }
} else if (isset($_GET['passlen'])) {
    $passlen = $_GET['passlen'];
    if ($passlen == "true") {
        $error_data =  "Passwords should be minimum 6 characters";
    }
} else {
    $error_data = '';
}



?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>ResumeStores - Signup</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <style>
        .login-center {
            display: flex;
            flex-direction: column;
            justify-content: center;
            place-items: center;

        }

        .signup-form {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: #fff000;
            width: 50%;
            border-radius: 20px;
            height: 70vh;
        }

        input {
            padding: 10px;

        }


        button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;

        }

        @media (max-width:500px) {
            .signup-form {
                width: 100%;
            }
        }
    </style>



</head>


<body>


    <main id="main">


        <section class=" contact">
            <div class="form">
                <center>
                    <h1 class="heading">SIGNUP</h1>
                </center>

                <div class="login-center">

                    <form action='register.php' method="POST" class="signup-form">

                        <?php echo !empty($error_data) ? "{$error_data}" : ''; ?>

                        <div class="name my-4">
                            <p class="plabel"><label for="name-id">Name</label></p>
                            <div class="input-wrapper">
                                <span class="icon"><i class="bi bi-person-circle"></i></span>
                                <input type="text" id="name-id" name="name-signup" class="input-form" placeholder="Name" required>
                            </div>
                        </div>

                        <div class="email my-4">
                            <p class="plabel"><label for="email-id">Email</label></p>
                            <div class="input-wrapper">
                                <span class="icon"><i class="bi bi-person-circle"></i></span>
                                <input type="email" id="email-id" name="email-signup" class="input-form" placeholder="Email" required>
                            </div>
                        </div>



                        <div class="password my-4">
                            <p class="plabel"><label for="password-id">Password</label></p>
                            <div class="input-wrapper">

                                <input type="password" id="password-id" name="password-signup" class="input-form" placeholder="Password" required>
                            </div>

                        </div>

                        <div class="password my-4">
                            <p class="plabel"><label for="cnf-password-id">Confirm Password</label></p>
                            <div class="input-wrapper">

                                <input type="password" id="cnf-password-id" name="confirm-password-signup" class="input-form" placeholder="Password" required>
                            </div>
                            <small class="text-muted" id="confirm-muted"></small>
                        </div>

                        <div>
                            <label for="usertype-1">Admin</label>
                            <input type="radio" name="usertype" value="admin" id="usertype-1">
                            <label for="usertype-2">User</label>
                            <input type="radio" name="usertype" value="user" id="usertype-2">

                        </div>


                        <div class="submit-query">

                            <button type="submit" id="submit-query-id" name="submit-query-signup" class="btn btn-primary submit-query-form my-4">SIGNUP
                            </button>

                            <p class="newuser">Already have an account? &nbsp; <a href="login.php"> Login</a></p>
                        </div>

                    </form>

                </div>
            </div>
        </section>

    </main>


</body>

</html>