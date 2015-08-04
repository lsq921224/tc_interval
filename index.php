<?php
	$datebegin = !is_null($_GET["datebegin"]) ? $_GET["datebegin"] : date("Y-m-d", strtotime(date('Y-m-01')));
	//$dateend = !is_null($_GET["dateend"]) ? $_GET["dateend"] : date("Y-m-d",strtotime( '-1 days' ));
	$dateend = !is_null($_GET["dateend"]) ? $_GET["dateend"] : date("Y-m-d");
	$clientid = $_GET["clientid"];
	$worktypeid = $_GET["worktypeid"];
	$moduleid = $_GET["moduleid"];
	$personid = $_GET["personid"];
	$projectid = $_GET["projectid"];
	$managerid = $_GET["managerid"];
	
	$username = '6cea4jyp5he';
	$password = 'X';
	$URL = 'https://api.myintervals.com/time/';
	$work_type_url = 'https://api.myintervals.com/worktype';
	$client_url = 'https://api.myintervals.com/client';
	$module_url = 'https://api.myintervals.com/module';
	$person_url = 'https://api.myintervals.com/person';
	$project_url = 'https://api.myintervals.com/project';
	$manager_url = 'https://api.myintervals.com/manager';

	$data = http_build_query(array('datebegin' => $datebegin, 'dateend' => $dateend, 'limit' => 2147483647));
	if (!is_null($worktypeid))
	{
		$w = 'worktypeid=' . $worktypeid;
		$data = $data . '&'. $w;
	}
	if (!is_null($clientid))
	{
		$c = 'clientid=' . $clientid;
		$data = $data . '&'. $c;
	}
	if (!is_null($moduleid))
	{
		$m = 'moduleid=' . $moduleid;
		$data = $data . '&'. $m;
	}
	if (!is_null($personid))
	{
		$m = 'personid=' . $personid;
		$data = $data . '&'. $m;
	}
	if (!is_null($projectid))
	{
		$m = 'projectid=' . $projectid;
		$data = $data . '&'. $m;
	}
	if (!is_null($managerid))
	{
		$m = http_build_query(array('manager' => $managerid));
		$data = $data . '&'. $m;
	}
	$ch = curl_init();
	curl_setopt($curl, CURLOPT_HEADER, array("Accept: application/json","Content-Type:       application/json"));
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_URL, $URL.'?'.$data);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
	$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	
	$result = curl_exec($ch);
	curl_setopt($ch, CURLOPT_URL, $work_type_url.'?limit=2147483647');
	$work_types = curl_exec($ch);
	curl_setopt($ch, CURLOPT_URL, $client_url.'?limit=2147483647');
	$clients = curl_exec($ch);
	curl_setopt($ch, CURLOPT_URL, $module_url.'?limit=2147483647');
	$modules = curl_exec($ch);
	curl_setopt($ch, CURLOPT_URL, $person_url.'?limit=2147483647');
	$persons = curl_exec($ch);
	curl_setopt($ch, CURLOPT_URL, $project_url.'?limit=2147483647');
	$projects = curl_exec($ch);
	curl_setopt($ch, CURLOPT_URL, $manager_url.'?limit=2147483647');
	$managers = curl_exec($ch);
	curl_close($ch);
 ?>
 
<html>
<meta charset="utf-8">
<head>
  <link rel="stylesheet" href="css/style.css"/>
  <link rel="stylesheet" href="css/dc.css"/>
  <link rel="stylesheet" href="css/bootstrap.css"/>
  <link rel="stylesheet" href="css/bootstrap-multiselect.css"/>
  <link rel="stylesheet" href="css/daterangepicker.css"/>
  <script src="js/jquery-2.1.4.min.js"></script>
  <script src="js/jquery.ba-bbq.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/bootstrap-multiselect.js"></script>
  <script src="js/moment.js"></script>
  <script src="js/daterangepicker.js"></script>
  <script src="js/d3.v3.js"></script>
  <script src="js/crossfilter.js"></script>
  <script src="js/dc.js"></script>
  <script src="js/jquery.csv.js"></script>
  <script src="js/FileSaver.min.js"></script>
  <script src="js/json2csv.js"></script>

