<?php
if (!isset($username)) {
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
    } else {
        $username = 'Guest';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <link
  href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #create-poll form input[type="text"],
        #create-poll form textarea {
            width:100%;
            box-sizing:border-box;
            margin-bottom:5px;
        }

        .poll-card {
            border:1px solid #ccc;
            padding:8px;
            margin-bottom:8px;
            background-color:#fdfdfd;
        }

        .poll-options {
            margin-top:4px;
            padding-left:20px;
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
        #nav-buttons {
            position:absolute;
            width:80px;
            top:15px; left:calc(50% - 40px);
        }
        #nav-buttons > button {
            display:inline-block;
            width:80px;
        }
        .modal {
            display:none;
            position:absolute;
            width:400px; height:300px;
            top:calc(50% - 150px); left:calc(50% - 200px);
            border:1px solid black;
            background-color:White;
            z-index:999;
        }#modal-signout #cancel-signout { 
            position:absolute; 
            left:5px; bottom:5px; }
        #modal-signout #submit-signout { 
            position:absolute; 
            right:5px; bottom:5px; }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 bg-warning text-center py-3 mb-2">
                <h1 class="m-0">Campus PollLive</h1>
                <p class="m-0">Logged in as <?php echo htmlspecialchars($username); ?></p>
            </div>
        </div>
    <div class="row">
            <div class="col-2 bg-light border-end vh-100">
                <div class="nav flex-column nav-pills p-3 gap-2">
                    <button class="btn btn-outline-primary" id="button-home">Home</button>
                    <button class="btn btn-outline-primary" id="button-mypolls">My Polls</button>
                    <button class="btn btn-outline-primary" id="button-create">Create Poll</button>
                    <button class="btn btn-outline-primary" id="button-profile">Profile</button>
                    <button class="btn btn-danger mt-3" id="button-signout">Sign Out</button>
                </div>
            </div>

