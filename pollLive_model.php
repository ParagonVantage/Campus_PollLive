<?php

$conn = mysqli_connect('localhost', 'f3alimaye', 'f3alimaye136', 'C354_f3alimaye');
if (!$conn) {
    echo "Failed to connect to C354_test: " . mysqli_connect_error();
}

/* 1) Check if a username is already registered
   Returns: true if exists, false otherwise*/
function checkUser($username) {
    global $conn;
    $sql = "SELECT * FROM USERS WHERE Username = '$username'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        return true;
    }
    return false;
}

// -----------------------------------------------------------------
// 2) Register a new user (username, password, email)
//    - Rejects if username already exists
//    - Inserts current date as INT Ymd
//    Returns: true on success, false on failure/duplicate
function insertUser($username, $password, $email) {
    global $conn;
    $sql = "SELECT * FROM USERS WHERE USERNAME = '$username'";
    $rs  = mysqli_query($conn, $sql);
    if (mysqli_num_rows($rs) > 0) {
        return false;
    }

    $current_date = date("Ymd");
    $ins_sql = "INSERT INTO USERS (ID, USERNAME, DisplayName, PASSWORD, EMAIL, BIO, DATE) 
    VALUES (NULL, '$username', '', '$password', '$email','', $current_date)";
    $ok = mysqli_query($conn, $ins_sql);
    return ($ok === true);
}

// -----------------------------------------------------------------
// 3) Validate credentials (username, password)
//    Returns: true if valid pair found, false otherwise
function is_valid($username, $password) {
    global $conn;
    $sql = "SELECT * FROM USERS WHERE Username = '$username' AND Password = '$password'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        return true;
    }
    return false;
}
// -----------------------------------------------------------------
// Insert a new poll with up to 4 options.
// Returns: PollID on success, false on failure.
function insert_poll($creator_username, $question, $description, $category, $options_array) {
    global $conn;

    // Basic escaping
    $creator_username = mysqli_real_escape_string($conn, $creator_username);
    $question   = mysqli_real_escape_string($conn, $question);
    $description = mysqli_real_escape_string($conn, $description);
    $category   = mysqli_real_escape_string($conn, $category);

    // Insert into POLLS table
    $sql = "INSERT INTO POLLS (CreatorUsername, Question, Description, Category)
            VALUES ('$creator_username', '$question', '$description', '$category')";
    if (!mysqli_query($conn, $sql)) {
        return false;
    }

    $poll_id = mysqli_insert_id($conn);

    // Insert each non-empty option
    foreach ($options_array as $text) {
        $text = trim($text);
        if ($text === '') continue;
        $text = mysqli_real_escape_string($conn, $text);
        $sql_opt = "INSERT INTO POLL_OPTIONS (PollID, OptionText)
                    VALUES ($poll_id, '$text')";
        mysqli_query($conn, $sql_opt);
    }

    return $poll_id;
}

// -----------------------------------------------------------------
// Fetch all polls (for the Home feed), including their options.
// Returns: array of polls; each poll has an "Options" sub-array.
function get_all_polls() {
    global $conn;

    $sql = "SELECT p.PollID, p.CreatorUsername, p.Question, p.Description,
                   p.Category, p.CreatedAt,
                   o.OptionID, o.OptionText
            FROM POLLS p
            LEFT JOIN POLL_OPTIONS o ON p.PollID = o.PollID
            ORDER BY p.CreatedAt DESC, o.OptionID ASC";

    $result = mysqli_query($conn, $sql);
    $polls = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $id = $row['PollID'];

        if (!isset($polls[$id])) {
            $polls[$id] = array(
                'PollID'          => $id,
                'CreatorUsername' => $row['CreatorUsername'],
                'Question'        => $row['Question'],
                'Description'     => $row['Description'],
                'Category'        => $row['Category'],
                'CreatedAt'       => $row['CreatedAt'],
                'Options'         => array()
            );
        }

       if (!empty($row['OptionID'])) {
    $polls[$id]['Options'][] = array(
        'OptionID'   => (int)$row['OptionID'],
        'OptionText' => $row['OptionText']
        );
    }
}

    // Reindex numerically for easier loops in the view
    return array_values($polls);
}
// -----------------------------------------------------------------
// Fetch all polls created by a specific user (for "My Polls").
// Returns: array of polls; each poll has an "Options" sub-array.
function get_polls_by_user($username) {
    global $conn;

    $username = mysqli_real_escape_string($conn, $username);

    $sql = "SELECT p.PollID, p.CreatorUsername, p.Question, p.Description,
                   p.Category, p.CreatedAt,
                   o.OptionID, o.OptionText
            FROM POLLS p
            LEFT JOIN POLL_OPTIONS o ON p.PollID = o.PollID
            WHERE p.CreatorUsername = '$username'
            ORDER BY p.CreatedAt DESC, o.OptionID ASC";

    $result = mysqli_query($conn, $sql);
    $polls = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $id = $row['PollID'];

        if (!isset($polls[$id])) {
            $polls[$id] = array(
                'PollID'          => $id,
                'CreatorUsername' => $row['CreatorUsername'],
                'Question'        => $row['Question'],
                'Description'     => $row['Description'],
                'Category'        => $row['Category'],
                'CreatedAt'       => $row['CreatedAt'],
                'Options'         => array()
            );
        }

        if (!empty($row['OptionID'])) {
    $polls[$id]['Options'][] = array(
        'OptionID'   => (int)$row['OptionID'],
        'OptionText' => $row['OptionText']
        );
    }
}

    // Reindex numerically for easier loops in the view
    return array_values($polls);
}
// Verify the option belongs to the poll (basic integrity check)
function option_belongs_to_poll($optionId, $pollId) {
    global $conn;
    $optionId = (int)$optionId;
    $pollId   = (int)$pollId;
    $sql = "SELECT 1 FROM POLL_OPTIONS WHERE OptionID=$optionId AND PollID=$pollId";
    $res = mysqli_query($conn, $sql);
    return mysqli_num_rows($res) > 0;
}

