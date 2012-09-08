
// init Portfolio's
$(document).ready(function(e) {
    swPortfolio_sortableAccordionSetup();
	
	$(".undoPortfolioSort")
		.button({icons:{primary:'ui-icon-arrowreturnthick-1-w'}})		// init button style
		.click(function(e) {
			var button = $(this);
			$.swUndo({update_key:$(this).data('updateKey')},function(response){
				var sortablePortfolio = button.siblings('.portfolioSortableAccordion');
				var sortIdArray = response.undoResponse.split(',');
				
				for (var i=0; i < sortIdArray.length; i++) {
					swGallery_getById(sortIdArray[i]).appendTo(sortablePortfolio);
				}
				
				button.fadeOut('fast');
			});
		});	
});

function swPortfolio_sortableAccordionSetup(context,activeItem)
{
	var accordions = (typeof(context) == 'undefined') ? $(".portfolioSortableAccordion") : $(".portfolioSortableAccordion",context);
	activeItem = (typeof(activeItem) == 'undefined')  ? false : activeItem;
	
	var accordionSortableStop = false;
	accordions.accordion("destroy")
			  .accordion({ header:"h3", autoHeight:false, collapsible:true, active:activeItem, autoActivate:true })
			  .sortable({axis:"y", handle:"h3", stop:function() {accordionSortableStop = true}, update:function(event,ui) { 
					swPortfolio_updateGalleryOrder($(this).data('portfolioid'),$(this));
				}})
			  .click(function( event ) {
					if ( accordionSortableStop ) {
						event.stopImmediatePropagation();
						event.preventDefault();
						stop = false;
					}
				});
}

function swPortfolio_updateGalleryOrder(portfolioID,sortableGalleries)
{
	var galleriesInOrderById = sortableGalleries.sortable("serialize",{key:'galleryid',attribute:'data-id',expression:'(.*)'});
	
	var data = {
		update_object:'swPortfolio',		// update session parameters
		update_object_id:portfolioID,		// update session parameters
		update_type:'sort_galleries',
		galleries_in_order_by_id:galleriesInOrderById
	}
	
	$.swUpdateSession(data,function(response){
		if (response.updateKey == '')
			sortableGalleries.siblings(".undoPortfolioSort").fadeOut('fast');
		else
			sortableGalleries.siblings(".undoPortfolioSort").fadeIn('fast').data('updateKey',response.updateKey);
	});
}

function swPortfolio_addGallery(portfolioID,addGalleryBox,theForm,container)
{
	var data = $.extend(
	{
		update_object:'swPortfolio',
		update_object_id:portfolioID,
		update_type:'add_gallery'
	},
	theForm.swSerialize())
	
	if (data.gallery_name == '') {
		alert('Please enter a name for the new Gallery');
		return;	// cancel the update if the gallery name is null
	}
	
	addGalleryBox.slideUp();
	
	$.swUpdateSession(data,function(response){
		var id = $(response.responseHTML).attr('data-id');		// get the new gallery id from the response
		container.append(response.responseHTML);	// add the gallery to the page
		swGallery_init(id);							// init the gallery
		swPortfolio_sortableAccordionSetup(container.parent(),container.children().length-1);	// removing item breakes accordion, so set it up again
		
		theForm[0].reset();			// reset the form
	})
}

function swPortfolio_deleteGallery(portfolioID,galleryID)
{
	if (confirm('Are you sure you want to delete?')) {
		var data = {
			update_object:'swPortfolio',
			update_object_id:portfolioID,
			update_type:'delete_gallery',
			gallery_id:galleryID
		}
		
		$.swUpdateSession(data, function(response){
			var gallery = swGallery_getById(galleryID);
			gallery.slideUp(function(){
				var portfolioContainer = gallery.parent().parent();
				gallery.remove();
				swPortfolio_sortableAccordionSetup(portfolioContainer);	// removing item breakes accordion, so set it up again
			});
		});
	}
}

function swPortfolio_enableGallery(portfolioID,galleryID,enable)
{
	var enable_text = (enable) ? 'Enable' : 'Disable';

	if (confirm('Are you sure you want to '+ enable_text +'?')) {
		var data = {
			update_object:'swPortfolio',		// update session parameters
			update_object_id:portfolioID,
			update_type:'enable_gallery',
			gallery_id:galleryID,
			enable:enable
		}
		
		$.swUpdateSession(data, function(response){
			var gallery = swGallery_getById(galleryID);				// get the gallery
			var galleryPosition = gallery.index();					// get the gallerys position
			var portfolioContainer = gallery.parent().parent();		// get the portfolio contianer
			
			gallery.replaceWith(response.responseHTML);				// replace the gallery with the updated one
			swGallery_init(galleryID);								// init the replaced gallery
			swPortfolio_sortableAccordionSetup(portfolioContainer,	// removing item breakes accordion, so set it up again
											   galleryPosition);	// and ensure we open the corrent gallery
		});
	}
}

function swPortfolio_renameGallery(portfolioID,galleryID,theForm,theRenameBox)
{
	theRenameBox.slideUp();
	
	var data = $.extend({
		update_object:'swPortfolio',
		update_object_id:portfolioID,
		update_type:'rename_gallery',
		gallery_id:galleryID
	},theForm.swSerialize());
	
	$.swUpdateSession(data,function(){
		var gallery = swGallery_getById(galleryID);
		var disabledText = (gallery.data('enabled') == false) ? ' (Disabled)' : '';
		$('H3>a',gallery).text(data.gallery_name + disabledText);	// set the new name
	});
}