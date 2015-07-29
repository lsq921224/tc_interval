<?php
	$username = '6cea4jyp5he';
	$password = 'X';
	$URL = 'https://api.myintervals.com/time';
	$work_type_url = 'https://api.myintervals.com/worktype';
	$client_url = 'https://api.myintervals.com/client';
	$module_url = 'https://api.myintervals.com/module';

	$data = http_build_query(array('date' => '2015-07-02', 'limit' => 2147483647));
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
  <link rel="stylesheet" href="css/dc.css"/>
  <link rel="stylesheet" href="css/bootstrap.css"/>
  <script src="js/jquery-2.1.4.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/d3.v3.js"></script>
  <script src="js/crossfilter.js"></script>
  <script src="js/dc.js"></script>
<script> 
 var work_type_json = <?php echo json_encode($work_types)?>;
 var work_type_json_obj = JSON.parse(work_type_json)['worktype'];
 var clients_json = <?php echo json_encode($clients)?>;
 var clients_json_obj = JSON.parse(clients_json)['client'];
 var module_json = <?php echo json_encode($modules)?>;
 var module_json_obj = JSON.parse(module_json)['module'];
 //console.log(work_type_json_obj);

$(document).ready(function(){
	for( index in work_type_json_obj )
    {
      $('#work_type_drop ul').append('<li><a href="#">'+ work_type_json_obj[index].name +'</a></li>');
    }
	
	for( index in clients_json_obj )
    {
      $('#client_drop ul').append('<li><a href="#">'+ clients_json_obj[index].name +'</a></li>');
    }
	
	for( index in module_json_obj )
    {
      $('#module_drop ul').append('<li><a href="#">'+ module_json_obj[index].name +'</a></li>');
    }
});
</script>
</head>

<body>
<div id="abc">

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


</div>

<div id="show"><!-- Show the content related to the item clicked in either of the lists here --></div>
<h1>My Intervals</h1>

<div id = "person"><p>Person</p></div>
<div id = "client"><p>Client</p></div>
<div id = "project"><p>Project</p></div>
<div id = "work_type"><p>Work Type</p></div>
<div id = "module"><p>Module</p></div>
<div id = "time"><p>Time</p></div>
<div id = "active"><p>Active</p></div>
<div id = "billable"><p>Billable</p></div>
<div id = "client_active"><p>Client Active</p></div>


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

console.log(json_d);
var active_chart = dc.pieChart('#active');
var billable_chart = dc.pieChart('#billable');
var client_active_chart = dc.pieChart('#client_active');
var client_chart = dc.rowChart('#client');
var person_chart = dc.rowChart('#person');
var module_chart = dc.rowChart('#module');
var project_chart = dc.rowChart('#project');
var work_type_chart = dc.rowChart('#work_type');
//var time_chart = dc.barChart('#time');

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

client_chart
	.width(400)
	.height(600)
	.dimension(client_d)
	.group(count_by_client)
	.elasticX(true);
	
var person_d = interval_cf.dimension(function(d){return d.person;});
var count_by_person = person_d.group();

person_chart
	.width(300)
	.height(1000)
	.dimension(person_d)
	.group(count_by_person)
	.elasticX(true);

var module_d = interval_cf.dimension(function(d){return d.module;});
var count_by_module = module_d.group();

module_chart
	.width(400)
	.height(600)
	.dimension(module_d)
	.group(count_by_module)
	.elasticX(true);

var project_d = interval_cf.dimension(function(d){return d.project;});
var count_by_project = project_d.group();

project_chart
	.width(400)
	.height(600)
	.dimension(project_d)
	.group(count_by_project)
	.elasticX(true);
	
var work_type_d = interval_cf.dimension(function(d){return d.worktype;});
var count_by_work_type = work_type_d.group();

work_type_chart
	.width(300)
	.height(400)
	.dimension(work_type_d)
	.group(count_by_work_type)
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
