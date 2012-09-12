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
	$(".buttonDeleteSmall",context).button({text:false,icons: {primary: "ui-icon-trash"}}).attr('title','Save');
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