// Has this user already voted on this poll?
function has_user_voted($pollId, $username) {
    global $conn;
    $pollId   = (int)$pollId;
    $username = mysqli_real_escape_string($conn, $username);
    $sql = "SELECT 1 FROM POLL_VOTES WHERE PollID=$pollId AND Username='$username' LIMIT 1";
    $res = mysqli_query($conn, $sql);
    return mysqli_num_rows($res) > 0;
}

// Cast a vote; returns array [ok=>bool, message=>string]
function cast_vote($pollId, $optionId, $username) {
    global $conn;

    $pollId   = (int)$pollId;
    $optionId = (int)$optionId;
    $username = mysqli_real_escape_string($conn, $username);

    if (!option_belongs_to_poll($optionId, $pollId)) {
        return ['ok'=>false, 'message'=>'Invalid option for this poll.'];
    }
    if (has_user_voted($pollId, $username)) {
        return ['ok'=>false, 'message'=>'You have already voted in this poll.'];
    }

    $sql = "INSERT INTO POLL_VOTES (PollID, OptionID, Username) VALUES ($pollId, $optionId, '$username')";
    if (!mysqli_query($conn, $sql)) {
        // If uniqueness is violated or any failure:
        return ['ok'=>false, 'message'=>'Vote failed (database error).'];
    }
    return ['ok'=>true, 'message'=>'Vote recorded.'];
}

// Get results for a poll: option text + counts + percents
function get_poll_results($pollId) {
    global $conn;
    $pollId = (int)$pollId;

    // Counts per option
    $sql = "SELECT o.OptionID, o.OptionText, COUNT(v.VoteID) AS Votes
            FROM POLL_OPTIONS o
            LEFT JOIN POLL_VOTES v ON v.OptionID = o.OptionID
            WHERE o.PollID = $pollId
            GROUP BY o.OptionID, o.OptionText
            ORDER BY o.OptionID ASC";
    $res = mysqli_query($conn, $sql);

    $rows = [];
    $total = 0;
    while ($r = mysqli_fetch_assoc($res)) {
        $r['Votes'] = (int)$r['Votes'];
        $rows[] = $r;
        $total += $r['Votes'];
    }
    // compute percentages
    foreach ($rows as &$r) {
        $r['Percent'] = ($total > 0) ? round(($r['Votes'] / $total) * 100) : 0;
    }
    return ['total'=>$total, 'rows'=>$rows];
}

// All polls + computed flags/results to render vote-or-results per card
function get_all_polls_with_votes($username) {
    $all = get_all_polls();  // existing function (poll + options)
    foreach ($all as &$p) {
        $p['HasVoted'] = has_user_voted($p['PollID'], $username);
        if ($p['HasVoted']) {
            $p['Results'] = get_poll_results($p['PollID']);
        }
    }
    return $all;
}

