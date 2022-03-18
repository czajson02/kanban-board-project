<!-- [IMPORTANT] Database name = "kanban" -->

<?php
// Getting table for currently logged user
session_start();
$table = $_SESSION['user'];

// Establishing cennection to MySQL server
function get_connection(){
    $dsn = "mysql:host=localhost;dbname=kanban";
    $user = "root";
    $passwd = "";
    $conn = new PDO($dsn, $user, $passwd);
    return $conn;
}

// Saving task function
function save_task($type, $task, $id, $d){
    $conn = get_connection();
    if($id){
        // Updating existing task
        $table = $_SESSION['user'];
        $sql = "UPDATE $table SET `task`=? WHERE id=?";
        $query = $conn->prepare($sql); 
        $query->execute([$task, $id]); 
        return $id;
    }
    else{
        // Creating new task
        $table = $_SESSION['user'];
        $sql = "INSERT INTO $table(`task`,`type`,`dif`) VALUES (?,?,?)"; 
        $query = $conn->prepare($sql); 
        $query->execute([$task,$type,$d]); 
        return $conn->lastInsertId();
    }
}

// Moving task function
function move_task($id, $position){
    $conn = get_connection();
    $table = $_SESSION['user'];
    $sql = "UPDATE $table SET `type`=? WHERE id=?";
    $query = $conn->prepare($sql); 
    $query->execute([$position,$id]); 
}

// Getting tasks form SQL Database for specific level
function get_tasks($type){
    $results = [];
    try{
        $conn = get_connection();
        $table = $_SESSION['user'];
        $query = $conn->prepare("SELECT * from $table WHERE type=? order by id desc");
        $query->execute([$type]);
        $results = $query->fetchAll();
    }
    catch (Exception $e){

    }
    return $results;
}

// Getting task by its id
function get_task($id){
    $results = [];
    try{
        $conn = get_connection();
        $table = $_SESSION['user'];
        $query = $conn->prepare("SELECT * from $table WHERE id=?");
        $query->execute([$id]);
        $results = $query->fetchAll();
        $results = $results[0];
    }
    catch (Exception $e){
    }
    return $results;
}

// Displaying task by its level and adding action buttons for each
function show_tile($taskObject, $type=""){
    $baseUrl = $_SERVER["PHP_SELF"]."?shift&id=".$taskObject["id"]."&type=";
    $editUrl = $_SERVER["PHP_SELF"] . "?edit&id=".$taskObject["id"]."&type=". $type;
    $deleteUrl = $_SERVER["PHP_SELF"] . "?delete&id=".$taskObject["id"];
    $o = '<span class="board" id="'.$taskObject["dif"].'">'.$taskObject["task"].'
        <hr>
        <span>
            <a href="'.$baseUrl.'backlog">1</a> 
            <a href="'.$baseUrl.'pending">2</a> 
            <a href="'.$baseUrl.'progress">3</a> 
            <a href="'.$baseUrl.'completed">4</a> 
            <a href="'.$editUrl.'">✏️</a>  
            <a href="'.$deleteUrl.'">❌</a>
        </span>
        </span>';
    return $o;
}

// Live selecting specific task
function get_active_value($type, $content){
    $currentType = isset($_GET['type']) ? $_GET['type']:  null;
        if($currentType == $type){
            return $content;
        }
    return "";
}

$activeId = "";
$activeTask = "";

// Moving specific task between tables event -> move_task()
if(isset($_GET['shift'])){
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    $type = isset($_GET['type']) ? $_GET['type'] : null;
    if($id){
        move_task($id, $type);
        header("Location: ". $_SERVER['PHP_SELF']);
        exit();
    }
    else{
        header("Location: ". $_SERVER['PHP_SELF']);
    }
}

// Editing specific task title -> get_task()
if(isset($_GET['edit'])){
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    $d = isset($_GET['dif']) ? $_GET['dif'] : null;
    $activeId = $id;
    $type = isset($_GET['type']) ? $_GET['type'] : null;
    if($id){
        $taskObject = get_task($id);
        $activeTask = $taskObject["task"];
    }
}

// Deleting specific task -> SQL Query
if(isset($_GET['delete'])){
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    if($id){
        try{
            $conn = get_connection();
            $table = $_SESSION['user'];
            $query = $conn->prepare("DELETE from $table WHERE id=?");
            $query->execute([$id]);
            header("Location: ". $_SERVER['PHP_SELF']);
        }
        catch (Exception $e){
        }
    }
}

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $backlog = "";
    $pending = "";
    $progress = "";
    $completed = "";
    $taskId = isset($_POST['task']) ? $_POST['task'] : null;

    //In "To Do table" adding task -> save_task()
    if(isset($_POST['save-backlog'])){
        $backlog = isset($_POST['backlog']) ? $_POST['backlog'] : null;
        $backlogd = isset($_POST['backlogd']) ? $_POST['backlogd'] : null;
        save_task('backlog', $backlog, $activeId, $backlogd);
    }
    //In "Pending table" adding task -> save_task()
    else if(isset($_POST['save-pending'])){
        $pending = isset($_POST['pending']) ? $_POST['pending'] : null;
        $pendingd = isset($_POST['pendingd']) ? $_POST['pendingd'] : null;
        save_task('pending', $pending, $activeId, $pendingd);
    }
    //In "In Progress table" adding task -> save_task()
    else if(isset($_POST['save-progress'])){
        $progress = isset($_POST['progress']) ? $_POST['progress'] : null;
        $progressd = isset($_POST['progressd']) ? $_POST['progressd'] : null;
        save_task('progress', $progress, $activeId, $progressd);
    }
    //In "Completed table" adding task -> save_task()
    else if(isset($_POST['save-completed'])){
        $completed = isset($_POST['completed']) ? $_POST['completed'] : null;
        $completedd = isset($_POST['completedd']) ? $_POST['completedd'] : null;
        save_task('completed', $completed, $activeId, $completedd);
    }
    if($activeId){
        // Refreshing page after any change on task
        header("Location: ". $_SERVER['PHP_SELF']);
    }
}

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Kanban board</title>
    <script src="date.js"></script>
    <script src="darkmode.js" defer></script>
