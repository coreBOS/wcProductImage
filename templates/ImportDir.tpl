{include file='Buttons_List.tpl'}
<form id="frmEditView" name="EditView" method="POST" ENCTYPE="multipart/form-data" action="index.php">
<input type="hidden" name="module" value="wcProductImage">
<input type="hidden" name="action" value="ImportDirSave">
<div class="slds-p-around_small">
	<div class="slds-form-element">
		<label class="slds-form-element__label" for="imgdir">{'imgdir'|@getTranslatedString}</label>
		<div class="slds-form-element__control">
		<input type="text" id="imgdir" name="imgdir"·required="" class="slds-input" />
		</div>
	</div>
	<div class="slds-form-element slds-p-top_small">
		<div class="slds-form-element">
			<label class="slds-form-element__label" for="assignto">{'Assigned To'|@getTranslatedString}</label>
			<div class="slds-form-element__control">
			<div class="slds-select_container">
			<select class="slds-select" id="assignto" name="assignto">
			{html_options options=$AUSERS}
			</select>
			</div>
			</div>
		</div>
	</div>
	<div class="slds-form-element slds-p-top_small">
		<button class="slds-button slds-button_success">{'LBL_SEND'|@getTranslatedString}</button>
	</div>
</div>