function get_polls_by_user_with_votes($username) {
    $mine = get_polls_by_user($username); // existing function
    foreach ($mine as &$p) {
        $p['HasVoted'] = has_user_voted($p['PollID'], $username);
        if ($p['HasVoted']) {
            $p['Results'] = get_poll_results($p['PollID']);
        }
    }
    return $mine;
}
// Get polls with optional filters and vote state.
// $onlyMine = true => only polls created by this user.
function get_filtered_polls($username, $search, $category, $mine) {
    global $conn;

    $username = mysqli_real_escape_string($conn, $username);
    $search   = mysqli_real_escape_string($conn, trim($search));
    $category = mysqli_real_escape_string($conn, trim($category));

    $sql = "SELECT p.PollID, p.CreatorUsername, p.Question, p.Description,
                   p.Category, p.CreatedAt,
                   o.OptionID, o.OptionText
            FROM POLLS p
            LEFT JOIN POLL_OPTIONS o ON p.PollID = o.PollID
            WHERE 1=1";

    if ($mine) {
        $sql .= " AND p.CreatorUsername = '$username'";
    }

    if ($search !== '') {
        $sql .= " AND (p.Question LIKE '%$search%' OR p.Description LIKE '%$search%')";
    }

    if ($category !== '') {
        $sql .= " AND p.Category LIKE '%$category%'";
    }

    $sql .= " ORDER BY p.CreatedAt DESC, o.OptionID ASC";

    $result = mysqli_query($conn, $sql);
    $polls = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $id = $row['PollID'];

        if (!isset($polls[$id])) {
            $polls[$id] = array(
                'PollID'          => (int)$row['PollID'],
                'CreatorUsername' => $row['CreatorUsername'],
                'Question'        => $row['Question'],
                'Description'     => $row['Description'],
                'Category'        => $row['Category'],
                'CreatedAt'       => $row['CreatedAt'],
                'Options'         => array()
            );
        }

        if (!empty($row['OptionID'])) {
            $polls[$id]['Options'][] = array(
                'OptionID'   => (int)$row['OptionID'],
                'OptionText' => $row['OptionText']
            );
        }
    }

    $polls = array_values($polls); // reindex numerically
    foreach ($polls as &$p) {
        $p['HasVoted'] = has_user_voted($p['PollID'], $username);
        if ($p['HasVoted']) {
            $p['Results'] = get_poll_results($p['PollID']);
        }
    }
    return $polls;
}

// Get profile info for a user
function get_user_profile($username) {
    global $conn;

    $username = mysqli_real_escape_string($conn, $username);
    $sql = "SELECT Username, Email, DisplayName, Bio
            FROM USERS
            WHERE Username = '$username'
            LIMIT 1";
    $res = mysqli_query($conn, $sql);
    if (!$res || mysqli_num_rows($res) === 0) {
        return null;
    }
    $row = mysqli_fetch_assoc($res);

    // If DisplayName is NULL or empty, fall back to Username
    if (empty($row['DisplayName'])) {
        $row['DisplayName'] = $row['Username'];
    }
    if ($row['Bio'] === null) {
        $row['Bio'] = '';
    }
    return $row;
}

// Update email, display name, and bio for a user
function update_user_profile($username, $email, $displayName, $bio) {
    global $conn;

    $username    = mysqli_real_escape_string($conn, $username);
    $email       = mysqli_real_escape_string($conn, trim($email));
    $displayName = mysqli_real_escape_string($conn, trim($displayName));
    $bio         = mysqli_real_escape_string($conn, trim($bio));

    // Simple rule: email is required, displayName can fallback to username
    if ($email === '') {
        return ['ok' => false, 'message' => 'Email cannot be empty.'];
    }
    if ($displayName === '') {
        $displayName = $username;
    }

    $sql = "UPDATE USERS
            SET Email = '$email', DisplayName = '$displayName', Bio= '$bio'
            WHERE Username  = '$username'";
    if (!mysqli_query($conn, $sql)) {
        return ['ok' => false, 'message' => 'Profile update failed (database error).'];
    }
    return ['ok' => true, 'message' => 'Profile updated successfully.'];
}

// Unsubscribe/delete user:
// 1) Mark their polls as created by a deleted user
// 2) Delete their votes
// 3) Delete their user record
function unsubscribe_user($username) {
    global $conn;

    $username = mysqli_real_escape_string($conn, $username);

    // Mark polls so we know they were created by a deleted account
    $sql1 = "UPDATE POLLS
             SET CreatorUsername = CONCAT('[deleted] ', CreatorUsername)
             WHERE CreatorUsername = '$username'";
    mysqli_query($conn, $sql1);

    // Remove their votes
    $sql2 = "DELETE FROM POLL_VOTES WHERE Username = '$username'";
    mysqli_query($conn, $sql2);

    // Finally remove user record
    $sql3 = "DELETE FROM USERS WHERE Username = '$username'";
    if (!mysqli_query($conn, $sql3)) {
        return ['ok' => false, 'message' => 'Unsubscribe failed (database error).'];
    }
    return ['ok' => true, 'message' => 'Your account has been deleted.'];
}
