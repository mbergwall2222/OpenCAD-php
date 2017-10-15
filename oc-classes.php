<?php
/**
Open source CAD system for RolePlaying Communities.
Copyright (C) 2017 Shane Gill

This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

This program comes with ABSOLUTELY NO WARRANTY; Use at your own risk.
**/
/*
    This file handles all actions for admin.php script
*/
include("./oc-config.php")
//////////////////////////////////////////////////////
//                  API                             //
//////////////////////////////////////////////////////

class pubTools{
if (isset($_GET['a'])){
    getActiveCalls();
}
if (isset($_GET['getCalls'])){
    getActiveCalls();
}
if (isset($_GET['getCallDetails'])){
    getCallDetails();
}
if (isset($_GET['getAvailableUnits'])){
    getAvailableUnits();
}
if (isset($_GET['getUnAvailableUnits'])){
    getUnAvailableUnits();
}
if (isset($_POST['changeStatus'])){
    changeStatus();
}
if (isset($_GET['getActiveUnits']))
{
    getActiveUnits();
}
if (isset($_GET['getActiveUnitsModal']))
{
    getActiveUnitsModal();
}
if (isset($_POST['logoutUser']))
{
    logoutUser();
}
if (isset($_POST['setTone']))
{
    setTone();
}
if (isset($_GET['checkTones']))
{
    checkTones();
}
if (isset($_GET['getDispatchers']))
{
    getDispatchers();
}
if (isset($_POST['new_911']))
{
    create911Call();
}
if (isset($_POST['quickStatus']))
{
    quickStatus();
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function quickStatus()
{
    $event = $_POST['event'];
    $callId = $_POST['callId'];
    session_start();
    $callsign = $_SESSION['callsign'];


    //var_dump($_SESSION);

    switch($event)
    {
        case "enroute":
            $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

            if (!$link) {
                die('Could not connect: ' .mysql_error());
            }

            //Update the call_notes to say they're en-route
            $narrativeAdd = date("Y-m-d H:i:s").': '.$callsign.': En-Route<br/>';


            $sql = "UPDATE calls SET call_notes = concat(call_notes, ?) WHERE call_id = ?";

            try {
                $stmt = mysqli_prepare($link, $sql);
                mysqli_stmt_bind_param($stmt, "si", $narrativeAdd, $callId);
                $result = mysqli_stmt_execute($stmt);

                if ($result == FALSE) {
                    die(mysqli_error($link));
                }
            }
            catch (Exception $e)
            {
                die("Failed to run query: " . $e->getMessage()); //TODO: A public function to send me an email when this occurs should be made
            }

            break;

        case "onscene":

            break;
    }

}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function getMyCall()
{
    //First, check to see if they're on a call
    $identifier = $_SESSION['identifier'];

    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $sql = "SELECT * from active_users WHERE identifier = '$identifier' AND status = '0' AND status_detail = '3'";

    $result = mysqli_query($link, $sql);

    $num_rows = $result->num_rows;

    echo '
        <div class="col-md-6 col-sm-6 col-xs-6">
            <div class="x_panel">
                <div class="x_title">
                <h2>My Call</h2>
                <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                </ul>
                <div class="clearfix"></div>
                </div>
                <!-- ./ x_title -->
                <div class="x_content">
    ';


    if($num_rows == 0)
    {
        echo '<div class="alert alert-info"><span>Not currently on a call</span></div>';
    }
    else
    {
        //Figure out what call the user is on
        $sql = "SELECT call_id from calls_users WHERE identifier = '$identifier'";

        $result = mysqli_query($link, $sql);

        while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
        {
            $call_id = $row[0];
        }

        //Get call details
        $sql = "SELECT * from calls WHERE call_id = '$call_id'";

        $result = mysqli_query($link, $sql);

        while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
        {
            $call_type = $row[2];
            $call_street1 = $row[3];
            $call_street2 = $row[4];
            $call_street3 = $row[5];
            $call_notes = $row[6];
        }


        echo '
            <p style="font-weight:bold">&nbsp&nbsp&nbspQuick Status Updates</p>
            <a class="btn btn-app"  id="enroute_btn">
                <i class="fa fa-car"></i> En Route
            </a>
            <a class="btn btn-app">
                <i class="fa fa-home"></i> On Scene
            </a>
            <a class="btn btn-app">
                <i class="fa fa-check"></i> Code 4
            </a>
            <a class="btn btn-app">
                <i class="fa fa-shield"></i> Subj. in Cust.
            </a>
            <a class="btn btn-app">
                <i class="fa fa-university"></i> En Route Jail
            </a>
            <a class="btn btn-app">
                <i class="fa fa-ambulance"></i> En Route Hospital
            </a>
            <a class="btn btn-app">
                <i class="fa fa-crosshairs" style="color:red"></i> 10-99
            </a>
            <br/><br/>

            <div class="form-group">
              <label class="col-lg-2 control-label">Incident ID</label>
              <div class="col-lg-10">
                <input type="text" id="call_id_det" name="call_id_det" class="form-control" value="'.$call_id.'" disabled>
              </div>
              <!-- ./ col-sm-9 -->
            </div>
            <br/>
            <!-- ./ form-group -->
            <div class="form-group">
              <label class="col-lg-2 control-label">Incident Type</label>
              <div class="col-lg-10">
                <input type="text" id="call_type_det" name="call_type_det" class="form-control" value="'.$call_type.'" disabled>
              </div>
              <!-- ./ col-sm-9 -->
            </div>
            <br/>
            <!-- ./ form-group -->
            <div class="form-group">
              <label class="col-lg-2 control-label">Street 1</label>
              <div class="col-lg-10">
                <input type="text" id="call_street1_det" name="call_street1_det" class="form-control" value="'.$call_street1.'" disabled>
              </div>
              <!-- ./ col-sm-9 -->
            </div>
            <br/>
            <!-- ./ form-group -->
            <div class="form-group">
              <label class="col-lg-2 control-label">Street 2</label>
              <div class="col-lg-10">
                <input type="text" id="call_street2_det" name="call_street2_det" class="form-control" value="'.$call_street2.'" disabled>
              </div>
              <!-- ./ col-sm-9 -->
            </div>
            <br/>
            <!-- ./ form-group -->
            <div class="form-group">
              <label class="col-lg-2 control-label">Street 3</label>
              <div class="col-lg-10">
                <input type="text" id="call_street3_det" name="call_street3_det" class="form-control" value="'.$call_street3.'" disabled>
              </div>
              <!-- ./ col-sm-9 -->
            </div>

            <div class="clearfix">
            <br/><br/><br/><br/>
            <!-- ./ form-group -->
            <div class="form-group">
              <label class="col-lg-2 control-label">Narrative</label>
              <div class="col-lg-10">
                <div name="call_narrative" id="call_narrative" contenteditable="false" style="background-color: #eee; opacity: 1; border: 1px solid #ccc; padding: 6px 12px; font-size: 14px;">'.$call_notes.'</div>
              </div>
              <!-- ./ col-sm-9 -->
            </div>
            <br/>
            <!-- ./ form-group -->
            <div class="form-group">
              <label class="col-lg-2 control-label">Add Narrative</label>
              <div class="col-lg-10">
                <textarea name="narrative_add" id="narrative_add" class="form-control" style="text-transform:uppercase" rows="2" required></textarea>
              </div>
              <!-- ./ col-sm-9 -->
            </div>
            <br/>
            <!-- ./ form-group -->

        ';
    }

    echo '
        </div>
        <!-- ./ x_content -->
        <br/>
        <div class="x_footer">

        </div>
        <!-- ./ x_footer -->
        </form>
    </div>
    <!-- ./ x_panel -->
</div>
<!-- ./ col-md-6 col-sm-6 col-xs-6 -->
    ';
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function create911Call()
{
    //var_dump($_POST);

    $caller = $_POST['911_caller'];
    $location = $_POST['911_location'];
    $description = $_POST['911_description'];

    $created = date("Y-m-d H:i:s").': 911 Call Received<br/><br/>Caller Name: '.$caller;

    $call_notes = $created.'<br/>Caller States: '.$description.'<br/>';

    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $sql = "INSERT IGNORE INTO calls (call_type, call_street1, call_notes) VALUES ('911', ?, ?)";

    try {
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $location, $call_notes);
        $result = mysqli_stmt_execute($stmt);

        if ($result == FALSE) {
            die(mysqli_error($link));
        }
    }
    catch (Exception $e)
    {
        die("Failed to run query: " . $e->getMessage()); //TODO: A public function to send me an email when this occurs should be made
    }

    session_start();
    $_SESSION['good911'] = '<div class="alert alert-success"><span>Successfully created 911 call</span></div>';

    sleep(1);
    header("Location:./civilian.php");

}

//Checks to see if there are any active tones. Certain tones will add a session variable
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function checkTones()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $sql = "SELECT * from tones";

    $result=mysqli_query($link, $sql);

    $encode = array();
    while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
    {
        // If the tone is set to active
        if ($row[2] == "1")
        {
            $encode[$row[1]] = "ACTIVE";
        }
        else if ($row[2] == "0")
        {
            $encode[$row[1]] = "INACTIVE";
        }
    }

    mysqli_close($link);
    echo json_encode($encode);

}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function setTone()
{
    $tone = $_POST['tone'];
    $action = $_POST['action'];

    $status;
    switch ($action)
    {
        case "start":
            $status = '1';
            break;
        case "stop":
            $status = '0';
            break;
    }

    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $sql = "UPDATE tones SET active = ? WHERE name = ?";

    try {
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $status, $tone);
        $result = mysqli_stmt_execute($stmt);

        if ($result == FALSE) {
            die(mysqli_error($link));
        }
    }
    catch (Exception $e)
    {
        die("Failed to run query: " . $e->getMessage()); //TODO: A public function to send me an email when this occurs should be made
    }

    mysqli_close($link);

    if ($action == "start")
    {
        echo "SUCCESS START";
    }
    else
    {
        echo "SUCCESS STOP";
    }

}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function logoutUser()
{
    $identifier = $_POST['unit'];

    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $sql = "DELETE FROM active_users WHERE identifier = ?";

    try {
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "s", $identifier);
        $result = mysqli_stmt_execute($stmt);

        if ($result == FALSE) {
            die(mysqli_error($link));
        }
    }
    catch (Exception $e)
    {
        die("Failed to run query: " . $e->getMessage()); //TODO: A public function to send me an email when this occurs should be made
    }

    mysqli_close($link);
    echo "SUCCESS";

}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
function changeStatus()
{

    //var_dump($_POST);

    $unit = $_POST['unit'];
    $status = $_POST['status'];
    $statusId;
    $statusDet;
    $onCall = false;

    switch ($status)
    {
        case "statusMeal":
            $statusId = '0';
            $statusDet = '4';
            break;
        case "statusOther":
            $statusId = '0';
            $statusDet = '2';
            break;
        case "statusAvailBusy":
            $statusId = '1';
            $statusDet = '1';
            $onCall = true;
            break;
		case "statusUnavailBusy":
            $statusId = '6';
            $statusDet = '6';
            $onCall = true;
            break;
        case "statusSig11":
            $statusId = '1';
            $statusDet = '5';
            break;
		case "statusArrivedOC":
            $statusId = '7';
            $statusDet = '7';
            $onCall = true;
            break;
		case "statusTransporting":
            $statusId = '8';
            $statusDet = '8';
            $onCall = true;
            break;

		case "10-65":
            $statusId = '8';
            $statusDet = '8';
            $onCall = true;
            break;
		case "10-23":
            $statusId = '7';
            $statusDet = '7';
            $onCall = true;
            break;
        case "10-8":
            $statusId = '1';
            $statusDet = '1';
            $onCall = true;
            break;
		case "10-7":
            $statusId = '6';
            $statusDet = '6';
            $onCall = false;
            break;
        case "10-6":
            $statusId = '0';
            $statusDet = '2';
            break;
        case "10-5":
            $statusId = '0';
            $statusDet = '4';
            break;
        case "sig11":
            $statusId = '1';
            $statusDet = '5';
            break;
    }

    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $sql = "UPDATE active_users SET status = ?, status_detail = ? WHERE identifier = ?";

    try {
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "iis", $statusId, $statusDet, $unit);
        $result = mysqli_stmt_execute($stmt);

        if ($result == FALSE) {
            die(mysqli_error($link));
        }
    }
    catch (Exception $e)
    {
        die("Failed to run query: " . $e->getMessage()); //TODO: A public function to send me an email when this occurs should be made
    }

    if ($onCall)
    {
        //echo $unit;
        //Figure out what call they're on
        $sql = "SELECT call_id FROM calls_users WHERE identifier = \"$unit\"";

        $result=mysqli_query($link, $sql);

        while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
        {
            $callId = $row[0];
        }

        //Get their callsign for the narrative
        $sql = "SELECT callsign FROM active_users WHERE identifier = \"$unit\"";

        $result=mysqli_query($link, $sql);

        while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
        {
            $callsign = $row[0];
        }

        //Update the call_notes to say they were cleared
        $narrativeAdd = date("Y-m-d H:i:s").': Unit Cleared: '.$callsign.'<br/>';

        $sql = "UPDATE calls SET call_notes = concat(call_notes, ?) WHERE call_id = ?";

        try {
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, "si", $narrativeAdd, $callId);
            $result = mysqli_stmt_execute($stmt);

            if ($result == FALSE) {
                die(mysqli_error($link));
            }
        }
        catch (Exception $e)
        {
            die("Failed to run query: " . $e->getMessage()); //TODO: A public function to send me an email when this occurs should be made
        }


       //Remove them from the call
       $sql = "DELETE FROM calls_users WHERE identifier = ?";

        try {
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, "s", $unit);
            $result = mysqli_stmt_execute($stmt);

            if ($result == FALSE) {
                die(mysqli_error($link));
            }
        }
        catch (Exception $e)
        {
            die("Failed to run query: " . $e->getMessage()); //TODO: A public function to send me an email when this occurs should be made
        }
    }

    mysqli_close($link);
    echo "SUCCESS";
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function deleteDispatcher()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $identifier = $_SESSION['identifier'];


