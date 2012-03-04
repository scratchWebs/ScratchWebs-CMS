$(document).ready(function(e){
	
	// RTE init and functions /////////////////
		$(".RTEbold, .RTEitalic, .RTEunderline").button({disabled:true});
		$(".RTEsave").button({text:false,icons:{primary:'ui-icon-disk'}}).attr("title","Save");
		//$(".RTEundo").button({text:false,icons:{primary:'ui-icon-arrowreturnthick-1-w'}}).attr("title","Undo");
		$(".RTEundo.ui-state-disabled").button( "option", "disabled", true );
	
		window.RTEditors = $(".editRTE").rte({
			width: 605,
			height: 200,
			controls_rte: rte_toolbar
		});
		
		/* EXAMPLE GET HTML FROM SECTIONS
		for (var rte in RTEditors)
			if (typeof RTEditors[rte].get_content == 'function')
				alert(rte + ' = ' + RTEditors[rte].get_content());*/
		
		
		$(".editRTE").hover(function(){$(this).css("border-color","#FAA");},function(){$(this).css("border-color","#CCC");})	// init
	//		.focusin(function() {$(this).css("border-color","#FAA").css("border-style","solid")})								// init
	//		.focusout(function() {$(this).css("border-color","#CCC").css("border-style","dashed")})	   
	//		.keyup(function(){																									// on item change keyup
	//			if($(this).html() != $("div[orig='" + $(this).attr("rel") + "']")) $(this).css("background-color","#FFBFBF").css("color","#F00");		// and set to red
	//			if($(this).html() == $("div[orig='" + $(this).attr("rel") + "']")) $(this).css("background-color","#fff").css("color","#000");			// and set to normal
	//			});		
	
});

// ajax update
function swSection_html_update(id,undoButton)
{
	var rte = RTEditors['divSectionHTML' + id];		// get the RTE object
	
	var data = {
		update_object:'swSection',
		update_object_id:id,
		update_type:'section_update_html',
		value:rte.get_content()			// get the html from the RTE object
	}
	$.swUpdateSession(data,function(response){
	
		if (response.updateKey == ''){
			undoButton.button("option","disabled",true ).data('updateKey','')							// disable undo button
			.parent().parent().removeClass('ui-state-error').prev().removeClass('ui-state-error');
		} else {
			undoButton.button("option","disabled",false ).data('updateKey',response.updateKey)			// enable undo button
			.parent().parent().addClass('ui-state-error').prev().addClass('ui-state-error');			// add highlighting	
		}
		
	})
}
function swSection_html_undo(id,undoButton)
{
	if (undoButton.hasClass('ui-state-disabled')) return;
	
	$.swUndo({update_key:undoButton.data('updateKey')},function(response){
		var rte = RTEditors['divSectionHTML' + id];		// get the RTE object
		rte.set_content(response);						// set the html
		undoButton.button("option","disabled",true ).data('updateKey','')							// disable undo button
		.parent().parent().removeClass('ui-state-error').prev().removeClass('ui-state-error');		// remove highlighting
	});
}