<script> 
 var data = <?php echo json_encode($data)?>;
 var work_type_json = <?php echo json_encode($work_types)?>;
 var work_type_json_obj = JSON.parse(work_type_json)['worktype'];
 var clients_json = <?php echo json_encode($clients)?>;
 var clients_json_obj = JSON.parse(clients_json)['client'];
 var module_json = <?php echo json_encode($modules)?>;
 var module_json_obj = JSON.parse(module_json)['module'];
 var person_json = <?php echo json_encode($persons)?>;
 var person_json_obj = JSON.parse(person_json)['person'];
 var project_json = <?php echo json_encode($projects)?>;
 var project_json_obj = JSON.parse(project_json)['project'];
 var manager_json = <?php echo json_encode($managers)?>;
 var manager_json_obj = JSON.parse(manager_json)['manager'];
 //console.log(work_type_json_obj);
 
 var work_type_selected_all = false;
 var client_selected_all = false;
 var module_selected_all = false;
 var person_selected_all = false;
 var project_selected_all = false;

var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};

$(document).ready(function(){
	var url = window.location.href;
	
	if (url[url.length - 1] != '/')
	{
		if (url[url.length - 1] != '&')
			url += '&';
	}
	else
		url += '?';
	
	var wid = getUrlParameter('worktypeid');
	var cid = getUrlParameter('clientid');
	var mid = getUrlParameter('moduleid');
	var pid = getUrlParameter('personid');
	var pjid = getUrlParameter('projectid');
	var mgid = getUrlParameter('managerid');

	var whtml = "";
	var chtml = "";
	var mhtml = "";
	var phtml = "";
	var pjhtml = "";


	//$('#work_type_drop').append('<option><a href="'+ work_type_all +'">'+ "All" +'</a></option>');
		 
	//$('#client_drop ul').append('<li><a href="'+ client_all +'">'+ "All" +'</a></li>');
		
	//$('#module_drop ul').append('<li><a href="'+ module_all +'">'+ "All" +'</a></li>');
	
	//$('#person_drop ul').append('<li><a href="'+ person_all +'">'+ "All" +'</a></li>');
	
	//$('#project_drop ul').append('<li><a href="'+ project_all +'">'+ "All" +'</a></li>');
	
	//$('#manager_drop ul').append('<li><a href="'+ manager_all +'">'+ "All" +'</a></li>');
	
	for( index in work_type_json_obj )
    {
	 if (wid)
	 {
		if (wid.indexOf(work_type_json_obj[index].id) > -1)
		{
			$('#work_type_drop').append('<option selected = "selected" value="' + work_type_json_obj[index].id + '">' +work_type_json_obj[index].name+'</option>');
			whtml += work_type_json_obj[index].name + ' | ';
		}
		else
			$('#work_type_drop').append('<option value="' + work_type_json_obj[index].id + '">' +work_type_json_obj[index].name+'</option>');
	 }
	 else 
		$('#work_type_drop').append('<option value="' + work_type_json_obj[index].id + '">' +work_type_json_obj[index].name+'</option>');
    }
	if (whtml == "")
		whtml = "All";
	$('#wt').html("Work Type - " + whtml);

	for( index in clients_json_obj )
    {
	if (cid)
	 {
		if (cid.indexOf(clients_json_obj[index].id) > -1)
		{
			$('#client_drop').append('<option selected = "selected" value="' + clients_json_obj[index].id + '">' +clients_json_obj[index].name+'</option>');
			chtml += clients_json_obj[index].name + ' | ';
		}
		else
			$('#client_drop').append('<option value="' + clients_json_obj[index].id + '">' +clients_json_obj[index].name+'</option>');
	 }
	 else 
		$('#client_drop').append('<option value="' + clients_json_obj[index].id + '">' +clients_json_obj[index].name+'</option>');
    }
	if (chtml == "")
		chtml = "All";	
	$('#ct').html("Client - " + chtml);

	
	for( index in module_json_obj )
    {
	  if (mid)
	 {
		if (mid.indexOf(module_json_obj[index].id) > -1)
		{
			$('#module_drop').append('<option selected = "selected" value="' + module_json_obj[index].id + '">' +module_json_obj[index].name+'</option>');
			mhtml += module_json_obj[index].name + ' | ';
		}
		else
			$('#module_drop').append('<option value="' + module_json_obj[index].id + '">' +module_json_obj[index].name+'</option>');
	 }
	 else 
		$('#module_drop').append('<option value="' + module_json_obj[index].id + '">' +module_json_obj[index].name+'</option>');
    }
	if (mhtml == "")
		mhtml = "All";
	$('#md').html("Module - " + mhtml);
	
	for( index in person_json_obj )
    {
     if (pid)
	 {
		if (pid.indexOf(person_json_obj[index].id) > -1)
		{
			$('#person_drop').append('<option selected = "selected" value="' + person_json_obj[index].id + '">' +person_json_obj[index].firstname　+ " " + person_json_obj[index].lastname +'</option>');
			phtml += person_json_obj[index].firstname +　" " + person_json_obj[index].lastname+ ' | ';
		}
		else
			$('#person_drop').append('<option value="' + person_json_obj[index].id + '">' +person_json_obj[index].firstname+ " " + person_json_obj[index].lastname + '</option>');
	 }
	 else 
		$('#person_drop').append('<option value="' + person_json_obj[index].id + '">' +person_json_obj[index].firstname + " " +person_json_obj[index].lastname + '</option>');
   }
   if (phtml == "")
	   phtml = "All";
   $('#ps').html("Person - " + phtml);
	
	for( index in project_json_obj )
    {
	if (pjid)
	 {
		if (pjid.indexOf(project_json_obj[index].id) > -1)
		{
			$('#project_drop').append('<option selected = "selected" value="' + project_json_obj[index].id + '">' +project_json_obj[index].name+'</option>');
			pjhtml += project_json_obj[index].name + ' | ';
		}
		else
			$('#project_drop').append('<option value="' + project_json_obj[index].id + '">' +project_json_obj[index].name+'</option>');
	 }
	 else 
		$('#project_drop').append('<option value="' + project_json_obj[index].id + '">' +project_json_obj[index].name+'</option>');
    }
	if (pjhtml == "")
	   pjhtml = "All";
   $('#pj').html("Project - " + pjhtml);
	
	for( index in manager_json_obj )
    {
		if (pid)
		{
		var h = $.param.querystring(url, 'managerid='+ manager_json_obj[index].id);
		 $('#manager_drop ul').append('<li><a href="'+ h +'">'+ manager_json_obj[index].firstname + ' ' + manager_json_obj[index].lastname +'</a></li>');
		 if (manager_json_obj[index].id == pid)
		  $('#mg').html("Manager - " + manager_json_obj[index].firstname + " " + manager_json_obj[index].lastname);
		}
		else
      $('#manager_drop ul').append('<li><a href="' + url + 'managerid='+ manager_json_obj[index].id + '">'+ manager_json_obj[index].firstname + " " + manager_json_obj[index].lastname +'</a></li>');
    }
	
	$("#s_json").click(function() {
		var json_string = JSON.stringify(person_d.top(Infinity));
		var blob = new Blob([json_string], {type: "application/json"});
		saveAs(blob, "interval_json_" + $('#options').text() + ".json");

	});
	
	
	$("#s_csv").click(function() {
		
		var json_obj = person_d.top(Infinity);
		var input = json_obj;
		if (!input) {
			return;
		}
		var json = json_obj;
		var inArray = arrayFrom(json);
		var outArray = [];
		for (var row in inArray)
		  outArray[outArray.length] = parse_object(inArray[row]);
		
		var csv = $.csv.fromObjects(outArray);
        var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
		saveAs(blob, "interval_csv_" + $('#options').text() + ".csv");
	});
	
	$('#work_type_drop').multiselect({
		includeSelectAllOption: true,
		enableFiltering: true,
		buttonText: function(options, select) {
                    return 'Work Type';
            },
		onSelectAll: function() {
           work_type_selected_all = true;
        }
	});
	
	$('#client_drop').multiselect({
		includeSelectAllOption: true,
		enableFiltering: true,
		buttonText: function(options, select) {
                    return 'Client';
            },
		onSelectAll: function() {
           client_selected_all = true;
        }
	});
	
	$('#module_drop').multiselect({
		includeSelectAllOption: true,
		enableFiltering: true,
		buttonText: function(options, select) {
                    return 'Module';
            },
		onSelectAll: function() {
           module_selected_all = true;
        }
	});
	
	$('#person_drop').multiselect({
		includeSelectAllOption: true,
		enableFiltering: true,
		buttonText: function(options, select) {
                    return 'Person';
            },
		onSelectAll: function() {
           person_selected_all = true;
        }
	});
	
	$('#project_drop').multiselect({
		includeSelectAllOption: true,
		enableFiltering: true,
		buttonText: function(options, select) {
                    return 'Project';
            },
		onSelectAll: function() {
           project_selected_all = true;
        }
	});
	
	$("#export_drop")
	.hover(
	function(){
		$('#message').html("Export selected " + active_d.top(Infinity).length + " objects to file");
		$('#message').show();
	},
	function(){
		$('#message').hide();
	}
	);
	
	$( "#callapi" ).submit(function( event ) {
		var parameter = "?";
		var url = window.location.href;
		
		var work_type_all = url.replace(/&?worktypeid=([^&]$|[^&]*)/i, "");
		var client_all = url.replace(/&?clientid=([^&]$|[^&]*)/i, "");
		var module_all = url.replace(/&?moduleid=([^&]$|[^&]*)/i, "");
		var person_all = url.replace(/&?personid=([^&]$|[^&]*)/i, "");
		var project_all = url.replace(/&?projectid=([^&]$|[^&]*)/i, "");
		var manager_all = url.replace(/&?managerid=([^&]$|[^&]*)/i, "");
	
		var work_types = $('#work_type_drop option:selected').map(function(a, item){return item.value;});
		var clients = $('#client_drop option:selected').map(function(a, item){return item.value;});
		var modules = $('#module_drop option:selected').map(function(a, item){return item.value;});
		var persons = $('#person_drop option:selected').map(function(a, item){return item.value;});
		var projects = $('#project_drop option:selected').map(function(a, item){return item.value;});

		if (work_types.length > 0)
		{
			if (!work_type_selected_all)
			{
				parameter = parameter + "&worktypeid=";
				for (var i = 0; i < work_types.length; i++)
				{
					if (i != work_types.length - 1)
						parameter += work_types[i] + ',';
					else
						parameter += work_types[i];
				}
			}
			else
			{
				url = work_type_all;
			}
		}
		
		if (clients.length > 0)
		{
			if (!client_selected_all)
			{
				parameter = parameter + "&clientid=";
				for (var i = 0; i < clients.length; i++)
				{
					if (i != clients.length - 1)
						parameter += clients[i] + ',';
					else
				parameter += clients[i];
			}
			}
			else
			{
				url = client_all;
			}
		}
		
		if (modules.length > 0)
		{
			if (!module_selected_all)
			{
				parameter = parameter + "&moduleid=";
				for (var i = 0; i < modules.length; i++)
				{
					if (i != modules.length - 1)
						parameter += modules[i] + ',';
					else
				parameter += modules[i];
			}
			}
			else
			{
				url = module_all;
			}
		}
		
		if (persons.length > 0)
		{
			if (!person_selected_all)
			{
				parameter = parameter + "&personid=";
				for (var i = 0; i < persons.length; i++)
				{
					if (i != persons.length - 1)
						parameter += persons[i] + ',';
					else
				parameter += persons[i];
			}
			}
			else
			{
				url = person_all;
			}
		}
		
		if (projects.length > 0)
		{
			if (!project_selected_all)
			{
				parameter = parameter + "&projectid=";
				for (var i = 0; i < projects.length; i++)
				{
					if (i != projects.length - 1)
						parameter += projects[i] + ',';
					else
				parameter += projects[i];
			}
			}
			else
			{
				url = project_all;
			}
		}
		
		//alert(parameter);
		window.location.href = $.param.querystring(url,parameter);
		event.preventDefault();
	});
});
</script>
</head>