mysqli_query($link,"DELETE FROM dispatchers WHERE identifier='".$identifier."'");
mysqli_close($link);

}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function setDispatcher($dep)
{
    session_start();

    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $identifier = $_SESSION['identifier'];

    $status;
    switch($dep)
    {
        case "1":
            $status = "0";
            break;
        case "2":
            $status = "1";
            break;
    }

    deleteDispatcher();

    $sql = "INSERT INTO dispatchers (identifier, callsign, status) VALUES (?, ?, ?)";


    try {
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $identifier, $identifier, $status);
        $result = mysqli_stmt_execute($stmt);

        if ($result == FALSE) {
            die(mysqli_error($link));
        }
    }
    catch (Exception $e)
    {
        die("Failed to run query: " . $e->getMessage()); //TODO: A public function to send me an email when this occurs should be made
    }

}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function getDispatchers()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $sql = "SELECT * from dispatchers WHERE status = '1'";

    $result = mysqli_query($link, $sql);

    $num_rows = $result->num_rows;

    if($num_rows == 0)
    {
        echo "<div class=\"alert alert-danger\"><span>No available units</span></div>";
    }
    else
    {

    echo '
            <table id="dispatchersTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                <th>Identifier</th>
                </tr>
            </thead>
            <tbody>
        ';
        while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
        {
            echo '
            <tr>
                <td>'.$row[0].'</td>
            </tr>
            ';
        }

        echo '
            </tbody>
            </table>
        ';
    mysqli_close($link);
}
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function setUnitActive($dep)
{
    session_start();

    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $identifier = $_SESSION['identifier'];

    $status;
    switch($dep)
    {
        case "1":
            $status = "1";
            break;
        case "2":
            $status = "2";
            break;
    }

    $sql = "REPLACE INTO active_users (identifier, callsign, status, status_detail) VALUES (?, ?, ?, '6')";


    try {
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "ssi", $identifier, $identifier, $status);
        $result = mysqli_stmt_execute($stmt);

        if ($result == FALSE) {
            die(mysqli_error($link));
        }
    }
    catch (Exception $e)
    {
        die("Failed to run query: " . $e->getMessage()); //TODO: A public function to send me an email when this occurs should be made
    }

}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function getAvailableUnits()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $sql = "SELECT * from active_users WHERE status = '1'";

    $result = mysqli_query($link, $sql);

    $num_rows = $result->num_rows;

    if($num_rows == 0)
    {
        echo "<div class=\"alert alert-danger\"><span>No available units</span></div>";
    }
    else
    {

    echo '
            <table id="activeUsers" class="table table-striped table-bordered">
            <thead>
                <tr>
                <th>Identifier</th>
                <th>Callsign</th>
                <th>Action</th>
                </tr>
            </thead>
            <tbody>
        ';


        $counter = 0;
        while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
        {
            echo '
            <tr>
                <td>'.$row[0].'</td>
                <td>'.$row[1].'</td>
                <td>
                <div class="dropdown"><button class="btn btn-link dropdown-toggle nopadding" type="button" data-toggle="dropdown">Status <span class="caret"></span></button><ul class="dropdown-menu">
                    <li><a id="statusMeal'.$counter.'" class="statusMeal '.$row[0].'" onclick="testpublic function(this);">10-5/Meal Break</a></li>
                    <li><a id="statusOther'.$counter.'" class="statusOther '.$row[0].'" onclick="testpublic function(this);">10-6/Other</a></li>
                    <li><a id="statusSig11'.$counter.'" class="statusSig11 '.$row[0].'" onclick="testpublic function(this);">Signal 11</a></li>
                </ul></div>

                </td>
                <input name="uid" type="hidden" value='.$row[0].' />
            </tr>
            ';
            $counter++;
        }

        echo '
            </tbody>
            </table>
        ';
    }
    mysqli_close($link);
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function getUnAvailableUnits()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $sql = "SELECT * from active_users WHERE status = '0'";

    $result = mysqli_query($link, $sql);

    $num_rows = $result->num_rows;

    if($num_rows == 0)
    {
        echo "<div class=\"alert alert-info\"><span>No unavailable units</span></div>";
    }
    else
    {
        echo '
                <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                    <th>Identifier</th>
                    <th>Callsign</th>
                    <th>Status</th>
                    <th>Action</th>
                    </tr>
                </thead>
                <tbody>
            ';



            while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
            {
                echo '
                <tr>
                    <td>'.$row[0].'</td>
                    <td>'.$row[1].'</td>
                    <td>';

                        getIndividualStatus($row[1]);

                    echo '</td>

                    <td>
                    <a id="logoutUser" class="nopadding logoutUser '.$row[0].'" onclick="logoutUser(this);" style="color:red; cursor:pointer;">Logout</a>&nbsp;&nbsp;&nbsp;
                    <div class="dropdown"><button class="btn btn-link dropdown-toggle nopadding" style="display: inline-block; vertical-align:top;" type="button" data-toggle="dropdown">Status <span class="caret"></span></button><ul class="dropdown-menu">
                        <li><a id="statusAvail" class="statusAvailBusy '.$row[0].'" onclick="testpublic function(this);">10-8/Available</a></li>
                    </ul></div>
                    </td>
                    <input name="uid" type="hidden" value='.$row[0].' />
                </tr>
                ';
            }

            echo '
                </tbody>
                </table>
            ';

      }
    mysqli_close($link);
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function getIndividualStatus($callsign)
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $sql = "SELECT status_detail FROM active_users WHERE callsign = \"$callsign\"";

    $result=mysqli_query($link, $sql);

    while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
    {
        $statusDetail = $row[0];
    }

    $sql = "SELECT status_text FROM statuses WHERE status_id = \"$statusDetail\"";

    $result=mysqli_query($link, $sql);

    while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
    {
        $statusText = $row[0];
    }

    echo $statusText;
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function getIncidentType()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $sql = "SELECT code_name FROM incident_type";

    $result=mysqli_query($link, $sql);

    while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
    {
        echo '<option value="'.$row[0].'">'.$row[0].'</option>';
    }
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC2
 */
public function getStreet()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $sql = "SELECT name FROM streets";

    $result=mysqli_query($link, $sql);

    while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
    {
        echo '<option value="'.$row[0].'">'.$row[0].'</option>';
    }
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function getActiveUnits()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $query = "SELECT callsign FROM active_users WHERE status = '1'";

    $result=mysqli_query($link, $query);

    $encode = array();
    while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
    {
        $encode[$row[0]] = $row[0];
    }

    echo json_encode($encode);
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function getActiveUnitsModal()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $query = "SELECT callsign, identifier FROM active_users WHERE status = '1'";

    $result=mysqli_query($link, $query);

    $encode = array();
    while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
    {
        $encode[$row[1]] = $row[0];
    }

    echo json_encode($encode);
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function getActiveCalls()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $sql = "SELECT * from calls";

    $result = mysqli_query($link, $sql);

    $num_rows = $result->num_rows;

    if($num_rows == 0)
    {
        echo '<div class="alert alert-info"><span>No active calls</span></div>';
    }
    else
    {
        echo '<table id="activeCalls" class="table table-striped table-bordered">
            <thead>
                <tr>
                <th>Call ID</th>
                <th>Call Type</th>
                <th>Units</th>
                <th>Location</th>
                <th>Actions</th>
                </tr>
            </thead>
            <tbody>
        ';


        $counter = 0;
        while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
        {
            echo '
            <tr id="'.$counter.'">
                <td>'.$row[0].'</td>';

                //Issue #28. Check if $row[1] == bolo. If so, change text color to orange
                if ($row[1] == "BOLO")
                {
                    echo '<td style="color:orange;">'.$row[1].'</td>';
                    echo '<td><!--Leave blank--></td>';
                }
                else
                {
                    echo '<td>'.$row[1].'</td>';
                    echo '
                        <td>';
                            getUnitsOnCall($row[0]);
                        echo '</td>';
                }


                echo '<td>'.$row[2].'/'.$row[3].'/'.$row[4].'</td>';

                if (isset($_GET['type']) && $_GET['type'] == "responder")
                {
                    echo'
                    <td>
                        <button id="'.$row[0].'" class="btn-link" name="call_details_btn" data-toggle="modal" data-target="#callDetails">Details</button>
                    </td>';
                }
                else
                {
                echo'
                <td>
                    <button id="'.$row[0].'" class="btn-link" style="color: red;" value="'.$row[0].'" onclick="clearCall('.$row[0].')">Clear</button>
                    <button id="'.$row[0].'" class="btn-link" name="call_details_btn" data-toggle="modal" data-target="#callDetails">Details</button>
                    <input id="'.$row[0].'" type="submit" name="assign_unit" data-toggle="modal" data-target="#assign" class="btn-link '.$row[0].'" value="Assign"/>
                    <input name="uid" name="uid" type="hidden" value="'.$row[0].'"/>
                </td>';
                }

            echo'
            </tr>
            ';
            $counter++;
        }

        echo '
            </tbody>
            </table>
        ';

    }
    mysqli_close($link);

}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function getUnitsOnCall($callId)
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $sql1 = "SELECT * FROM calls_users WHERE call_id = \"$callId\"";

    $result1=mysqli_query($link, $sql1);

    $units = "";

    $num_rows = $result1->num_rows;

    if($num_rows == 0)
    {
        $units = '<span style="color: red;">Unassigned</span>';
    }
    else
    {
        while($row1 = mysqli_fetch_array($result1, MYSQLI_BOTH))
        {
            $units = $units.''.$row1[2].', ';
        }
    }



    echo $units;

    mysqli_close($link);
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function getCallDetails()
{
    $callId = $_GET['callId'];

    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $sql = "SELECT * FROM calls WHERE call_id = \"$callId\"";

    $result=mysqli_query($link, $sql);

    $encode = array();
    while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
    {
        $encode["call_id"] = $row[0];
        $encode["call_type"] = $row[1];
        $encode["call_street1"] = $row[2];
        $encode["call_street2"] = $row[3];
        $encode["call_street3"] = $row[4];
        $encode["narrative"] = $row[5];

    }

    echo json_encode($encode);
    mysqli_close($link);
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function getCivilianNamesOption()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $sql = "SELECT id, first_name, last_name FROM ncic_names";

    $result=mysqli_query($link, $sql);

    while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
    {
        echo "<option value=".$row[0].">".$row[1]." ".$row[2]."</option>";
    }
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function getCitations()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $sql = "SELECT citation_name FROM citations";

    $result=mysqli_query($link, $sql);

    while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
    {
        echo "<option value=".$row[0].">".$row[0]."</option>";
    }
}

/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC2
 */
public function getVehicleMakes()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $query = "SELECT DISTINCT vehicles.Make FROM vehicles";

    $result=mysqli_query($link, $query);

    $num_rows = $result->num_rows;

    while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
    {
        echo '<option value="'.$row[0].'">'.$row[0].'</option>';
    }
}


/**#@+
 * public function getVehicleModels()
 *
 * Querys database to retrieve all vehicle models.
 *
 * @since 1.0a RC2
 */
public function getVehicleModels()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $query = "SELECT DISTINCT vehicles.Model FROM vehicles";

    $result=mysqli_query($link, $query);

    $num_rows = $result->num_rows;

    while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
    {
        echo '<option value="'.$row[0].'">'.$row[0].'</option>';
    }
}


/**#@+
 * public function getGenders()
 *
 * Querys database to retrieve genders.
 *
 * @since 1.0a RC2
 */
public function getGenders()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $query = "SELECT DISTINCT genders.genders FROM genders";

    $result=mysqli_query($link, $query);

    $num_rows = $result->num_rows;

    while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
    {
        echo '<option value="'.$row[0].'">'.$row[0].'</option>';
    }
}
//////////////////////////////////////////////////////
//                  permissions                     //
//////////////////////////////////////////////////////
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function checkIfHeadAdmin()
{
    //Check if the permission is already set
    if (isset($_SESSION['headAdmin']))
    {
        if ($_SESSION['headAdmin'] == "true")
        {
            return true;
            exit();
        }
        else if ($_SESSION['headAdmin'] == "false")
        {
            return false;
            exit();
        }
    }

    $user_id = $_SESSION['id'];
    $department_id = '8'; // Table departments department_name = head administrators

    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $sql = 'SELECT * from user_departments WHERE user_id = "'.$user_id.'" AND department_id = "'.$department_id.'"';

    $result = mysqli_query($link, $sql);

    $num_rows = $result->num_rows;

    if ($num_rows == 0)
    {
        $_SESSION['headAdmin'] = "false";
        return false;
        exit();
    }
    else
    {
        $_SESSION['headAdmin'] = "true";
        return true;
        exit();
    }
}
}
//////////////////////////////////////////////////////
//                  adminActions                    //
//////////////////////////////////////////////////////

/*
This file handles all actions for admin.php script
*/

/* Handle POST requests */
class adminTools{
if (isset($_POST['approveUser']))
{
    approveUser();
}
if (isset($_POST['rejectUser']))
{
    rejectUser();
}
if (isset($_POST['suspendUser']))
{
    suspendUser();
}
if (isset($_POST['reactivateUser']))
{
    reactivateUser();
}
if (isset($_POST['deleteUser']))
{
    delete_user();
}
if (isset($_POST['getUserDetails']))
{
    getUserDetails();
}
if (isset($_POST['delete_callhistory']))
{
    delete_callhistory();
}

/* public functionS */
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function getRanks()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $site = BASE_URL;
    if (!$link)
    {
        die('Could not connect: ' . mysql_error());
    }

