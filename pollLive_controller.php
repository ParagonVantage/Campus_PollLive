<?php
session_start();
require_once 'pollLive_model.php';

// AJAX endpoint: load polls (Home & My Polls) as JSON
if (isset($_GET['ajax']) && $_GET['ajax'] === 'load_polls') {

    if (!isset($_SESSION['username'])) {
        echo json_encode(array(
            'ok'       => false,
            'message'  => 'You must be logged in.',
            'polls'    => array(),
            'my_polls' => array()
        ));
        exit();
    }
    $username = $_SESSION['username'];

    $search   = isset($_GET['search'])   ? $_GET['search']   : '';
    $category = isset($_GET['category']) ? $_GET['category'] : '';

    $polls    = get_filtered_polls($username, $search, $category, false);
    $my_polls = get_filtered_polls($username, $search, $category, true);

    echo json_encode(array(
        'ok'       => true,
        'message'  => '',
        'polls'    => $polls,
        'my_polls' => $my_polls
    ));
    exit();
}


// AJAX endpoint: cast a vote and return updated polls
if (isset($_POST['ajax']) && $_POST['ajax'] === 'vote') {

    if (!isset($_SESSION['username'])) {
        echo json_encode(array(
            'ok'      => false,
            'message' => 'You must be logged in to vote.'
        ));
        exit();
    }

    $username = $_SESSION['username'];
    $pollId   = isset($_POST['poll_id'])   ? (int)$_POST['poll_id']   : 0;
    $optionId = isset($_POST['option_id']) ? (int)$_POST['option_id'] : 0;

    if ($pollId <= 0 || $optionId <= 0) {
        echo json_encode(array(
            'ok'      => false,
            'message' => 'Please select an option before voting.'
        ));
        exit();
    }

    $res = cast_vote($pollId, $optionId, $username);

    // Reload polls with updated vote state
    $polls    = get_all_polls_with_votes($username);
    $my_polls = get_polls_by_user_with_votes($username);

    echo json_encode(array(
        'ok'       => $res['ok'],
        'message'  => $res['message'],
        'polls'    => $polls,
        'my_polls' => $my_polls
    ));
    exit();
}