<body>
<form id = "callapi" class="form-horizontal" method="GET" action="">
<div id="selections">

<!-- btn-group --> 
  				<select id="person_drop" class="btn-group" multiple ="multiple" >Person
              </select><!-- /btn-group -->
			  
 <!-- btn-group --> 
  				<select id="work_type_drop" class="btn-group" multiple="multiple">Work Type
              </select><!-- /btn-group -->


 <!-- btn-group --> <select id="client_drop" class="btn-group" multiple="multiple">Client</select>
 

 <!-- btn-group --> <select id="module_drop" class="btn-group" multiple="multiple">Module</select><!-- /btn-group -->
			  
<!-- btn-group --> <select id="project_drop" class="btn-group" multiple="multiple">Project</select><!-- /btn-group -->
			  
<!-- btn-group --> <div id="manager_drop" class="btn-group"><button type="button" class="btn dropdown-toggle" data-toggle="dropdown">Manager</button><ul class="dropdown-menu">

                  </ul>
              </div><!-- /btn-group -->

			  
<!-- btn-group --> 

			  <div id="reportrange" class="pull-left" style="cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; margin-right:4px; height: 34px; border-radius: 4px;" >
    <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
    <span></span> <b class="caret"></b>
</div>
    <button type="submit" class="btn btn-default">Submit</button>
	
