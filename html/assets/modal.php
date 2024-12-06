    <div class="container">
		<div class="modal fade" id="infoModal" role="dialog" style="z-index:9999999">
			<div class="modal-dialog">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<button class="close" data-dismiss="modal" type="button">&times;</button>
						<h4 class="modal-title">Modal Title</h4>
					</div>
                    <div class="modal-input">
						<input type="hidden" class="command" value="" />
                        <input type="hidden" class="output" value="" />
                    </div>
					<div class="modal-body">
						<p>Modal body</p>
					</div>
                    <div class="modal-footer">
						<button type="button" class="btn-yes btn btn-info" data-dismiss="modal">Ok</button>
                    </div>
				</div>
			</div>
		</div>
        
        <div class="modal fade" id="confirmModal" role="dialog" style="z-index:9999999">
			<div class="modal-dialog">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<button class="close" data-dismiss="modal" type="button">&times;</button>
						<h4 class="modal-title">Modal Title</h4>
					</div>
                    <div class="modal-input">
						<input type="hidden" class="command" value="" />
                        <input type="hidden" class="output" value="" />
                    </div>
					<div class="modal-body">
						<p class="modal-body-p">Modal body</p>
					</div>
                    <div class="modal-footer">
						<button type="button" class="btn-yes btn btn-success" data-dismiss="modal">Ya</button>
                        <button type="button" class="btn-no btn btn-warning" data-dismiss="modal">Tidak</button>
                    </div>
				</div>
			</div>
		</div>
        
	</div>
	
	<script>
	
	var ar_modal = [];
	var active_modal = '';
	
	function doReposition(obj_modal) {
		var modal = $(obj_modal), dialog = modal.find('.modal-dialog');
		modal.css('display', 'block');
		dialog.css("margin-top", Math.max(0, ($(window).height() - dialog.height()) / 2));
	}
	
	$(document).ready(function() {
		function reposition() {
			doReposition($(this));
		}
		$('.modal').on('show.bs.modal', reposition);
		$(window).on('resize', function() {
			$('.modal:visible').each(reposition);
		});
		
		$('#confirmModal .btn-yes').click(function(e) {
            $('#confirmModal .modal-input .output').val('1');
        });
		
		$('#confirmModal .btn-no').click(function(e) {
            $('#confirmModal .modal-input .output').val('0');
        });
		
		//save id when any modal shown
		$(".modal").on('shown.bs.modal', function() {
			active_modal = $(this).attr('id');
			ar_modal = ar_modal.concat(active_modal);
			if($(this).attr('id')=='loginModal' || $(this).attr('id')=='registerModal'){
				$(this).find('.login_imgloading').css('visibility','hidden');
				$(this).find('.login_imgloading_fb').css('visibility','hidden');
				$(this).find('.login_imgloading_g').css('visibility','hidden');
			}
			if($(this).attr('id')=='registerModal') RenderCaptcha();
			
		});
		
		//close last modal by id when user press esc
		/* $(document).keyup(function(e){
			if(e.keyCode == 27) {
				if(ar_modal.length>0){
					$('#' + ar_modal.pop()).modal('hide');
				}
			}
		}); */
		
		//remove modal on hidden
		$(".modal").on('hidden.bs.modal', function() {
			if($(this).attr('id')=='confirmModal'){	//jika confirmModal
				if($('#' + $(this).attr('id') + ' .modal-input .output').val()=='1'){
					eval($('#' + $(this).attr('id') + ' .modal-input .command').val());
				}
			}else if($(this).attr('id')=='infoModal'){
				var command = $('#' + $(this).attr('id') + ' .modal-input .command').val();
				if(command!='') eval(command);
			}
			var idx = ar_modal.indexOf($(this).attr('id'));
			if(idx > -1) ar_modal.splice(idx, 1);
			active_modal = '';
			/* if(is_toggle_modal){
				$('body').addClass('modal-open');
				is_toggle_modal = false;
			} */
			$('body').attr('style','');
		});
		
	});
	
	function showInfo(str_title, str_body, str_command){
		$('#infoModal .modal-header .modal-title').html(str_title);
		if(str_body.indexOf("<p")>-1){
			$('#infoModal .modal-body').html(str_body);
		}
		else{
			$('#infoModal .modal-body').html("<p>" + str_body + "</p>");
		}
		if(typeof str_command !== 'undefined'){
			$('#infoModal .modal-input .command').val(str_command);
		}else{
			$('#infoModal .modal-input .command').val('');
		}
		$('#infoModal').modal('show');
	}
	
	function getConfirm(str_title, str_body, str_command){
		$('#confirmModal .modal-header .modal-title').html(str_title);
		$('#confirmModal .modal-body .modal-body-p').html(str_body);
		$('#confirmModal .modal-input .command').val(str_command);
		$('#confirmModal').modal('show');
	}
	
	</script>