    $query = "SELECT * FROM ranks";

    $result = mysqli_query($link, $query);

    echo '
        <table id="ranks" class="table table-striped table-bordered">
        <thead>
            <tr>
            <th>Rank ID</th>
            <th>Rank Name</th>
            <th>User Can Choose <i class="fa fa-question-circle" aria-hidden="true" data-toggle="tooltip" data-placement="bottom" title="This indicates whether or not regular users may select this rank for themselves"></i></th>
            </tr>
        </thead>
        <tbody>
    ';

    while ($row = mysqli_fetch_array($result, MYSQLI_BOTH))
    {
        echo '
        <tr>
            <td>' . $row[0] . '</td>
            <td>' . $row[1] . '</td>';

        switch ($row[2])
        {
            case "1":
                echo "<td>True</td>";
            break;
            case "0":
                echo "<td>False</td>";
            break;
        }

        echo '
        </tr>
        ';
    }

    echo '
        </tbody>
        </table>
    ';
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function delete_user()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $site = BASE_URL;
    if (!$link)
    {
        die('Could not connect: ' . mysql_error());
    }

    $uid = $_POST['uid'];
    echo $uid;

    $query = "DELETE FROM users WHERE id = ?";

    try
    {
        $stmt = mysqli_prepare($link, $query);
        mysqli_stmt_bind_param($stmt, "i", $uid);
        $result = mysqli_stmt_execute($stmt);

        if ($result == false)
        {
            die(mysqli_error($link));
        }
    }
    catch(Exception $e)
    {
        die("Failed to run query: " . $e->getMessage());
    }

    session_start();
    $_SESSION['userMessage'] = '<div class="alert alert-success"><span>Successfully removed user from database</span></div>';
    header("Location: ../oc-admin/userManagement.php#user_panel");
}

/* Gets the user count. Returns value */
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function getUserCount()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $site = BASE_URL;
    if (!$link)
    {
        die('Could not connect: ' . mysql_error());
    }

    $query = "SELECT COUNT(*) from users";

    $result = mysqli_query($link, $query);
    $row = mysqli_fetch_array($result, MYSQLI_BOTH);

    mysqli_close($link);

    return $row[0];
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function getPendingUsers()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $site = BASE_URL;
    if (!$link)
    {
        die('Could not connect: ' . mysql_error());
    }

    $query = "SELECT id, name, email, identifier FROM users WHERE approved = '0'";

    $result = mysqli_query($link, $query);

    $num_rows = $result->num_rows;

    if ($num_rows == 0)
    {
        echo "<div class=\"alert alert-info\"><span>There are currently no access requests</span></div>";
    }
    else
    {
        echo '
            <table id="pendingUsers" class="table table-striped table-bordered">
            <thead>
                <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Identifier</th>
                <th>Groups</th>
                <th>Actions</th>
                </tr>
            </thead>
            <tbody>
        ';

        while ($row = mysqli_fetch_array($result, MYSQLI_BOTH))
        {
            echo '
            <tr>
                <td>' . $row[1] . '</td>
                <td>' . $row[2] . '</td>
                <td>' . $row[3] . '</td>
                <td>';

            getUserGroups($row[0]);

            echo ' </td>
                <td>
                    <form action="'.$site.'/actions/adminActions.php" method="post">
                    <input name="approveUser" type="submit" class="btn btn-xs btn-link" value="Approve" />
                    <input name="rejectUser" type="submit" class="btn btn-xs btn-link" value="Reject" />
                    <input name="uid" type="hidden" value=' . $row[0] . ' />
                    </form>
                </td>
            </tr>
            ';
        }

        echo '
            </tbody>
            </table>
        ';
    }
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function getDepartments()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $site = BASE_URL;
    if (!$link)
    {
        die('Could not connect: ' . mysql_error());
    }

    $sql = 'SELECT * from departments WHERE department_name <>"EMS"';

    $result = mysqli_query($link, $sql);

    while ($row = mysqli_fetch_array($result, MYSQLI_BOTH))
    {
        if ($row[0] == '0' || $row[0] == '8')
        {
            echo '<option value="' . $row[0] . '" disabled>' . $row[1] . '</option>';
        }
        else
        {
            echo '<option value="' . $row[0] . '">' . $row[1] . '</option>';
        }

    }
}

/* Get from temp table */
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function getUserGroups($uid)
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $site = BASE_URL;
    if (!$link)
    {
        die('Could not connect: ' . mysql_error());
    }

    $sql = "SELECT departments.department_name FROM user_departments_temp INNER JOIN departments on user_departments_temp.department_id=departments.department_id WHERE user_departments_temp.user_id = \"$uid\"";

    $result1 = mysqli_query($link, $sql);

    while ($row1 = mysqli_fetch_array($result1, MYSQLI_BOTH))
    {
        echo $row1[0] . "<br/>";
    }
}

/* Get from perm table */
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function getUserGroupsApproved($uid)
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $site = BASE_URL;
    if (!$link)
    {
        die('Could not connect: ' . mysql_error());
    }

    $sql = "SELECT departments.department_name FROM user_departments INNER JOIN departments on user_departments.department_id=departments.department_id WHERE user_departments.user_id = \"$uid\"";

    $result1 = mysqli_query($link, $sql);

    while ($row1 = mysqli_fetch_array($result1, MYSQLI_BOTH))
    {
        echo $row1[0] . "<br/>";
    }
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function approveUser()
{
    $uid = $_POST['uid'];
    $site = BASE_URL;
    /* If a user has been approved, the following needs to be done:
    1. Insert user's groups from temp table to regular table
    2. Set user's approved status to 1
    */

    /* Copy from temp table to regular table */
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link)
    {
        die('Could not connect: ' . mysql_error());
    }

    //Insert into user_departments
    $query = "INSERT INTO user_departments SELECT u.* FROM user_departments_temp u WHERE user_id = ?";

    try
    {
        $stmt = mysqli_prepare($link, $query);
        mysqli_stmt_bind_param($stmt, "i", $uid);
        $result = mysqli_stmt_execute($stmt);

        if ($result == false)
        {
            die(mysqli_error($link));
        }
    }
    catch(Exception $e)
    {
        die("Failed to run query: " . $e->getMessage());
    }

    /* Delete from user_departments_temp */
    $query = "DELETE FROM user_departments_temp WHERE user_id = ?";

    try
    {
        $stmt = mysqli_prepare($link, $query);
        mysqli_stmt_bind_param($stmt, "i", $uid);
        $result = mysqli_stmt_execute($stmt);

        if ($result == false)
        {
            die(mysqli_error($link));
        }
    }
    catch(Exception $e)
    {
        die("Failed to run query: " . $e->getMessage());
    }

    /* Set user's approved status */
    $query = "UPDATE users SET approved = '1' WHERE id = ?";

    try
    {
        $stmt = mysqli_prepare($link, $query);
        mysqli_stmt_bind_param($stmt, "i", $uid);
        $result = mysqli_stmt_execute($stmt);

        if ($result == false)
        {
            die(mysqli_error($link));
        }
    }
    catch(Exception $e)
    {
        die("Failed to run query: " . $e->getMessage());
    }

    mysqli_close($link);

    session_start();
    $_SESSION['accessMessage'] = '<div class="alert alert-success"><span>Successfully approved user access</span></div>';

    sleep(1);
    header("Location:./oc-admin/admin.php");

}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function rejectUser()
{
    /* If a user has been rejected, the following needs to be done:
    1. Delete user's group's from user_departments_temp table
    2. Delete user's profile from users table
    */
    $uid = $_POST['uid'];

    /* Delete groups from temp table */
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $site = BASE_URL;
    if (!$link)
    {
        die('Could not connect: ' . mysql_error());
    }

    $query = "DELETE FROM user_departments_temp where user_id = ?";

    try
    {
        $stmt = mysqli_prepare($link, $query);
        mysqli_stmt_bind_param($stmt, "i", $uid);
        $result = mysqli_stmt_execute($stmt);

        if ($result == false)
        {
            die(mysqli_error($link));
        }
    }
    catch(Exception $e)
    {
        die("Failed to run query: " . $e->getMessage());
    }

    /* Delete user from user table */

    $query = "DELETE FROM users where id = ?";

    try
    {
        $stmt = mysqli_prepare($link, $query);
        mysqli_stmt_bind_param($stmt, "i", $uid);
        $result = mysqli_stmt_execute($stmt);

        if ($result == false)
        {
            die(mysqli_error($link));
        }
    }
    catch(Exception $e)
    {
        die("Failed to run query: " . $e->getMessage());
    }

    mysqli_close($link);

    session_start();
    $_SESSION['accessMessage'] = '<div class="alert alert-danger"><span>Successfully rejected user access</span></div>';

    sleep(1);
    header("Location:./oc-admin/admin.php");

}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function getGroupCount($gid)
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $site = BASE_URL;
    if (!$link)
    {
        die('Could not connect: ' . mysql_error());
    }

    $query = "SELECT COUNT(*) from user_departments WHERE department_id = \"$gid\"";

    $result = mysqli_query($link, $query);
    $row = mysqli_fetch_array($result, MYSQLI_BOTH);

    mysqli_close($link);

    return $row[0];
}

/* NOTE: This public function will only build table for users with status 1 & 2. Unapproved users will not be included in this list */
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function getUsers()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $site = BASE_URL;
    if (!$link)
    {
        die('Could not connect: ' . mysql_error());
    }

    $query = "SELECT id, name, email, identifier, approved FROM users WHERE approved = '1' OR approved = '2'";

    $result = mysqli_query($link, $query);

    echo '
        <table id="allUsers" class="table table-striped table-bordered">
        <thead>
            <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Identifier</th>
            <th>Groups</th>
            <th>Actions</th>
            </tr>
        </thead>
        <tbody>
    ';

    while ($row = mysqli_fetch_array($result, MYSQLI_BOTH))
    {
        echo '
        <tr>
            <td>' . $row[1] . '</td>
            <td>' . $row[2] . '</td>
            <td>' . $row[3] . '</td>
            <td>';

        getUserGroupsApproved($row[0]);

        echo ' </td>
            <td>
                <form action="'.$site.'/actions/adminActions.php" method="post">
                <button name="editUser" type="button" data-toggle="modal" id="' . $row[0] . '" data-target="#editUserModal" class="btn btn-xs btn-link">Edit</button>
                <input name="deleteUser" type="submit" class="btn btn-xs btn-link" onclick="deleteUser(' . $row[0] . ')" value="Delete" />
                ';
        if ($row[4] == '2')
        {
            echo '<input name="reactivateUser" type="submit" class="btn btn-xs btn-link" value="Reactivate" />';
        }
        else
        {
            echo '<input name="suspendUser" type="submit" class="btn btn-xs btn-link" value="Suspend" />';
        }
        echo '

                <input name="uid" type="hidden" value=' . $row[0] . ' />
                </form>
            </td>
        </tr>
        ';
    }

    echo '
        </tbody>
        </table>
    ';

}