</head>
<body>
    <header>
        <section class="left">
            <p><img src="pics/calendar.png" alt="" id="cal"></p><p id="data1"> </p>
            <!--Loading live date script-->
            <script type="text/javascript">window.onload = date_time('data1');</script>
        </section>
        <section class="mid">
            <div class="big">
                <p id="big1">
                    <span id="l1">K</span>
                    <span id="l2">A</span>
                    <span id="l3">N</span>
                    <span id="l4">B</span>
                    <span id="l5">A</span>
                    <span id="l6">N</span>
                </p>
            </div>
        </section>
        <section class="right">
            <!-- Checking if any user is logged -->
            <?php
            if(isset($_SESSION['user'])){
                echo "<p class='user'><img src='pics/user.png' id='userimg'> <p class='uid' id='uid'>".$_SESSION['user']."</p></p>";
            }
            else{
                //if user is not logged in -> redirect to login page
                header("Location: ./login.php");
            }

            ?>
            <!-- Logout button execute function-->
            <button id="wyloguj" onclick="logout()"><img src="pics/logout.png" alt="" id="logout1"></button>
        </section>
    </header>
    <main>
            <form method="post" class="mid1">
                <!-- Hidden input with currently active task -->
                <input type="hidden" value="<?php echo $activeId;?>" name="task"/>
            <div class="board-column">
                <h3>1. &nbsp;To Do</h3>
                <div class="board-form">
                    <!-- "To Do" table form -->
                    <input value="<?php echo get_active_value('backlog', $activeTask);?>" type="text" name="backlog" style="height: 30px; width: 60%" autocomplete="off"/>
                    <select name="backlogd" style="height: 35px; width: 20%" id="">
                        <option value="e">Easy</option>
                        <option value="m">Medium</option>
                        <option value="h">Hard</option>
                    </select>
                    <button type="submit" name="save-backlog">+</button>
                  </div>
                  <div class="board-items">
                    <!-- Printing task for this table -->
                    <?php foreach (get_tasks('backlog') as $task):?>
                    <?php echo show_tile($task,'backlog');?>
                    <?php endforeach;?>
                  </div>
            </div>
            <div class="board-column">
                <h3>2. &nbsp;Pending</h3>
                <div class="board-form">
                    <!-- "Pending" table form -->
                    <input value="<?php echo get_active_value('pending', $activeTask);?>" type="text" name="pending" style="height: 30px; width: 60%" autocomplete="off"/>
                    <select name="pendingd" style="height: 35px; width: 20%" id="">
                        <option value="e">Easy</option>
                        <option value="m">Medium</option>
                        <option value="h">Hard</option>
                    </select>
                    <button type="submit" name="save-pending">+</button>
                  </div>
                  <div class="board-items">
                    <!-- Printing task for this table -->
                    <?php foreach (get_tasks('pending') as $task):?>
                    <?php echo show_tile($task,'pending');?>
                    <?php endforeach;?>
                  </div>
            </div>
            <div class="board-column">
                <h3>3. &nbsp;In Progress</h3>
                <div class="board-form">
                    <!-- "In Progress" table form -->
                    <input value="<?php echo get_active_value('progress', $activeTask);?>" type="text" name="progress" style="height: 30px; width: 60%" autocomplete="off"/>
                    <select name="progressd" style="height: 35px; width: 20%" id="">
                        <option value="e">Easy</option>
                        <option value="m">Medium</option>
                        <option value="h">Hard</option>
                    </select>
                    <button type="submit" name="save-progress">+</button>
                  </div>
                  <div class="board-items">
                    <!-- Printing task for this table -->
                    <?php foreach (get_tasks('progress') as $task):?>
                    <?php echo show_tile($task,'progress');?>
                    <?php endforeach;?>
                  </div>
            </div>
            <div class="board-column">
                <h3>4. &nbsp;Completed</h3>
                <div class="board-form">
                    <!-- "Completed" table form -->
                    <input value="<?php echo get_active_value('completed', $activeTask);?>" type="text" name="completed" style="height: 30px; width: 60%" autocomplete="off"/>
                    <select name="completedd" style="height: 35px; width: 20%" id="">
                        <option value="e">Easy</option>
                        <option value="m">Medium</option>
                        <option value="h">Hard</option>
                    </select>
                    <button type="submit" name="save-completed">+</button>
                  </div>
                  <div class="board-items">
                    <!-- Printing task for this table -->
                    <?php foreach (get_tasks('completed') as $task):?>
                    <?php echo show_tile($task,'completed');?>
                    <?php endforeach;?>
                  </div>
            </div>
        </form>
    </main>
    <footer>
    <section class="mid">
        <!-- Dark/Light mode button switch elements -->
        <div class="darkmode">
            <label>
                <input type="checkbox" name="" id="dark">
                <span class="check"></span>
            </label>
        </div>
        <div id="napis1"></div>
    </section>
    </footer>
</body>
</html>