<div id="export_drop" class="btn-group"><button type="button" class="btn dropdown-toggle" data-toggle="dropdown">Export</button><ul class="dropdown-menu">
	<li><a id = "s_json">JSON</a></li>
	<li><a id = "s_csv">CSV</a></li>
                  </ul>
				  <span class ="alert alert-info" id='message' style ="margin-left:4px;" hidden></span>
              </div><!-- /btn-group -->
</div>
</form>

<script type="text/javascript">
$(function() {

    function cb(start, end) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    }
    cb(moment("<?php echo $datebegin;?>"),moment("<?php echo $dateend;?>"));

    $('#reportrange').daterangepicker({
        ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
		   'This Year': [moment().startOf('year'), moment()]   
	
        }
    }, cb);

	$('#reportrange').on('apply.daterangepicker', function(ev, picker) {
		
	console.log(picker.startDate.format('YYYY-MM-DD'));
	console.log(picker.endDate.format('YYYY-MM-DD'));
	window.location.href = $.param.querystring(window.location.href,"?datebegin=" + picker.startDate.format('YYYY-MM-DD') + "&dateend=" + picker.endDate.format('YYYY-MM-DD'));

});
});
</script>

</div>

<div id = 'options'>
<h3 id = "it">Intervals - <a href = "/interval">reset</a><?php 
if (is_null($worktypeid))
	$worktypeid = "All";