//public function to suspend a user account
// TODO: Add reason, duration
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function suspendUser()
{
    $uid = $_POST['uid'];
    $site = BASE_URL;
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link)
    {
        die('Could not connect: ' . mysql_error());
    }

    $query = "UPDATE users SET approved = '2' WHERE id = ?";

    try
    {
        $stmt = mysqli_prepare($link, $query);
        mysqli_stmt_bind_param($stmt, "i", $uid);
        $result = mysqli_stmt_execute($stmt);

        if ($result == false)
        {
            die(mysqli_error($link));
        }
    }
    catch(Exception $e)
    {
        die("Failed to run query: " . $e->getMessage());
    }

    mysqli_close($link);

    session_start();
    $_SESSION['accessMessage'] = '<div class="alert alert-success"><span>Successfully suspended user account</span></div>';

    sleep(1);
    header("Location:./oc-admin/userManagement.php");
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function reactivateUser()
{
    $uid = $_POST['uid'];
    $site = BASE_URL;
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link)
    {
        die('Could not connect: ' . mysql_error());
    }

    $query = "UPDATE users SET approved = '1' WHERE id = ?";

    try
    {
        $stmt = mysqli_prepare($link, $query);
        mysqli_stmt_bind_param($stmt, "i", $uid);
        $result = mysqli_stmt_execute($stmt);

        if ($result == false)
        {
            die(mysqli_error($link));
        }
    }
    catch(Exception $e)
    {
        die("Failed to run query: " . $e->getMessage());
    }

    mysqli_close($link);

    session_start();
    $_SESSION['accessMessage'] = '<div class="alert alert-success"><span>Successfully reactivated user account</span></div>';

    sleep(1);
    header("Location:./oc-admin/userManagement.php");
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function getUserDetails()
{
    $userId = $_POST['userId'];
    $site = BASE_URL;
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link)
    {
        die('Could not connect: ' . mysql_error());
    }

    $sql = "SELECT id, name, email, identifier FROM users WHERE ID = $userId";

    $result = mysqli_query($link, $sql);

    $encode = array();
    while ($row = mysqli_fetch_array($result, MYSQLI_BOTH))
    {
        $encode["userId"] = $row[0];
        $encode["name"] = $row[1];
        $encode["email"] = $row[2];
        $encode["identifier"] = $row[3];

    }

    mysqli_close($link);
    //Pass the array and userID to getUserGroupsEditor which will return it
    getUserGroupsEditor($encode, $userId);

}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function getUserGroupsEditor($encode, $userId)
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $site = BASE_URL;
    if (!$link)
    {
        die('Could not connect: ' . mysql_error());
    }

    $sql = "SELECT departments.department_name FROM user_departments INNER JOIN departments on user_departments.department_id=departments.department_id WHERE user_departments.user_id = \"$userId\"";

    $result = mysqli_query($link, $sql);

    $counter = 0;
    while ($row = mysqli_fetch_array($result, MYSQLI_BOTH))
    {
        $encode["department"][$counter] = $row[0];
        $counter++;
    }

    echo json_encode($encode);

    mysqli_close($link);
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function getStreetNames()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $site = BASE_URL;
    if (!$link)
    {
        die('Could not connect: ' . mysql_error());
    }

    $query = "SELECT name, county FROM streets";

    $result = mysqli_query($link, $query);

    echo '
        <table id="streets" class="table table-striped table-bordered">
        <thead>
            <tr>
            <th>Name</th>
            <th>County</th>
            </tr>
        </thead>
        <tbody>
    ';

    while ($row = mysqli_fetch_array($result, MYSQLI_BOTH))
    {
        echo '
        <tr>
            <td>' . $row[0] . '</td>
            <td>' . $row[1] . '</td>
        </tr>
        ';
    }

    echo '
        </tbody>
        </table>
    ';
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function getCodes()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $site = BASE_URL;
    if (!$link)
    {
        die('Could not connect: ' . mysql_error());
    }

    $query = "SELECT code_id, code_name FROM codes";

    $result = mysqli_query($link, $query);

    echo '
        <table id="codes" class="table table-striped table-bordered">
        <thead>
            <tr>
            <th>Code ID</th>
            <th>Code Name</th>
            </tr>
        </thead>
        <tbody>
    ';

    while ($row = mysqli_fetch_array($result, MYSQLI_BOTH))
    {
        echo '
        <tr>
            <td>' . $row[0] . '</td>
            <td>' . $row[1] . '</td>
        </tr>
        ';
    }

    echo '
        </tbody>
        </table>
    ';
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function getCallHistory()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $site = BASE_URL;
    if (!$link)
    {
        die('Could not connect: ' . mysql_error());
    }

    $query = "SELECT * FROM call_history";

    $result = mysqli_query($link, $query);

    $num_rows = $result->num_rows;

    if ($num_rows == 0)
    {
        echo "<div class=\"alert alert-info\"><span>There are currently no archived calls</span></div>";
    }
    else
    {
        echo '
        <table id="call_history" class="table table-striped table-bordered">
        <thead>
            <tr>
            <th>Call ID</th>
            <th>Call Type</th>
            <th>Primary Unit</th>
            <th>Street 1</th>
            <th>Street 2</th>
            <th>Street 3</th>
            <th>Narrative</th>
            <th>Actions</th>
            </tr>
        </thead>
        <tbody>
    ';

        while ($row = mysqli_fetch_array($result, MYSQLI_BOTH))
        {
            echo '
        <tr>
            <td>' . $row[0] . '</td>
            <td>' . $row[1] . '</td>
            <td>' . $row[2] . '</td>
            <td>' . $row[3] . '</td>
            <td>' . $row[4] . '</td>
            <td>' . $row[5] . '</td>
            <td>' . $row[6] . '</td>
            <td>
                <form action="'.$site.'/actions/adminActions.php" method="post">
                <input name="delete_callhistory" type="submit" class="btn btn-xs btn-link" style="color: red;" value="Delete"/>
                <input name="call_id" type="hidden" value=' . $row[0] . ' />
                </form>
            </td>
        </tr>
        ';
        }

        echo '
        </tbody>
        </table>
    ';
    }
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function delete_callhistory()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $site = BASE_URL;
    if (!$link)
    {
        die('Could not connect: ' . mysql_error());
    }

    $callid = $_POST['call_id'];
    echo $callid;

    $query = "DELETE FROM call_history WHERE call_id = ?";

    try
    {
        $stmt = mysqli_prepare($link, $query);
        mysqli_stmt_bind_param($stmt, "i", $callid);
        $result = mysqli_stmt_execute($stmt);

        if ($result == false)
        {
            die(mysqli_error($link));
        }
    }
    catch(Exception $e)
    {
        die("Failed to run query: " . $e->getMessage());
    }

    session_start();
    $_SESSION['historyMessage'] = '<div class="alert alert-success"><span>Successfully removed archived call</span></div>';
    header("Location: ../oc-admin/callhistory.php#history_panel");
}

//////////////////////////////////////////////////////
//                 ncicAdminActions                 //
//////////////////////////////////////////////////////

