<!DOCTYPE html>
<html>
<head>
    <style>
        #layout-title {
            position:absolute;
            width:100%; height:80px;
            top:0; left:0;
            text-align:center;
            background-color:Beige;
        }
        #layout-left {
            position:absolute;
            top:80px; left:0;
            width:100px; height:calc(100vh - 80px);
            background-color:AliceBlue;
        }
        #layout-right {
            position:absolute;
            top:80px; left:100px;
            width:calc(100vw - 100px); height:calc(100vh - 80px);
        }
        #nav-buttons {
            position:absolute;
            width:80px;
            top:15px; left:calc(50% - 40px);
        }
        #nav-buttons > button {
            display:inline-block;
            width:80px;
        }
        #blanket {
            display:none;
            position:absolute;
            top:0; left:0;
            width:100%; height:100%;
            background-color:LightGrey;
            opacity:0.5;
            z-index:998;
        }
        .modal {
            display:none;
            position:absolute;
            width:400px; height:300px;
            top:calc(50% - 150px); left:calc(50% - 200px);
            border:1px solid black;
            background-color:White;
            z-index:999;
        }
        .modal label {
            display:inline-block; 
            width:80px;
            position:relative;
            left:5px;
        }
        #modal-signin #cancel-signin {
            position:absolute;
            left:5px; bottom:5px;
        }
        #modal-signin #submit-signin {
            position:absolute;
            right:5px; bottom:5px;
        }
        #modal-signup #cancel-signup { 
            position:absolute; 
            left:5px; bottom:5px; }
        #modal-signup #submit-signup { 
            position:absolute; 
            right:5px; bottom:5px; }
</style>
</head>
<body>
    <div id='layout-title'>
        <h1>Campus PollLive</h1>
    </div>
    
    <div id='layout-left'>
        <div id='nav-buttons'>
            <button class='button-nav' id='button-signin'>Sign In</button><br><br>
            <button class='button-nav' id='button-signup'>Sign Up</button>
        </div>
    </div>
    <div id='layout-right'>
        <div id='blanket'></div>
            <div class='modal' id='modal-signin'>
                <h2 style='text-align:center'>Sign In</h2>
                <hr>
                <br>
                <form method = 'POST' action = 'pollLive_controller.php'>
                    <input type='hidden' name='page' value='StartPage'>
                    <input type='hidden' name='command' value='SignIn'>
                    <label for='signin-username'>Username:</label>
                    <input type='text' name='username'><?php if(!empty($error_msg_username)) echo $error_msg_username; ?><br><br>
                    <label for='signin-password'>Password:</label>
                    <input type='password' name='password'><?php if(!empty($error_msg_password)) echo $error_msg_password; ?><br>
                    <input id='cancel-signin' type='button' value='Cancel'>
                    <input id='submit-signin' type='submit'>
                </form>
            </div>
            <div class='modal' id='modal-signup'>
                <h2 style='text-align:center'>Sign Up</h2>
                <hr>
                <br>
                <form method = 'POST' action = 'pollLive_controller.php'>
                    <input type='hidden' name='page' value='StartPage'>
                    <input type='hidden' name='command' value='SignUp'>
                    <label for='signup-username'>Username:</label>
                    <input type='text' name='username'><?php if(!empty($error_msg_username_UP)) echo $error_msg_username_UP; ?><br><br>
                    <label for='signup-password'>Password:</label>
                    <input type='password' name='password'><br><br>
                    <label for='signup-email'>Email:</label>
                    <input type='email' name='email'><br>
                    <input id='cancel-signup' type='button' value='Cancel'>
                    <input id='submit-signup' type='submit'>
                </form>
            </div>
    </div>
</body>

<script>
    document.getElementById('button-signin').addEventListener('click', function() {
        document.getElementById('blanket').style.display = 'block';
        document.getElementById('modal-signin').style.display = 'block';
        document.getElementById('modal-signup').style.display = 'none';
    });
    document.getElementById('cancel-signin').addEventListener('click', function() {
        document.getElementById('blanket').style.display = 'none';
        document.getElementById('modal-signin').style.display = 'none';
    });
    document.getElementById('button-signup').addEventListener('click', function() {
        document.getElementById('blanket').style.display = 'block';
        document.getElementById('modal-signup').style.display = 'block';
        document.getElementById('modal-signin').style.display = 'none';
    });
    document.getElementById('cancel-signup').addEventListener('click', function() {
        document.getElementById('blanket').style.display = 'none';
        document.getElementById('modal-signup').style.display = 'none';
    });
    document.getElementById('blanket').addEventListener('click', function() {
        document.getElementById('blanket').style.display = 'none';
        document.getElementById('modal-signin').style.display = 'none';
        document.getElementById('modal-signup').style.display = 'none';
    });

    function show_signin_modal_window() {
        document.getElementById('blanket').style.display = 'block';
        document.getElementById('modal-signin').style.display = 'block';
    }
    function show_signup_modal_window() {
        document.getElementById('blanket').style.display = 'block';
        document.getElementById('modal-signup').style.display = 'block';
    }
    <?php
         if ($display_modal_window == 'signin')
         echo 'show_signin_modal_window();';  // The code to invoke show_signin_modal_window()
         else if ($display_modal_window == 'signup')
         echo 'show_signup_modal_window();';  // The code to invoke show_signup_modal_window
    ?>
</script>
</html>