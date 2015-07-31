<?php
	$datebegin = !is_null($_GET["datebegin"]) ? $_GET["datebegin"] : date("Y-m-d", strtotime(date('Y-m-01')));
	$dateend = !is_null($_GET["dateend"]) ? $_GET["dateend"] : date("Y-m-d",strtotime( '-1 days' ));
	$clientid = $_GET["clientid"];
	$worktypeid = $_GET["worktypeid"];
	$moduleid = $_GET["moduleid"];
	
	
	$username = '6cea4jyp5he';
	$password = 'X';
	$URL = 'https://api.myintervals.com/time/';
	$work_type_url = 'https://api.myintervals.com/worktype';
	$client_url = 'https://api.myintervals.com/client';
	$module_url = 'https://api.myintervals.com/module';

	$data = http_build_query(array('datebegin' => $datebegin, 'dateend' => $dateend, 'limit' => 2147483647));
	if (!is_null($worktypeid))
	{
		$w = http_build_query(array('worktypeid' => $worktypeid));
		$data = $data . '&'. $w;
	}
	if (!is_null($clientid))
	{
		$c = http_build_query(array('clientid' => $clientid));
		$data = $data . '&'. $c;
	}
	if (!is_null($moduleid))
	{
		$m = http_build_query(array('moduleid' => $moduleid));
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
	curl_close($ch);
 ?>
 
<html>
<meta charset="utf-8">
<head>
  <link rel="stylesheet" href="css/style.css"/>
  <link rel="stylesheet" href="css/dc.css"/>
  <link rel="stylesheet" href="css/bootstrap.css"/>
  <link rel="stylesheet" href="css/daterangepicker.css"/>
  <script src="js/jquery-2.1.4.min.js"></script>
  <script src="js/jquery.ba-bbq.js"></script>
  <script src="js/bootstrap.min.js"></script>
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
 //console.log(work_type_json_obj);
 
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
	
	var work_type_all = url.replace(/&?worktypeid=([^&]$|[^&]*)/i, "");
	var client_all = url.replace(/&?clientid=([^&]$|[^&]*)/i, "");
	var module_all = url.replace(/&?moduleid=([^&]$|[^&]*)/i, "");
	
	$('#work_type_drop ul').append('<li><a href="'+ work_type_all +'">'+ "All" +'</a></li>');
		 
	$('#client_drop ul').append('<li><a href="'+ client_all +'">'+ "All" +'</a></li>');
		
	$('#module_drop ul').append('<li><a href="'+ module_all +'">'+ "All" +'</a></li>');
	
	for( index in work_type_json_obj )
    {
	if (wid)
	  {
		var h = $.param.querystring(url, 'worktypeid='+ work_type_json_obj[index].id);
		 $('#work_type_drop ul').append('<li><a href="'+ h +'">'+ work_type_json_obj[index].name +'</a></li>');
		if (work_type_json_obj[index].id == wid)
			$('#wt').html("Work Type - " + work_type_json_obj[index].name);
	  }
	  else
		$('#work_type_drop ul').append('<li><a href="'+ url + 'worktypeid=' + work_type_json_obj[index].id +'">'+ work_type_json_obj[index].name +'</a></li>');
	 
    }
	
	for( index in clients_json_obj )
    {
		if (cid)
		{
		var h = $.param.querystring(url, 'clientid='+ clients_json_obj[index].id);
		 $('#client_drop ul').append('<li><a href="'+ h +'">'+ clients_json_obj[index].name +'</a></li>');
		 if (clients_json_obj[index].id == cid)
			 $('#ct').html("Client - " + clients_json_obj[index].name);
		}
		else
      $('#client_drop ul').append('<li><a href="' + url + 'clientid='+ clients_json_obj[index].id + '">'+ clients_json_obj[index].name +'</a></li>');
    }
	
	for( index in module_json_obj )
    {
		if (mid)
		{
		var h = $.param.querystring(url, 'moduleid='+ module_json_obj[index].id);
		 $('#module_drop ul').append('<li><a href="'+ h +'">'+ module_json_obj[index].name +'</a></li>');
		 if (module_json_obj[index].id == mid)
		  $('#md').html("Module - " + module_json_obj[index].name);
		}
		else
      $('#module_drop ul').append('<li><a href="' + url + 'moduleid='+ module_json_obj[index].id + '">'+ module_json_obj[index].name +'</a></li>');
    }
	
	$("#s_json").click(function() {
		var json_string = JSON.stringify(person_d.top(Infinity));
		var blob = new Blob([json_string], {type: "application/json"});
		saveAs(blob, "interval_json_" + $('#ct').text() + "_" + $('#wt').text() + '_' + $('#md').text() + ".json");

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
		saveAs(blob, "interval_csv_" + $('#ct').text() + "_" + $('#wt').text() + '_' + $('#md').text() + ".csv");
	});
	
	$("#export_drop")
	.hover(
	function(){
		$('#message').html("Export selected " + person_d.top(Infinity).length + " objects to file");
		$('#message').show();
	},
	function(){
		$('#message').hide();
	}
	);
});
</script>
</head>