/* Handle POST requests */
if (isset($_POST['create_citation'])){
    create_citation();
}
if (isset($_POST['delete_citation'])){
    delete_citation();
}
if (isset($_POST['delete_name'])){
    delete_name();
}
if (isset($_POST['delete_plate'])){
    delete_plate();
}
if (isset($_POST['delete_warrant'])){
    delete_warrant();
}
if (isset($_POST['create_warrant'])){
    create_warrant();
}
if (isset($_POST['create_name'])){
    create_name();
}
if (isset($_POST['create_plate'])){
    create_plate();
}
if (isset($_POST['reject_identity_request'])){
    rejectRequest();
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function rejectRequest()
{
    $req_id = $_POST['id'];

    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $query = "DELETE FROM identity_requests WHERE req_id = ?";

    try {
        $stmt = mysqli_prepare($link, $query);
        mysqli_stmt_bind_param($stmt, "i", $req_id);
        $result = mysqli_stmt_execute($stmt);

        if ($result == FALSE) {
            die(mysqli_error($link));
        }
    }
    catch (Exception $e)
    {
        die("Failed to run query: " . $e->getMessage());
    }

    session_start();
    $_SESSION['identityRequestMessage'] = '<div class="alert alert-success"><span>Successfully rejected request</span></div>';
    header("Location: ../oc-admin/ncicAdmin.php");
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function getIdentityRequests()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $query = "SELECT req_id, submittedByName, submitted_on FROM identity_requests";

    $result=mysqli_query($link, $query);

    $num_rows = $result->num_rows;

    if($num_rows == 0)
    {
        echo "<div class=\"alert alert-info\"><span>There are no identity requests</span></div>";
    }
    else
    {
        echo '
            <table id="identityRequests" class="table table-striped table-bordered">
            <thead>
                <tr>
                <th>Request ID</th>
                <th>Submitted By</th>
                <th>Submitted On</th>
                <th>Actions</th>
                </tr>
            </thead>
            <tbody>
        ';

        while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
        {
            echo '
            <tr>
                <td>'.$row[0].'</td>
                <td>'.$row[1].'</td>
                <td>'.$row[2].'</td>
                <td>
                    <form action="../actions/ncicAdminActions.php" method="post">
                    <button name="viewRequestDetails" data-toggle="modal" data-target="#requestDetails" class="btn btn-xs btn-link" type="button">Details</button>
                    <input name="reject_identity_request" type="submit" class="btn btn-xs btn-link" style="color: red;" value="Quick Reject"/>
                    <input name="accept_identity_request" type="submit" class="btn btn-xs btn-link" style="color: green;" value="Quick Accept"/>
                    <input name="id" type="hidden" value='.$row[0].' />
                    </form>
                </td>
            </tr>
            ';
        }

        echo '
            </tbody>
            </table>
        ';
    }
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function ncicGetNames()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $query = "SELECT * FROM ncic_names";

    $result=mysqli_query($link, $query);

    $num_rows = $result->num_rows;

    if($num_rows == 0)
    {
        echo "<div class=\"alert alert-info\"><span>There are currently no names in the NCIC Database</span></div>";
    }
    else
    {
        echo '
            <table id="ncic_names" class="table table-striped table-bordered">
            <thead>
                <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>DOB</th>
                <th>Address</th>
                <th>Gender</th>
                <th>Race</th>
                <th>DL Status</th>
                <th>Hair Color</th>
                <th>Build</th>
                <th>Actions</th>
                </tr>
            </thead>
            <tbody>
        ';

        while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
        {
            echo '
            <tr>
                <td>'.$row[3].'</td>
                <td>'.$row[4].'</td>
                <td>'.$row[5].'</td>
                <td>'.$row[6].'</td>
                <td>'.$row[7].'</td>
                <td>'.$row[8].'</td>
                <td>'.$row[9].'</td>
                <td>'.$row[10].'</td>
                <td>'.$row[11].'</td>
                <td>
                    <button name="edit_name" data-toggle="modal" data-target="#editNameModal" class="btn btn-xs btn-link" disabled>Edit</button>
                    <form action="../actions/ncicAdminActions.php" method="post">
                    <input name="delete_name" type="submit" class="btn btn-xs btn-link" style="color: red;" value="Delete"/>
                    <input name="uid" type="hidden" value='.$row[0].' />
                    </form>
                </td>
            </tr>
            ';
        }

        echo '
            </tbody>
            </table>
        ';
    }
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function ncicGetPlates()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $query = "SELECT ncic_plates.*, ncic_names.first_name, ncic_names.last_name FROM ncic_plates INNER JOIN ncic_names ON ncic_names.id=ncic_plates.name_id";

    $result=mysqli_query($link, $query);

    $num_rows = $result->num_rows;

    if($num_rows == 0)
    {
        echo "<div class=\"alert alert-info\"><span>There are currently no vehicles in the NCIC Database</span></div>";
    }
    else
    {
        echo '
            <table id="ncic_plates" class="table table-striped table-bordered">
            <thead>
                <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Plate</th>
                <th>Reg. State</th>
                <th>Make</th>
                <th>Model</th>
                <th>Color</th>
                <th>Ins. Status</th>
                <th>Flags</th>
                <th>Notes</th>
                <th>Actions</th>
                </tr>
            </thead>
            <tbody>
        ';

        while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
        {
            $owner = $row[12]." ".$row[13];

            echo '
            <tr>
                <td>'.$row[11].'</td>
                <td>'.$row[12].'</td>
                <td>'.$row[2].'</td>
                <td>'.$row[8].'</td>
                <td>'.$row[3].'</td>
                <td>'.$row[4].'</td>
                <td>'.$row[5].'</td>
                <td>'.$row[6].'</td>
                <td>'.$row[7].'</td>
                <td>'.$row[9].'</td>
                <td>
                    <form action="../actions/ncicAdminActions.php" method="post">
                    <input name="approveUser" type="submit" class="btn btn-xs btn-link" value="Edit" disabled />
                    <input name="delete_plate" type="submit" class="btn btn-xs btn-link" style="color: red;" value="Delete" enabled/>
                    <input name="vehid" type="hidden" value='.$row[0].' />
                    </form>
                </td>
            </tr>
            ';
        }

        echo '
            </tbody>
            </table>
        ';
    }
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function ncic_warrants()
{
   $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $query = "SELECT ncic_names.first_name, ncic_names.last_name, ncic_warrants.id, ncic_warrants.issued_date, ncic_warrants.expiration_date, ncic_warrants.warrant_name, ncic_warrants.issuing_agency, ncic_warrants.status FROM ncic_warrants INNER JOIN ncic_names ON ncic_warrants.name_id=ncic_names.id";

    $result=mysqli_query($link, $query);

    $num_rows = $result->num_rows;

    if($num_rows == 0)
    {
        echo "<div class=\"alert alert-info\"><span>There are currently no warrants in the NCIC Database</span></div>";
    }
    else
    {
        echo '
            <table id="ncic_warrants" class="table table-striped table-bordered">
            <thead>
                <tr>
                <th>Status</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Warrant Name</th>
                <th>Issued On</th>
                <th>Expires On</th>
                <th>Issuing Agency</th>
                <th>Actions</th>
                </tr>
            </thead>
            <tbody>
        ';

        while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
        {
            echo '
            <tr>
                <td>'.$row[7].'</td>
                <td>'.$row[0].'</td>
                <td>'.$row[1].'</td>
                <td>'.$row[5].'</td>
                <td>'.$row[3].'</td>
                <td>'.$row[4].'</td>
                <td>'.$row[6].'</td>
                <td>
                    <form action="../actions/ncicAdminActions.php" method="post">
                    <input name="approveUser" type="submit" class="btn btn-xs btn-link" value="Edit" disabled />
                    ';
                        if ($row[7] == "Active")
                        {
                            echo '<input name="serveWarrant" type="submit" class="btn btn-xs btn-link" value="Serve" disabled/>';
                        }
                        else
                        {
                            //Do Nothing
                        }
                    echo '
                    <input name="delete_warrant" type="submit" class="btn btn-xs btn-link" style="color: red;" value="Expunge" />
                    <input name="wid" type="hidden" value='.$row[2].' />
                    </form>
                </td>
            </tr>
            ';
        }

        echo '
            </tbody>
            </table>
        ';
    }
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function ncic_citations()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $query = "SELECT ncic_names.first_name, ncic_names.last_name, ncic_citations.id, ncic_citations.citation_name, ncic_citations.issued_date, ncic_citations.issued_by FROM ncic_citations INNER JOIN ncic_names ON ncic_citations.name_id=ncic_names.id WHERE ncic_citations.status = '1'";

    $result=mysqli_query($link, $query);

    $num_rows = $result->num_rows;

    if($num_rows == 0)
    {
        echo "<div class=\"alert alert-info\"><span>There are currently no citations in the NCIC Database</span></div>";
    }
    else
    {
        echo '
            <table id="ncic_citations" class="table table-striped table-bordered">
            <thead>
                <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Citation Name</th>
                <th>Issued On</th>
                <th>Issued By</th>
                <th>Actions</th>
                </tr>
            </thead>
            <tbody>
        ';

        while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
        {
            echo '
            <tr>
                <td>'.$row[0].'</td>
                <td>'.$row[1].'</td>
                <td>'.$row[3].'</td>
                <td>'.$row[4].'</td>
                <td>'.$row[5].'</td>
                <td>
                    <form action="../actions/ncicAdminActions.php" method="post">
                    <input name="edit_citation" type="submit" class="btn btn-xs btn-link" value="Edit" disabled />
                    <input name="delete_citation" type="submit" class="btn btn-xs btn-link" style="color: red;" value="Expunge"/>
                    <input name="cid" type="hidden" value='.$row[2].' />
                    </form>
                </td>
            </tr>
            ';
        }

        echo '
            </tbody>
            </table>
        ';
    }
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function getCivilianNamesOption()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $sql = "SELECT id, first_name, last_name FROM ncic_names";

    $result=mysqli_query($link, $sql);

    while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
    {
        echo "<option value=".$row[0].">".$row[1]." ".$row[2]."</option>";
    }
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function getCivilianNames()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

	if (!$link) {
		die('Could not connect: ' .mysql_error());
	}

	$sql = "SELECT ncic_names.id, ncic_names.first_name, ncic_names.last_name FROM ncic_names";

	$result=mysqli_query($link, $sql);

	while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
	{
		echo "<option value=\"$row[0]\">$row[1] $row[2]</option>";
	}
	mysqli_close($link);
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function getUserList()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

	if (!$link) {
		die('Could not connect: ' .mysql_error());
	}

	$sql = "SELECT users.id, users.name FROM users";

	$result=mysqli_query($link, $sql);

	while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
	{
		echo "<option value=\"$row[0]\">$row[1] $row[2]</option>";
	}
	mysqli_close($link);
    
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function getAgencies()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

	if (!$link) {
		die('Could not connect: ' .mysql_error());
	}

	$sql = 'SELECT * FROM departments
            WHERE department_name <>"Administrators"
            AND department_name <>"EMS"
            AND department_name <>"Fire"
            AND department_name <>"Civilian"
            AND department_name <>"Communications (Dispatch)"
            AND department_name <>"Head Administrators"';

	$result=mysqli_query($link, $sql);

	while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
	{
		echo "<option value=\"$row[1]\">$row[1]</option>";
	}
	mysqli_close($link);
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function create_citation()
{
    $userId = $_POST['civilian_names'];
    $citation_name = $_POST['citation_name'];
    session_start();
    $issued_by = $_SESSION['name'];

    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

	if (!$link) {
		die('Could not connect: ' .mysql_error());
	}

    $sql = "INSERT INTO ncic_citations (name_id, citation_name, issued_by, status) VALUES (?, ?, ?, '1')";


	try {
		$stmt = mysqli_prepare($link, $sql);
		mysqli_stmt_bind_param($stmt, "iss", $userId, $citation_name, $issued_by);
		$result = mysqli_stmt_execute($stmt);

		if ($result == FALSE) {
			die(mysqli_error($link));
		}
	}
	catch (Exception $e)
	{
		die("Failed to run query: " . $e->getMessage()); //TODO: A public function to send me an email when this occurs should be made
	}
	mysqli_close($link);

    session_start();
    $_SESSION['citationMessage'] = '<div class="alert alert-success"><span>Successfully created citation</span></div>';

    header("Location:./oc-admin/ncicAdmin.php#citation_panel");
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function create_warrant()
{
    $userId = $_POST['civilian_names'];
    $warrant_name = $_POST['warrant_name_sel'];
    $issuing_agency = $_POST['issuing_agency'];

    $expiry = substr($_POST['warrant_name_sel'], -1);

    $warrant_name = substr($_POST['warrant_name_sel'], 0, -1);

    switch ($expiry)
    {
        case "1":
            $interval = 60;
            break;
        case "2":
            $interval = 30;
            break;
    }

    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

	if (!$link) {
		die('Could not connect: ' .mysql_error());
	}

    $sql = "INSERT INTO ncic_warrants (name_id, expiration_date, warrant_name, issuing_agency) SELECT ?, DATE_ADD(NOW(), INTERVAL ? day), ?, ?";


	try {
		$stmt = mysqli_prepare($link, $sql);
		mysqli_stmt_bind_param($stmt, "iiss", $userId, $interval, $warrant_name, $issuing_agency);
		$result = mysqli_stmt_execute($stmt);

		if ($result == FALSE) {
			die(mysqli_error($link));
		}
	}
	catch (Exception $e)
	{
		die("Failed to run query: " . $e->getMessage()); //TODO: A public function to send me an email when this occurs should be made
	}
	mysqli_close($link);

    session_start();
    $_SESSION['warrantMessage'] = '<div class="alert alert-success"><span>Successfully created warrant</span></div>';

    header("Location:./oc-admin/ncicAdmin.php#warrant_panel");
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function delete_name()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

	if (!$link) {
		die('Could not connect: ' .mysql_error());
	}

    $uid = $_POST['uid'];

    $query = "DELETE FROM ncic_names WHERE id = ?";

    try {
        $stmt = mysqli_prepare($link, $query);
        mysqli_stmt_bind_param($stmt, "i", $uid);
        $result = mysqli_stmt_execute($stmt);

        if ($result == FALSE) {
            die(mysqli_error($link));
        }
    }
    catch (Exception $e)
    {
        die("Failed to run query: " . $e->getMessage());
    }

    session_start();
    $_SESSION['nameMessage'] = '<div class="alert alert-success"><span>Successfully removed civilian name</span></div>';
    header("Location: ../oc-admin/ncicAdmin.php#name_panel");
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function delete_plate()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

	if (!$link) {
		die('Could not connect: ' .mysql_error());
	}

    $vehid = $_POST['vehid'];
    echo $vehid;

    $query = "DELETE FROM ncic_plates WHERE id = ?";

    try {
        $stmt = mysqli_prepare($link, $query);
        mysqli_stmt_bind_param($stmt, "i", $vehid);
        $result = mysqli_stmt_execute($stmt);

        if ($result == FALSE) {
            die(mysqli_error($link));
        }
    }
    catch (Exception $e)
    {
        die("Failed to run query: " . $e->getMessage());
    }

    session_start();
    $_SESSION['plateMessage'] = '<div class="alert alert-success"><span>Successfully removed civilian plate</span></div>';
    header("Location: ../oc-admin/ncicAdmin.php#plate_panel");
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function delete_citation()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

	if (!$link) {
		die('Could not connect: ' .mysql_error());
	}

    $cid = $_POST['cid'];

    $query = "DELETE FROM ncic_citations WHERE id = ?";

    try {
        $stmt = mysqli_prepare($link, $query);
        mysqli_stmt_bind_param($stmt, "i", $cid);
        $result = mysqli_stmt_execute($stmt);

        if ($result == FALSE) {
            die(mysqli_error($link));
        }
    }
    catch (Exception $e)
    {
        die("Failed to run query: " . $e->getMessage());
    }

    session_start();
    $_SESSION['citationMessage'] = '<div class="alert alert-success"><span>Successfully removed citation</span></div>';
    header("Location: ../oc-admin/ncicAdmin.php#citation_panel");
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function delete_warrant()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

	if (!$link) {
		die('Could not connect: ' .mysql_error());
	}

    $wid = $_POST['wid'];
    echo $wid;

    $query = "DELETE FROM ncic_warrants WHERE id = ?";

    try {
        $stmt = mysqli_prepare($link, $query);
        mysqli_stmt_bind_param($stmt, "i", $wid);
        $result = mysqli_stmt_execute($stmt);

        if ($result == FALSE) {
            die(mysqli_error($link));
        }
    }
    catch (Exception $e)
    {
        die("Failed to run query: " . $e->getMessage());
    }

    session_start();
    $_SESSION['warrantMessage'] = '<div class="alert alert-success"><span>Successfully removed warrant</span></div>';
    header("Location: ../oc-admin/ncicAdmin.php#warrant_panel");
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function create_name()
{
    session_start();

    $fullName = $_POST['civNameReq'];
    $firstName = explode(" ", $fullName) [0];
    $lastName = explode(" ", $fullName) [1];

    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link)
    {
        die('Could not connect: ' . mysql_error());
    }

    $query = 'SELECT first_name, last_name FROM ncic_names WHERE first_name = "' . $firstName . '" AND last_name = "' . $lastName . '"';

    $result = mysqli_query($link, $query);

    $num_rows = $result->num_rows;

    if (!$num_rows == 0)
    {
        $_SESSION['identityMessage'] = '<div class="alert alert-danger"><span>Name already exists</span></div>';

        sleep(1);
        header("Location:./oc-admin/ncicAdmin.php#plate_panel");
    }

    $firstName;
    $lastName;
    $dob = $_POST['civDobReq'];
    $address = $_POST['civAddressReq'];
    $sex = $_POST['civSexReq'];
    $race = $_POST['civRaceReq'];
	$dlstatus = $_POST['civDL'];
    $hair = $_POST['civHairReq'];
    $build = $_POST['civBuildReq'];

    $query = "INSERT INTO ncic_names (first_name, last_name, dob, address, gender, race, dl_status, hair_color, build)
    VALUES (?,?,?,?,?,?,?,?,?)";

    try
    {
        $stmt = mysqli_prepare($link, $query);
        mysqli_stmt_bind_param($stmt, "sssssssss", $firstName, $lastName, $dob, $address, $sex, $race, $dlstatus, $hair, $build);
        $result = mysqli_stmt_execute($stmt);

        if ($result == false)
        {
            die(mysqli_error($link));
        }
    }
    catch(Exception $e)
    {
        die("Failed to run query: " . $e->getMessage()); //TODO: A public function to send me an email when this occurs should be made
        
    }

    $_SESSION['identityMessage'] = '<div class="alert alert-success"><span>Successfully submitted identity request</span></div>';

    sleep(1);
    header("Location:./oc-admin/ncicAdmin.php#name_panel");

}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function create_plate()
{
	session_start();
	
    $submittedById = $_SESSION['id'];
    $userId = $_POST['civilian_names'];
    $veh_plate = $_POST['veh_plate'];
    $veh_make = $_POST['veh_make'];
    $veh_model = $_POST['veh_model'];
    $veh_color = $_POST['veh_color'];
    $veh_insurance = $_POST['veh_insurance'];
    $flags = $_POST['flags'];
    $veh_reg_state = $_POST['veh_reg_state'];
    $notes = $_POST['notes'];
    $hidden_notes = $_POST['hidden_notes'];

    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

	if (!$link) {
		die('Could not connect: ' .mysql_error());
	}

    $sql = "INSERT INTO ncic_plates (name_id, veh_plate, veh_make, veh_model, veh_color, veh_insurance, flags, veh_reg_state, notes, hidden_notes, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";


	try {
		$stmt = mysqli_prepare($link, $sql);
		mysqli_stmt_bind_param($stmt, "issssssssss", $userId, $veh_plate, $veh_make, $veh_model, $veh_color, $veh_insurance, $flags, $veh_reg_state, $notes, $hidden_notes, $submittedById);
		$result = mysqli_stmt_execute($stmt);

		if ($result == FALSE) {
			die(mysqli_error($link));
		}
	}
	catch (Exception $e)
	{
		die("Failed to run query: " . $e->getMessage()); //TODO: A public function to send me an email when this occurs should be made
	}
	mysqli_close($link);

    session_start();
    $_SESSION['plateMessage'] = '<div class="alert alert-success"><span>Successfully added plate to the database</span></div>';

    header("Location:./oc-admin/ncicAdmin.php#plate_panel");
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function getCitations()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $sql = "SELECT citation_name FROM citations";

    $result=mysqli_query($link, $sql);

    while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
    {
        echo "<option value=".$row[0].">".$row[0]."</option>";
    }
}
}

//////////////////////////////////////////////////////
//                  civActions                      //
//////////////////////////////////////////////////////

/* Handle POST requests */
class civTools{
if (isset($_POST['delete_name'])){
    delete_name();
}
if (isset($_POST['delete_plate'])){
    delete_plate();
}
if (isset($_POST['create_name'])){
    create_name();
}
if (isset($_POST['create_plate'])){
    create_plate();
}

/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function ncicGetNames()
{
    $uid = $_SESSION['id'];

    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $query = 'SELECT ncic_names.* FROM `ncic_names` WHERE ncic_names.submittedById = "' . $uid . '"';

    $result=mysqli_query($link, $query);

    $num_rows = $result->num_rows;

    if($num_rows == 0)
    {
        echo "<div class=\"alert alert-info\"><span>You currently have no identities</span></div>";
    }
    else
    {
        echo '
            <table id="ncic_names" class="table table-striped table-bordered">
            <thead>
                <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>DOB</th>
                <th>Address</th>
                <th>Gender</th>
                <th>Race</th>
                <th>DL Status</th>
                <th>Hair Color</th>
                <th>Build</th>
                <th>Actions</th>
                </tr>
            </thead>
            <tbody>
        ';

        while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
        {
            echo '
            <tr>
                <td>'.$row[3].'</td>
                <td>'.$row[4].'</td>
                <td>'.$row[5].'</td>
                <td>'.$row[6].'</td>
                <td>'.$row[7].'</td>
                <td>'.$row[8].'</td>
                <td>'.$row[9].'</td>
                <td>'.$row[10].'</td>
                <td>'.$row[11].'</td>
                <td>
                    <button name="edit_name" data-toggle="modal" data-target="#editNameModal" class="btn btn-xs btn-link" disabled>Edit</button>
                    <form action="../actions/civActions.php" method="post">
                    <input name="delete_name" type="submit" class="btn btn-xs btn-link" style="color: red;" value="Delete"/>
                    <input name="uid" type="hidden" value='.$row[0].' />
                    </form>
                </td>
            </tr>
            ';
        }

        echo '
            </tbody>
            </table>
        ';
    }
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC2
 */
public function ncicGetPlates()
{

    $uid = $_SESSION['id'];

    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $query = 'SELECT ncic_plates.*, ncic_names.first_name, ncic_names.last_name FROM ncic_plates INNER JOIN ncic_names ON ncic_names.id=ncic_plates.name_id WHERE ncic_plates.user_id = "' . $uid . '"';

    $result=mysqli_query($link, $query);

    $num_rows = $result->num_rows;

    if($num_rows == 0)
    {
        echo "<div class=\"alert alert-info\"><span>You currently have no vehicles</span></div>";
    }
    else
    {
        echo '
            <table id="ncic_plates" class="table table-striped table-bordered">
            <thead>
                <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Plate</th>
                <th>Reg. State</th>
                <th>Make</th>
                <th>Model</th>
                <th>Color</th>
                <th>Ins. Status</th>
                <th>Flags</th>
                <th>Notes</th>
                <th>Actions</th>
                </tr>
            </thead>
            <tbody>
        ';

        while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
        {

            echo '
            <tr>
                <td>'.$row[11].'</td>
                <td>'.$row[12].'</td>
                <td>'.$row[2].'</td>
                <td>'.$row[8].'</td>
                <td>'.$row[3].'</td>
                <td>'.$row[4].'</td>
                <td>'.$row[5].'</td>
                <td>'.$row[6].'</td>
                <td>'.$row[7].'</td>
                <td>'.$row[9].'</td>
                <td>
                    <form action="../actions/civActions.php" method="post">
                    <input name="approveUser" type="submit" class="btn btn-xs btn-link" value="Edit" disabled />
                    <input name="delete_plate" type="submit" class="btn btn-xs btn-link" style="color: red;" value="Delete" enabled/>
                    <input name="vehid" type="hidden" value='.$row[0].' />
                    </form>
                </td>
            </tr>
            ';
        }

        echo '
            </tbody>
            </table>
        ';
    }
}

/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC2
 */
public function getCivilianNamesOption()
{
    session_start();

    $uid = $_SESSION['id'];

    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $sql = 'SELECT id, first_name, last_name FROM ncic_names WHERE civilian_names.user_id = "' . $uid . '"';

    $result=mysqli_query($link, $sql);

    while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
    {
        echo "<option value=".$row[0].">".$row[1]." ".$row[2]."</option>";
    }
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC2
 */
public function getCivilianNames()
{
    session_start();


    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

	if (!$link) {
		die('Could not connect: ' .mysql_error());
	}

	$sql = 'SELECT ncic_names.id, ncic_names.first_name, ncic_names.last_name FROM ncic_names';

	$result=mysqli_query($link, $sql);

	while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
	{
		echo "<option value=\"$row[0]\">$row[1] $row[2]</option>";
	}
	mysqli_close($link);
}

/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC2
 */
public function delete_name()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

	if (!$link) {
		die('Could not connect: ' .mysql_error());
	}

    $uid = $_POST['uid'];

    $query = "DELETE FROM ncic_names WHERE id = ?";

    try {
        $stmt = mysqli_prepare($link, $query);
        mysqli_stmt_bind_param($stmt, "i", $uid);
        $result = mysqli_stmt_execute($stmt);

        if ($result == FALSE) {
            die(mysqli_error($link));
        }
    }
    catch (Exception $e)
    {
        die("Failed to run query: " . $e->getMessage());
    }

    session_start();
    $_SESSION['nameMessage'] = '<div class="alert alert-success"><span>Successfully removed civilian name</span></div>';
    header("Location: ../civilian.php");
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC2
 */
public function delete_plate()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

	if (!$link) {
		die('Could not connect: ' .mysql_error());
	}

    $vehid = $_POST['vehid'];

    $query = "DELETE FROM ncic_plates WHERE id = ?";

    try {
        $stmt = mysqli_prepare($link, $query);
        mysqli_stmt_bind_param($stmt, "i", $vehid);
        $result = mysqli_stmt_execute($stmt);

        if ($result == FALSE) {
            die(mysqli_error($link));
        }
    }
    catch (Exception $e)
    {
        die("Failed to run query: " . $e->getMessage());
    }

    session_start();
    $_SESSION['plateMessage'] = '<div class="alert alert-success"><span>Successfully removed civilian plate</span></div>';
    header("Location: ../civilian.php");
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC2
 */
public function create_name()
{
    session_start();

    $fullName = $_POST['civNameReq'];
    $firstName = explode(" ", $fullName) [0];
    $lastName = explode(" ", $fullName) [1];

    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link)
    {
        die('Could not connect: ' . mysql_error());
    }

    $query = 'SELECT first_name, last_name FROM ncic_names WHERE first_name = "' . $firstName . '" AND last_name = "' . $lastName . '"';

    $result = mysqli_query($link, $query);

    $num_rows = $result->num_rows;

    if (!$num_rows == 0)
    {
        $_SESSION['identityMessage'] = '<div class="alert alert-danger"><span>Name already exists</span></div>';

        sleep(1);
        header("Location:./civilian.php");
    }

    // If name doesn't exist, add it to ncic_requests table
    //Who submitted it
    $submittedByName = $_SESSION['name'];
    $submitttedById = $_SESSION['id'];
    //Submission Data
    $firstName;
    $lastName;
    $dob = $_POST['civDobReq'];
    $address = $_POST['civAddressReq'];
    $sex = $_POST['civSexReq'];
    $race = $_POST['civRaceReq'];
	$dlstatus = $_POST['civDL'];
    $hair = $_POST['civHairReq'];
    $build = $_POST['civBuildReq'];

    $query = "INSERT INTO ncic_names (submittedByName, submittedById, first_name, last_name, dob, address, gender, race, dl_status, hair_color, build)
    VALUES (?,?,?,?,?,?,?,?,?,?,?)";

    try
    {
        $stmt = mysqli_prepare($link, $query);
        mysqli_stmt_bind_param($stmt, "sssssssssss", $submittedByName, $submitttedById, $firstName, $lastName, $dob, $address, $sex, $race, $dlstatus, $hair, $build);
        $result = mysqli_stmt_execute($stmt);

        if ($result == false)
        {
            die(mysqli_error($link));
        }
    }
    catch(Exception $e)
    {
        die("Failed to run query: " . $e->getMessage()); //TODO: A public function to send me an email when this occurs should be made

    }

    $_SESSION['identityMessage'] = '<div class="alert alert-success"><span>Successfully submitted identity request</span></div>';

    sleep(1);
    header("Location:./civilian.php");

}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC2
 */
public function create_plate()
{
	session_start();
    $uid = $_SESSION['id'];

    $submittedById = $_SESSION['id'];
    $userId = $_POST['civilian_names'];
    $veh_plate = $_POST['veh_plate'];
    $veh_make = $_POST['veh_make'];
    $veh_model = $_POST['veh_model'];
    $veh_color = $_POST['veh_color'];
    $veh_insurance = $_POST['veh_insurance'];
    $flags = $_POST['flags'];
    $veh_reg_state = $_POST['veh_reg_state'];
    $notes = $_POST['notes'];

    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

	if (!$link) {
		die('Could not connect: ' .mysql_error());
	}

    $sql = "INSERT INTO ncic_plates (name_id, veh_plate, veh_make, veh_model, veh_color, veh_insurance, flags, veh_reg_state, notes, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";


	try {
		$stmt = mysqli_prepare($link, $sql);
		mysqli_stmt_bind_param($stmt, "isssssssss", $userId, $veh_plate, $veh_make, $veh_model, $veh_color, $veh_insurance, $flags, $veh_reg_state, $notes, $submittedById);
		$result = mysqli_stmt_execute($stmt);

		if ($result == FALSE) {
			die(mysqli_error($link));
		}
	}
	catch (Exception $e)
	{
		die("Failed to run query: " . $e->getMessage()); //TODO: A public function to send me an email when this occurs should be made
	}
	mysqli_close($link);

    session_start();
    $_SESSION['plateMessage'] = '<div class="alert alert-success"><span>Successfully added plate to the database</span></div>';

    header("Location:./civilian.php");
}
}


//////////////////////////////////////////////////////
//                  dispatchActions                 //
//////////////////////////////////////////////////////

class cadTools{
if (isset($_POST['clearCall']))
{
    storeCall();
}
if (isset($_POST['newCall']))
{
    newCall();
}
if (isset($_POST['assignUnit']))
{
    assignUnit();
}
if (isset($_POST['addNarrative']))
{
    addNarrative();
}

if (isset($_GET['term'])) {
    $data = array();

    $term = $_GET['term'];
    //echo json_encode($term);
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $query = "SELECT * from streets WHERE name LIKE \"%$term%\"";

    $result=mysqli_query($link, $query);

    while($row = $result->fetch_assoc())
    {
        $data[] = $row['name'];
    }

    echo json_encode($data);


}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function addNarrative()
{
    session_start();
    $details = $_POST['details'];
    $callId = $_POST['callId'];
    $who = $_SESSION['identifier'];

    $detailsArr = explode("&", $details);

    $narrativeAdd = explode("=", $detailsArr[0])[1];
    $narrativeAdd = strtoupper($narrativeAdd);

    $narrativeAdd = date("Y-m-d H:i:s").': '.$who.': '.$narrativeAdd.'<br/>';

    $narrativeAdd = str_replace("+", " ", $narrativeAdd);


    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $sql = "UPDATE calls SET call_notes = concat(call_notes, ?) WHERE call_id = ?";

    try {
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "si", $narrativeAdd, $callId);
        $result = mysqli_stmt_execute($stmt);

        if ($result == FALSE) {
            die(mysqli_error($link));
        }
    }
    catch (Exception $e)
    {
        die("Failed to run query: " . $e->getMessage()); //TODO: A public function to send me an email when this occurs should be made
    }

    echo "SUCCESS";

}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function assignUnit()
{
    //var_dump($_POST);
    //Need to explode the details by &
    $details = $_POST['details'];
    $detailsArr = explode("&", $details);

    if ($detailsArr[0] == 'unit=')
    {
        echo "ERROR";
        die();
    }

    $unit = explode("=", $detailsArr[0])[1];
    $callId = explode("=", $detailsArr[1])[1];
    $unit = str_replace("+", " ", $unit);

    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $sql = "SELECT callsign FROM active_users WHERE identifier = \"$unit\"";

    $result=mysqli_query($link, $sql);

	while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
	{
		$callsign = $row[0];
	}

    $sql = "INSERT INTO calls_users (call_id, identifier, callsign) VALUES (?, ?, ?)";

    try {
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "iss", $callId, $unit, $callsign);
        $result = mysqli_stmt_execute($stmt);

        if ($result == FALSE) {
            die(mysqli_error($link));
        }
    }
    catch (Exception $e)
    {
        die("Failed to run query: " . $e->getMessage()); //TODO: A public function to send me an email when this occurs should be made
    }

    //Now we need to modify the assigned user's status
    $sql = "UPDATE active_users SET status = '0', status_detail = '3' WHERE active_users.callsign = ?";

    try {
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "s", $callsign);
        $result = mysqli_stmt_execute($stmt);

        if ($result == FALSE) {
            die(mysqli_error($link));
        }
    }
    catch (Exception $e)
    {
        die("Failed to run query: " . $e->getMessage()); //TODO: A public function to send me an email when this occurs should be made
    }

    //Now we'll add data to the call log for unit history
    $narrativeAdd = date("Y-m-d H:i:s").': Dispatched: '.$callsign.'<br/>';

    $sql = "UPDATE calls SET call_notes = concat(call_notes, ?) WHERE call_id = ?";

    try {
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "si", $narrativeAdd, $callId);
        $result = mysqli_stmt_execute($stmt);

        if ($result == FALSE) {
            die(mysqli_error($link));
        }
    }
    catch (Exception $e)
    {
        die("Failed to run query: " . $e->getMessage()); //TODO: A public function to send me an email when this occurs should be made
    }

    echo "SUCCESS";
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function storeCall()
{

    $callId = $_POST['callId'];

    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $query = "INSERT INTO call_history SELECT calls.* FROM calls WHERE call_id = ?";

    try {
        $stmt = mysqli_prepare($link, $query);
        mysqli_stmt_bind_param($stmt, "i", $callId);
        $result = mysqli_stmt_execute($stmt);

        if ($result == FALSE) {
            die(mysqli_error($link));
        }
    }
    catch (Exception $e)
    {
        die("Failed to run query: " . $e->getMessage());
    }

    clearCall();
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function clearCall()
{

    $callId = $_POST['callId'];

    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    //First delete from calls list
    $query = "DELETE FROM calls WHERE call_id = ?";

    try {
        $stmt = mysqli_prepare($link, $query);
        mysqli_stmt_bind_param($stmt, "i", $callId);
        $result = mysqli_stmt_execute($stmt);

        if ($result == FALSE) {
            die(mysqli_error($link));
        }
    }
    catch (Exception $e)
    {
        die("Failed to run query: " . $e->getMessage());
    }

    //Get units that were on the call
    $query = "SELECT identifier FROM calls_users WHERE call_id = \"$callId\"";

    $result=mysqli_query($link, $query);

	while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
	{
		clearUnitFromCall($callId, $row[0]);
	}

}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function clearUnitFromCall($callId, $unit)
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    //First delete from calls list
    $query = "DELETE FROM calls_users WHERE call_id = ? AND identifier = ?";

    try {
        $stmt = mysqli_prepare($link, $query);
        mysqli_stmt_bind_param($stmt, "is", $callId, $unit);
        $result = mysqli_stmt_execute($stmt);

        if ($result == FALSE) {
            die(mysqli_error($link));
        }

        echo "Here ".$unit;
        freeUnitStatus($unit);
    }
    catch (Exception $e)
    {
        die("Failed to run query: " . $e->getMessage());
    }
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function freeUnitStatus($unit)
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $sql = "UPDATE active_users SET status = '1', status_detail = '1' WHERE active_users.identifier = ?";

    try {
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "s", $unit);
        $result = mysqli_stmt_execute($stmt);

        if ($result == FALSE) {
            die(mysqli_error($link));
        }
    }
    catch (Exception $e)
    {
        die("Failed to run query: " . $e->getMessage()); //TODO: A public function to send me an email when this occurs should be made
    }
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function newCall()
{
    //Need to explode the details by &
    $details = $_POST['details'];
    $details = urldecode($details);

    $detailsArr = explode("&", $details);

    //Now, each item in the details array needs to be exploded by = to get the value
    $call_type = explode("=", $detailsArr[0])[1];
    $street1 = str_replace('+',' ', explode("=", $detailsArr[1])[1]);
    $street2 = str_replace('+',' ', explode("=", $detailsArr[2])[1]);
    $street3 = str_replace('+',' ', explode("=", $detailsArr[3])[1]);
    $unit1 = str_replace('+',' ', explode("=", $detailsArr[4])[1]);
    $unit2 = str_replace('+',' ', explode("=", $detailsArr[5])[1]);
    $narrative = str_replace('+',' ', explode("=", $detailsArr[6])[1]);
    $narrative = strtoupper($narrative);

    $created = date("Y-m-d H:i:s").': Call Created<br/>';
    if ($narrative == "")
    {
        $narrative = $created;
    }
    else
    {
        $narrative = $created.date("Y-m-d H:i:s").': '.$narrative.'<br/>';
    }

    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

	if (!$link) {
		die('Could not connect: ' .mysql_error());
	}

    $sql = "INSERT INTO calls (call_type, call_street1, call_street2, call_street3, call_notes) VALUES (?, ?, ?, ?, ?)";

	try {
		$stmt = mysqli_prepare($link, $sql);
		mysqli_stmt_bind_param($stmt, "sssss", $call_type, $street1, $street2, $street3, $narrative);
		$result = mysqli_stmt_execute($stmt);

        //Get the ID of the new call to assign units to it
        $last_id = mysqli_insert_id($link);

		if ($result == FALSE) {
			die(mysqli_error($link));
		}
	}
	catch (Exception $e)
	{
		die("Failed to run query: " . $e->getMessage()); //TODO: A public function to send me an email when this occurs should be made
	}

    $query = "SELECT identifier FROM active_users WHERE callsign = \"$unit1\"";

	$result=mysqli_query($link, $query);

	while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
	{
		$unit_call_identifier = $row[0];
	}

    //Add the units into the calls_users table
    if ($unit1 == "")
    { /*Do nothing*/ }
    else
    {
        $sql = "INSERT INTO calls_users (call_id, identifier, callsign) VALUES (?, ?, ?)";

        try {
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, "iss", $last_id, $unit_call_identifier, $unit1);
            $result = mysqli_stmt_execute($stmt);

            if ($result == FALSE) {
                die(mysqli_error($link));
            }
        }
        catch (Exception $e)
        {
            die("Failed to run query: " . $e->getMessage()); //TODO: A public function to send me an email when this occurs should be made
        }

        //Now we need to modify the assigned user's status'
        $sql = "UPDATE active_users SET status = '0', status_detail = '3' WHERE active_users.callsign = ?";

        try {
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, "s", $unit1);
            $result = mysqli_stmt_execute($stmt);

            if ($result == FALSE) {
                die(mysqli_error($link));
            }
        }
        catch (Exception $e)
        {
            die("Failed to run query: " . $e->getMessage()); //TODO: A public function to send me an email when this occurs should be made
        }

        //Now we'll add data to the call log for unit history
        $narrativeAdd = date("Y-m-d H:i:s").': Dispatched: '.$unit1.'<br/>';

        $sql = "UPDATE calls SET call_notes = concat(call_notes, ?) WHERE call_id = ?";

        try {
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, "si", $narrativeAdd, $last_id);
            $result = mysqli_stmt_execute($stmt);

            if ($result == FALSE) {
                die(mysqli_error($link));
            }
        }
        catch (Exception $e)
        {
            die("Failed to run query: " . $e->getMessage()); //TODO: A public function to send me an email when this occurs should be made
        }
    }

    //Add the units into the calls_users table
    if ($unit2 == "")
    { /*Do nothing*/ }
    else
    {
        $sql = "INSERT INTO calls_users (call_id, identifier) VALUES (?, ?)";

        try {
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, "is", $last_id, $unit2);
            $result = mysqli_stmt_execute($stmt);

            if ($result == FALSE) {
                die(mysqli_error($link));
            }
        }
        catch (Exception $e)
        {
            die("Failed to run query: " . $e->getMessage()); //TODO: A public function to send me an email when this occurs should be made
        }

        //Now we need to modify the assigned user's status'
        $sql = "UPDATE active_users SET status = '0', status_detail = '3' WHERE active_users.callsign = ?";

        try {
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, "s", $unit2);
            $result = mysqli_stmt_execute($stmt);

            if ($result == FALSE) {
                die(mysqli_error($link));
            }
        }
        catch (Exception $e)
        {
            die("Failed to run query: " . $e->getMessage()); //TODO: A public function to send me an email when this occurs should be made
        }

        //Now we'll add data to the call log for unit history
        $narrativeAdd = date("Y-m-d H:i:s").': Dispatched: '.$unit2.'<br/>';

        $sql = "UPDATE calls SET call_notes = concat(call_notes, ?) WHERE call_id = ?";

        try {
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, "si", $narrativeAdd, $last_id);
            $result = mysqli_stmt_execute($stmt);

            if ($result == FALSE) {
                die(mysqli_error($link));
            }
        }
        catch (Exception $e)
        {
            die("Failed to run query: " . $e->getMessage()); //TODO: A public function to send me an email when this occurs should be made
        }
    }





	mysqli_close($link);

    echo "SUCCESS";

}

