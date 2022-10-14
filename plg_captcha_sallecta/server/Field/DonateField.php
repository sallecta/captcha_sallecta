<?php
/**
 * 
 * 
 * 
 * 
 * 	 Copyright © 2022 - All rights reserved.
 * @license      GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace CAPTCHASALLECTA\Field;

\defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;

/**
 * Form Field to display a list of the layouts for module display from the module or template overrides.
 *
 * @since  1.6
 */
class DonateField extends FormField
{
	protected $type = 'donate';
	
	protected function getLabel(){
		return '';
	}
	protected function getInput()
	{
		$html = '
		<div style="text-align: center; -webkit-filter: drop-shadow( 3px 3px 4px rgba(0, 0, 0, 0.3)); filter: drop-shadow( 3px 3px 4px rgba(0, 0, 0, 0.3));">
		<h2>'.Text::_("PLG_CAPTCHASALLECTA_DONATE_TITLE").'</h2>
		<div id="smart-button-container" style="max-width: 540px; margin: 0 auto;">
			<div style="text-align: center">
				<label for="description">'.Text::_("PLG_CAPTCHASALLECTA_DONATE_DESC_LBL").'</label>
				<input type="text" name="descriptionInput" id="description" class="form-control" maxlength="127" value="'.Text::_("PLG_CAPTCHASALLECTA_DONATE_DESC_VAL").'">
			</div>
			<p id="descriptionError" style="visibility: hidden; color:red; text-align: center;">'.Text::_("PLG_CAPTCHASALLECTA_DONATE_DESC_ERR").'</p>
			
			<div style="text-align: center">
				<label for="amount">'.Text::_("PLG_CAPTCHASALLECTA_DONATE_AMOUNT_LBL").'</label>
				<input name="amountInput" type="number" id="amount" class="form-control" value="5" >
			</div>
			<p id="priceLabelError" style="visibility: hidden; color:red; text-align: center;">'.Text::_("PLG_CAPTCHASALLECTA_DONATE_AMOUNT_ERR").'</p>
			
			<div id="invoiceidDiv" style="text-align: center; display: none;">
				<label for="invoiceid"></label>
				<input name="invoiceid" maxlength="127" type="text" id="invoiceid" class="form-control" value="PLG_captchasallecta" >
			</div>
			<p id="invoiceidError" style="visibility: hidden; color:red; text-align: center;">Please enter an Invoice ID</p>
			
			<div style="text-align: center; margin-top: 0.625rem;" id="paypal-button-container"></div>
			
			<p style="color:#3300ff;">'.Text::_("PLG_CAPTCHASALLECTA_DONATE_TEXT").'</p>
		</div>
		</div>
		<script src="https://www.paypal.com/sdk/js?client-id=AanroroJditQnxtadgNxWwPMMFQZUfIt0YkQpmHn7Rbe5JIe8YeDgOreSFesBkfBn9ZhxxXzJ9DHdvWo&enable-funding=venmo,giropay&currency=EUR" data-sdk-integration-source="button-factory"></script>
		  
		<script>
		function initPayPalButton() {
			var description = document.querySelector("#smart-button-container #description");
			var amount = document.querySelector("#smart-button-container #amount");
			var descriptionError = document.querySelector("#smart-button-container #descriptionError");
			var priceError = document.querySelector("#smart-button-container #priceLabelError");
			var invoiceid = document.querySelector("#smart-button-container #invoiceid");
			var invoiceidError = document.querySelector("#smart-button-container #invoiceidError");
			var invoiceidDiv = document.querySelector("#smart-button-container #invoiceidDiv");

			var elArr = [description, amount, invoiceid];

			var purchase_units           = [];
				purchase_units[0]        = {};
				purchase_units[0].amount = {};

			function validate(event) {
				return event.value.length > 0;
			}

			paypal.Buttons({
				style: {
					color: "blue",
					shape: "pill",
					label: "paypal",
					layout: "vertical"
				},

				onInit: function(data, actions){
					var result = elArr.every(validate);
					if (result) {
						actions.enable();
					} else {
						actions.disable();
					}

					elArr.forEach(function (item) {
						item.addEventListener("keyup", function (event) {
							var result = elArr.every(validate);
							if (result) {
							  actions.enable();
							} else {
							  actions.disable();
							}
						});
					});
				},

				onClick: function(){
					if (description.value.length < 1) {
					  descriptionError.style.visibility = "visible";
					} else {
					  descriptionError.style.visibility = "hidden";
					}

					if (amount.value.length < 1) {
					  priceError.style.visibility = "visible";
					} else {
					  priceError.style.visibility = "hidden";
					}

					if (invoiceid.value.length < 1) {
					  invoiceidError.style.visibility = "visible";
					} else {
					  invoiceidError.style.visibility = "hidden";
					}

					purchase_units[0].description  = description.value;
					purchase_units[0].amount.value = amount.value;
					purchase_units[0].invoice_id   = invoiceid.value;
				},

				createOrder: function(data, actions){
					return actions.order.create({
						purchase_units: purchase_units,
					});
				},

				onApprove: function(data, actions){
					return actions.order.capture().then(function (orderData) {

					  // Full available details
					  //console.log("Capture result", orderData, JSON.stringify(orderData, null, 2));

					  // Show a success message within this page, e.g.
					  const element = document.getElementById("paypal-button-container");
					  element.innerHTML = "";
					  element.innerHTML = "<h3>'.Text::_("PLG_CAPTCHASALLECTA_DONATE_DESC_LBL").'</h3>";

					  // Or go to another URL:  actions.redirect("thank_you.html");
					  
					});
				},
			  
				onCancel: function(data){
					console.log("cancel");
				},
  

				onError: function(err){
					console.log(err);
				}
			}).render("#paypal-button-container");
		}
		initPayPalButton();
		</script>
		
		';
		
		return $html;
	}
}