if (is_null($clientid))
	$clientid = "All";
if (is_null($moduleid))
	$moduleid = "All";
if (is_null($personid))
	$personid = "All";
if (is_null($projectid))
	$projectid = "All";
if (is_null($managerid))
	$managerid = "All";
echo "<br>From ". $datebegin. " to ".$dateend;?> </h3>
<h3 class = "names" id = "ps">Person - <?php echo $personid;?> </h3>
<h3 class = "names" id = "wt">Work Type - <?php echo $worktypeid;?> </h3>
<h3 class = "names" id = "ct">Client - <?php echo $clientid; ?></h3>
<h3 class = "names" id = "md">Module - <?php echo $moduleid; ?></h3>
<h3 class = "names" id = "pj">Project - <?php echo $projectid; ?></h3>
<h3 class = "names" id = "mg">Manager - <?php echo $managerid; ?></h3>
</div>
<h3 id = "nresults" style = "margin-bottom: 0px;"></h3>
<h3 id = "sresults" style = "margin-top: 0px;"></h3>
<div id = "error" hidden><p>Error Getting data from myinterval API, may be requesting too many data.</p></div>
<div id = "norsults" hidden><p>No data to show!</p></div>
<div id = "charts">
<div id = "person"><p>Person - 
              <a class="reset" href="javascript:person_chart.filterAll();dc.redrawAll();" style = "display: none;">reset</a> </p>