/**#@+
 * public function cadGetVehicleBOLOS()
 *
 * Querys database to retrieve all currently entered Vehicle BOLOS.
 *
 * @since 1.0a RC2
 */

public function cadGetVehicleBOLOS()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $query = "SELECT bolos_vehicles.* FROM bolos_vehicles";

    $result=mysqli_query($link, $query);

    $num_rows = $result->num_rows;

    if($num_rows == 0)
    {
        echo "<div class=\"alert alert-info\"><span>Good work! No Active Vehicle BOLOS.</span></div>";
    }
    else
    {
        echo '
            <table id="ncic_plates" class="table table-striped table-bordered">
            <thead>
                <tr>
                <th>Vehicle Make</th>
                <th>Vehicle Model</th>
                <th>Vehicle Plate</th>
                <th>Primary Color</th>
                <th>Secondary Color</th>
                <th>Reason Wanted</th>
                <th>Last Seen</th>
                <th>Actions</th>
                </tr>
            </thead>
            <tbody>
        ';

        while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
        {

            echo '
            <tr>
                <td>'.$row[1].'</td>
                <td>'.$row[2].'</td>
                <td>'.$row[3].'</td>
                <td>'.$row[4].'</td>
                <td>'.$row[5].'</td>
                <td>'.$row[6].'</td>
                <td>'.$row[7].'</td>
                <td>
                    <form action="../actions/ncicAdminActions.php" method="post">
                    <input name="approveUser" type="submit" class="btn btn-xs btn-link" value="Edit" disabled />
                    <input name="delete_plate" type="submit" class="btn btn-xs btn-link" style="color: red;" value="Delete" disabled/>
                    <input name="id" type="hidden" value='.$row[0].' />
                    </form>
                </td>
            </tr>
            ';
        }

        echo '
            </tbody>
            </table>
        ';
    }
}