if (empty($_POST['page'])) {

    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];

        $polls    = get_all_polls_with_votes($username);
        $my_polls = get_polls_by_user_with_votes($username);
        $profile  = get_user_profile($username);
        $info_msg = '';

        include('pollLive_mainpage.php');
        exit();
    } else {
        $display_modal_window = 'none';
        include('pollLive_startpage.php');
        exit();
    }
}
$page = $_POST['page'];
$command = $_POST['command'];
// Case 2: When a command comes from StartPage
if ($page === 'StartPage' && $command === 'SignIn') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    if (is_valid($username,  $password)) {
        $_SESSION['username'] = $username;
        $polls    = get_all_polls_with_votes( $_SESSION['username']);
        $my_polls = get_polls_by_user_with_votes( $_SESSION['username']);
        $profile  = get_user_profile( $_SESSION['username']);
        $info_msg = '';   // no message on first load
        include ('pollLive_mainpage.php');  // The user will see Main page.
        exit();
    } else {
        $error_msg_username = '* WRONG USERNAME OR';
        $error_msg_password = '* WRONG PASSWORD OR';
        $display_modal_window = 'signin';  // This variable will be used in 'pollLive_startpage.php'.
        include ('pollLive_startpage.php');  // The user will see StartPage with SignIn modal window on.
        exit();
    }
}
else if ($page === 'StartPage' && $command === 'SignUp') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email    = $_POST['email'];

    if (checkUser($username)) {
        // Username already exists
        $error_msg_username_UP = '* USERNAME ALREADY REGISTERED';
        $display_modal_window = 'signup';
        include('pollLive_startpage.php');
        exit();
    } else {
        // Try inserting the new user
        $success = insertUser($username, $password, $email);
        if ($success) {
            $display_modal_window = 'signin'; 
        } else {
            $display_modal_window = 'signup'; 
            $error_msg_username = '* REGISTRATION FAILED';
        }
        include('pollLive_startpage.php');
        exit();
    }
}
else if ($page === 'MainPage' && $command === 'CreatePoll') {

    // If somehow not logged in, send back to start page
    if (!isset($_SESSION['username'])) {
        $display_modal_window = 'none';
        include('pollLive_startpage.php');
        exit();
    }

    $username     = $_SESSION['username'];
    $question    = isset($_POST['question'])    ? $_POST['question']    : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $category    = isset($_POST['category'])    ? $_POST['category']    : '';
    $options     = array(
        isset($_POST['option1']) ? $_POST['option1'] : '',
        isset($_POST['option2']) ? $_POST['option2'] : '',
        isset($_POST['option3']) ? $_POST['option3'] : '',
        isset($_POST['option4']) ? $_POST['option4'] : ''
    );

    $question = trim($question);

    if ($question === '') {
        $info_msg = 'Please enter a question for the poll.';
    } else {
        $poll_id = insert_poll($username, $question, $description, $category, $options);
        if ($poll_id) {
            $info_msg = 'Poll created successfully.';
        } else {
            $info_msg = 'Poll was not created due to a database error.';
        }
    }
    $polls    = get_all_polls_with_votes($username);
    $my_polls = get_polls_by_user_with_votes($username);
    $profile  = get_user_profile($username);
    
    include('pollLive_mainpage.php');
    exit();
}
else if ($page === 'MainPage' && $command === 'Vote') {

    if (!isset($_SESSION['username'])) {
        $display_modal_window = 'none';
        include('pollLive_startpage.php'); // or your startpage filename
        exit();
    }

    $username = $_SESSION['username'];
    $pollId   = isset($_POST['poll_id'])   ? (int)$_POST['poll_id']   : 0;
    $optionId = isset($_POST['option_id']) ? (int)$_POST['option_id'] : 0;

    if ($pollId <= 0 || $optionId <= 0) {
        $info_msg = 'Please select an option before voting.';
    } else {
        $res = cast_vote($pollId, $optionId, $username);
        $info_msg = $res['message'];
    }

    // Reload lists with vote state so the voted poll shows results
    $polls    = get_all_polls_with_votes($username);
    $my_polls = get_polls_by_user_with_votes($username);
    $profile  = get_user_profile($username);

    include('pollLive_mainpage.php'); // or your mainpage filename
    exit();
}
else if ($page === 'MainPage' && $command === 'UpdateProfile') {

    if (!isset($_SESSION['username'])) {
        $display_modal_window = 'none';
        include('pollLive_startpage.php'); // adjust to your actual startpage filename
        exit();
    }

    $username    = $_SESSION['username'];
    $email       = isset($_POST['email'])        ? $_POST['email']        : '';
    $displayName = isset($_POST['display_name']) ? $_POST['display_name'] : '';
    $bio         = isset($_POST['bio'])          ? $_POST['bio']          : '';

    $res = update_user_profile($username, $email, $displayName, $bio);
    $profile_msg = $res['message'];

    // Reload main page data
    $polls    = get_all_polls_with_votes($username);
    $my_polls = get_polls_by_user_with_votes($username);
    $profile  = get_user_profile($username);
    include('pollLive_mainpage.php');
    exit();
}
else if ($page === 'MainPage' && $command === 'Unsubscribe') {

    if (!isset($_SESSION['username'])) {
        $display_modal_window = 'none';
        include('pollLive_startpage.php');
        exit();
    }

    $username = $_SESSION['username'];

    $res = unsubscribe_user($username);
    $unsubscribe_msg = $res['message'];

    // Clear session
    $_SESSION = array();
    if (session_id() !== '' || isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    session_destroy();

    // Back to start page; you can optionally show a simple message there later
    $display_modal_window = 'none';
    include('pollLive_startpage.php');
    exit();
}

else if ($page === 'MainPage' && $command === 'SignOut') {
    session_unset();
    session_destroy();
    $display_modal_window = 'none';
    include('pollLive_startpage.php');
    exit();
}
?>