</div>
<div id = "time"><p>Time - 
<a class="reset" href="javascript:time_chart.filterAll();dc.redrawAll();" style = "display: none;">reset</a> </p>
<br>
<p id = "total_time">Total: Loading..</p> 
</div>

<div id = "work_type"><p>Work Type - 
<a class="reset" href="javascript:work_type_chart.filterAll();dc.redrawAll();" style = "display: none;">reset</a> 
</p></div>
<div id = "client"><p>Client - 
<a class="reset" href="javascript:client_chart.filterAll();dc.redrawAll();" style = "display: none;">reset</a> 
</p></div>
<div id = "module"><p>Module - 
<a class="reset" href="javascript:module_chart.filterAll();dc.redrawAll();" style = "display: none;">reset</a> 
</p></div>
<div id = "project"><p>Project - 
<a class="reset" href="javascript:project_chart.filterAll();dc.redrawAll();" style = "display: none;">reset</a> 
</p></div>

<div id = "active"><p>Active - 
<a class="reset" href="javascript:active_chart.filterAll();dc.redrawAll();" style = "display: none;">reset</a> 
</p></div>
<div id = "billable"><p>Billable - 
<a class="reset" href="javascript:billable_chart.filterAll();dc.redrawAll();" style = "display: none;">reset</a> 
</p></div>
<div id = "client_active"><p>Client Active - 
<a class="reset" href="javascript:client_active_chart.filterAll();dc.redrawAll();" style = "display: none;">reset</a> 
</p></div>
</div>


<script>
/*
var tok = "6cea4jyp5he:X";
var token  = btoa(tok);
//console.log(token);

var json_raw = <?php echo json_encode($result)?>;
url = "https://api.myintervals.com/time/";

authInfo = "Basic " + token;
console.log(authInfo);
$.support.cors = true;
$.ajax({

     url: url,
     //beforeSend: function (xhr) { xhr.setRequestHeader ("Authorization", authInfo); },
     headers: {authorization: authInfo, nocache:Math.random()},
     type: "GET",
     async: false,
     dataType: "json",
     crossDomain: true,
     success:  function(html){
         console.log(html);
     },
     error: function(html){
         console.log('error');
     }
 });
*/
//var json_d = json_raw["response"].docs;
//console.log(json_raw);

var json_raw = <?php echo json_encode($result)?>;
var json_d = JSON.parse(json_raw);
var length = json_d['listcount'];
var times = json_d['time'];
$('#nresults').html("Number of Results: "+ length);
if (json_d == false)
{
	$('#charts').hide();
	$('#error').show();
}
else if (length == 0)
{
	$('#charts').hide();
	$('#norsults').show();
}
console.log(json_d);
var active_chart = dc.pieChart('#active');
var billable_chart = dc.pieChart('#billable');
var client_active_chart = dc.pieChart('#client_active');
var client_chart = dc.rowChart('#client');
var person_chart = dc.rowChart('#person');
var module_chart = dc.rowChart('#module');
var project_chart = dc.rowChart('#project');
var work_type_chart = dc.rowChart('#work_type');
var time_chart = dc.barChart('#time');

var charts = [time_chart,active_chart, billable_chart, client_active_chart, client_chart, person_chart, module_chart, project_chart, work_type_chart];

//var eng_lines = json_d;
//eng_lines.forEach(function(x){
//	x.totalChatLines = parseInt(x.totalChatLines);
//	x.totalAgentLines = parseInt(x.totalAgentLines);
//	if (!x.agentGroupName)
//		x.agentGroupName = "N/A";
//});

var interval_cf = crossfilter(times);