/**#@+
 * public function cadGetPersonBOLOS()
 *
 * Querys database to retrieve all currently entered Person BOLOS.
 *
 * @since 1.0a RC2
 */

public function cadGetPersonBOLOS()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $query = "SELECT bolos_persons.* FROM bolos_persons";

    $result=mysqli_query($link, $query);

    $num_rows = $result->num_rows;

    if($num_rows == 0)
    {
        echo "<div class=\"alert alert-info\"><span>Good work! No Active Person BOLOS.</span></div>";
    }
    else
    {
        echo '
            <table id="bolo_board" class="table table-striped table-bordered">
            <thead>
                <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Gender</th>
                <th>Physical Description</th>
                <th>Reason Wanted</th>
                <th>Last Seen</th>
                <th>Actions</th>
                </tr>
            </thead>
            <tbody>
        ';

        while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
        {

            echo '
            <tr>
                <td>'.$row[1].'</td>
                <td>'.$row[2].'</td>
                <td>'.$row[3].'</td>
                <td>'.$row[4].'</td>
                <td>'.$row[5].'</td>
                <td>'.$row[6].'</td>
                <td>
                    <form action="../actions/ncicAdminActions.php" method="post">
                    <input name="approveUser" type="submit" class="btn btn-xs btn-link" value="Edit" disabled />
                    <input name="delete_plate" type="submit" class="btn btn-xs btn-link" style="color: red;" value="Delete" disabled/>
                    <input name="id" type="hidden" value='.$row[0].' />
                    </form>
                </td>
            </tr>
            ';
        }

        echo '
            </tbody>
            </table>
        ';
    }
}

//////////////////////////////////////////////////////
//                  ncic                            //
//////////////////////////////////////////////////////