<div class="col-10 px-4" id='layout-right'>
    <h2 id="section-title" class="mb-3">Home</h2>
    <p>This page currently supports a simple “Create Poll” form and a basic poll feed.</p>
    <div id="section-home" class="content-section">
        <div id="create-poll" class="card p-3 mb-4 shadow-sm">
            <h3>Create a New Poll</h3>
            <?php if (!empty($info_msg)): ?>
                <div class="alert alert-success py-2"><?php echo $info_msg; ?></div>
            <?php endif; ?>
            <form method="POST" action="pollLive_controller.php" class="mt-3">
                <input type="hidden" name="page" value="MainPage">
                <input type="hidden" name="command" value="CreatePoll">
                <div class="mb-3">
                    <label>Question:</label><br>
                    <input type="text" name="question" required><br><br>
                </div>
                <div class="mb-3">
                    <label>Description (optional):</label><br>
                    <textarea name="description" rows="3"></textarea><br><br>
                </div>
                <div class="mb-3">
                    <label>Category (optional):</label><br>
                    <input type="text" name="category" placeholder="e.g., Campus, Courses"><br><br>
                </div>

                <label class="form-label">Options (at least two)</label>

                <div class="row g-2">
                    <div class="col-6">
                        <input type="text" name="option1" class="form-control" placeholder="Option 1" required>
                    </div>
                    <div class="col-6">
                        <input type="text" name="option2" class="form-control" placeholder="Option 2" required>
                    </div>
                    <div class="col-6">
                        <input type="text" name="option3" class="form-control" placeholder="Option 3 (optional)">
                    </div>
                    <div class="col-6">
                        <input type="text" name="option4" class="form-control" placeholder="Option 4 (optional)">
                    </div>
                </div>
                <button class="btn btn-success mt-3">Create Poll</button>
            </form>
        </div>
    <hr>
    <!--search/filter bar -->
    <div id="search-bar" class="card p-3 mb-4 shadow-sm">
        <h3 class="mb-3">Search & Filter</h3>
            <div class="row g-3">
            <div class="col-6">
                <input type="text" id="search-text" class="form-control"
                    placeholder="Keyword (search in question or description)">
            </div>
            <div class="col-4">
                <input type="text" id="search-category" class="form-control"
                    placeholder="Category">
            </div>
            <div class="col-2 d-flex">
                <button type="button" id="button-search" class="btn btn-primary flex-fill me-2">Search</button>
                <button type="button" id="button-clear" class="btn btn-secondary flex-fill">Clear</button>
            </div>
        </div>
    </div>
    <hr>
    <div id="poll-feed">
        <h3>All Polls</h3>
        <?php if (empty($polls)) { ?>
            <p>No polls have been created yet. Try adding one using the form above.</p>
        <?php } else { ?>
            <?php foreach ($polls as $poll) { ?>
                <div class="poll-card">
                    <p class="poll-meta">
                        Created by <?php echo ($poll['CreatorUsername']); ?>
                        on <?php echo ($poll['CreatedAt']); ?>
                    </p>
                    <h4><?php echo ($poll['Question']); ?></h4>

                    <?php if (!empty($poll['Description'])) { ?>
                        <p><?php echo nl2br(($poll['Description'])); ?></p>
                    <?php } ?>

                    <?php if (!empty($poll['Category'])) { ?>
                        <p><strong>Category:</strong> <?php echo ($poll['Category']); ?></p>
                    <?php } ?>

                    <?php if ($poll['HasVoted']): ?>
                        <!-- Show results -->
                        <p><strong>Total votes:</strong> <?php echo $poll['Results']['total']; ?></p>
                        <ul class="poll-options">
                            <?php foreach ($poll['Results']['rows'] as $row): ?>
                                <li>
                                    <?php echo htmlspecialchars($row['OptionText']); ?>
                                    <?php echo $row['Votes']; ?> (<?php echo $row['Percent']; ?>%)
                                    <div style="width:100%; background:#eee;">
                                    <div style="width:<?php echo $row['Percent']; ?>%; background:#87cefa; height:6px;"></div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <!-- Show vote form -->
                        <form method="POST" action="pollLive_controller.php" class="vote-form" style="margin-top: 6px;">
                            <input type="hidden" name="page" value="MainPage">
                            <input type="hidden" name="command" value="Vote">
                            <input type="hidden" name="poll_id" value="<?php echo $poll['PollID']; ?>">
                            <?php if (!empty($poll['Options'])): ?> 
                                <?php foreach ($poll['Options'] as $opt): ?>
                                    <label style="display:block;">
                                        <input type="radio" name="option_id" value="<?php echo $opt['OptionID']; ?>" required>
                                        <?php echo htmlspecialchars($opt['OptionText']); ?>
                                    </label>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <button type="submit">Vote</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
    </div>
<!-- MY POLLS SECTION -->
        <div id="section-mypolls" class="content-section" style="display:none;">
            <h3>My Polls</h3>
                <div id="mypolls-feed">
                    <?php if (empty($my_polls)) { ?>
                    <p>You have not created any polls yet.</p>
                    <?php } else { ?>
                    <?php foreach ($my_polls as $poll) { ?>
                        <div class="poll-card">
                            <p>
                                Created on <?php echo htmlspecialchars($poll['CreatedAt']); ?>
                            </p>
                            <h4><?php echo ($poll['Question']); ?></h4>

                            <?php if (!empty($poll['Description'])) { ?>
                                <p><?php echo nl2br(htmlspecialchars($poll['Description'])); ?></p>
                            <?php } ?>

                            <?php if (!empty($poll['Category'])) { ?>
                                <p><strong>Category:</strong> <?php echo htmlspecialchars($poll['Category']); ?></p>
                            <?php } ?>

                            <?php if ($poll['HasVoted']): ?>
                                <!-- Show results -->
                                <p><strong>Total votes:</strong> <?php echo $poll['Results']['total']; ?></p>
                                <ul class="poll-options">
                                    <?php foreach ($poll['Results']['rows'] as $row): ?>
                                        <li>
                                            <?php echo htmlspecialchars($row['OptionText']); ?>
                                            — <?php echo $row['Votes']; ?> (<?php echo $row['Percent']; ?>%)
                                            <div style="width:100%; background:#eee;">
                                                <div style="width:<?php echo $row['Percent']; ?>%; background:#87cefa; height:6px;"></div>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <!-- Show vote form -->
                                <form method="POST" action="pollLive_controller.php">
                                    <input type="hidden" name="page" value="MainPage">
                                    <input type="hidden" name="command" value="Vote">
                                    <input type="hidden" name="poll_id" value="<?php echo $poll['PollID']; ?>">

                                    <?php foreach ($poll['Options'] as $opt): ?>
                                        <label style="display:block;">
                                            <input type="radio" name="option_id" value="<?php echo $opt['OptionID']; ?>" required>
                                            <?php echo htmlspecialchars($opt['OptionText']); ?>
                                        </label>
                                    <?php endforeach; ?>

                                    <button type="submit">Vote</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
        <!-- PROFILE SECTION -->
    <div id="section-profile" style="display:none;" class="content-section">
        <div class="card p-3 shadow-sm mb-4">
            <h3>Profile</h3>

            <?php if (!empty($profile_msg)): ?>
                <div class="alert alert-info"><?php echo $profile_msg; ?></div>
            <?php endif; ?>
            <?php if ($profile): ?>
            <form method="POST" action="pollLive_controller.php">
                <input type="hidden" name="page" value="MainPage">
                <input type="hidden" name="command" value="UpdateProfile">

                <div class="mb-3">
                    <label class="form-label">Username (read-only)</label>
                    <input type="text" class="form-control" readonly value="<?php echo htmlspecialchars($profile['Username']); ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="text" name="email" class="form-control" value="<?php echo htmlspecialchars($profile['Email']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Display Name</label>
                    <input type="text" name="display_name" class="form-control" value="<?php echo htmlspecialchars($profile['DisplayName']); ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Bio</label>
                    <textarea name="bio" class="form-control" rows="4"><?php echo htmlspecialchars($profile['Bio']); ?></textarea>
                </div>

                <button class="btn btn-success">Save Profile</button>
            </form>

            <hr>

            <h4>Unsubscribe / Delete Account</h4>
            <p>
                If you unsubscribe, your account will be deleted.
                Your existing polls will remain in the system, but they will be marked as created by a deleted user.
                Your votes and login information will be removed.
            </p>

            <form method="POST" action="pollLive_controller.php"
                onsubmit="return confirm('Are you sure you want to delete your account? This cannot be undone.');">
                <input type="hidden" name="page" value="MainPage">
                <input type="hidden" name="command" value="Unsubscribe">
                <button class="btn btn-danger">Delete Account</button>
            </form>
            <?php endif; ?>
        </div>
    </div>
    <div id='blanket'></div>
    <div class='modal' id='modal-signout'>
        <h2 style='text-align:center'>Sign Out</h2>
        <hr>
        <br>
        <form method="POST" action="pollLive_controller.php">
        <input type="hidden" name="page" value="MainPage">
        <input type="hidden" name="command" value="SignOut">
        <input id='cancel-signout' type='button' value='Cancel'>
        <input id='submit-signout' type='submit'>
        </form>
     </div>
</body>
<script>
    function showSection(name) {
        let sections = ['home', 'mypolls', 'profile'];
        sections.forEach(function(s) {
            let div = document.getElementById('section-' + s);
            if (!div) 
                return;
            div.style.display = (s === name) ? 'block' : 'none';
        });

        let title = 'Home';
        if (name === 'mypolls') 
            title = 'My Polls';
        else if (name === 'profile') 
            title = 'Profile';

        let h2 = document.getElementById('section-title');
        if (h2) 
            h2.textContent = title;
    }
    document.getElementById('button-home').addEventListener('click', function() {
        showSection('home');
        loadPollsAjax();
    });
    document.getElementById('button-mypolls').addEventListener('click', function() {
        showSection('mypolls');
        loadPollsAjax();
    });
    document.getElementById('button-create').addEventListener('click', function() {
        showSection('home');
        let q = document.querySelector('#create-poll input[name="question"]');
        if (q) 
            q.focus();
    });
    document.getElementById('button-profile').addEventListener('click', function() {
        showSection('profile');
    });
    let btnSearch = document.getElementById('button-search');
    btnSearch.addEventListener('click', function() {
            showSection('home');
            loadPollsAjax();
        });

    var btnClear = document.getElementById('button-clear');
    if (btnClear) {
        btnClear.onclick = function() {
            var searchInput   = document.getElementById('search-text');
            var categoryInput = document.getElementById('search-category');
            searchInput.value = '';
            categoryInput.value = '';
            showSection('home');
            loadPollsAjax();
        };
    }
    function loadPollsAjax() {
    let searchinput = document.getElementById('search-text');
    let categoryinput = document.getElementById('search-category');
        let search = "";
        if (searchinput) {
            search = encodeURIComponent(searchinput.value);
        }

        let category = "";
        if (categoryinput) {
            category = encodeURIComponent(categoryinput.value);
        }

        let url = 'pollLive_controller.php?ajax=load_polls' +
                  '&search=' + search +
                  '&category=' + category;
        ajaxGet(url, function(data) {
            if (!data.ok) 
                {
                    console.log('Error loading polls: ' + data.message);
                    return;
                }
            updatePollLists(data);
        });
    }
    function updatePollLists(data) {
        let homeDiv = document.getElementById('poll-feed');
        let myDiv   = document.getElementById('mypolls-feed');

        if (!homeDiv || !myDiv) 
            return;

        // Home polls
        if (!data.polls || data.polls.length === 0) {
            homeDiv.innerHTML = "<p>No polls found. Try creating one.</p>";
        } 
        else 
            {
            let htmlHome = "";
            for (let i = 0; i < data.polls.length; i++) 
                {
                    htmlHome += buildPoll(data.polls[i]);
                }
            homeDiv.innerHTML = htmlHome;
        }

        // My polls
        if (!data.my_polls || data.my_polls.length === 0) {
            myDiv.innerHTML = "<p>You have not created any polls yet.</p>";
        } else {
            let htmlMy = "";
            for (let j = 0; j < data.my_polls.length; j++) {
                htmlMy += buildPoll(data.my_polls[j]);
            }
            myDiv.innerHTML = htmlMy;
        }
        voteHandling();
    }
    function voteHandling() {
        let forms = document.getElementsByClassName('vote-form');
        for (let i = 0; i < forms.length; i++) {
            forms[i].onsubmit = function(e) {
                e.preventDefault();

                let pollIdInput = this.querySelector('input[name="poll_id"]');
                let optionInput = this.querySelector('input[name="option_id"]:checked');

                if (!pollIdInput || !optionInput) {
                    alert('Please select an option before voting.');
                    return false;
                }

                let params = 'ajax=vote' +
                            '&poll_id=' + encodeURIComponent(pollIdInput.value) +
                            '&option_id=' + encodeURIComponent(optionInput.value);

                ajaxPost('pollLive_controller.php', params, function(data) {
                    if (!data.ok) {
                        alert(data.message || 'Vote failed.');
                        return;
                    }
                    // Re-render lists with updated JSON
                    updatePollLists(data);

                    if (data.message) {
                        alert(data.message);
                    }
                });

                return false;
            };
        }
    }
    
    function ajaxGet(url, callback) {
    let xhr = new XMLHttpRequest();
    xhr.open('GET', url, true);

    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                callback(JSON.parse(xhr.responseText));
            }
        }
    };

    xhr.send();
}
    function ajaxPost(url, params, callback) {
        let xhr = new XMLHttpRequest();
        xhr.open('POST', url, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                    let data = JSON.parse(xhr.responseText);
                    callback(data);
                } else {
                    console.log('AJAX POST error: ' + xhr.status);
                }
            };

        xhr.send(params);
    }
    function buildPoll(poll) {
        let str = "";
        str += "<div class='card mb-3 shadow-sm'><div class='card-body'>";
        str += "<p class='text-muted small'>Created by " + poll.CreatorUsername +
                " on " + poll.CreatedAt + "</p>";
        str += "<h5 class='card-title'>" + poll.Question + "</h5>";

        if (poll.Description) {
            str += "<p class= 'card-text'>" + poll.Description + "</p>";
        }
        if (poll.Category) {
            str += "<p><span class='badge bg-info text-dark'>Category: " + poll.Category + "</span></p>";
        }

        if (poll.HasVoted && poll.Results) {
            // Show results
            str += "<p><b>Total Votes:</b> " + poll.Results.total + "</p>";
            str += "<ul class='list-group list-group-flush'>";
            for (let i = 0; i < poll.Results.rows.length; i++) {
                let r = poll.Results.rows[i];
                str += "<div class='d-flex justify-content-between align-items-center'>";
                str += "<span>" + r.OptionText + "</span>";
                str += "<span>" + r.Votes + " vote(s) (" + r.Percent + "%)</span>";
                str += "</div>";
                str += "<div class='progress mt-1' style='height: 6px;'>";
                str += "<div class='progress-bar' role='progressbar' ";
                str += "style='width: " + r.Percent + "%;' ";
                str += "aria-valuenow='" + r.Percent + "' aria-valuemin='0' aria-valuemax='100'>";
                str += "</div>";
                str += "</div>";

                str += "</li>";
            }
            str += "</ul>";
        } else {
            // Vote form
            str += "<form method='POST' action='pollLive_controller.php' class='vote-form mt-2'>";
            str += "<input type='hidden' name='page' value='MainPage'>";
            str += "<input type='hidden' name='command' value='Vote'>";
            str += "<input type='hidden' name='ajax' value='vote'>";
            str += "<input type='hidden' name='poll_id' value='" + poll.PollID + "'>";

            if (poll.Options && poll.Options.length > 0) {
                for (let j = 0; j < poll.Options.length; j++) {
                    let opt = poll.Options[j];
                    str += "<label style='display:block; margin:4px 0;'>";
                    str += "<input type='radio' name='option_id' value='" + opt.OptionID + "'>";
                    str += " " + opt.OptionText;
                    str += "</label>";
                }
            }

            str += "<button class='btn btn-primary btn-sm mt-2'>Vote</button>";
            str += "</form>";
        }

        str += "</div></div>";
        return str;
    }
    
// Sign-out modal controls
    document.getElementById('button-signout').addEventListener('click', function() {
        document.getElementById('blanket').style.display = 'block';
        document.getElementById('modal-signout').style.display = 'block';
    });
    document.getElementById('cancel-signout').addEventListener('click', function() {
        document.getElementById('blanket').style.display = 'none';
        document.getElementById('modal-signout').style.display = 'none';
    });
    document.getElementById('blanket').addEventListener('click', function() {
        document.getElementById('blanket').style.display = 'none';
        document.getElementById('modal-signout').style.display = 'none';
    });

    
    showSection('home');
    loadPollsAjax();

    setInterval(function() {
    loadPollsAjax();
    }, 5000);

</script>
</html>