<body>
<div id="selections">

 <!-- btn-group --> 
  				<div id="work_type_drop" class="btn-group"><button type="button" class="btn dropdown-toggle" data-toggle="dropdown">Work Type</button>
                   <ul class="dropdown-menu">
                  </ul>
              </div><!-- /btn-group -->


 <!-- btn-group --> <div id="client_drop" class="btn-group"><button type="button" class="btn dropdown-toggle" data-toggle="dropdown">Client</button>
  				<ul class="dropdown-menu">

                  </ul>
              </div><!-- /btn-group -->

 <!-- btn-group --> <div id="module_drop" class="btn-group"><button type="button" class="btn dropdown-toggle" data-toggle="dropdown">Module</button><ul class="dropdown-menu">

                  </ul>
              </div><!-- /btn-group -->
			  
<!-- btn-group --> <div id="export_drop" class="btn-group"><button type="button" class="btn dropdown-toggle" data-toggle="dropdown">Export</button><ul class="dropdown-menu">
	<li><a id = "s_json">JSON</a></li>
	<li><a id = "s_csv">CSV</a></li>
                  </ul>
				  <span class ="alert alert-info" id='message' style ="margin-left:4px;" hidden></span>
              </div><!-- /btn-group -->

			  <div id="reportrange" class="pull-left" style="background: #ddd; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; margin-right:4px; height: 34px; border-radius: 4px;" >
    <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
    <span></span> <b class="caret"></b>
</div>
</div>
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

<h3 id = "it">Intervals - <a href = "/interval">reset</a><?php 
if (is_null($worktypeid))
	$worktypeid = "All";
if (is_null($clientid))
	$clientid = "All";
if (is_null($moduleid))
	$moduleid = "All";
echo "<br>From ". $datebegin. " to ".$dateend;?> </h3>
<h3 class = "names" id = "wt">Work Type - <?php echo $worktypeid;?> </h3>
<h3 class = "names" id = "ct">Client - <?php echo $clientid; ?></h3>
<h3 class = "names" id = "md">Module - <?php echo $moduleid; ?></h3>
<h3 id = "nresults"></h3>
<h3 id = "sresults"></h3>
<div id = "error" hidden>Error Getting data from myinterval API, may be requesting too many data</div>
<div id = "charts">
<div id = "person"><p>Person - 
              <a class="reset" href="javascript:person_chart.filterAll();dc.redrawAll();" style = "display: none;">reset</a> </p>
</div>
<div id = "time"><p>Time - 
<a class="reset" href="javascript:time_chart.filterAll();dc.redrawAll();" style = "display: none;">reset</a> </p></div>

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
	
var client_d = interval_cf.dimension(function(d){return d.client;});
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

var module_d = interval_cf.dimension(function(d){return d.module;});
var count_by_module = module_d.group();
var module_height = count_by_module.all().length * 20 + 200;

module_chart
	.width(400)
	.height(module_height)
	.dimension(module_d)
	.group(count_by_module)
	.elasticX(true);

var project_d = interval_cf.dimension(function(d){return d.project;});
var count_by_project = project_d.group();
var project_height = count_by_project.all().length * 20 + 200;

project_chart
	.width(400)
	.height(project_height)
	.dimension(project_d)
	.group(count_by_project)
	.elasticX(true);
	
var work_type_d = interval_cf.dimension(function(d){return d.worktype;});
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



dc.renderAll();
</script>

</body>
</html>