/*
    Returns information on name run through NCIC.
    TODO: Add a check here to check the admin panel to determine if Randomized names are allowed
*/
if (isset($_POST['ncic_name'])){
    name();
}
if (isset($_POST['ncic_plate'])){
    plate();
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function name()
{
    $name = $_POST['ncic_name'];


    if(strpos($name, ' ') !== false) {
        $name_arr = explode(" ", $name);
        $first_name = $name_arr[0];
        $last_name = $name_arr[1];

        $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        if (!$link) {
            die('Could not connect: ' .mysql_error());
        }

        $sql = "SELECT id, first_name, last_name, dob, address, sex, race, dl_status, hair_color, build, TIMESTAMPDIFF(YEAR, dob, CURDATE()) AS age FROM ncic_names WHERE first_name = \"$first_name\" and last_name = \"$last_name\"";

        $result=mysqli_query($link, $sql);

        $encode = array();

        $num_rows = $result->num_rows;
        if($num_rows == 0)
        {
            $encode["noResult"] = "true";
        }
        else
        {

            while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
            {
                $userId = $row[0];
                $encode["userId"] = $row[0];
                $encode["first_name"] = $row[1];
                $encode["last_name"] = $row[2];
                $encode["dob"] = $row[3];
                $encode["address"] = $row[4];
                $encode["sex"] = $row[5];
                $encode["race"] = $row[6];
                $encode["dl_status"] = $row[7];
                $encode["hair_color"] = $row[8];
                $encode["build"] = $row[9];
                $encode["age"] = $row[10];
            }
            mysqli_close($link);

            /* Check for Warrants */
            $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

            if (!$link) {
                die('Could not connect: ' .mysql_error());
            }

            $sql = "SELECT id, name_id, warrant_name FROM ncic_warrants WHERE name_id = \"$userId\"";

            $result=mysqli_query($link, $sql);

            $num_rows = $result->num_rows;
            if($num_rows == 0)
            {
                $encode["noWarrants"] = "true";
            }
            else
            {
                $warrantIndex = 0;
                while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
                {
                    $encode["warrantId"][$warrantIndex] = $row[0];
                    $encode["warrant_name"][$warrantIndex] = $row[2];

                    $warrantIndex++;
                }
                mysqli_close($link);
            }

            /* Check for Citations */
            $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

            if (!$link) {
                die('Could not connect: ' .mysql_error());
            }

            $sql = "SELECT id, name_id, citation_name FROM ncic_citations WHERE name_id = \"$userId\"";

            $result=mysqli_query($link, $sql);

            $num_rows = $result->num_rows;
            if($num_rows == 0)
            {
                $encode["noCitations"] = "true";
            }
            else
            {
                $citationIndex = 0;
                while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
                {
                    $encode["citationId"][$citationIndex] = $row[0];
                    $encode["citation_name"][$citationIndex] = $row[2];

                    $citationIndex++;
                }
                mysqli_close($link);
            }

        }

        echo json_encode($encode);


    } else {
        $encode = array();
        $encode["noResult"] = "true";
        echo json_encode($encode);
    }
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function plate()
{
    $plate = $_POST['ncic_plate'];

    //Remove all spaces from plate
    $plate = str_replace(' ', '', $plate);
    //Set plate to all uppercase
    $plate = strtoupper($plate);
    //Convert all O to 0
    $plate = str_replace('O', '0', $plate);
    //Remove al hyphens
    $plate = str_replace('-', '', $plate);
    //Remove all special characters
    $plate = preg_replace('/[^A-Za-z0-9\-]/', '', $plate);

    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $sql = "SELECT ncic_plates.*, ncic_names.first_name, ncic_names.last_name FROM ncic_plates INNER JOIN ncic_names ON ncic_names.id=ncic_plates.name_id WHERE veh_plate = \"$plate\"";

    $result=mysqli_query($link, $sql);

    $encode = array();

    $num_rows = $result->num_rows;
    if($num_rows == 0)
    {
        $encode["noResult"] = "true";
    }
    else
    {
        $result=mysqli_query($link, $sql);

        while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
        {
            $owner = $row[12]." ".$row[13];

            $encode["plate"] = $row[2];
            $encode["veh_make"] = $row[3];
            $encode["veh_model"] = $row[4];
            $encode["veh_color"] = $row[5];
            $encode["veh_ro"] = $owner;
            $encode["veh_insurance"] = $row[6];
            $encode["flags"] = $row[7];
            $encode["veh_reg_state"] = $row[8];
            $encode["notes"] = $row[9];

        }
        mysqli_close($link);
    }

    echo json_encode($encode);
}

public function firearm()
{

}
}

//////////////////////////////////////////////////////
//                    login                         //
//////////////////////////////////////////////////////
class userMgmt{
    if(!empty($_POST))
    {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        if (!$link) {
            die('Could not connect: ' .mysql_error());
        }

        $query = "SELECT id, name, password, email, identifier, password_reset, approved FROM users WHERE email = ?";

        try {
            $stmt = mysqli_prepare($link, $query);
            mysqli_stmt_bind_param($stmt, "s", $email);
            $result = mysqli_stmt_execute($stmt);

            if ($result == FALSE) {
                die(mysqli_error($link));
            }
        }
        catch (Exception $e)
        {
            die("Failed to run query: " . $e->getMessage());
        }

        $login_ok = false;

        mysqli_stmt_bind_result($stmt, $id, $name, $pw, $email, $identifier, $password_reset, $approved);
	    mysqli_stmt_fetch($stmt);

        if (password_verify($password, $pw))
        {
            $login_ok = true;
        }
        else
        {
            session_start();
            $_SESSION['loginMessageDanger'] = 'Invalid credentials';
            header("Location:./index.php");
            exit();
        }

        /* Check to see if they're approved to use the system
            0 = Pending Approval
            1 = Approved
            2 = Suspended
        */
        if ($approved == "0")
        {
            session_start();
            $_SESSION['loginMessageDanger'] = 'Your account hasn\'t been approved yet. Please wait for an administrator to approve your access request.';
            header("Location:./index.php");
            exit();
        }
        else if ($approved == "2")
        {
            session_start();
            $_SESSION['loginMessageDanger'] = 'Your account has been suspended by an administrator.';
            header("Location:./index.php");
            exit();
        }

        /* TODO: Handle password resets */
        session_start();
        $_SESSION['logged_in'] = 'YES';
        $_SESSION['id'] = $id;
        $_SESSION['name'] = $name;
        $_SESSION['email'] = $email;
        $_SESSION['identifier'] = $identifier;
        $_SESSION['callsign'] = $identifier; //Set callsign to default to identifier until the unit changes it

        header("Location:./dashboard.php");
    }




//////////////////////////////////////////////////////
//                  logout                          //
//////////////////////////////////////////////////////

if (isset($_GET['responder']))
{
    logoutResponder();
}

//Need to make sure they're out of the active_users table
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function logoutResponder()
{
    $identifier = $_GET['responder'];

    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

	if (!$link) {
		die('Could not connect: ' .mysql_error());
	}

    $sql = "DELETE FROM active_users WHERE identifier = ?";

    try {
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "s", $identifier);
        $result = mysqli_stmt_execute($stmt);

        if ($result == FALSE) {
            die(mysqli_error($link));
        }
    }
    catch (Exception $e)
    {
        die("Failed to run query: " . $e->getMessage()); //TODO: A public function to send me an email when this occurs should be made
    }

    mysqli_close($link);
}

session_start();
session_unset();
session_destroy();

header("Location: ../index.php?loggedOut=true");
exit();

 //////////////////////////////////////////////////////
//                  profileActions                  //
//////////////////////////////////////////////////////

//Handle requests
if (isset($_POST['update_profile_btn']))
{
    updateProfile();
}
if (isset($_GET['getMyRank']))
{
	getMyRank();
}


/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function updateProfile()
{
    session_start();
    $id = $_SESSION['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $identifier = $_POST['identifier'];

    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

	if (!$link) {
		die('Could not connect: ' .mysql_error());
	}

    $query = "UPDATE users SET name = ?, email = ?, identifier = ? WHERE ID = ?";

	try {
		$stmt = mysqli_prepare($link, $query);
		mysqli_stmt_bind_param($stmt, "sssi", $name, $email, $identifier, $id);
		$result = mysqli_stmt_execute($stmt);

		if ($result == FALSE) {
            if (mysqli_errno($link) == 1062) {
                $_SESSION['profileUpdate'] = '<div class="alert alert-danger"><span>Update unsuccessful. Emails and Identifiers must be unique.</span></div>';
                sleep(1); //Seconds to wait
	            header("Location: ../profile.php");
            }
			die(mysqli_error($link));
		}
	}
	catch (Exception $e)
	{
		die("Failed to run query: " . $e->getMessage());
	}

    //Reset the session variables so on refresh the fields are populated correctly
	$_SESSION['email'] = $email;
	$_SESSION['name'] = $name;
	$_SESSION['identifier'] = $identifier;

	//Let the user know their information was updated
	$_SESSION['profileUpdate'] = '<div class="alert alert-success"><span>Successfully updated your user information</span></div>';

	sleep(1); //Seconds to wait
	header("Location: ../profile.php");
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function getMyRank()
{
	$id = $_GET['unit'];
	$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    //Get all ranks
    $query = "SELECT ranks.rank_name FROM ranks_users INNER JOIN ranks ON ranks.rank_id=ranks_users.rank_id WHERE ranks_users.user_id = '$id';";

    $result=mysqli_query($link, $query);

	while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
	{
		echo $row[0];
	}
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function getRanks()
{
	$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    //Get all ranks
    $query = "SELECT * FROM ranks";

    $result=mysqli_query($link, $query);

	while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
	{
		if ($row[2] == "1")
		{
			echo '<option value="'.$row[1].'">'.$row[1].'</option>';
		}
		else if ($row[2] == "0")
		{
			echo '<option value="'.$row[1].'" style="background: #969aa3; color: #ffffff;" disabled>'.$row[1].'</option>';
		}
	}
}


//////////////////////////////////////////////////////
//                  register                        //
//////////////////////////////////////////////////////

    $name = $_POST['uname'];
    $email = $_POST['email'];
    $identifier = $_POST['identifier'];
    $divisions = array();
    foreach ($_POST['division'] as $selectedOption)
    {
        array_push($divisions, $selectedOption);
    }

    if($_POST['password'] !== $_POST['password1'])
    {
        session_start();
        $_SESSION['register_error'] = "Passwords do not match";
        sleep(1);
        header("Location:./index.php#signup");
        exit();

    }

    //Hash the password
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);


    //Establish database connection
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

	if (!$link) {
		die('Could not connect: ' .mysql_error()); //TODO: A public function to send me an email when this occurs
	}

    //Check to see if the email has already been used
    $query = "SELECT email from users where email = \"".$email."\"";
    $result = mysqli_query($link, $query);
	$num_rows = $result->num_rows;

	if ($num_rows>0)
	{
		session_start();
        $_SESSION['register_error'] = "Email already exists";
        sleep(1);
        header("Location:./index.php#signup");
        exit();
	}

    $query = "INSERT INTO users (name, email, password, identifier) VALUES (?, ?, ?, ?)";


	try {
		$stmt = mysqli_prepare($link, $query);
		mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $password, $identifier);
		$result = mysqli_stmt_execute($stmt);

		if ($result == FALSE) {
			die(mysqli_error($link));
		}
	}
	catch (Exception $e)
	{
		die("Failed to run query: " . $e->getMessage()); //TODO: A public function to send me an email when this occurs should be made
	}



    /*Add user to departments they requested, temporary table */
    /*This is really inefficient. There should be a better way*/

    foreach($divisions as $division)
    {
        if($division == "communications")
        {$division = "1";}
        elseif($division == "ems")
            {$division = "2";}
        elseif($division == "fire")
            {$division = "3";}
        elseif($division == "highway")
            {$division = "4";}
        elseif($division == "police")
            {$division = "5";}
        elseif($division == "sheriff")
            {$division = "6";}
        elseif($division == "civilian")
            {$division = "7";}
        elseif($division == "state")
            {$division = "9";}

        $query = "INSERT INTO user_departments_temp (user_id, department_id)
              SELECT id , ?
              FROM users
              WHERE email = ?";

        try {
            $stmt = mysqli_prepare($link, $query);
            mysqli_stmt_bind_param($stmt, "is", $division, $email);
            $result = mysqli_stmt_execute($stmt);

            if ($result == FALSE) {
                die(mysqli_error($link));
            }
        }
        catch (Exception $e)
        {
            die("Failed to run query: " . $e->getMessage()); //TODO: A public function to send admins an email when this occurs should be made
        }
    }


    mysqli_close($link);

    session_start();
    $_SESSION['register_success'] = "Successfully requested access. Please wait for an administrator to approve your request.";
    sleep(1);
    header("Location:./index.php#signup");

}

//////////////////////////////////////////////////////
//                responderActions                  //
//////////////////////////////////////////////////////

/* Handle POST requests */
class mdtTools{}
if (isset($_POST['updateCallsign'])){
    updateCallsign();
}

/* Handle GET requests */
if (isset($_GET['getStatus']))
{
    getStatus();
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function updateCallsign()
{
    $details = $_POST['details'];
    $details = str_replace('+', ' ', $details);
    $details = str_replace('%7C', '|', $details);
    $detailsArr = explode("&", $details);
    //Now, each item in the details array needs to be exploded by = to get the value
    $callsign = explode("=", $detailsArr[0])[1];

    //Use the user's session ID
    session_start();
    $identifier = $_SESSION['identifier'];

    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

	if (!$link) {
		die('Could not connect: ' .mysql_error());
	}

    $sql = "UPDATE `active_users` SET `callsign` = ?, status = '0', status_detail='2' WHERE `active_users`.`identifier` = ?";

	try {
		$stmt = mysqli_prepare($link, $sql);
		mysqli_stmt_bind_param($stmt, "ss", $callsign, $identifier);
		$result = mysqli_stmt_execute($stmt);

		if ($result == FALSE) {
			die(mysqli_error($link));
		}
	}
	catch (Exception $e)
	{
		die("Failed to run query: " . $e->getMessage()); //TODO: A public function to send me an email when this occurs should be made
	}
	mysqli_close($link);

    $_SESSION['callsign'] = $callsign;

    echo "SUCCESS";
}
/**#@+
 * public function getVehicleMakes()
 *
 * Querys database to retrieve all vehicle makes.
 *
 * @since 1.0a RC1
 */
public function getStatus()
{
    session_start();
    $identifier = $_SESSION['identifier'];

    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $sql = "SELECT status_detail FROM active_users WHERE identifier = \"$identifier\"";

    $result=mysqli_query($link, $sql);

    while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
    {
        $statusDetail = $row[0];
    }

    $sql = "SELECT status_text FROM statuses WHERE status_id = \"$statusDetail\"";

    $result=mysqli_query($link, $sql);

    while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
    {
        $statusText = $row[0];
    }

    echo $statusText;
}


/**#@+
 * public function mdtGetVehicleBOLOS()
 *
 * Querys database to retrieve all currently entered Vehicle BOLOS.
 *
 * @since 1.0a RC2
 */

public function mdtGetVehiclesBOLOS()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $query = "SELECT bolos_vehicles.* FROM bolos_vehicles";

    $result=mysqli_query($link, $query);

    $num_rows = $result->num_rows;

    if($num_rows == 0)
    {
        echo "<div class=\"alert alert-info\"><span>Good work! No Active Vehicle BOLOS.</span></div>";
    }
    else
    {
        echo '
            <table id="bolo_board" class="table table-striped table-bordered bolo_board">
            <thead>
                <tr>
                  <th style="text-align: center;" >Vehicle Make</th>
                  <th>Vehicle Model</th>
                  <th>Vehicle Plate</th>
                  <th>Primary Color</th>
                  <th>Secondary Color</th>
                  <th>Reason Wanted</th>
                  <th>Last Seen</th>
                </tr>
            </thead>
            <tbody>
        ';

        while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
        {

            echo '
            <tr>
                <td>'.$row[1].'</td>
                <td>'.$row[2].'</td>
                <td>'.$row[3].'</td>
                <td>'.$row[4].'</td>
                <td>'.$row[5].'</td>
                <td>'.$row[6].'</td>
                <td>'.$row[7].'</td>
            </tr>
            ';
        }

        echo '
            </tbody>
            </table>
        ';
    }
}

/**#@+
 * public function mdtGetPersonsBOLOS()
 *
 * Querys database to retrieve all currently entered Person BOLOS.
 *
 * @since 1.0a RC2
 */

public function mdtGetPersonsBOLOS()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$link) {
        die('Could not connect: ' .mysql_error());
    }

    $query = "SELECT bolos_persons.* FROM bolos_persons";

    $result=mysqli_query($link, $query);

    $num_rows = $result->num_rows;

    if($num_rows == 0)
    {
        echo "<div class=\"alert alert-info\"><span>Good work! No Active Persons BOLOS.</span></div>";
    }
    else
    {
        echo '
            <table id="bolo_board" class="table table-striped table-bordered">
            <thead>
                <tr>
                  <th>First Name</th>
                  <th>Last Name</th>
                  <th>Gender</th>
                  <th>Physical Description</th>
                  <th>Reason Wanted</th>
                  <th>Last Seen</th>
                </tr>
            </thead>
            <tbody>
        ';

        while($row = mysqli_fetch_array($result, MYSQLI_BOTH))
        {
            echo '
            <tr>
                <td>'.$row[1].'</td>
                <td>'.$row[2].'</td>
                <td>'.$row[4].'</td>
                <td>'.$row[3].'</td>
                <td>'.$row[5].'</td>
                <td>'.$row[6].'</td>
            </tr>
            ';
        }

        echo '
            </tbody>
            </table>
        ';
    }
}
}
?>