var active_d = interval_cf.dimension(function(d){return d.active == 't'? "TRUE" : "FALSE";});
var count_by_active = active_d.group();


active_chart
	.width(200)
	.height(150)
	.slicesCap(2)
	.innerRadius(20)
	.dimension(active_d)
	.group(count_by_active)
	.legend(dc.legend());

var billable_d = interval_cf.dimension(function(d){return d.billable == 't'? "TRUE":"FALSE";});
var count_by_billable = billable_d.group();


billable_chart
	.width(200)
	.height(150)
	.slicesCap(2)
	.innerRadius(20)
	.dimension(billable_d)
	.group(count_by_billable)
	.legend(dc.legend());

var client_active_d = interval_cf.dimension(function(d){return d.clientactive == 't'?"TRUE":"FALSE";});
var count_by_client_active = client_active_d.group();

client_active_chart
	.width(200)
	.height(150)
	.slicesCap(2)
	.innerRadius(20)
	.dimension(client_active_d)
	.group(count_by_client_active)
	.legend(dc.legend());
	
var client_d = interval_cf.dimension(function(d){return d.client.replace(/&amp;/g, '&');});
var count_by_client = client_d.group();
var client_height = count_by_client.all().length * 20 + 200;

client_chart
	.width(400)
	.height(client_height)
	.dimension(client_d)
	.group(count_by_client)
	.elasticX(true);
	
var person_d = interval_cf.dimension(function(d){return d.person;});
var count_by_person = person_d.group();
var person_height = count_by_person.all().length * 20 + 200;

person_chart
	.width(300)
	.height(person_height)
	.dimension(person_d)
	.group(count_by_person)
	.elasticX(true);

var module_d = interval_cf.dimension(function(d){return d.module.replace(/&amp;/g, '&');});
var count_by_module = module_d.group();
var module_height = count_by_module.all().length * 20 + 200;

module_chart
	.width(400)
	.height(module_height)
	.dimension(module_d)
	.group(count_by_module)
	.elasticX(true);

var project_d = interval_cf.dimension(function(d){return d.project.replace(/&amp;/g, '&');});
var count_by_project = project_d.group();
var project_height = count_by_project.all().length * 20 + 200;

project_chart
	.width(400)
	.height(project_height)
	.dimension(project_d)
	.group(count_by_project)
	.elasticX(true);
	
var work_type_d = interval_cf.dimension(function(d){return d.worktype.replace(/&amp;/g, '&');});
var count_by_work_type = work_type_d.group();
var work_type_height = count_by_work_type.all().length * 20 + 200;

work_type_chart
	.width(400)
	.height(work_type_height)
	.dimension(work_type_d)
	.group(count_by_work_type)
	.elasticX(true);
	
var time_d = interval_cf.dimension(function(d){return d.time;});
var count_by_time = time_d.group();

time_chart
	.width(400)
	.height(250)
	.dimension(time_d)
	.group(count_by_time)
    .x(d3.scale.linear().domain([0,50]))
	.xAxisLabel("Hrs")
	.elasticX(true);

function remove_small_agents_groups(source_group) {
    return {
        all:function () {
            return source_group.all().filter(function(d) {
                return d.value > AGENT_GROUP_FILTER;
            });
        }
    };
}

var total_default = interval_cf.groupAll().reduceSum(function(d){return d.time;}).value();
var num_default = interval_cf.groupAll().value();
var avg_default = total_default / num_default;
$("#total_time").html("Total: " + total_default.toFixed(2) + " hours&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Avg: " + avg_default.toFixed(2) + " hours");
dc.renderAll();

for (var i = 0; i < charts.length; i++)
{
charts[i].on("filtered", function(chart, filter){
		var total = interval_cf.groupAll().reduceSum(function(d){return d.time;}).value();
		var num = interval_cf.groupAll().value();
		var avg = total / num;
		$("#total_time").html("Total: " + total.toFixed(2) + " hours&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Avg: " + avg.toFixed(2) + " hours");
		$("#sresults").html("Number of selected results: " + num);
});
}



</script>

</body>
</html>
