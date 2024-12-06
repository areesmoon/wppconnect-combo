<div class="container">
	<div class="row">
		<div class="col-sm-12" style="padding-top:15px">
			&nbsp;
		</div>
	</div>
	<div class="row">
		<div class="col-sm-2"></div>
		<div class="col-sm-8">
			<div class="jumbotron text-center">
				<h1>Whatsapp BOT Control</h1>  
			</div>
		</div>
		<div class="col-sm-2"></div>
	</div>
	<div class="row">
		<div class="col-sm-2"></div>
		<div class="col-sm-8 text-center">
			<div id="div_checking" style="display: inline-block;">
				<div class="alert alert-warning">
					<h3 id="title_checking">Checking Whatsapp BOT status!&nbsp;<img id="img_loading_checking" src="images/loading1.gif" /></h3>
				</div>
			</div>
			<div id="div_qrcode" style="display: none;">
				<div class="alert alert-warning text-center">
					<h3>Scan the following QR Code!</h3>
				</div>
				<p><div id="qrcode" style="width:360px; height:360px; margin:5px;"></div></p>
			</div>
			<div id="div_online" style="display: none;">
				<div class="alert alert-success">
					<h3 id="title_online">Whatsapp BOT is already logged in!</h3>
				</div>
				<p>Use the following button stop or logout Whatsapp BOT</p>
				<p><a class="btn btn-primary btn-sm" href="javascript:stopWABot();" title="Stop WA Bot"><i class="fa fa-stop fa-lg"></i></a>&nbsp;<a class="btn btn-warning btn-sm" href="javascript:logoutWABot();" title="Logout WA Bot"><strong>Logout WA BOT</strong>&nbsp;<i class="fa fa-sign-out fa-lg"></i></a>&nbsp;<img id="img_loading_online" src="images/loading1.gif" style="visibility:hidden" /></p>
			</div>
			<div id="div_offline" style="display: none;">
				<div class="alert alert-danger">
					<h3>This Whatsapp BOT is offline!</h3>
				</div>
				<p>Click this button to start Whatsapp BOT then scan the QR Code</p>
				<p><a class="btn btn-success btn-sm" href="javascript:startWABot();" title="Start WA Bot"><strong>Start WA BOT</strong>&nbsp;<i class="fa fa-play fa-lg"></i></a>&nbsp;<img id="img_loading_offline" src="images/loading1.gif" style="visibility:hidden" /></p>
			</div>
		</div>
		<div class="col-sm-2"></div>
	</div>
</div>

<script>

var controller_url = '<?=base_url();?>';
var tmrQrCode;
var tmrStatus;
var lastStatus;
var is_starting = false;

$(document).ready(function(e) {
	checkStatus(true);
});

function hideAllStatus(show_checking = false){
	$('#div_checking').css('display', show_checking ? 'inline-block' : 'none');
	$('#div_offline').css('display', 'none');
	$('#div_online').css('display', 'none');
	$('#div_qrcode').css('display', 'none');
}

function checkStatusBg(){
	$.post('status',{},function(data){
		if(data.status!==lastStatus){
			checkStatus();
		}
	},'json');
}

function checkStatus(show_checking=false){
	clearInterval(tmrStatus);
	if(is_starting) $('#title_checking').html('Starting Whatsapp! Please wait&nbsp;<img id="img_loading_checking" src="images/loading1.gif" />');
	if(show_checking) $('#div_checking').css('display', 'inline-block');
	$.post('status',{},function(data){
		if(data.status=='CLOSED'){	// CLOSED, INITIALIZING, QRCODE, CONNECTED
			if(is_starting){
				setTimeout(checkStatus, 1000)
			}else{
				hideAllStatus();
				$('#div_offline').css('display', 'inline-block');
				tmrStatus = setInterval(checkStatusBg, 5000);
			}
		}else if(data.status=='CONNECTED'){
			hideAllStatus();
			if(is_starting){
				$('#title_checking').html("Successfully login Whatsapp BOT!");
			}else{
				$('#title_checking').html("Whatsapp BOT is already logged in!");
			}
			is_starting = false;
			$('#div_online').css('display', 'inline-block');
			clearInterval(tmrQrCode);
			tmrStatus = setInterval(checkStatusBg, 5000);
		}else if(data.status=='QRCODE'){
			is_starting = false;
			hideAllStatus();
			$('#div_qrcode').css('display', 'inline-block');
			clearInterval(tmrStatus);
			tmrQrCode = setInterval(funRefreshQrCode, 2000);
		}else if(data.status=='INITIALIZING'){
			is_starting = true;
			// refresh until not INITIALIZING
			setTimeout(checkStatus, 1000)
		}
		lastStatus = data.status;
	},'json');
}

function startWABot(){
	getConfirm('Confirmation', 'Start WA Bot?', "doStartWABot();");
}

function doStartWABot(){
	clearInterval(tmrStatus);
	document.getElementById('img_loading_offline').style.visibility = 'visible';
	$.post('start',{},function(data){
		document.getElementById('img_loading_offline').style.visibility = 'hidden';
		is_starting = true;
		hideAllStatus(true);
		checkStatus(true);
	},'json');
}

function stopWABot(){
	getConfirm('Confirmation', 'Stop WA BOT?', "doStopWABot();");
}

function doStopWABot(){
	clearInterval(tmrStatus);
	document.getElementById('img_loading_online').style.visibility = 'visible';
	$.post('stop',{},function(data){
		document.getElementById('img_loading_online').style.visibility = 'hidden';
		hideAllStatus(true);
		checkStatus(true);
	},'json');
}

function logoutWABot(){
	getConfirm('Confirmation', 'Logout Whatsapp?', "doLogoutWABot();");
}

function doLogoutWABot(){
	clearInterval(tmrStatus);
	document.getElementById('img_loading_online').style.visibility = 'visible';
	$.post('logout',{},function(data){
		document.getElementById('img_loading_online').style.visibility = 'hidden';
		hideAllStatus(true);
		checkStatus(true);
	},'json');
}

function funRefreshQrCode(){
	$.post('qrcode',{},function(data){
		if(data.status=="QRCODE"){
			$('#qrcode').html('');
			$('#qrcode').qrcode({width: 320,height: 320,text: data.urlcode});
		}else{
			clearInterval(tmrQrCode);
			$('#qrcode').html('');
			checkStatus();
		}
	},'json');
}

function cancelStartWABot(){
	$.post(controller_url + '/setstartrequest/' + waClientId + '/-1',{'apikey': ''},function(data){},'text');
	closeModal('qrCodeModal');
}

function closeModal(modal_id)
{
	$('#' + modal_id).modal("hide");
}

</script>