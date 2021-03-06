function common_initRTE(context)
{
	context = (typeof(context) == 'undefined') ? $(document) : context;
	
	// RTE init and functions /////////////////
	$(".RTEbold, .RTEitalic, .RTEunderline",context).button({disabled:true});
	$(".RTEsave",context).button({text:false,icons:{primary:'ui-icon-disk'}}).attr("title","Save");
	$(".RTEundo.ui-state-disabled",context).button( "option", "disabled", true );
	
	var newRTEditors = $(".editRTE",context).rte({
		width: 605,
		height: 200,
		controls_rte: rte_toolbar
	});
	
	// add all editors to an array so they can be accessed later
	if (typeof window.RTEditors != 'undefined') {
		window.RTEditors = window.RTEditors.concat(newRTEditors);
	} else {
		window.RTEditors = newRTEditors;
	}
	
	/* EXAMPLE GET HTML FROM SECTIONS
	for (var rte in RTEditors)
		if (typeof RTEditors[rte].get_content == 'function')
			alert(rte + ' = ' + RTEditors[rte].get_content());*/
	
	// init
	$(".editRTE",context).hover(function(){
		$(this).css("border-color","#FAA");
	},function(){
		$(this).css("border-color","#CCC");
	});
}

function common_initializeMenus(context)
{
	var menus = (typeof(context) == 'undefined') ? $(".menu") : $(".menu",context);
	menus.menu();
}

function common_configureDropDownButtons(context)
{
	var buttons = (typeof(context) == 'undefined') ? $(".buttonOptions") : $(".buttonOptions",context);
	
	// configure dropdown buttons
	buttons.button({icons: {primary:"ui-icon-gear", secondary:"ui-icon-triangle-1-s"}})
		   .click(function(){ return false; });
						
	common_initializeMenus(context);
}

function common_initButtons(context)
{
	context = (typeof(context) == 'undefined') ? $(document) : context;
	
	$(".uiButton",context).button();
	$(".uiButtonSet",context).buttonset();
	$(".buttonSave",context).button({icons: {primary: "ui-icon-disk"}});
	$(".buttonEdit",context).button({icons: {primary: "ui-icon-pencil"}});
	$(".buttonEnable",context).button({icons: {primary: "ui-icon-power"}});
	$(".buttonPreview",context).button({icons: {primary: "ui-icon-circle-zoomout"}});
	$(".buttonAdd",context).button({icons: {primary: "ui-icon-plus"}});
	$(".buttonDelete",context).button({icons: {primary: "ui-icon-trash"}});
	$(".buttonCancel",context).button({icons: {primary: "ui-icon-close"}});
	$(".buttonUpload",context).button({icons: {primary: "ui-icon-arrowthick-1-n"}});
	$(".buttonNext",context).button({icons: {primary: "ui-icon-arrowthick-1-e"}});
	$(".buttonSaveSmall",context).button({text:false,icons: {primary: "ui-icon-disk"}}).attr('title','Save');
	$(".buttonDeleteSmall",context).button({text:false,icons: {primary: "ui-icon-trash"}}).attr('title','Delete');
	$(".buttonSortSmall",context).button({text:false,icons: {primary: "ui-icon-arrowthick-2-n-s"}}).attr('title','Click + Drag to Sort')
								 .css('width','10px')
								 .css('height','10px')
								 .css('padding','3px');
	$(".undoChange:not(.ui-state-disabled)",context).button({text:false,icons:{primary:'ui-icon-arrowreturnthick-1-w'}}).attr("title","Undo");
	$(".undoChange.ui-state-disabled",context).button({text:false,disabled:true,icons:{primary:'ui-icon-arrowreturnthick-1-w'}}).attr("title","Undo");
}

function common_accordionSortableSetup(context,activeItem)
{
	var accordions = (typeof(context) == 'undefined') ? $(".accordionSortable") : $(".accordionSortable",context);
	var activeItem = (typeof(activeItem) == 'undefined')  ? false : activeItem;
	
	var accordionSortableStop = false;
	accordions.accordion("destroy")
			  .accordion({ header:"h3", autoHeight:false, collapsible:true, active:activeItem, autoActivate:true })
			  .sortable({axis:"y", handle:"h3", stop:function() {accordionSortableStop = true;}})
			  .click(function( event ) {
					if ( accordionSortableStop ) {
						event.stopImmediatePropagation();
						event.preventDefault();
						stop = false;
					}
				});
}

function isInt(n) {
   return n % 1 == 0;
}

function common_diff(a,b)
{
	return Math.abs(a-b);
}